<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tax;

use App\Domain\Access\MenuKey;
use App\Domain\Shared\Format;
use App\Domain\Tax\FilingDocuments;
use App\Domain\Tax\TaxFilingService;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\ErpPage;
use App\Models\Company\Branch;
use App\Models\Sales\SalesInvoice;
use App\Models\Tax\TaxFiling;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * e-Tax Invoice Export: the period's tax invoices, exported to the tax
 * office's bulk-import file; the serial numbers it hands back are pasted in
 * here.
 */
class ETaxInvoiceExport extends ErpPage implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.tax.e-tax-invoice-export';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowUp;

    public ?array $filters = [];

    public static function menuKey(): MenuKey
    {
        return MenuKey::ETaxInvoiceExport;
    }

    /** Which file this screen writes: the tax office's current layout here, the older one on the legacy screen. */
    protected function format(): string
    {
        return TaxFiling::CORETAX;
    }

    public function mount(): void
    {
        $this->form->fill([
            'kind' => TaxFiling::OUT,
            'month' => (int) today()->format('n'),
            'year' => (int) today()->format('Y'),
            'day_from' => 1,
            'day_to' => 31,
            'branch_id' => null,
            'search' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $year = (int) today()->format('Y');
        $months = collect(range(1, 12))->mapWithKeys(fn (int $m) => [$m => CarbonImmutable::create($year, $m, 1)->format('F')])->all();
        $days = collect(range(1, 31))->mapWithKeys(fn (int $d) => [$d => (string) $d])->all();

        return $schema
            ->components([
                Section::make()->columns(5)->schema([
                    Select::make('kind')->label('Tax')
                        ->options([TaxFiling::OUT => 'VAT out (sales)', TaxFiling::IN => 'VAT in (purchases)'])
                        ->default(TaxFiling::OUT)->native(false)->selectablePlaceholder(false)->live(),
                    Select::make('month')->label('Month')->options($months)->default((int) today()->format('n'))->native(false)->selectablePlaceholder(false)->live(),
                    Select::make('year')->label('Year')
                        ->options(collect(range($year - 2, $year + 1))->mapWithKeys(fn (int $y) => [$y => (string) $y])->all())
                        ->default($year)->native(false)->selectablePlaceholder(false)->live(),
                    Select::make('day_from')->label('From day')->options($days)->default(1)->native(false)->selectablePlaceholder(false)->live(),
                    Select::make('day_to')->label('To day')->options($days)->default(31)->native(false)->selectablePlaceholder(false)->live(),
                    Select::make('branch_id')->label('Branch')
                        ->options(fn () => Branch::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->nullable()->placeholder('All branches')->native(false)->live(),
                    TextInput::make('search')->label('Search')->placeholder('Number, serial or name')->live(onBlur: true),
                ]),
            ])
            ->statePath('filters');
    }

    public function updatedFilters(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => FilingDocuments::query($this->kind(), $this->from(), $this->until(), $this->branchId(), $this->filters['search'] ?? null))
            ->columns([
                TextColumn::make('trans_date')->label('Tax date')->formatStateUsing(fn ($state) => Format::date($state))->sortable(),
                TextColumn::make('number')->label('Transaction No.')->fontFamily('mono')->searchable(),
                TextColumn::make('serial')->label('Tax invoice No.')
                    ->state(fn ($record) => $record instanceof SalesInvoice ? $record->nsfp : $record->tax_invoice_number)
                    ->placeholder('—')->fontFamily('mono'),
                Rupiah::make('dpp_total')->label('Tax base (DPP)'),
                Rupiah::make('tax_total')->label('VAT'),
                TextColumn::make('document')->label('Document')->state(fn () => 'Tax invoice'),
                TextColumn::make('status')->label('Status')->badge()
                    ->state(fn ($record) => FilingDocuments::status($record))
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'numbered' => 'success',
                        'exported' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('party_tax_id')->label('Tax ID')->state(fn ($record) => ($record->customer ?? $record->vendor)?->wp_number)->placeholder('—'),
                TextColumn::make('party_name')->label('Name')->state(fn ($record) => ($record->customer ?? $record->vendor)?->wp_name ?: ($record->customer ?? $record->vendor)?->name),
            ])
            ->selectable()
            ->bulkActions([
                BulkAction::make('export')
                    ->label('Export selected')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary')
                    ->visible(fn () => $this->kind() === TaxFiling::OUT && static::canUpdate())
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records) {
                        try {
                            $filing = app(TaxFilingService::class)->export($records, $this->format(), (int) $this->filters['year'], (int) $this->filters['month'], $this->branchId());
                        } catch (\RuntimeException $e) {
                            Notification::make()->title('Cannot export')->body($e->getMessage())->danger()->persistent()->send();

                            return null;
                        }
                        Notification::make()->title("{$filing->document_count} invoice(s) written to {$filing->file_name}")->success()->send();

                        return response()->download(Storage::disk('local')->path($filing->file_path), $filing->file_name);
                    }),
            ])
            ->defaultSort('trans_date')
            ->emptyStateHeading('No tax invoices in this period')
            ->emptyStateDescription('Taxable invoices dated within the chosen days appear here; pick them and export, then paste the serial numbers back.');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('pasteSerials')
                ->label('Paste serial numbers')
                ->icon('heroicon-m-clipboard-document')
                ->color('gray')
                ->visible(fn () => static::canUpdate())
                ->schema([
                    Textarea::make('pasted')->label('One per line: invoice number, then the serial')->rows(8)->required()
                        ->helperText('Separated by a tab, comma, semicolon or space; e.g. INV-2611-0001  010.002-26.00000123'),
                ])
                ->action(function (array $data): void {
                    $result = app(TaxFilingService::class)->storeSerials((string) $data['pasted'], $this->kind());
                    Notification::make()->title(count($result['stored']).' serial(s) stored')->success()->send();
                    if ($result['unknown'] !== []) {
                        Notification::make()->title(count($result['unknown']).' line(s) not understood')
                            ->body(implode("\n", $result['unknown']))
                            ->warning()->persistent()->send();
                    }
                    $this->resetTable();
                }),
            Action::make('filings')
                ->label('Previous exports')
                ->icon('heroicon-m-folder')
                ->color('gray')
                ->modalHeading('Previous exports')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->modalContent(fn () => view('filament.pages.tax.filings', [
                    'filings' => TaxFiling::query()->where('format', $this->format())->latest('created_at')->limit(20)->get(),
                ])),
        ];
    }

    /** The tax office's reference codes, printed so the accountant can check them against the file. */
    public function referenceCodes(): array
    {
        $codes = (array) config('pajak.coretax', []);

        return collect($codes)
            ->filter(fn ($value) => is_scalar($value))
            ->mapWithKeys(fn ($value, string $key) => [ucfirst(str_replace('_', ' ', $key)) => (string) $value])
            ->all();
    }

    protected function kind(): string
    {
        return ($this->filters['kind'] ?? TaxFiling::OUT) === TaxFiling::IN ? TaxFiling::IN : TaxFiling::OUT;
    }

    protected function from(): string
    {
        $month = $this->month();
        $day = min(max((int) ($this->filters['day_from'] ?? 1), 1), $month->daysInMonth);

        return $month->day($day)->toDateString();
    }

    protected function until(): string
    {
        $month = $this->month();
        $day = min(max((int) ($this->filters['day_to'] ?? 31), 1), $month->daysInMonth);

        return $month->day($day)->toDateString();
    }

    protected function branchId(): ?int
    {
        $branchId = $this->filters['branch_id'] ?? null;

        return $branchId === null || $branchId === '' ? null : (int) $branchId;
    }

    /** The first day of the chosen month. */
    private function month(): CarbonImmutable
    {
        $year = (int) ($this->filters['year'] ?? today()->format('Y'));
        $month = min(max((int) ($this->filters['month'] ?? today()->format('n')), 1), 12);

        return CarbonImmutable::create($year, $month, 1);
    }
}
