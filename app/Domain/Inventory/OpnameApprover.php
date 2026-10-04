<?php

declare(strict_types=1);

namespace App\Domain\Inventory;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Audit\Auditor;
use App\Domain\Pengaturan\Saklar;
use App\Domain\Posting\DocumentRepository;
use App\Domain\Shared\Format;
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\InventoryAdjustmentLine;
use App\Models\Inventory\ItemCost;
use App\Models\Inventory\StockOpnameResult;
use App\Models\User;
use Brick\Math\BigDecimal;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Approving a stock opname result posts the differences between the count
 * and the system as one inventory adjustment in the order's warehouse. With
 * segregation of duties on, the approver is never the person who counted.
 */
final class OpnameApprover
{
    public function __construct(private readonly DocumentRepository $documents, private readonly HakAkses $akses) {}

    public function approve(StockOpnameResult $result, User $approver): ?InventoryAdjustment
    {
        if ($result->isApproved()) {
            throw new RuntimeException("{$result->number} is already approved.");
        }
        if (! $this->akses->allowsSpecial($approver, HakKhusus::ApproveTransactions)) {
            throw new RuntimeException('Approving a stock count takes the "approve transactions" right.');
        }
        if (Saklar::SegregationOfDuties->isOn() && $result->created_by !== null && $result->created_by === $approver->id) {
            throw new RuntimeException('Segregation of duties: the person who entered the count cannot approve it.');
        }

        return DB::transaction(function () use ($result, $approver): ?InventoryAdjustment {
            $result->load(['order', 'lines.item.units']);
            $warehouse = $result->order->warehouse_id;
            $lines = [];
            $sort = 0;

            foreach ($result->lines as $line) {
                $diff = BigDecimal::of((string) $line->base_quantity)->minus((string) $line->system_qty);
                if ($diff->isZero()) {
                    continue;
                }
                $lines[] = [
                    'sort' => $sort++,
                    'item_id' => $line->item_id,
                    'adjustment_type' => InventoryAdjustmentLine::QUANTITY,
                    'quantity' => (string) $diff,
                    'unit_id' => $line->item->unit1_id,
                    'base_quantity' => (string) $diff,
                    'unit_cost' => $diff->isPositive() ? (string) (ItemCost::query()->where('item_id', $line->item_id)->where('warehouse_id', $warehouse)->value('avg_cost') ?? $line->item->purchase_price) : '0',
                    'total_cost' => 0,
                    'warehouse_id' => $warehouse,
                    'memo' => "Stock count {$result->number}: counted ".Format::quantity((string) $line->base_quantity).', system '.Format::quantity((string) $line->system_qty),
                ];
            }

            $adjustment = null;
            if ($lines !== []) {
                $adjustment = InventoryAdjustment::query()->create([
                    'number' => 'OPN-'.$result->number,
                    'trans_date' => $result->trans_date,
                    'description' => "Stock count variance, {$result->number}",
                    'created_by' => $approver->id,
                ]);
                $adjustment->lines()->createMany($lines);
                $this->documents->created($adjustment);
            }

            $result->forceFill([
                'status' => StockOpnameResult::APPROVED,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'inventory_adjustment_id' => $adjustment?->id,
            ])->saveQuietly();
            $result->order->forceFill(['status' => 'counted'])->saveQuietly();
            Auditor::log('approved', $result, $result->number, ['adjustment' => $adjustment?->number]);

            return $adjustment;
        });
    }

    /** Fills system_qty on every line from the stock cache of the order's warehouse. */
    public function snapshotSystemQuantities(StockOpnameResult $result): void
    {
        $warehouse = $result->order->warehouse_id;
        foreach ($result->lines as $line) {
            $qty = ItemCost::query()->where('item_id', $line->item_id)->where('warehouse_id', $warehouse)->value('qty_on_hand') ?? 0;
            $line->forceFill(['system_qty' => (string) $qty])->saveQuietly();
        }
    }
}
