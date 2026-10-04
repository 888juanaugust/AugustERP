<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;

/** Purchases by Vendor (purchases-by-vendor). */
class PurchasesByVendor extends ReportPage
{
    public static function reportKey(): string
    {
        return 'purchases-by-vendor';
    }

    public static function title(): string
    {
        return 'Purchases by Vendor';
    }

    public static function group(): string
    {
        return 'Purchasing';
    }

    public static function description(): string
    {
        return 'Invoiced purchases per vendor in the period: invoices, quantity, amount, VAT and total.';
    }

    protected function rows(): array
    {
        return TradeReports::purchasesBy('party', $this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('name', 'Vendor'),
            static::text('invoices', 'Invoices')->alignEnd(),
            static::quantity('quantity', 'Quantity'),
            static::money('amount', 'Amount'),
            static::money('tax', 'VAT'),
            static::money('total', 'Total'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Vendor', 'Invoices', 'Quantity', 'Amount', 'VAT', 'Total'];
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
