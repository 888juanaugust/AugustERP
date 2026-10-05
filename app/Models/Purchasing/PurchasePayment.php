<?php

namespace App\Models\Purchasing;

use App\Domain\Approval\RequiresApproval;
use App\Domain\CashBank\Contracts\GiroSource;
use App\Domain\CashBank\GiroDetails;
use App\Domain\Documents\Accounts;
use App\Domain\Documents\PaymentMethod;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Models\CashBank\Giro;
use App\Models\Company\Branch;
use App\Models\GeneralLedger\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Purchase Payment: money out of a bank account against one vendor's
 * invoices, down payments and returns (as credits), with a discount taken
 * per line. Each line is an allocation in the settlement ledger.
 */
class PurchasePayment extends Model implements GiroSource, Postable
{
    use PostsToLedger;
    use RequiresApproval;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'cheque_date' => 'date', 'amount' => 'integer', 'payment_method' => PaymentMethod::class];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchasePaymentLine::class)->orderBy('sort');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
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

    public function giro(): MorphOne
    {
        return $this->morphOne(Giro::class, 'source');
    }

    public function giroDetails(): ?GiroDetails
    {
        if (! $this->payment_method?->isCheque()) {
            return null;
        }

        return new GiroDetails(Giro::OUT, (string) ($this->cheque_no ?: $this->number), $this->cheque_date?->toDateString(), (int) $this->amount, $this->vendor?->name, $this->vendor_id);
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        if ($this->giro?->isBounced()) {
            return; // a bounced giro paid nothing: the bills are open again
        }
        $payable = Accounts::payable($this->vendor);
        $paid = 0;
        foreach ($this->lines()->with('payable')->get() as $line) {
            $amount = (int) $line->amount;
            $discount = (int) $line->discount;
            $builder->signed($payable, $amount + $discount, $line->payable?->number);
            if ($discount !== 0) {
                $builder->signed($line->discount_account_id ?? Accounts::purchaseDiscounts(), -$discount, 'Payment discount');
            }
            $builder->allocate([
                'receivable_type' => $line->payable_type,
                'receivable_id' => $line->payable_id,
                'amount' => $amount,
                'discount' => $discount,
                'discount_account_id' => $line->discount_account_id,
            ]);
            $paid += $amount;
        }
        $credit = $this->giro?->isOutstanding() ? Accounts::giroPayable() : $this->bank_account_id;
        $builder->credit($credit, $paid, $this->description ?? "Payment to {$this->vendor->name}");
    }
}
