<?php

namespace App\Models\Company;

use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingBuilder;
use App\Domain\Posting\PostsToLedger;
use App\Domain\Posting\Tags;
use App\Domain\Settlement\Contracts\PaidByPayment;
use App\Domain\Settlement\SettlementService;
use App\Models\GeneralLedger\Account;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A payroll entry: the journal of one pay period, employee by employee.
 * Gross pay is the expense, the income tax withheld is owed to the tax
 * office, the net is owed to the employees until paid. Payroll itself
 * (the calculation) is outside the system. The net pay is settled by payment
 * lines pointing at the entry.
 */
class PayrollEntry extends Model implements PaidByPayment, Postable
{
    use PostsToLedger;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['trans_date' => 'date', 'due_date' => 'date', 'period_year' => 'integer', 'period_month' => 'integer', 'gross_total' => 'integer', 'tax_total' => 'integer', 'total' => 'integer', 'paid_amount' => 'integer'];
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PayrollEntryLine::class)->orderBy('sort');
    }

    public function expensePayableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_payable_account_id');
    }

    public function taxPayableAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'tax_payable_account_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function periodLabel(): string
    {
        return CarbonImmutable::create($this->period_year, $this->period_month, 1)->format('F Y');
    }

    public function refreshTotal(): void
    {
        $gross = (int) $this->lines()->sum('gross_amount');
        $tax = (int) $this->lines()->sum('income_tax');
        $this->forceFill(['gross_total' => $gross, 'tax_total' => $tax, 'total' => (int) $this->lines()->sum('net_amount')])->saveQuietly();
        app(SettlementService::class)->refresh($this);
    }

    public function settlementAccountId(): int
    {
        return (int) $this->expense_payable_account_id;
    }

    public function buildPostings(PostingBuilder $builder): void
    {
        $taxAccount = $this->tax_payable_account_id ?? Account::query()->where('no', '2220')->value('id');
        $fallbackExpense = Account::query()->where('no', '6100')->value('id');
        $tax = 0;
        $net = 0;
        foreach ($this->lines()->with(['employee', 'component'])->get() as $line) {
            $expense = $line->component?->expense_account_id ?? $fallbackExpense;
            $builder->debit($expense, (int) $line->gross_amount, $line->employee?->name, null, Tags::of($line));
            $tax += (int) $line->income_tax;
            $net += (int) $line->net_amount;
        }
        $builder->credit($taxAccount, $tax, 'Income tax withheld');
        $builder->credit($this->expense_payable_account_id, $net, $this->description ?: 'Net pay, '.$this->periodLabel());
    }
}
