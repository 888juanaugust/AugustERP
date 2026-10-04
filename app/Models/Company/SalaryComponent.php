<?php

namespace App\Models\Company;

use App\Domain\Audit\RecordsActivity;
use App\Models\GeneralLedger\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A salary or allowance component and the expense account it is booked to. */
class SalaryComponent extends Model
{
    use RecordsActivity;

    /** The tax office's income kinds for form 1721, as the reference system lists them. */
    public const FEE_TYPES = [
        'salary' => 'Salary / pension / old-age benefit',
        'tax_allowance' => 'Income tax allowance',
        'tax_subsidy' => 'Income tax subsidy',
        'other_allowance' => 'Other allowances',
        'overtime' => 'Overtime and the like',
        'accident_insurance' => 'Work accident insurance allowance',
        'death_insurance' => 'Death insurance',
        'honorarium' => 'Honoraria and similar rewards',
        'health_premium_employer' => 'Health insurance premium paid by the employer',
        'in_kind' => 'Benefits in kind',
        'bonus' => 'Bonus, gratuity, production incentive and holiday allowance',
        'pension_employer' => 'Pension contribution paid by the employer',
        'deduction_no_tax' => 'Salary deduction (not reducing tax)',
        'deduction_tax' => 'Salary reduction (reducing tax)',
        'health_premium_employee' => 'Health insurance premium paid by the employee',
        'pension_employee' => 'Pension contribution paid by the employee',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function expenseAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'expense_account_id');
    }

    public function isDeduction(): bool
    {
        return str_starts_with((string) $this->fee_type, 'deduction') || str_ends_with((string) $this->fee_type, '_employee');
    }
}
