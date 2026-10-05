<?php

declare(strict_types=1);

namespace App\Domain\Tax;

use App\Domain\Audit\Auditor;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Sales\SalesDownPayment;
use App\Models\Sales\SalesInvoice;
use App\Models\Tax\TaxFiling;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Exports a period's invoices to the tax office's file, keeps the file and
 * what went into it, and takes the serial numbers back (T-02).
 */
final class TaxFilingService
{
    public function __construct(private readonly CoretaxXmlWriter $coretax, private readonly LegacyCsvWriter $legacy) {}

    /** @param  Collection<int, SalesInvoice>  $invoices */
    public function export(Collection $invoices, string $format, int $year, int $month, ?int $branchId = null, ?int $userId = null): TaxFiling
    {
        if ($invoices->isEmpty()) {
            throw new RuntimeException('Pick at least one invoice to export.');
        }
        $invoices = $invoices->sortBy([['trans_date', 'asc'], ['number', 'asc']])->values();
        $content = $format === TaxFiling::LEGACY ? $this->legacy->write($invoices) : $this->coretax->write($invoices);
        $stamp = now()->format('Ymd-His');
        $fileName = sprintf('%s-%04d%02d-%s.%s', $format === TaxFiling::LEGACY ? 'efaktur' : 'coretax', $year, $month, $stamp, $format === TaxFiling::LEGACY ? 'csv' : 'xml');
        $path = "tax-filings/{$fileName}";
        Storage::disk('local')->put($path, $content);

        return DB::transaction(function () use ($invoices, $format, $year, $month, $branchId, $userId, $fileName, $path): TaxFiling {
            $filing = TaxFiling::query()->create([
                'kind' => TaxFiling::OUT,
                'format' => $format,
                'period_year' => $year,
                'period_month' => $month,
                'from_date' => $invoices->min('trans_date'),
                'to_date' => $invoices->max('trans_date'),
                'branch_id' => $branchId,
                'file_name' => $fileName,
                'file_path' => $path,
                'document_count' => $invoices->count(),
                'dpp_total' => (int) $invoices->sum('dpp_total'),
                'tax_total' => (int) $invoices->sum('tax_total'),
                'created_by' => $userId ?? auth()->id(),
                'created_at' => now(),
            ]);
            foreach ($invoices as $invoice) {
                $filing->documents()->create(['document_type' => $invoice->getMorphClass(), 'document_id' => $invoice->getKey(), 'dpp' => (int) $invoice->dpp_total, 'tax' => (int) $invoice->tax_total]);
            }
            Auditor::log('tax_filing_exported', $filing, $fileName, ['documents' => $invoices->count(), 'dpp' => $filing->dpp_total, 'tax' => $filing->tax_total], CarbonImmutable::create($year, $month, 1)->endOfMonth()->toDateString());

            return $filing;
        });
    }

    /**
     * Pasted lines of "invoice number<tab|,|;|space>serial number" (the tax
     * office's response pasted straight in); every match stores the serial
     * on the invoice. Returns what was stored and what was not understood.
     *
     * @return array{stored: list<string>, unknown: list<string>}
     */
    public function storeSerials(string $pasted, string $kind = TaxFiling::OUT, ?int $userId = null): array
    {
        $stored = [];
        $unknown = [];
        foreach (preg_split('/\R/', trim($pasted)) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            $parts = preg_split('/[\t;,]+|\s{1,}/', $line, 2) ?: [];
            if (count($parts) < 2) {
                $unknown[] = $line;

                continue;
            }
            [$number, $serial] = array_map('trim', $parts);
            $document = $kind === TaxFiling::IN
                ? PurchaseInvoice::query()->where('number', $number)->orWhere('bill_number', $number)->first()
                : SalesInvoice::query()->where('number', $number)->first();
            if ($document === null) {
                $unknown[] = $line;

                continue;
            }
            $column = $kind === TaxFiling::IN ? 'tax_invoice_number' : 'nsfp';
            $document->forceFill([$column => $serial] + ($kind === TaxFiling::IN ? [] : ['nsfp_filed_at' => now()]))->saveQuietly();
            Auditor::log('tax_serial_stored', $document, $document->number, ['serial' => $serial], (string) $document->trans_date?->toDateString());
            $stored[] = "{$document->number} → {$serial}";
        }

        return ['stored' => $stored, 'unknown' => $unknown];
    }

    /** Takes a recorded serial off a sales invoice or down payment so it can be corrected; audited with the serial it had. */
    public function clearSerial(SalesInvoice|SalesDownPayment $document): void
    {
        $serial = $document->nsfp;
        if (blank($serial)) {
            return;
        }
        $document->forceFill(['nsfp' => null] + ($document instanceof SalesInvoice ? ['nsfp_filed_at' => null] : []))->saveQuietly();
        Auditor::log('tax_serial_cleared', $document, $document->number, ['serial' => $serial], (string) $document->trans_date?->toDateString());
    }
}
