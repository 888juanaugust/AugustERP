<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Reports\TradeReports;

/** Open Purchase Orders (open-purchase-orders). */
class OpenPurchaseOrders extends ReportPage
{
    public static function reportKey(): string
    {
        return 'open-purchase-orders';
    }

    public static function title(): string
    {
        return 'Open Purchase Orders';
    }

    public static function group(): string
    {
        return 'Purchasing';
    }

    public static function description(): string
    {
        return 'Order lines not yet fully received or invoiced, with what is left and its value.';
    }

    protected function rows(): array
    {
        return TradeReports::openPurchaseOrders($this->period());
    }

    protected function columns(): array
    {
        return [
            static::text('number', 'Order')->fontFamily('mono'),
            static::date('trans_date', 'Date'),
            static::text('party', 'Vendor'),
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
        return ['Order', 'Date', 'Vendor', 'Item', 'Ordered', 'Processed', 'Remaining', 'Open value', 'Status'];
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
