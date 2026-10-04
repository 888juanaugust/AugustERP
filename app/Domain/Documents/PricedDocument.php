<?php

declare(strict_types=1);

namespace App\Domain\Documents;

use App\Domain\Fulfilment\StatusDeriver;
use App\Models\Company\Fob;
use App\Models\Company\Shipment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The common behaviour of a priced document model: recomputing its lines and
 * cached totals from the LineCalculator, and its fulfilment status from its
 * lines. Models declare lines(), and charges() when they have them.
 */
trait PricedDocument
{
    /** Recomputes every line's amounts and the header totals; called after lines are saved, before posting. */
    public function refreshTotal(): void
    {
        $this->refreshPricedTotal();
    }

    public function refreshPricedTotal(): void
    {
        $lines = $this->lines()->get();
        $charges = method_exists($this, 'charges') ? $this->charges()->get() : collect();

        $result = LineCalculator::compute(
            $lines->map(fn ($l) => $l->getAttributes())->all(),
            (bool) $this->taxable,
            (bool) $this->inclusive_tax,
            (string) ($this->discount_percent ?? 0),
            (int) ($this->discount_amount ?? 0),
            $charges->map(fn ($c) => $c->getAttributes())->all(),
        );

        foreach ($lines as $i => $line) {
            $computed = $result['lines'][$i];
            $line->forceFill([
                'discount_amount' => $computed['discount_amount'],
                'amount' => $computed['amount'],
                'dpp_amount' => $computed['dpp_amount'],
                'tax_amount' => $computed['tax_amount'],
            ])->saveQuietly();
        }

        $this->forceFill([
            'subtotal' => $result['subtotal'],
            'discount_amount' => $result['discount_amount'],
            'charges_total' => $result['charges_total'],
            'dpp_total' => $result['dpp_total'],
            'tax_total' => $result['tax_total'],
            'total' => $result['total'],
        ])->saveQuietly();

        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $this->forceFill(['status' => StatusDeriver::derive($this->lines()->get(), $this->status === StatusDeriver::CLOSED)])->saveQuietly();
    }

    public function isClosed(): bool
    {
        return $this->status === StatusDeriver::CLOSED;
    }

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function fob(): BelongsTo
    {
        return $this->belongsTo(Fob::class);
    }
}
