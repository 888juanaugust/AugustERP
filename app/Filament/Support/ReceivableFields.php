<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Approval\ApprovalEngine;
use App\Models\Company\OpeningBalance;
use App\Models\Sales\Customer;
use App\Models\Sales\SalesDownPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Sales\SalesReturn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/** The open documents of a customer a receipt can settle: invoices, down payments, opening balances and (with "use credit") credit notes. */
final class ReceivableFields
{
    /**
     * The open items in one currency (null: the base currency); balances are in that currency.
     *
     * @return Collection<string, array{model: Model, label: string, balance: int}> keyed "type:id"
     */
    public static function openFor(int $customerId, bool $withCredits = true, int|string|null $currencyId = null): Collection
    {
        $classes = [SalesInvoice::class, SalesDownPayment::class];
        if ($withCredits) {
            $classes[] = SalesReturn::class;
        }
        $out = collect();
        foreach ($classes as $class) {
            foreach ($class::query()->where('customer_id', $customerId)->where('payment_status', '!=', 'paid')->orderBy('trans_date')->get()->filter(fn ($doc) => app(ApprovalEngine::class)->isApproved($doc) && SettlementLineFields::inCurrency($doc, $currencyId)) as $doc) {
                $balance = SettlementLineFields::open($doc);
                if ($balance === 0) {
                    continue;
                }
                $out[$doc->getMorphClass().':'.$doc->id] = ['model' => $doc, 'label' => SettlementLineFields::label($doc, $doc->number, $doc->trans_date, $balance), 'balance' => $balance];
            }
        }

        foreach (OpeningBalance::query()->where('party_type', (new Customer)->getMorphClass())->where('party_id', $customerId)->where('payment_status', '!=', 'paid')->orderBy('document_date')->get() as $opening) {
            $balance = SettlementLineFields::open($opening);
            if ($balance !== 0 && SettlementLineFields::inCurrency($opening, $currencyId)) {
                $out[$opening->getMorphClass().':'.$opening->id] = ['model' => $opening, 'label' => SettlementLineFields::label($opening, $opening->postingNumber(), $opening->agingDate(), $balance, __('opening balance')), 'balance' => $balance];
            }
        }

        return $out;
    }
}
