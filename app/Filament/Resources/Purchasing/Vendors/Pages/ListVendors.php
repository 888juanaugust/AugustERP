<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\Vendors\Pages;

use App\Domain\Imports\MasterImporter;
use App\Filament\Resources\Purchasing\Vendors\VendorResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ListVendors extends ListRecords
{
    protected static string $resource = VendorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New vendor'),
            Action::make('template')
                ->label('Download template')
                ->icon('heroicon-m-document-arrow-down')
                ->color('gray')
                ->action(fn () => response()->streamDownload(fn () => print (MasterImporter::template('vendors')), 'vendors-template.csv', ['Content-Type' => 'text/csv'])),
            Action::make('import')
                ->label('Import')
                ->icon('heroicon-m-arrow-up-tray')
                ->color('gray')
                ->visible(fn () => static::getResource()::canCreate())
                ->schema([
                    FileUpload::make('file')
                        ->label('CSV or XLSX file')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->disk('local')
                        ->directory('imports')
                        ->required()
                        ->storeFileNamesIn('file_name')
                        ->helperText('The first row names the columns, as in the template. A row with a number updates that record; without one, a new record is numbered from the series.'),
                ])
                ->action(function (array $data): void {
                    $path = Storage::disk('local')->path($data['file']);

                    try {
                        $result = app(MasterImporter::class)->import('vendors', $path);
                    } catch (RuntimeException $e) {
                        Notification::make()->title('Cannot import')->body($e->getMessage())->danger()->persistent()->send();

                        return;
                    }

                    Notification::make()->title("{$result['created']} created, {$result['updated']} updated")->success()->send();

                    if ($result['errors'] !== []) {
                        $lines = array_slice($result['errors'], 0, 15);
                        if (count($result['errors']) > 15) {
                            $lines[] = '…';
                        }
                        Notification::make()->title(count($result['errors']).' row(s) skipped')->body(implode("\n", $lines))->warning()->persistent()->send();
                    }

                    $this->resetTable();
                }),
        ];
    }
}
