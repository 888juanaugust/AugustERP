<?php

declare(strict_types=1);

namespace App\Domain\Tax;

use App\Models\Sales\SalesInvoice;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * The older e-Faktur CSV: an FK row per invoice, an LT row for the buyer and
 * an OF row per line, so a filing made under it can be reproduced.
 */
final class LegacyCsvWriter
{
    /** @param  Collection<int, SalesInvoice>  $invoices */
    public function write(Collection $invoices): string
    {
        $cfg = config('pajak.legacy');
        $rows = [$cfg['header'], $cfg['lt'], $cfg['of']];

        foreach ($invoices as $invoice) {
            $buyer = TaxParty::fromParty($invoice->customer);
            $date = CarbonImmutable::parse($invoice->trans_date);
            $rows[] = [
                'FK', $cfg['kd_jenis_transaksi'], $cfg['fg_pengganti'], preg_replace('/\D/', '', (string) $invoice->nsfp) ?: '',
                (string) $date->month, (string) $date->year, $date->format('d/m/Y'),
                $buyer->idNumber ?: '000000000000000', $buyer->name, $buyer->address,
                (string) (int) $invoice->dpp_total, (string) (int) $invoice->tax_total, '0', '', '0', '0', '0', '0', $invoice->number,
            ];
            $rows[] = ['LT', $buyer->idNumber ?: '000000000000000', $buyer->name, $buyer->address, '', '', '', '', '', '', '', '', '', ''];
            foreach ($invoice->lines as $line) {
                $gross = (int) $line->amount + (int) $line->discount_amount;
                $rows[] = [
                    'OF', (string) ($line->item?->item_tax_code ?: ''), (string) ($line->item?->name ?? ''),
                    (string) (int) round((float) $line->unit_price), $this->decimal($line->quantity), (string) $gross,
                    (string) (int) $line->discount_amount, (string) (int) $line->dpp_amount, (string) (int) $line->tax_amount, '0', '0',
                ];
            }
        }

        $out = fopen('php://memory', 'r+');
        foreach ($rows as $row) {
            fputcsv($out, array_map(self::cell(...), $row), ',', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out) ?: '';
        fclose($out);

        return $csv;
    }

    private function decimal(string|int|float|null $value): string
    {
        $text = rtrim(rtrim(number_format((float) $value, 4, '.', ''), '0'), '.');

        return $text === '' ? '0' : $text;
    }

    /** A spreadsheet opening the file reads a cell starting with = + - @ (or a tab or return) as a formula: such text is quoted. */
    private static function cell(string $value): string
    {
        return $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) && ! is_numeric($value) ? "'".$value : $value;
    }
}
