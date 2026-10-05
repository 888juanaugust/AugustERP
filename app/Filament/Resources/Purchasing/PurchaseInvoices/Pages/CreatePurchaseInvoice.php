<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchaseInvoices\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\Purchasing\PurchaseInvoices\PurchaseInvoiceResource;
use App\Filament\Support\CreateDocument;
use App\Filament\Support\CurrencyFields;
use App\Filament\Support\DocumentPages;
use App\Filament\Support\PricedDocumentForm;
use App\Filament\Support\TagFields;
use App\Models\Purchasing\GoodsReceipt;
use App\Models\Purchasing\PurchaseOrder;

/** Opened with ?source=receipt:ID or ?source=order:ID, the invoice starts from that document. */
class CreatePurchaseInvoice extends CreateDocument
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::PurchaseInvoice;
    }

    public function mount(): void
    {
        parent::mount();

        [$kind, $id] = array_pad(explode(':', (string) request()->query('source'), 2), 2, null);
        $source = match ($kind) {
            'receipt' => GoodsReceipt::query()->find((int) $id),
            'order' => PurchaseOrder::query()->find((int) $id),
            default => null,
        };
        if ($source === null) {
            return;
        }
        $state = $this->form->getRawState();
        $this->form->fill(array_merge($state, CurrencyFields::fromSource($source, $state['trans_date'] ?? null), [
            'vendor_id' => $source->vendor_id,
            'taxable' => $source->taxable,
            'inclusive_tax' => $source->inclusive_tax,
            'payment_term_id' => $source->payment_term_id ?? $source->vendor?->payment_term_id,
            'to_address' => $source->to_address,
            'description' => "From {$source->number}",
            ...TagFields::from($source),
        ]));
        $this->data['lines'] = DocumentPages::keyedRows(
            PricedDocumentForm::pulledLines($source->lines()->with('item')->get(), $kind === 'receipt' ? 'goods_receipt_line' : 'purchase_order_line'),
        );
    }
}
