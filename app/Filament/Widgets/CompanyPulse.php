<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Domain\Shared\Money;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * The dashboard's KPI row (DESIGN.md page type D). The figures are computed
 * from the ledgers once the documents exist (phases 3 to 7); until then each
 * tile shows the shape of the number it will carry.
 */
class CompanyPulse extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    /** Rendered with the page, not after it: the figures are the first thing the owner reads. */
    protected static bool $isLazy = false;

    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Sales this month', Money::rupiah(0))
                ->description('vs last month')
                ->color('gray'),
            Stat::make('Receivables outstanding', Money::rupiah(0))
                ->description('0 invoices open')
                ->color('gray'),
            Stat::make('Payables outstanding', Money::rupiah(0))
                ->description('0 bills open')
                ->color('gray'),
            Stat::make('Cash and bank', Money::rupiah(0))
                ->description('all accounts')
                ->color('gray'),
        ];
    }
}
