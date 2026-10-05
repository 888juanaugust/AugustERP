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

    /**
     * Narrows the period's documents: by kind ("invoice": a buyer with a tax
     * ID; "aggregated": without one, reported in the aggregate) and by
     * status (draft, exported, numbered).
     */
    public static function narrow(Builder $query, string $kind, ?string $document, ?string $status): Builder
    {
        $party = $kind === TaxFiling::IN ? 'vendor' : 'customer';
        $serial = $kind === TaxFiling::IN ? 'tax_invoice_number' : 'nsfp';
        $filed = fn (Builder $q) => $q->whereExists(fn ($e) => $e->selectRaw('1')->from('tax_filing_documents')
            ->whereColumn('tax_filing_documents.document_id', $q->getModel()->getTable().'.id')
            ->where('tax_filing_documents.document_type', $q->getModel()->getMorphClass()));

        return $query
            ->when($document === 'invoice', fn (Builder $q) => $q->whereHas($party, fn (Builder $p) => $p->whereNotNull('wp_number')->where('wp_number', '!=', '')))
            ->when($document === 'aggregated', fn (Builder $q) => $q->whereDoesntHave($party, fn (Builder $p) => $p->whereNotNull('wp_number')->where('wp_number', '!=', '')))
            ->when($status === 'numbered', fn (Builder $q) => $q->whereNotNull($serial)->where($serial, '!=', ''))
            ->when($status === 'exported', fn (Builder $q) => $q->where(fn ($w) => $w->whereNull($serial)->orWhere($serial, ''))->where(fn ($w) => $filed($w)))
            ->when($status === 'draft', fn (Builder $q) => $q->where(fn ($w) => $w->whereNull($serial)->orWhere($serial, ''))->whereNot(fn ($w) => $filed($w)));
    }

    /** A document exported in more than one filing is a correction (a replacement tax invoice). */
    public static function isCorrection(SalesInvoice|PurchaseInvoice $document): bool
    {
        return TaxFilingDocument::query()->where('document_type', $document->getMorphClass())->where('document_id', $document->getKey())->count() > 1;
    }

    /** What the tax office's file will make of the document, for the Info column. @return list<string> */
    public static function info(SalesInvoice|PurchaseInvoice $document): array
    {
        $party = $document instanceof SalesInvoice ? $document->customer : $document->vendor;
        $notes = [];
        if (blank($party?->wp_number)) {
            $notes[] = __('No buyer tax ID: reported in the aggregate.');
        }
        $uncoded = $document->lines->filter(fn ($line) => blank($line->item?->item_tax_code))->map(fn ($line) => $line->item?->name)->filter()->unique();
        if ($uncoded->isNotEmpty()) {
            $notes[] = __('Default goods code for :items.', ['items' => $uncoded->join(', ')]);
        }

        return $notes;
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
