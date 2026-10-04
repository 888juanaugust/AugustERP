<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sales\SalesReceipts\Pages;

use App\Domain\Numbering\TransactionType;
use App\Domain\Settlement\SettlementService;
use App\Filament\Resources\Sales\SalesReceipts\SalesReceiptResource;
use App\Filament\Support\CreateDocument;
use App\Filament\Support\DocumentPages;
use App\Filament\Support\PayableFields;

/** Opened with ?source=sales_invoice:ID (or a down payment), the receipt starts with that document's balance. */
class CreateSalesReceipt extends CreateDocument
{
    protected static string $resource = SalesReceiptResource::class;

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
        $this->form->fill(array_merge($this->form->getRawState(), ['customer_id' => $doc->customer_id]));
        $this->data['lines'] = DocumentPages::keyedRows([
            ['receivable_key' => $key, 'amount' => app(SettlementService::class)->balance($doc), 'discount' => 0],
        ]);
    }
}
