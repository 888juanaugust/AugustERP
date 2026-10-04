<?php

namespace App\Models\Sales;

use App\Domain\Documents\Accounts;
use App\Domain\Documents\PricedDocument;
use App\Domain\Inventory\Costing\CostEngine;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Models\Company\Branch;
use App\Models\Inventory\StockMovement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Delivery Order: goods out to the customer, pulled from orders, before the
 * invoice. Stock goes out at its average cost into Goods Delivered, Not
 * Invoiced; the invoice turns that into cost of goods sold.
 */
class Delivery extends Model implements Postable
{
    use PostsToLedger, PricedDocument;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'ship_date' => 'date', 'taxable' => 'boolean', 'inclusive_tax' => 'boolean', 'is_printed' => 'boolean',
            'subtotal' => 'integer', 'discount_amount' => 'integer', 'charges_total' => 'integer', 'dpp_total' => 'integer', 'tax_total' => 'integer', 'total' => 'integer'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(DeliveryLine::class)->orderBy('sort');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        $engine = app(CostEngine::class);
        $transit = Accounts::goodsDeliveredNotInvoiced();
        foreach ($this->lines()->with('item.category')->get() as $line) {
            if (! $line->item->item_type->isStocked()) {
                continue;
            }
            $cost = $engine->issueCost($line->item_id, $line->warehouse_id, $this->trans_date, (string) $line->base_quantity);
            $builder->stock(['item_id' => $line->item_id, 'warehouse_id' => $line->warehouse_id, 'direction' => StockMovement::OUT, 'base_quantity' => (string) $line->base_quantity,
                'unit_cost' => $cost['unit_cost'], 'total_cost' => $cost['total_cost'], 'source_line_type' => 'delivery_line', 'source_line_id' => $line->id]);
            $builder->debit($transit, $cost['total_cost'], $line->memo);
            $builder->credit(Accounts::inventory($line->item), $cost['total_cost'], $line->memo);
        }
    }
}
