<?php

declare(strict_types=1);

namespace App\Filament\Resources\CashBank\CashPayments\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\CashBank\CashPayments\CashPaymentResource;
use App\Filament\Support\CreateDocument;

class CreateCashPayment extends CreateDocument
{
    protected static string $resource = CashPaymentResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::CashBankVoucher;
    }
}
