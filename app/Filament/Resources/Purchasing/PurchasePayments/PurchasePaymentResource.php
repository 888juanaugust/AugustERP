<?php

declare(strict_types=1);

namespace App\Filament\Resources\Purchasing\PurchasePayments;

use App\Domain\Access\MenuKey;
use App\Domain\Documents\PaymentMethod;
use App\Domain\Numbering\TransactionType;
use App\Domain\Settlement\SettlementService;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Filament\Resources\Purchasing\PurchasePayments\Pages\CreatePurchasePayment;
use App\Filament\Resources\Purchasing\PurchasePayments\Pages\EditPurchasePayment;
use App\Filament\Resources\Purchasing\PurchasePayments\Pages\ListPurchasePayments;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\ErpResource;
use App\Filament\Support\GiroActions;
use App\Filament\Support\LineTotals;
use App\Filament\Support\NumberFields;
use App\Filament\Support\PayableFields;
use App\Filament\Support\PricedDocumentForm;
use App\Filament\Support\PrintAction;
use App\Filament\Support\VendorFields;
use App\Models\GeneralLedger\Account;
use App\Models\Purchasing\PurchasePayment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/** Purchase Payments: money out of a bank account against a vendor's open invoices, down payments and credit notes, with discounts taken. */
class PurchasePaymentResource extends ErpResource
{
    protected static ?string $model = PurchasePayment::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'Purchase payment';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::PurchasePayments;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                VendorFields::select(fillsTerms: false)->label('Paid to'),
                Select::make('bank_account_id')->label('Bank')->options(fn () => Account::options(AccountType::CashBank))->searchable()->required()->native(false),
                Select::make('payment_method')->label('Payment method')->options(PaymentMethod::class)->default(PaymentMethod::BankTransfer)->required()->native(false)->live(),
                DatePicker::make('trans_date')->label('Payment date')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                NumberFields::make(TransactionType::CashBankVoucher, 'Voucher No.'),
                Placeholder::make('amount_preview')->label('Amount paid')->content(fn (Get $get) => Format::rupiah(LineTotals::sum($get('lines'), 'amount'))),
                TextInput::make('cheque_no')->label('Cheque / giro No.')->maxLength(40)->visible(fn (Get $get) => $get('payment_method') === PaymentMethod::Cheque->value || $get('payment_method') === PaymentMethod::Cheque),
                DatePicker::make('cheque_date')->label('Cheque date')->native(false)->displayFormat(Format::DATE_INPUT)->visible(fn (Get $get) => $get('payment_method') === PaymentMethod::Cheque->value || $get('payment_method') === PaymentMethod::Cheque),
            ]),
            Tabs::make('payment')->tabs([
                Tab::make('Invoices')->schema([
                    Action::make('pullOpen')
                        ->label('Pull every open document')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('gray')
                        ->visible(fn (Get $get) => (bool) $get('vendor_id'))
                        ->action(function (Set $set, Get $get): void {
                            $rows = [];
                            foreach (PayableFields::openFor((int) $get('vendor_id')) as $key => $open) {
                                $rows[(string) Str::uuid()] = ['payable_key' => $key, 'amount' => $open['balance'], 'discount' => 0];
                            }
                            $set('lines', $rows);
                            Notification::make()->title(count($rows).' open document(s) pulled')->success()->send();
                        }),
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make('Document'),
                            TableColumn::make('Open balance')->alignment(Alignment::End),
                            TableColumn::make('Pay')->alignment(Alignment::End),
                            TableColumn::make('Discount')->alignment(Alignment::End),
                            TableColumn::make('Discount account'),
                        ])
                        ->schema([
                            Select::make('payable_key')
                                ->options(fn (Get $get) => PayableFields::openFor((int) $get('../../vendor_id'))->map(fn ($o) => $o['label'])->all())
                                ->getOptionLabelUsing(fn ($value) => $value && ($doc = PayableFields::resolve($value)) ? $doc->number : $value)
                                ->required()->native(false)->live()
                                ->afterStateUpdated(fn (Set $set, $state) => $set('amount', $state && ($doc = PayableFields::resolve($state)) ? app(SettlementService::class)->balance($doc) : 0)),
                            Placeholder::make('open')->hiddenLabel()->content(fn (Get $get) => ($key = $get('payable_key')) && ($doc = PayableFields::resolve($key)) ? Format::number(app(SettlementService::class)->balance($doc)) : ''),
                            PricedDocumentForm::money('amount', 'Pay')->required()->live(onBlur: true)->minValue(fn (Get $get) => ($key = $get('payable_key')) && ($doc = PayableFields::resolve($key)) && method_exists($doc, 'isCredit') && $doc->isCredit() ? null : 0),
                            PricedDocumentForm::money('discount', 'Discount')->live(onBlur: true),
                            Select::make('discount_account_id')->options(fn () => Account::options(AccountType::CostOfSales, AccountType::OtherIncome, AccountType::OtherExpense))->native(false)->placeholder('Purchase Discounts'),
                            Hidden::make('payable_type'),
                            Hidden::make('payable_id'),
                        ])
                        ->minItems(1)->defaultItems(0)->live()
                        ->addActionLabel('Add document')
                        ->mutateRelationshipDataBeforeFillUsing(fn (array $data) => $data + ['payable_key' => ($data['payable_type'] ?? '').':'.($data['payable_id'] ?? '')])
                        ->mutateRelationshipDataBeforeCreateUsing(fn (array $data) => self::splitKey($data))
                        ->mutateRelationshipDataBeforeSaveUsing(fn (array $data) => self::splitKey($data)),
                ]),
                Tab::make(__('fields.other_info'))->schema([
                    Textarea::make('description')->label(__('fields.description'))->rows(3),
                ]),
            ]),
        ])->columns(1);
    }

    private static function splitKey(array $data): array
    {
        [$type, $id] = array_pad(explode(':', (string) ($data['payable_key'] ?? ''), 2), 2, null);
        $data['payable_type'] = $type;
        $data['payable_id'] = (int) $id;
        unset($data['payable_key']);

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['vendor', 'bankAccount', 'giro']))
            ->columns([
                TextColumn::make('number')->label('Number')->searchable()->sortable()->fontFamily('mono'),
                Tanggal::make('trans_date')->label(__('fields.trans_date')),
                TextColumn::make('cheque_no')->label('Cheque No.')->placeholder('—')->toggleable(),
                Tanggal::make('cheque_date')->label('Cheque date')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('vendor.name')->label(__('fields.vendor'))->searchable(),
                TextColumn::make('bankAccount.name')->label('Bank'),
                TextColumn::make('payment_method')->label('Method')->badge()->color('gray'),
                TextColumn::make('description')->label(__('fields.description'))->limit(40)->placeholder('—'),
                TextColumn::make('giro.status')->label('Giro')->badge()->formatStateUsing(fn (string $state) => ucfirst($state))->color(fn (string $state) => GiroActions::statusColor($state))->placeholder('—'),
                Rupiah::make('amount')->label('Amount paid'),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('payment_method')->label('Method')->options(PaymentMethod::class),
                SelectFilter::make('bank_account_id')->label('Bank')->options(fn () => Account::options(AccountType::CashBank)),
                SelectFilter::make('vendor_id')->label('Paid to')->relationship('vendor', 'name')->searchable(),
            ])
            ->recordActions([EditAction::make(), ...GiroActions::forRecord(), PrintAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchasePayments::route('/'),
            'create' => CreatePurchasePayment::route('/create'),
            'edit' => EditPurchasePayment::route('/{record}/edit'),
        ];
    }
}
