<?php

declare(strict_types=1);

namespace App\Domain\Reports;

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/** Every report leaves as a spreadsheet: a title row, the headers, the rows as shown. */
final class ExcelExport
{
    /**
     * @param  list<string>  $headers
     * @param  iterable<array<int, scalar|null>>  $rows
     */
    public static function download(string $title, string $period, array $headers, iterable $rows): BinaryFileResponse
    {
        $dir = storage_path('app/exports');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $file = $dir.'/'.preg_replace('/[^\w-]+/', '-', strtolower($title)).'-'.now()->format('Ymd-His').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($file);
        $writer->addRow(Row::fromValues([$title]));
        $writer->addRow(Row::fromValues([$period]));
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues($headers));
        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues(array_map(fn ($v) => $v instanceof \DateTimeInterface ? $v->format('Y-m-d') : $v, array_values($row))));
        }
        $writer->close();

        return response()->download($file, basename($file))->deleteFileAfterSend(true);
    }
}
