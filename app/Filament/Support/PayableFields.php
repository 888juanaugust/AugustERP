<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Approval\ApprovalEngine;
use App\Domain\Settlement\SettlementService;
use App\Domain\Shared\Format;
use App\Models\Company\OpeningBalance;
use App\Models\Purchasing\PurchaseDownPayment;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseReturn;
use App\Models\Purchasing\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;

/** The open documents of a vendor that a payment can settle: invoices, down payments, opening balances and returns (as credits). */
final class PayableFields
{
    /** @return Collection<string, array{model: Model, label: string, balance: int}> keyed "type:id" */
    public static function openFor(int $vendorId): Collection
    {
        $settlement = app(SettlementService::class);
        $out = collect();
        foreach ([PurchaseInvoice::class, PurchaseDownPayment::class, PurchaseReturn::class] as $class) {
            $docs = $class::query()->where('vendor_id', $vendorId)->where('payment_status', '!=', 'paid')->orderBy('trans_date')->get()->filter(fn ($doc) => app(ApprovalEngine::class)->isApproved($doc));
            foreach ($docs as $doc) {
                $balance = $settlement->balance($doc);
                if ($balance === 0) {
                    continue;
                }
                $key = $doc->getMorphClass().':'.$doc->id;
                $out[$key] = ['model' => $doc, 'label' => $doc->number.' · '.Format::date($doc->trans_date).' · '.Format::rupiah((int) $doc->total).' · open '.Format::rupiah($balance), 'balance' => $balance];
            }
        }
        foreach (OpeningBalance::query()->where('party_type', (new Vendor)->getMorphClass())->where('party_id', $vendorId)->where('payment_status', '!=', 'paid')->orderBy('document_date')->get() as $opening) {
            $balance = $settlement->balance($opening);
            if ($balance !== 0) {
                $out[$opening->getMorphClass().':'.$opening->id] = ['model' => $opening, 'label' => $opening->postingNumber().' · '.Format::date($opening->agingDate()).' · '.__('opening balance').' · open '.Format::rupiah($balance), 'balance' => $balance];
            }
        }

        return $out;
    }

    public static function resolve(string $key): ?Model
    {
        [$type, $id] = array_pad(explode(':', $key, 2), 2, null);
        $class = Relation::getMorphedModel($type) ?? null;

        return $class && $id ? $class::query()->find((int) $id) : null;
    }
}
