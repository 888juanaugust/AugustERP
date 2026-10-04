<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;

/** Open Sales Orders (open-sales-orders). */
class OpenSalesOrders extends ReportPage
{
    public static function reportKey(): string
    {
        return 'open-sales-orders';
    }

    public static function title(): string
    {
        return __('Open Sales Orders');
    }

    public static function group(): string
    {
        return 'Sales';
    }

    public static function description(): string
    {
        return __('Order lines not yet fully delivered or invoiced, with what is left and its value.');
    }

    protected function rows(): array
    {
        return TradeReports::openSalesOrders($this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('number', 'Order')->fontFamily('mono'),
            static::date('trans_date', 'Date'),
            static::text('party', 'Customer'),
            static::text('item', 'Item'),
            static::quantity('ordered', 'Ordered'),
            static::quantity('processed', 'Processed'),
            static::quantity('remaining', 'Remaining'),
            static::money('value', 'Open value'),
            static::text('status', 'Status'),
        ];
    }

    protected function exportHeaders(): array
    {
        return ['Order', 'Date', 'Customer', 'Item', 'Ordered', 'Processed', 'Remaining', 'Open value', 'Status'];
    }

    protected function exportRow(array $row): array
    {
        return [
            $row['number'],
            $row['trans_date'],
            $row['party'],
            $row['item'],
            $row['ordered'],
            $row['processed'],
            $row['remaining'],
            $row['value'],
            $row['status'],
        ];
    }
}
