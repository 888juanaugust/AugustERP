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
use App\Models\User;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Sales Return: goods back from the customer. Stock returns at the cost it
 * left with (the current average when unknown); the credit note reverses
 * revenue and VAT out and reduces what the customer owes, applied in a
 * receipt with "use credit".
 */
class SalesReturn extends Model implements Postable
{
    use PostsToLedger, PricedDocument;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'taxable' => 'boolean', 'inclusive_tax' => 'boolean', 'is_printed' => 'boolean',
            'subtotal' => 'integer', 'discount_amount' => 'integer', 'charges_total' => 'integer', 'dpp_total' => 'integer', 'tax_total' => 'integer', 'total' => 'integer', 'paid_amount' => 'integer'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesReturnLine::class)->orderBy('sort');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(SalesReturnCharge::class)->orderBy('sort');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function isCredit(): bool
    {
        return true;
    }

    public function refreshTotal(): void
    {
        $this->refreshPricedTotal();
        $this->forceFill(['status' => $this->payment_status === 'paid' ? 'processed' : 'pending'])->saveQuietly();
    }

    public function balance(): int
    {
        return -($this->total - $this->paid_amount);
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        $engine = app(CostEngine::class);
        $customer = $this->customer;

        foreach ($this->lines()->with(['item.category', 'taxCode'])->get() as $line) {
            $net = $line->netAmount();
            $builder->debit(Accounts::salesReturn($line->item, $customer), $net, $line->memo);
            if ((int) $line->tax_amount > 0) {
                $builder->debit(Accounts::vatOut($line->taxCode), (int) $line->tax_amount, 'VAT out reversed');
            }
            if (! $line->item->item_type->isStocked()) {
                continue;
            }
            $unitCost = $this->costItLeftWith($line) ?? $engine->costAt($line->item_id, $line->warehouse_id, $this->trans_date);
            $total = BigDecimal::of($unitCost)->multipliedBy((string) $line->base_quantity)->toScale(0, RoundingMode::HalfUp)->toInt();
            $builder->stock(['item_id' => $line->item_id, 'warehouse_id' => $line->warehouse_id, 'direction' => StockMovement::IN, 'base_quantity' => (string) $line->base_quantity,
                'unit_cost' => $unitCost, 'total_cost' => $total, 'source_line_type' => 'sales_return_line', 'source_line_id' => $line->id]);
            $builder->debit(Accounts::inventory($line->item), $total, $line->memo);
            $builder->credit(Accounts::costOfSales($line->item, $customer), $total, $line->memo);
        }
        foreach ($this->charges as $charge) {
            $builder->debit($charge->account_id, (int) $charge->amount, $charge->description ?? 'Other charges');
        }
        $builder->credit(Accounts::receivable($customer), (int) $this->total, $this->description ?? "Return from {$customer->name}");
    }

    /** The unit cost the goods left with, when the return points at an invoice whose line moved stock. */
    private function costItLeftWith(SalesReturnLine $line): ?string
    {
        if ($this->source_type !== 'sales_invoice' || ! $this->source_id) {
            return null;
        }
        $invoiceLine = SalesInvoiceLine::query()->where('sales_invoice_id', $this->source_id)->where('item_id', $line->item_id)->first();
        if ($invoiceLine === null) {
            return null;
        }
        $movement = StockMovement::query()->active()->where('source_line_type', 'sales_invoice_line')->where('source_line_id', $invoiceLine->id)->first()
            ?? ($invoiceLine->source_line_type === 'delivery_line' ? StockMovement::query()->active()->where('source_line_type', 'delivery_line')->where('source_line_id', $invoiceLine->source_line_id)->first() : null);

        return $movement ? (string) $movement->unit_cost : null;
    }
}
