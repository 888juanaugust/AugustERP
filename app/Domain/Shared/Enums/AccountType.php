<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use Filament\Support\Contracts\HasLabel;

/** The sixteen account types of the reference system's chart of accounts. */
enum AccountType: string implements HasLabel
{
    case CashBank = 'cash_bank';
    case AccountsReceivable = 'accounts_receivable';
    case Inventory = 'inventory';
    case OtherCurrentAsset = 'other_current_asset';
    case FixedAsset = 'fixed_asset';
    case AccumulatedDepreciation = 'accumulated_depreciation';
    case OtherAsset = 'other_asset';
    case AccountsPayable = 'accounts_payable';
    case OtherCurrentLiability = 'other_current_liability';
    case LongTermLiability = 'long_term_liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case CostOfSales = 'cost_of_sales';
    case Expense = 'expense';
    case OtherIncome = 'other_income';
    case OtherExpense = 'other_expense';

    public function getLabel(): string
    {
        return match ($this) {
            self::CashBank => 'Cash / Bank',
            self::AccountsReceivable => 'Accounts Receivable',
            self::Inventory => 'Inventory',
            self::OtherCurrentAsset => 'Other Current Asset',
            self::FixedAsset => 'Fixed Asset',
            self::AccumulatedDepreciation => 'Accumulated Depreciation',
            self::OtherAsset => 'Other Asset',
            self::AccountsPayable => 'Accounts Payable',
            self::OtherCurrentLiability => 'Other Current Liability',
            self::LongTermLiability => 'Long-term Liability',
            self::Equity => 'Equity',
            self::Revenue => 'Revenue',
            self::CostOfSales => 'Cost of Sales',
            self::Expense => 'Expense',
            self::OtherIncome => 'Other Income',
            self::OtherExpense => 'Other Expense',
        };
    }

    /** Whether the account grows on the debit side. */
    public function isDebitNormal(): bool
    {
        return match ($this) {
            self::CashBank, self::AccountsReceivable, self::Inventory, self::OtherCurrentAsset,
            self::FixedAsset, self::OtherAsset, self::CostOfSales, self::Expense, self::OtherExpense => true,
            default => false,
        };
    }

    public function isBalanceSheet(): bool
    {
        return ! in_array($this, [self::Revenue, self::CostOfSales, self::Expense, self::OtherIncome, self::OtherExpense], true);
    }
}
