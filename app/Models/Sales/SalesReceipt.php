<?php

namespace App\Models\Sales;

use App\Domain\Documents\Accounts;
use App\Domain\Documents\PaymentMethod;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Models\Company\Branch;
use App\Models\GeneralLedger\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Sales Receipt: money in from a customer against its open invoices and down
 * payments, credit notes applied when "use credit" is on, a discount or
 * write-off per line. Each line is an allocation; an invoice is paid only
 * through these.
 */
class SalesReceipt extends Model implements Postable
{
    use PostsToLedger;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'cheque_date' => 'date', 'amount' => 'integer', 'use_credit' => 'boolean', 'payment_method' => PaymentMethod::class];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(SalesReceiptLine::class)->orderBy('sort');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function refreshTotal(): void
    {
        $this->forceFill(['amount' => (int) $this->lines()->sum('amount')])->saveQuietly();
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        $receivable = Accounts::receivable($this->customer);
        $received = 0;
        foreach ($this->lines()->with('receivable')->get() as $line) {
            $amount = (int) $line->amount;
            $discount = (int) $line->discount;
            $builder->signed($receivable, -($amount + $discount), $line->receivable?->number);
            if ($discount !== 0) {
                $builder->signed($line->discount_account_id ?? Accounts::salesDiscount($this->customer), $discount, 'Settlement discount');
            }
            $builder->allocate([
                'receivable_type' => $line->receivable_type,
                'receivable_id' => $line->receivable_id,
                'amount' => $amount,
                'discount' => $discount,
                'discount_account_id' => $line->discount_account_id,
            ]);
            $received += $amount;
        }
        $builder->debit($this->bank_account_id, $received, $this->description ?? "Receipt from {$this->customer->name}");
    }
}
