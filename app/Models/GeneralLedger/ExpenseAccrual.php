<?php

namespace App\Models\GeneralLedger;

use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Domain\Settlement\Contracts\PaidByPayment;
use App\Domain\Settlement\SettlementService;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Expenses booked against a payable account, paid later by a payment line that settles it. */
class ExpenseAccrual extends Model implements PaidByPayment, Postable
{
    use PostsToLedger;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'due_date' => 'date', 'total' => 'integer', 'paid_amount' => 'integer', 'is_printed' => 'boolean'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(ExpenseAccrualLine::class)->orderBy('sort');
    }

    public function payableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'payable_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        $total = 0;
        foreach ($this->lines as $line) {
            $builder->debit($line->account_id, (int) $line->amount, $line->memo, $line->branch_id);
            $total += (int) $line->amount;
        }
        $builder->credit($this->payable_account_id, $total, $this->description);
    }

    public function refreshTotal(): void
    {
        $this->forceFill(['total' => (int) $this->lines()->sum('amount')])->saveQuietly();
        app(SettlementService::class)->refresh($this);
    }

    public function settlementAccountId(): int
    {
        return (int) $this->payable_account_id;
    }

    public function balance(): int
    {
        return $this->total - $this->paid_amount;
    }
}
