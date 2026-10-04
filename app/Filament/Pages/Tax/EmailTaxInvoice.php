<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tax;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class EmailTaxInvoice extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::EmailTaxInvoice;
    }

    protected function explanation(): string
    {
        return 'Emailing tax invoices to customers is outside the first release; the exported file and the serial numbers are the record. Send the PDF from the invoice list when printing lands.';
    }
}
