<?php

declare(strict_types=1);

namespace App\Domain\Sales;

use App\Domain\Access\HakAkses;
use App\Domain\Access\HakKhusus;
use App\Domain\Shared\Format;
use App\Models\Inventory\Item;
use App\Models\Sales\Delivery;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesQuotation;
use App\Models\Sales\SalesReturn;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\ValidationException;

/**
 * A selling document saved by someone without the "change the selling price" right sells at the price in force:
 * each line at the price the resolver gives (a line pulled from another document at that document's price), no
 * line discount beyond the one in force, and no discount on the total. The form shows the price read-only; this is
 * the check the server makes, whatever the browser sent. Runs after the lines and totals are saved, inside the
 * save's transaction, so a refusal saves nothing.
 */
final class SellingPriceGuard
{
    private const DOCUMENTS = [SalesQuotation::class, SalesOrder::class, Delivery::class, SalesInvoice::class, SalesReturn::class];

    public function __construct(private readonly HakAkses $akses) {}

    public function check(Model $document): void
    {
        $user = auth()->user();
        if (! in_array($document::class, self::DOCUMENTS, true) || $user === null
            || $this->akses->allowsSpecial($user, HakKhusus::ChangeSellingPrice)) {
            return;
        }
        if (BigDecimal::of((string) ($document->getAttribute('discount_percent') ?? 0))->isPositive() || (int) $document->getAttribute('discount_amount') > 0) {
            $this->refuse(__('A discount on the total takes the "change the selling price" right.'));
        }

        $customer = $document->customer;
        $rate = (string) ($document->getAttribute('exchange_rate') ?: 1);
        // A foreign price is typed in cents: allow one cent of the document's currency either way.
        $tolerance = BigDecimal::of($rate)->isGreaterThan(1) ? BigDecimal::of($rate)->dividedBy(100, 4, RoundingMode::HalfUp) : BigDecimal::of('0.5');
        foreach ($document->lines()->get() as $line) {
            $item = $line->item_id ? Item::query()->with('units')->find($line->item_id) : null;
            if ($item === null) {
                continue;
            }
            $source = $this->sourceLine($line);
            if ($source !== null) {
                $expected = (string) ($source->getAttribute('unit_price') ?? $line->unit_price);
                $discount = (string) ($source->getAttribute('discount_percent') ?? 0);
            } else {
                $resolved = PriceResolver::resolve($customer, $item, $line->unit_id ? (int) $line->unit_id : null, $document->trans_date, (string) $line->base_quantity);
                [$expected, $discount] = [$resolved['price'], $resolved['discount_percent']];
            }
            if (BigDecimal::of((string) $line->unit_price)->minus($expected)->abs()->isGreaterThan($tolerance)) {
                $this->refuse(__(':item is priced at :price; changing a selling price takes the "change the selling price" right.', ['item' => $item->name, 'price' => Format::price($expected)]));
            }
            // The discount in force at most, whether typed as a percent or slipped in as an amount.
            $gross = BigDecimal::of((string) $line->quantity)->multipliedBy((string) $line->unit_price);
            $allowed = $gross->multipliedBy($discount)->dividedBy(100, 0, RoundingMode::HalfUp)->toInt();
            if ((int) $line->discount_amount > $allowed + 1) {
                $this->refuse(__('A discount on :item beyond :percent% takes the "change the selling price" right.', ['item' => $item->name, 'percent' => Format::quantity($discount)]));
            }
        }
    }

    private function sourceLine(Model $line): ?Model
    {
        $type = $line->getAttribute('source_line_type');
        $id = $line->getAttribute('source_line_id');
        $class = $type ? Relation::getMorphedModel((string) $type) : null;

        return $class !== null && $id ? $class::query()->find($id) : null;
    }

    private function refuse(string $message): never
    {
        throw ValidationException::withMessages(['data.lines' => $message]);
    }
}
