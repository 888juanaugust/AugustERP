<?php

declare(strict_types=1);

namespace App\Filament\Resources\CashBank\CashReceipts\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\CashBank\CashReceipts\CashReceiptResource;
use App\Filament\Support\CreateDocument;

class CreateCashReceipt extends CreateDocument
{
    protected static string $resource = CashReceiptResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::CashBankVoucher;
    }
}
