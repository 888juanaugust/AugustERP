<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;

/** Sales by Customer (sales-by-customer). */
class SalesByCustomer extends ReportPage
{
    public static function reportKey(): string
    {
        return 'sales-by-customer';
    }

    public static function title(): string
    {
        return __('Sales by Customer');
    }

    public static function group(): string
    {
        return 'Sales';
    }

    public static function description(): string
    {
        return __('Invoiced sales per customer in the period: invoices, quantity, amount, VAT and total.');
    }

    protected function rows(): array
    {
        return TradeReports::salesBy('party', $this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('name', 'Customer'),
            static::text('invoices', 'Invoices')->alignEnd(),
            static::quantity('quantity', 'Quantity'),
            static::money('amount', 'Amount'),
            static::money('tax', 'VAT'),
            static::money('total', 'Total'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Customer', 'Invoices', 'Quantity', 'Amount', 'VAT', 'Total'];
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
