<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\FinancialStatements;

/** Trial Balance (R-03): one line per top-level account, opening to closing. */
class TrialBalance extends ReportPage
{
    public static function reportKey(): string
    {
        return 'trial-balance';
    }

    public static function title(): string
    {
        return 'Trial Balance';
    }

    public static function group(): string
    {
        return 'Financial';
    }

    public static function description(): string
    {
        return "Every account with its opening balance, the period's debits and credits, and the closing balance.";
    }

    protected function rows(): array
    {
        return FinancialStatements::trialBalance($this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('no', 'No.'),
            static::text('name', 'Account'),
            static::money('opening', 'Opening'),
            static::money('debit', 'Debit'),
            static::money('credit', 'Credit'),
            static::money('closing', 'Closing'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['No.', 'Account', 'Opening', 'Debit', 'Credit', 'Closing'];
    }

    protected function exportRow(array $row): array
    {
        return [$row['no'] ?? '', $row['name'] ?? '', $row['opening'] ?? null, $row['debit'] ?? null, $row['credit'] ?? null, $row['closing'] ?? null];
    }
}
