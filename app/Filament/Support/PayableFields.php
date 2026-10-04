<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Settlement\SettlementService;
use App\Domain\Shared\Format;
use App\Models\Purchasing\PurchaseDownPayment;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/** The open documents of a vendor that a payment can settle: invoices, down payments and returns (as credits). */
final class PayableFields
{
    /** @return Collection<string, array{model: Model, label: string, balance: int}> keyed "type:id" */
    public static function openFor(int $vendorId): Collection
    {
        $settlement = app(SettlementService::class);
        $out = collect();
        foreach ([PurchaseInvoice::class, PurchaseDownPayment::class, PurchaseReturn::class] as $class) {
            $docs = $class::query()->where('vendor_id', $vendorId)->where('payment_status', '!=', 'paid')->orderBy('trans_date')->get();
            foreach ($docs as $doc) {
                $balance = $settlement->balance($doc);
                if ($balance === 0) {
                    continue;
                }
                $key = $doc->getMorphClass().':'.$doc->id;
                $out[$key] = ['model' => $doc, 'label' => $doc->number.' · '.Format::date($doc->trans_date).' · '.Format::rupiah((int) $doc->total).' · open '.Format::rupiah($balance), 'balance' => $balance];
            }
        }

        return $out;
    }

    public static function resolve(string $key): ?Model
    {
        [$type, $id] = array_pad(explode(':', $key, 2), 2, null);
        $class = Relation::getMorphedModel($type) ?? null;

        return $class && $id ? $class::query()->find((int) $id) : null;
    }
}
