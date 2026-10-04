<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\JournalVouchers\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\GeneralLedger\JournalVouchers\JournalVoucherResource;
use App\Filament\Support\CreateDocument;

class CreateJournalVoucher extends CreateDocument
{
    protected static string $resource = JournalVoucherResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::JournalVoucher;
    }
}
