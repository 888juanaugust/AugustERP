<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchaseInvoices;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Format;
use App\Filament\Resources\Purchasing\PurchaseInvoices\Pages\CreatePurchaseInvoice;
use App\Filament\Resources\Purchasing\PurchaseInvoices\Pages\EditPurchaseInvoice;
use App\Filament\Resources\Purchasing\PurchaseInvoices\Pages\ListPurchaseInvoices;
use App\Filament\Resources\Purchasing\PurchasePayments\PurchasePaymentResource;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\ErpResource;
use App\Filament\Support\PricedDocumentForm;
use App\Filament\Support\PullAction;
use App\Filament\Support\VendorFields;
use App\Models\Inventory\Item;
use App\Models\Purchasing\GoodsReceipt;
use App\Models\Purchasing\PurchaseDownPayment;
use App\Models\Purchasing\PurchaseInvoice;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\VendorPrice;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/** Purchase Invoices: the vendor's bill, from receipts, from orders or direct; charges to accounts or into cost; down payments deducted. */
class PurchaseInvoiceResource extends ErpResource
{
    protected static ?string $model = PurchaseInvoice::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?string $modelLabel = 'Purchase invoice';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::PurchaseInvoices;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            PricedDocumentForm::header(VendorFields::select(), TransactionType::PurchaseInvoice, 'Form No.', [
                TextInput::make('bill_number')->label("Vendor's invoice No.")->maxLength(60),
            ]),
            Tabs::make('invoice')->tabs([
                PricedDocumentForm::linesTab(
                    before: [
                        PullAction::make('Pull from receipts', 'vendor_id',
                            fn (Get $get) => GoodsReceipt::query()->where('vendor_id', $get('vendor_id'))->whereIn('status', ['pending', 'partial'])->orderByDesc('trans_date')->get(),
                            fn (int $id) => PricedDocumentForm::pulledLines(GoodsReceipt::query()->findOrFail($id)->lines()->with('item')->get(), 'goods_receipt_line'),
                        ),
                        PullAction::make('Pull from orders', 'vendor_id',
                            fn (Get $get) => PurchaseOrder::query()->where('vendor_id', $get('vendor_id'))->whereIn('status', ['pending', 'partial'])->orderByDesc('trans_date')->get(),
                            fn (int $id) => PricedDocumentForm::pulledLines(PurchaseOrder::query()->findOrFail($id)->lines()->with('item')->get(), 'purchase_order_line'),
                        )->name('pullOrders'),
                    ],
                    priceResolver: fn (Item $item, Get $get) => VendorPrice::lookup((int) $get('../../vendor_id'), $item->id, $get('../../trans_date') ?: today()) ?? (string) $item->purchase_price,
                ),
                PricedDocumentForm::otherInfoTab([
                    VendorFields::paymentTerm(),
                    VendorFields::bankAccount(),
                    DatePicker::make('due_date')->label('Due date')->native(false)->displayFormat(Format::DATE_INPUT)->helperText('Blank: from the payment term.'),
                    TextInput::make('tax_invoice_number')->label('Tax invoice No. (vendor)')->maxLength(40),
                ]),
                PricedDocumentForm::chargesTab(allocateToCost: true),
                Tab::make('Down payments')->schema([
                    Repeater::make('downPayments')
                        ->hiddenLabel()
                        ->relationship()
                        ->table([TableColumn::make('Down payment'), TableColumn::make('Amount deducted')->alignment(Alignment::End)])
                        ->schema([
                            Select::make('purchase_down_payment_id')
                                ->options(fn (Get $get) => PurchaseDownPayment::query()->where('vendor_id', $get('../../vendor_id'))->whereIn('status', ['pending', 'partial'])->get()
                                    ->mapWithKeys(fn ($dp) => [$dp->id => "{$dp->number} · ".Format::date($dp->trans_date).' · open '.Format::rupiah($dp->remaining())]))
                                ->required()->native(false)->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('amount', $state ? (string) PurchaseDownPayment::query()->find($state)?->remaining() : 0)),
                            PricedDocumentForm::money('amount', 'Amount')->required()->minValue(1),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Deduct a down payment'),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('vendor'))
            ->columns([
                TextColumn::make('number')->label('Number')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('bill_number')->label('Invoice No.')->searchable()->placeholder('—'),
                Tanggal::make('trans_date')->label(__('fields.trans_date')),
                TextColumn::make('vendor.name')->label(__('fields.vendor'))->searchable(),
                TextColumn::make('description')->label(__('fields.description'))->limit(40)->placeholder('—'),
                TextColumn::make('payment_status')->label(__('fields.status'))->badge()
                    ->formatStateUsing(fn (string $state) => __('status.payment.'.($state === 'partial' ? 'partially_paid' : $state)))
                    ->color(fn (string $state) => match ($state) {
                        'paid' => 'success', 'partial' => 'warning', default => 'gray'
                    }),
                TextColumn::make('age')->label('Age (days)')->state(fn (PurchaseInvoice $r) => $r->payment_status === 'paid' ? '' : (string) $r->trans_date->diffInDays(today()))->alignEnd(),
                Rupiah::make('total')->label(__('fields.total')),
                IconColumn::make('is_printed')->label(__('fields.is_printed'))->boolean()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('vendor_id')->label(__('fields.vendor'))->relationship('vendor', 'name')->searchable(),
                TernaryFilter::make('is_printed')->label(__('fields.is_printed')),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('pay')->label('Pay')->icon('heroicon-m-banknotes')->color('primary')
                    ->visible(fn (PurchaseInvoice $record) => $record->payment_status !== 'paid' && PurchasePaymentResource::canCreate())
                    ->url(fn (PurchaseInvoice $record) => PurchasePaymentResource::getUrl('create', ['source' => 'purchase_invoice:'.$record->id])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseInvoices::route('/'),
            'create' => CreatePurchaseInvoice::route('/create'),
            'edit' => EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }
}
