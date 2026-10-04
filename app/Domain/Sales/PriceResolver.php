<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use App\Domain\Inventory\Units\UnitConverter;
use App\Models\Inventory\Item;
use App\Models\Sales\Customer;
use App\Models\Sales\SellingPriceAdjustment;
use App\Models\Sales\SellingPriceAdjustmentLine;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * The selling price of an item for a customer on a date, and why: the price
 * adjustment in force for the customer's price category, else the item's
 * price for that category, else the item's base price scaled to the unit.
 * Every sales document asks here; nothing else computes a price.
 */
final class PriceResolver
{
    /** @return array{price: string, discount_percent: string, source: string} */
    public static function resolve(?Customer $customer, Item $item, ?int $unitId, DateTimeInterface|string|null $date = null): array
    {
        $date = Carbon::parse($date ?? today())->toDateString();
        $unitId ??= $item->unit1_id;
        $categoryId = $customer?->price_category_id;
        $item->loadMissing(['units', 'prices']);
        $discount = (string) ($customer?->default_sales_disc ?? $item->default_discount ?? 0);

        if ($categoryId !== null) {
            $adjusted = self::adjustment($categoryId, $item->id, $unitId, $date, SellingPriceAdjustment::PRICE);
            $discountAdjusted = self::adjustment($categoryId, $item->id, $unitId, $date, SellingPriceAdjustment::DISCOUNT);
            if ($discountAdjusted !== null) {
                $discount = $discountAdjusted['value'];
            }
            if ($adjusted !== null) {
                return ['price' => $adjusted['value'], 'discount_percent' => $discount, 'source' => "price adjustment {$adjusted['number']}"];
            }

            $categoryPrice = $item->prices->first(fn ($p) => $p->price_category_id === $categoryId && $p->unit_id === $unitId)
                ?? $item->prices->first(fn ($p) => $p->price_category_id === $categoryId && $p->unit_id === null);
            if ($categoryPrice !== null) {
                $price = $categoryPrice->unit_id === null && $unitId !== $item->unit1_id
                    ? self::scale((string) $categoryPrice->price, $item, $unitId)
                    : (string) $categoryPrice->price;

                return ['price' => $price, 'discount_percent' => $discount, 'source' => 'item price for the price category'];
            }
        }

        $unitPrice = $item->units->first(fn ($u) => $u->unit_id === $unitId);
        if ($unitPrice !== null && (int) $unitPrice->sell_price > 0) {
            return ['price' => (string) $unitPrice->sell_price, 'discount_percent' => $discount, 'source' => 'item unit price'];
        }

        return ['price' => self::scale((string) $item->sell_price, $item, $unitId), 'discount_percent' => $discount, 'source' => 'item base price'];
    }

    /** @return array{value: string, number: string}|null */
    private static function adjustment(int $categoryId, int $itemId, int $unitId, string $date, string $type): ?array
    {
        $line = SellingPriceAdjustmentLine::query()
            ->join('selling_price_adjustments as a', 'a.id', '=', 'selling_price_adjustment_lines.selling_price_adjustment_id')
            ->where('a.price_category_id', $categoryId)
            ->where('a.sales_adjustment_type', $type)
            ->where('a.is_active', true)
            ->where('a.trans_date', '<=', $date)
            ->where(fn ($q) => $q->whereNull('a.end_date')->orWhere('a.end_date', '>=', $date))
            ->where('selling_price_adjustment_lines.item_id', $itemId)
            ->where(fn ($q) => $q->where('selling_price_adjustment_lines.unit_id', $unitId)->orWhereNull('selling_price_adjustment_lines.unit_id'))
            ->orderByDesc('a.trans_date')
            ->orderByRaw('selling_price_adjustment_lines.unit_id IS NULL')
            ->select('selling_price_adjustment_lines.*', 'a.number as adjustment_number')
            ->first();

        return $line ? ['value' => (string) $line->value, 'number' => $line->adjustment_number] : null;
    }

    private static function scale(string $basePrice, Item $item, int $unitId): string
    {
        return (string) BigDecimal::of($basePrice)->multipliedBy(UnitConverter::ratio($item, $unitId))->toScale(4, RoundingMode::HalfUp);
    }
}
