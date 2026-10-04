<?php

declare(strict_types=1);

namespace App\Filament\Resources\Inventory\Items\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\Inventory\Items\ItemResource;
use App\Filament\Support\CreatesNumberedRecord;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    use CreatesNumberedRecord;

    protected static string $resource = ItemResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::Item;
    }
}
