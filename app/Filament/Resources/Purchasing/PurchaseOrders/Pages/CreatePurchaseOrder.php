<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchaseOrders\Pages;

use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\Purchasing\PurchaseOrders\PurchaseOrderResource;
use App\Filament\Support\CreateDocument;
use App\Filament\Support\PrefillsFromSource;
use App\Filament\Support\PricedDocumentForm;
use App\Models\Purchasing\PurchaseRequisition;
use Illuminate\Database\Eloquent\Model;

class CreatePurchaseOrder extends CreateDocument
{
    use PrefillsFromSource;

    protected static string $resource = PurchaseOrderResource::class;

    protected function transactionType(): TransactionType
    {
        return TransactionType::PurchaseOrder;
    }

    protected function sourceModel(): string
    {
        return PurchaseRequisition::class;
    }

    protected function dataFromSource(Model $source): array
    {
        return [
            'description' => "From requisition {$source->number}",
            'lines' => PricedDocumentForm::pulledLines($source->lines()->with('item')->get(), 'purchase_requisition_line', withPrices: false),
        ];
    }
}
