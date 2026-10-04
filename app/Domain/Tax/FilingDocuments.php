<?php

declare(strict_types=1);

namespace App\Domain\Tax;

use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Sales\SalesInvoice;
use App\Models\Tax\TaxFiling;
use App\Models\Tax\TaxFilingDocument;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

/**
 * Which documents a VAT period holds: taxable sales invoices for VAT out,
 * taxable purchase invoices for VAT in, within the dates, per branch.
 */
final class FilingDocuments
{
    public static function query(string $kind, CarbonImmutable|string $from, CarbonImmutable|string $until, ?int $branchId = null, ?string $search = null): Builder
    {
        $from = CarbonImmutable::parse($from)->toDateString();
        $until = CarbonImmutable::parse($until)->toDateString();
        $query = $kind === TaxFiling::IN
            ? PurchaseInvoice::query()->with(['vendor', 'lines.item', 'lines.unit', 'lines.taxCode'])
            : SalesInvoice::query()->with(['customer', 'lines.item', 'lines.unit', 'lines.taxCode']);

        return $query
            ->where('taxable', true)
            ->where('tax_total', '>', 0)
            ->whereBetween('trans_date', [$from, $until])
            ->when($branchId, fn (Builder $q) => $q->where('branch_id', $branchId))
            ->when($search, fn (Builder $q) => $q->where(fn (Builder $w) => $w
                ->where('number', 'ilike', "%{$search}%")
                ->orWhere($kind === TaxFiling::IN ? 'tax_invoice_number' : 'nsfp', 'ilike', "%{$search}%")
                ->orWhereHas($kind === TaxFiling::IN ? 'vendor' : 'customer', fn (Builder $p) => $p->where('name', 'ilike', "%{$search}%"))))
            ->orderBy('trans_date')->orderBy('number');
    }

    /** 'draft' before any export, 'exported' once in a filing, 'numbered' once the serial is back. */
    public static function status(SalesInvoice|PurchaseInvoice $document): string
    {
        $serial = $document instanceof SalesInvoice ? $document->nsfp : $document->tax_invoice_number;
        if (filled($serial)) {
            return 'numbered';
        }

        return TaxFilingDocument::query()->where('document_type', $document->getMorphClass())->where('document_id', $document->getKey())->exists() ? 'exported' : 'draft';
    }
}
