<?php

declare(strict_types=1);

namespace App\Domain\Settlement;

use App\Domain\Documents\PaymentStatus;
use App\Models\Settlement\PaymentAllocation;
use Illuminate\Database\Eloquent\Model;

/**
 * What a document has been paid: the sum of active allocations (amount plus
 * discount taken). paid_amount is a cache written here and only here; the
 * status derives from it. Nothing ever flips "paid" by hand.
 */
final class SettlementService
{
    public function paidAmount(Model $document): int
    {
        return (int) PaymentAllocation::query()->active()
            ->where('receivable_type', $document->getMorphClass())
            ->where('receivable_id', $document->getKey())
            ->selectRaw('COALESCE(SUM(amount + discount), 0) AS paid')
            ->value('paid');
    }

    public function refresh(Model $document): void
    {
        $paid = $this->paidAmount($document);
        // A credit (a return) is applied with negative amounts; it is settled when the credit is used up.
        if (method_exists($document, 'isCredit') && $document->isCredit()) {
            $paid = -$paid;
        }
        $total = (int) ($document->getAttribute('total') ?? 0) - (int) ($document->getAttribute('down_payment_total') ?? 0);
        $document->forceFill([
            'paid_amount' => $paid,
            'payment_status' => PaymentStatus::derive($total, $paid),
        ])->saveQuietly();
    }

    public function balance(Model $document): int
    {
        $paid = $this->paidAmount($document);
        if (method_exists($document, 'isCredit') && $document->isCredit()) {
            return -((int) $document->getAttribute('total') + $paid);
        }

        return (int) ($document->getAttribute('total') ?? 0) - (int) ($document->getAttribute('down_payment_total') ?? 0) - $paid;
    }

    public function hasAllocations(Model $document): bool
    {
        return PaymentAllocation::query()->active()
            ->where('receivable_type', $document->getMorphClass())
            ->where('receivable_id', $document->getKey())
            ->exists();
    }
}
