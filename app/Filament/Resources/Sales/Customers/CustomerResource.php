<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sales\Customers;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Pengaturan\Preferensi;
use App\Domain\Pengaturan\PreferensiKey;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Enums\TaxDocumentCode;
use App\Domain\Shared\Enums\WpType;
use App\Domain\Shared\Format;
use App\Filament\Resources\Sales\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Sales\Customers\Pages\EditCustomer;
use App\Filament\Resources\Sales\Customers\Pages\ListCustomers;
use App\Filament\Support\AddressFields;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\MasterResource;
use App\Filament\Support\NumberFields;
use App\Models\Company\Employee;
use App\Models\Company\PaymentTerm;
use App\Models\GeneralLedger\Account;
use App\Models\Sales\Customer;
use App\Models\Sales\CustomerCategory;
use App\Models\Sales\PriceCategory;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** The Customer screen: every tab of the reference system's form. */
class CustomerResource extends MasterResource
{
    protected static ?string $model = Customer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $modelLabel = 'Customer';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Customers;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('General')
                ->columns(3)
                ->schema([
                    TextInput::make('name')->label(__('fields.name'))->required()->maxLength(150)->columnSpan(2),
                    NumberFields::make(TransactionType::Customer, 'Customer ID'),
                    Select::make('category_id')->label('Category')->relationship('category', 'name')->preload()->searchable()->native(false)
                        ->default(fn () => CustomerCategory::query()->where('is_default', true)->value('id')),
                    TextInput::make('work_phone')->label('Work phone')->tel()->maxLength(30),
                    TextInput::make('mobile_phone')->label('Mobile')->tel()->maxLength(30),
                    TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(30),
                    TextInput::make('email')->label('Email')->email()->maxLength(150),
                    TextInput::make('fax')->label('Fax')->maxLength(30),
                    TextInput::make('website')->label('Website')->maxLength(150),
                    Select::make('branch_id')->label('Used in branch')->relationship('branch', 'name')->preload()->native(false),
                    self::activeToggle()->inline(false),
                ]),
            Tabs::make('customer')
                ->persistTabInQueryString()
                ->tabs([
                    Tab::make('Billing address')->schema([AddressFields::make('bill', 'Billing address')]),
                    Tab::make('Contacts')->schema([
                        Repeater::make('contacts')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->table([
                                TableColumn::make('Full name'),
                                TableColumn::make('Position'),
                                TableColumn::make('Email'),
                                TableColumn::make('Mobile'),
                            ])
                            ->schema([
                                TextInput::make('name')->required()->maxLength(150),
                                TextInput::make('position')->maxLength(100),
                                TextInput::make('email')->email()->maxLength(150),
                                TextInput::make('mobile_phone')->tel()->maxLength(30),
                            ])
                            ->addActionLabel('Add contact')
                            ->defaultItems(0),
                    ]),
                    Tab::make('Shipping')->schema([
                        Toggle::make('ship_same_as_bill')->label('Same as the billing address')->default(true)->live(),
                        AddressFields::make('ship', 'Shipping address')->visible(fn (Get $get) => ! $get('ship_same_as_bill')),
                        Repeater::make('addresses')
                            ->label('Other delivery addresses')
                            ->relationship()
                            ->orderColumn('sort')
                            ->simple(Textarea::make('address')->rows(2)->required())
                            ->addActionLabel('Add address')
                            ->defaultItems(0),
                    ]),
                    Tab::make('Sales')->schema([
                        Grid::make(2)->schema([
                            Select::make('price_category_id')->label('Price category')->relationship('priceCategory', 'name')->preload()->native(false)
                                ->default(fn () => PriceCategory::query()->where('is_default', true)->value('id')),
                            Select::make('discount_category_id')->label('Discount category')->relationship('discountCategory', 'name')->preload()->native(false),
                            Select::make('salesman_id')->label('Default salesperson')->options(fn () => Employee::query()->salesmen()->orderBy('name')->pluck('name', 'id'))->searchable()->native(false),
                            Select::make('payment_term_id')->label(__('fields.payment_term'))->relationship('paymentTerm', 'name', fn ($query) => $query->where('is_active', true))->preload()->native(false)
                                ->default(fn () => PaymentTerm::default()?->id),
                            TextInput::make('default_sales_disc')->label('Default discount (%)')->numeric()->minValue(0)->maxValue(100)->default(0),
                            TextInput::make('default_invoice_desc')->label('Default invoice description')->maxLength(255),
                        ]),
                        Fieldset::make('Accounts')
                            ->columns(2)
                            ->schema([
                                Select::make('receivable_account_id')->label('Receivable')->options(fn () => Account::options(AccountType::AccountsReceivable))->searchable()->native(false),
                                Select::make('down_payment_account_id')->label('Down payments')->options(fn () => Account::options(AccountType::OtherCurrentLiability))->searchable()->native(false),
                                Select::make('sales_account_id')->label('Sales')->options(fn () => Account::options(AccountType::Revenue))->searchable()->native(false),
                                Select::make('item_discount_account_id')->label('Item discounts')->options(fn () => Account::options(AccountType::Revenue))->searchable()->native(false),
                                Select::make('cogs_account_id')->label('Cost of goods sold')->options(fn () => Account::options(AccountType::CostOfSales))->searchable()->native(false),
                                Select::make('sales_return_account_id')->label('Sales returns')->options(fn () => Account::options(AccountType::Revenue))->searchable()->native(false),
                                Select::make('sales_discount_account_id')->label('Sales discounts')->options(fn () => Account::options(AccountType::Revenue))->searchable()->native(false),
                            ]),
                    ]),
                    Tab::make('Tax')->schema([
                        Toggle::make('default_inc_tax')->label('Invoice totals include tax by default')
                            ->default(fn () => app(Preferensi::class)->isOn(PreferensiKey::NewCustomerInclusiveTax)),
                        Grid::make(2)->schema([
                            Select::make('wp_type')->label('Tax ID type')->options(WpType::class)->native(false),
                            TextInput::make('wp_number')->label('Tax ID number')->maxLength(30),
                            TextInput::make('wp_name')->label('Taxpayer name')->maxLength(150),
                            TextInput::make('nitku')->label('Business location ID (NITKU)')->maxLength(30),
                            TextInput::make('country_tax_code')->label('Country code')->maxLength(5)->default('IDN'),
                            Select::make('document_code')->label('Transaction type')->options(TaxDocumentCode::options(TaxDocumentCode::forCustomers()))->native(false),
                        ]),
                        Toggle::make('tax_same_as_bill')->label('Tax address is the billing address')->default(true)->live(),
                        AddressFields::make('tax', 'Tax address')->visible(fn (Get $get) => ! $get('tax_same_as_bill')),
                    ]),
                    Tab::make('Opening balance')->schema([
                        Repeater::make('openingBalances')
                            ->hiddenLabel()
                            ->relationship()
                            ->orderColumn('sort')
                            ->table([
                                TableColumn::make('Date'),
                                TableColumn::make('Amount'),
                                TableColumn::make('Payment term'),
                                TableColumn::make('Number'),
                                TableColumn::make('Description'),
                            ])
                            ->schema([
                                DatePicker::make('trans_date')->required()->native(false)->displayFormat(Format::DATE_INPUT),
                                TextInput::make('amount')->required()->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->prefix('Rp'),
                                Select::make('payment_term_id')->relationship('paymentTerm', 'name')->native(false),
                                TextInput::make('number')->maxLength(60),
                                TextInput::make('description')->maxLength(255),
                            ])
                            ->addActionLabel('Add open invoice')
                            ->defaultItems(0),
                    ]),
                    Tab::make('Other')->schema([
                        Fieldset::make('Credit limit')->schema([
                            Radio::make('credit_limit_mode')
                                ->hiddenLabel()
                                ->options(['per_customer' => 'Per customer', 'parent' => 'Shared with a parent customer'])
                                ->default('per_customer')
                                ->live(),
                            Select::make('parent_customer_id')->label('Parent customer')
                                ->relationship('parentCustomer', 'name', fn ($query, ?Customer $record) => $query->when($record, fn ($query) => $query->whereKeyNot($record->getKey())))
                                ->searchable()->preload()->native(false)
                                ->visible(fn (Get $get) => $get('credit_limit_mode') === 'parent'),
                            Grid::make(2)->schema([
                                Toggle::make('credit_limit_age_enabled')->label('Block when an invoice is older than')->live()->inline(false),
                                TextInput::make('credit_limit_age_days')->label('days')->numeric()->integer()->minValue(0)->default(0)->visible(fn (Get $get) => $get('credit_limit_age_enabled')),
                                Toggle::make('credit_limit_amount_enabled')->label('Block when receivables and open orders exceed')->live()->inline(false),
                                TextInput::make('credit_limit_amount')->label('amount')->prefix('Rp')->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0)->visible(fn (Get $get) => $get('credit_limit_amount_enabled')),
                            ])->visible(fn (Get $get) => $get('credit_limit_mode') === 'per_customer'),
                        ]),
                        Select::make('default_warehouse_id')->label('Default warehouse')->relationship('defaultWarehouse', 'name', fn ($query) => $query->where('is_active', true))->preload()->native(false),
                        Textarea::make('notes')->label(__('fields.memo'))->rows(3),
                    ]),
                ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['category', 'priceCategory', 'discountCategory', 'branch', 'paymentTerm', 'contacts']))
            ->columns([
                TextColumn::make('name')->label(__('fields.name'))->searchable()->sortable()->weight('medium'),
                TextColumn::make('primary_contact')->label('Primary contact')->state(fn (Customer $r) => $r->contacts->first()?->name)->placeholder('—'),
                TextColumn::make('number')->label('Customer ID')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('category.name')->label('Category')->placeholder('—'),
                TextColumn::make('priceCategory.name')->label('Price category')->placeholder('—'),
                TextColumn::make('discountCategory.name')->label('Discount category')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('tax_address')->label('Tax address')->state(fn (Customer $r) => $r->taxAddress())->limit(40)->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('branch.name')->label(__('fields.branch'))->placeholder('—'),
                TextColumn::make('bill_address')->label('Address')->state(fn (Customer $r) => $r->billAddress())->limit(40)->placeholder('—'),
                TextColumn::make('paymentTerm.name')->label(__('fields.payment_term'))->placeholder('—'),
                Rupiah::make('credit_limit_amount')->label('Credit limit')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                self::activeFilter(),
                SelectFilter::make('category_id')->label('Category')->relationship('category', 'name'),
                SelectFilter::make('branch_id')->label(__('fields.branch'))->relationship('branch', 'name'),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }
}
