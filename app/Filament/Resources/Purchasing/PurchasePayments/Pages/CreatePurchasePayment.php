<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchasePayments\Pages;

use App\Domain\Numbering\TransactionType;
use App\Domain\Settlement\SettlementService;
use App\Filament\Resources\Purchasing\PurchasePayments\PurchasePaymentResource;
use App\Filament\Support\CreateDocument;
use App\Filament\Support\DocumentPages;
use App\Filament\Support\PayableFields;

/** Opened with ?source=purchase_invoice:ID (or a down payment, a return), the payment starts with that document's balance. */
class CreatePurchasePayment extends CreateDocument
{
    protected static string $resource = PurchasePaymentResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::CashBankVoucher;
    }

    public function mount(): void
    {
        parent::mount();

        $key = (string) request()->query('source');
        $doc = $key ? PayableFields::resolve($key) : null;
        if ($doc === null) {
            return;
        }
        $this->form->fill(array_merge($this->form->getRawState(), ['vendor_id' => $doc->vendor_id]));
        $this->data['lines'] = DocumentPages::keyedRows([
            ['payable_key' => $key, 'amount' => app(SettlementService::class)->balance($doc), 'discount' => 0],
        ]);
    }
}
