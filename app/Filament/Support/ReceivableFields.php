<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Settlement\SettlementService;
use App\Domain\Shared\Format;
use App\Models\Sales\SalesDownPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/** The open documents of a customer a receipt can settle: invoices, down payments and (with "use credit") credit notes. */
final class ReceivableFields
{
    /** @return Collection<string, array{model: Model, label: string, balance: int}> keyed "type:id" */
    public static function openFor(int $customerId, bool $withCredits = true): Collection
    {
        $settlement = app(SettlementService::class);
        $classes = [SalesInvoice::class, SalesDownPayment::class];
        if ($withCredits) {
            $classes[] = SalesReturn::class;
        }
        $out = collect();
        foreach ($classes as $class) {
            foreach ($class::query()->where('customer_id', $customerId)->where('payment_status', '!=', 'paid')->orderBy('trans_date')->get() as $doc) {
                $balance = $settlement->balance($doc);
                if ($balance === 0) {
                    continue;
                }
                $out[$doc->getMorphClass().':'.$doc->id] = ['model' => $doc, 'label' => $doc->number.' · '.Format::date($doc->trans_date).' · '.Format::rupiah((int) $doc->total).' · open '.Format::rupiah($balance), 'balance' => $balance];
            }
        }

        return $out;
    }
}
