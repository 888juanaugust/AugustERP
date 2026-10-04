<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchaseRequisitions\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\Purchasing\PurchaseRequisitions\PurchaseRequisitionResource;
use App\Filament\Support\CreateDocument;

class CreatePurchaseRequisition extends CreateDocument
{
    protected static string $resource = PurchaseRequisitionResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::PurchaseRequisition;
    }
}
