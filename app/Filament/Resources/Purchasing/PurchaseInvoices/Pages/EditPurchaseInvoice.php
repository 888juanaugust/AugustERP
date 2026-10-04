<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchaseInvoices\Pages;

use App\Filament\Resources\Purchasing\PurchaseInvoices\PurchaseInvoiceResource;
use App\Filament\Support\EditDocument;

class EditPurchaseInvoice extends EditDocument
{
    protected static string $resource = PurchaseInvoiceResource::class;
}
