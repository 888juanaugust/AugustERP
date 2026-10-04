<?php

declare(strict_types=1);

namespace App\Filament\Resources\CashBank\CashPayments;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Filament\Resources\CashBank\CashPayments\Pages\CreateCashPayment;
use App\Filament\Resources\CashBank\CashPayments\Pages\EditCashPayment;
use App\Filament\Resources\CashBank\CashPayments\Pages\ListCashPayments;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\ErpResource;
use App\Filament\Support\GiroActions;
use App\Filament\Support\LineTotals;
use App\Filament\Support\NumberFields;
use App\Filament\Support\PricedDocumentForm;
use App\Models\CashBank\CashPayment;
use App\Models\GeneralLedger\Account;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Payments: money out of a cash or bank account to any accounts, one line each; paid by giro it waits in giros payable until the giro clears. */
class CashPaymentResource extends ErpResource
{
    protected static ?string $model = CashPayment::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowUpTray;

    protected static ?string $modelLabel = 'Payment';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Payments;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                Select::make('bank_account_id')->label('Cash / Bank')->options(fn () => Account::options(AccountType::CashBank))->searchable()->required()->native(false),
                DatePicker::make('trans_date')->label('Date')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                NumberFields::make(TransactionType::CashBankVoucher, 'Voucher No.'),
                Placeholder::make('amount_preview')->label('Amount')->content(fn (Get $get) => Format::rupiah(LineTotals::sum($get('lines'), 'amount'))),
            ]),
            Tabs::make('payment')->tabs([
                Tab::make('Payment details')->schema([
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make('Account'),
                            TableColumn::make('Amount')->alignment(Alignment::End),
                            TableColumn::make('Memo'),
                        ])
                        ->schema([
                            Select::make('account_id')->options(fn () => Account::options())->searchable()->required()->native(false),
                            PricedDocumentForm::money('amount', 'Amount')->required()->live(onBlur: true),
                            TextInput::make('memo')->maxLength(255),
                        ])
                        ->minItems(1)->defaultItems(1)->live()
                        ->addActionLabel('Add line'),
                ]),
                Tab::make('Other info')->schema([
                    TextInput::make('cheque_no')->label('Cheque / giro No.')->maxLength(40)->helperText('Filling this registers a giro that clears or bounces later.'),
                    DatePicker::make('cheque_date')->label('Giro due date')->native(false)->displayFormat(Format::DATE_INPUT),
                    Textarea::make('payee')->label('Payee')->rows(2),
                    Textarea::make('description')->label('Notes')->rows(3),
                ])->columns(2),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['bankAccount', 'giro']))
            ->columns([
                TextColumn::make('number')->label('Number')->searchable()->sortable()->fontFamily('mono'),
                Tanggal::make('trans_date')->label('Date'),
                TextColumn::make('bankAccount.name')->label('Cash / Bank'),
                TextColumn::make('cheque_no')->label('Cheque No.')->placeholder('—'),
                TextColumn::make('description')->label('Notes')->limit(40)->placeholder('—'),
                TextColumn::make('giro.status')->label('Giro')->badge()->formatStateUsing(fn (string $state) => ucfirst($state))->color(fn (string $state) => GiroActions::statusColor($state))->placeholder('—'),
                Rupiah::make('amount')->label('Amount'),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('bank_account_id')->label('Cash / Bank')->options(fn () => Account::options(AccountType::CashBank)),
            ])
            ->recordActions([EditAction::make(), ...GiroActions::forRecord()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashPayments::route('/'),
            'create' => CreateCashPayment::route('/create'),
            'edit' => EditCashPayment::route('/{record}/edit'),
        ];
    }
}
