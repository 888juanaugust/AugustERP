<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Sales\PriceResolver;
use App\Models\Inventory\Item;
use App\Models\Sales\Customer;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;

/** The sales line grid: the priced grid with a salesperson per line and prices from the resolver; typing a price takes a right. */
final class SalesLinesTab
{
    public static function make(array $before = [], bool $prices = true, bool $warehouse = true, bool $processed = false): Tab
    {
        $tab = PricedDocumentForm::linesTab(
            before: $before,
            prices: $prices,
            warehouse: $warehouse,
            processed: $processed,
            priceResolver: function (Item $item, Get $get) {
                $customer = $get('../../customer_id') ? Customer::query()->find($get('../../customer_id')) : null;

                return PriceResolver::resolve($customer, $item, $get('unit_id') ? (int) $get('unit_id') : null, $get('../../trans_date') ?: today())['price'];
            },
            salesman: true,
            groupItems: true,
        );

        return $tab;
    }

    public static function canTypePrices(): bool
    {
        return app(HakAkses::class)->allowsSpecial(auth()->user(), HakKhusus::ChangeSellingPrice);
    }
}
