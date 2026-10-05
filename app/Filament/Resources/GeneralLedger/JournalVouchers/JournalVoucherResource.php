<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\JournalVouchers;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Format;
use App\Filament\Resources\GeneralLedger\JournalVouchers\Pages\CreateJournalVoucher;
use App\Filament\Resources\GeneralLedger\JournalVouchers\Pages\EditJournalVoucher;
use App\Filament\Resources\GeneralLedger\JournalVouchers\Pages\ListJournalVouchers;
use App\Filament\Support\BranchFields;
use App\Filament\Support\ErpResource;
use App\Filament\Support\LineTotals;
use App\Filament\Support\MoneyInput;
use App\Filament\Support\NumberFields;
use App\Models\GeneralLedger\Account;
use App\Models\GeneralLedger\JournalVoucher;
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
use Filament\Tables\Table;

/** Journal Vouchers: the manual journal form; the list shows every journal entry the books hold. */
class JournalVoucherResource extends ErpResource
{
    protected static ?string $model = JournalVoucher::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $modelLabel = 'Journal voucher';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::JournalVouchers;
    }

    public static function money(string $name, string $label): TextInput
    {
        return MoneyInput::make($name)->label($label)->default(0)->live(onBlur: true);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columns(3)
                ->schema([
                    DatePicker::make('trans_date')->label(__('fields.trans_date'))->required()->native(false)->default(today()),
                    NumberFields::make(TransactionType::JournalVoucher, 'Number'),
                    BranchFields::select(),
                ]),
            Tabs::make('voucher')->tabs([
                Tab::make(__('Journal lines'))->schema([
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make(__('Account')),
                            TableColumn::make(__('Debit'))->alignment(Alignment::End),
                            TableColumn::make(__('Credit'))->alignment(Alignment::End),
                            TableColumn::make(__('Memo')),
                        ])
                        ->schema([
                            Select::make('account_id')->options(fn () => Account::options())->searchable()->required()->native(false),
                            self::money('debit', 'Debit'),
                            self::money('credit', 'Credit'),
                            TextInput::make('memo')->maxLength(255),
                        ])
                        ->live()
                        ->minItems(2)
                        ->defaultItems(2)
                        ->addActionLabel('Add line')
                        ->rule(fn () => function (string $attribute, $value, $fail) {
                            $debit = LineTotals::sum($value, 'debit');
                            $credit = LineTotals::sum($value, 'credit');
                            if ($debit !== $credit) {
                                $fail('The journal must balance: debit '.Format::number($debit).', credit '.Format::number($credit).'.');
                            }
                            if ($debit === 0) {
                                $fail('The journal has no amounts.');
                            }
                        }),
                    Placeholder::make('totals')
                        ->hiddenLabel()
                        ->content(fn (Get $get): string => 'Debit '.Format::rupiah(LineTotals::sum($get('lines'), 'debit')).'   ·   Credit '.Format::rupiah(LineTotals::sum($get('lines'), 'credit'))),
                ]),
                Tab::make(__('Other info'))->schema([
                    Textarea::make('description')->label(__('fields.description'))->rows(3),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table; // the list page builds the derived journal table itself
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalVouchers::route('/'),
            'create' => CreateJournalVoucher::route('/create'),
            'edit' => EditJournalVoucher::route('/{record}/edit'),
        ];
    }
}
