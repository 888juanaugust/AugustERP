<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;

/** Sales by Item (sales-by-item). */
class SalesByItem extends ReportPage
{
    public static function reportKey(): string
    {
        return 'sales-by-item';
    }

    public static function title(): string
    {
        return __('Sales by Item');
    }

    public static function group(): string
    {
        return 'Sales';
    }

    public static function description(): string
    {
        return __('Invoiced sales per item in the period: invoices, quantity, amount, VAT and total.');
    }

    protected function rows(): array
    {
        return TradeReports::salesBy('item', $this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('name', 'Item'),
            static::text('invoices', 'Invoices')->alignEnd(),
            static::quantity('quantity', 'Quantity'),
            static::money('amount', 'Amount'),
            static::money('tax', 'VAT'),
            static::money('total', 'Total'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Item', 'Invoices', 'Quantity', 'Amount', 'VAT', 'Total'];
    }

    protected function exportRow(array $row): array
    {
        return [
            $row['name'],
            $row['invoices'],
            $row['quantity'],
            $row['amount'],
            $row['tax'],
            $row['total'],
        ];
    }
}
