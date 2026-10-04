<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixedAssets\FixedAssets\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\FixedAssets\FixedAssets\FixedAssetResource;
use App\Filament\Support\CreateDocument;

/** Numbered from the asset-code series; saving posts the acquisition. */
class CreateFixedAsset extends CreateDocument
{
    protected static string $resource = FixedAssetResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::FixedAsset;
    }
}
