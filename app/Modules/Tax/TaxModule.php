<?php

declare(strict_types=1);

namespace App\Modules\Tax;

use App\Domain\Access\MenuKey;
use App\Domain\Pengaturan\PreferensiKey;
use App\Models\Tax\TaxFiling;
use App\Models\Tax\TaxFilingDocument;
use App\Modules\BaseModule;

/** Tax filings: the VAT export files and the VAT return. Switched by the Tax feature. */
final class TaxModule extends BaseModule
{
    public static function key(): string
    {
        return 'tax';
    }

    public static function feature(): ?PreferensiKey
    {
        return PreferensiKey::Tax;
    }

    public static function menuKeys(): array
    {
        return [MenuKey::ETaxInvoiceExport, MenuKey::EmailTaxInvoice, MenuKey::LegacyETaxExport, MenuKey::VATReturn];
    }

    public static function morphMap(): array
    {
        return ['tax_filing' => TaxFiling::class, 'tax_filing_document' => TaxFilingDocument::class];
    }
}
