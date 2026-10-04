<?php

namespace App\Models\CashBank;

use App\Domain\CashBank\Contracts\GiroSource;
use App\Domain\CashBank\GiroDetails;
use App\Domain\Documents\Accounts;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Models\Company\Branch;
use App\Models\GeneralLedger\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

/**
 * Payment: money out of a cash or bank account to any accounts, one line
 * each (K-02). Paid by giro, it credits giros payable until the giro clears.
 */
class CashPayment extends Model implements GiroSource, Postable
{
    use PostsToLedger;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'cheque_date' => 'date', 'amount' => 'integer', 'is_printed' => 'boolean'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(CashPaymentLine::class)->orderBy('sort');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'bank_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function giro(): MorphOne
    {
        return $this->morphOne(Giro::class, 'source');
    }

    public function refreshTotal(): void
    {
        $this->forceFill(['amount' => (int) $this->lines()->sum('amount')])->saveQuietly();
    }

    public function giroDetails(): ?GiroDetails
    {
        if (blank($this->cheque_no)) {
            return null;
        }

        return new GiroDetails(Giro::OUT, (string) $this->cheque_no, $this->cheque_date?->toDateString(), (int) $this->amount, $this->payee);
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        if ($this->giro?->isBounced()) {
            return; // a bounced giro paid nothing
        }
        $total = 0;
        foreach ($this->lines as $line) {
            $builder->signed($line->account_id, (int) $line->amount, $line->memo, $line->branch_id);
            $total += (int) $line->amount;
        }
        $credit = $this->giro?->isOutstanding() ? Accounts::giroPayable() : $this->bank_account_id;
        $builder->credit($credit, $total, $this->description ?: ($this->payee ? "Payment to {$this->payee}" : null));
    }
}
