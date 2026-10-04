<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;
use Filament\Forms\Components\Select;

/** Receivable Aging (receivable-aging). */
class ReceivableAging extends ReportPage
{
    public static function reportKey(): string
    {
        return 'receivable-aging';
    }

    public static function title(): string
    {
        return __('Receivable Aging');
    }

    public static function group(): string
    {
        return 'Sales';
    }

    public static function description(): string
    {
        return "Open receivables per customer by age at the period's end: current, 1–30, 31–60, 61–90, 91–120 and over 120 days.";
    }

    protected function defaultFilters(): array
    {
        return parent::defaultFilters() + ['basis' => 'invoice_date'];
    }

    protected function extraFilters(): array
    {
        return [
            Select::make('basis')
                ->label(__('Age from'))
                ->options(['invoice_date' => __('Invoice date'), 'due_date' => __('Due date')])
                ->default('invoice_date')
                ->native(false)
                ->live(),
        ];
    }

    protected function rows(): array
    {
        return TradeReports::receivableAging($this->period(), $this->filters['basis'] ?? 'invoice_date');
    }

    protected function columns(): array
    {
        return [
            static::text('name', 'Customer'),
            static::text('invoices', 'Open')->alignEnd(),
            static::money('current', 'Current'),
            static::money('1_30', '1–30'),
            static::money('31_60', '31–60'),
            static::money('61_90', '61–90'),
            static::money('91_120', '91–120'),
            static::money('over_120', '> 120'),
            static::money('total', 'Total'),
            static::text('oldest_days', 'Oldest (days)')->alignEnd(),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Customer', 'Open', 'Current', '1–30', '31–60', '61–90', '91–120', '> 120', 'Total', 'Oldest (days)'];
    }

    protected function exportRow(array $row): array
    {
        return [
            $row['name'],
            $row['invoices'],
            $row['current'],
            $row['1_30'],
            $row['31_60'],
            $row['61_90'],
            $row['91_120'],
            $row['over_120'],
            $row['total'],
            $row['oldest_days'],
        ];
    }
}
