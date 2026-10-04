<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\Accounts;

use App\Domain\Access\MenuKey;
use App\Domain\Posting\AccountBalances;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Filament\Resources\GeneralLedger\Accounts\Pages\ManageAccounts;
use App\Filament\Support\MasterResource;
use App\Models\GeneralLedger\Account;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** The Chart of Accounts: sixteen types, parents and sub-accounts, bank details, opening balance, users. */
class AccountResource extends MasterResource
{
    protected static ?string $model = Account::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $modelLabel = 'Account';

    public static function menuKey(): MenuKey
    {
        return MenuKey::ChartOfAccounts;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('account')->tabs([
                Tab::make('General')->schema([
                    Select::make('account_type')->label('Account type')->options(AccountType::class)->required()->native(false)->live(),
                    Toggle::make('is_sub')->label('Sub-account')->live()->inline(false),
                    Select::make('parent_id')
                        ->label('Parent account')
                        ->relationship('parent', 'name', fn ($query, ?Account $record, Get $get) => $query
                            ->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                            ->when($get('account_type'), fn ($q, $type) => $q->where('account_type', $type instanceof AccountType ? $type->value : $type))
                            ->orderBy('no'))
                        ->getOptionLabelFromRecordUsing(fn (Account $a) => $a->displayName())
                        ->searchable()->preload()->native(false)
                        ->required(fn (Get $get) => (bool) $get('is_sub'))
                        ->visible(fn (Get $get) => (bool) $get('is_sub')),
                    TextInput::make('no')->label('Account number')->required()->maxLength(30)->unique(ignoreRecord: true),
                    TextInput::make('name')->label(__('fields.name'))->required()->maxLength(150)->placeholder('e.g. BCA a/c 123-456'),
                    Textarea::make('memo')->label(__('fields.memo'))->rows(2),
                    self::activeToggle()->inline(false),
                ]),
                Tab::make('Bank')
                    ->visible(fn (Get $get) => ($get('account_type') instanceof AccountType ? $get('account_type')->value : $get('account_type')) === AccountType::CashBank->value)
                    ->schema([
                        Select::make('bank_id')->label('Bank')->relationship('bank', 'name')->preload()->searchable()->native(false),
                        TextInput::make('bank_account')->label('Account number')->maxLength(50),
                        TextInput::make('bank_account_name')->label('Account holder')->maxLength(150),
                    ]),
                Tab::make('Opening balance')->schema([
                    TextInput::make('opening_amount')->label('Balance')->prefix(Format::symbol())->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()
                        ->helperText('With the account\'s normal sign: a positive liability is a credit balance. Posted against Opening Balance Equity.'),
                    DatePicker::make('opening_date')->label('As of')->native(false)->displayFormat(Format::DATE_INPUT),
                ]),
                self::usersTab(),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        $balances = fn () => once(fn () => AccountBalances::asOf());

        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('parent'))
            ->columns([
                TextColumn::make('no')->label('Account number')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('name')->label(__('fields.name'))->searchable()->sortable()
                    ->state(fn (Account $r) => ($r->is_sub ? '    ' : '').$r->name)
                    ->extraAttributes(fn (Account $r) => $r->is_sub ? ['style' => 'padding-left: 1.5rem'] : [])
                    ->weight(fn (Account $r) => $r->is_sub ? null : 'medium'),
                TextColumn::make('account_type')->label('Account type')->badge()->color('gray'),
                TextColumn::make('balance')->label('Balance')->alignEnd()
                    ->state(fn (Account $r) => Format::number($balances()[$r->id] ?? 0))
                    ->extraCellAttributes(['class' => 'ae-money']),
                self::activeColumn(),
            ])
            ->defaultSort('no')
            ->filters([
                self::activeFilter(),
                SelectFilter::make('account_type')->label('Account type')->options(AccountType::class),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()->hidden(fn (Account $r) => $r->is_system)]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageAccounts::route('/')];
    }
}
