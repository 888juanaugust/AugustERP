<?php

declare(strict_types=1);

namespace App\Filament\Resources\CashBank\CashPayments\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\CashBank\CashPayments\CashPaymentResource;
use App\Filament\Support\CreateDocument;
use App\Filament\Support\PrefillsFromMemorized;

class CreateCashPayment extends CreateDocument
{
    use PrefillsFromMemorized;

    protected static string $resource = CashPaymentResource::class;

    public function mount(): void
    {
        parent::mount();
        $this->prefillFromMemorized();
    }

    protected static function memorizedType(): string
    {
        return 'cash_bank_voucher_payment';
    }

    protected function transactionType(): TransactionType
    {
        return TransactionType::CashBankVoucher;
    }
}
