<?php

declare(strict_types=1);

namespace App\Filament\Resources\Company\Employees;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Enums\PtkpStatus;
use App\Domain\Shared\Enums\WorkStatus;
use App\Domain\Shared\Format;
use App\Filament\Resources\Company\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Company\Employees\Pages\EditEmployee;
use App\Filament\Resources\Company\Employees\Pages\ListEmployees;
use App\Filament\Support\AddressFields;
use App\Filament\Support\MasterResource;
use App\Filament\Support\NumberFields;
use App\Models\Company\Employee;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/** The Employee screen: personal data, employment, address, income tax, salary account. */
class EmployeeResource extends MasterResource
{
    protected static ?string $model = Employee::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $modelLabel = 'Employee';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Employees;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('Personal data'))
                ->columns(3)
                ->schema([
                    Select::make('salutation')->label(__('Salutation'))->options(['Mr' => __('Mr'), 'Mrs' => __('Mrs'), 'Ms' => __('Ms')])->native(false),
                    TextInput::make('name')->label(__('Full name'))->required()->maxLength(150)->columnSpan(2),
                    TextInput::make('nik_no')->label(__('National ID (NIK)'))->maxLength(30),
                    TextInput::make('email')->label(__('Email'))->email()->maxLength(150),
                    TextInput::make('mobile_phone')->label(__('Mobile'))->tel()->maxLength(30),
                    TextInput::make('work_phone')->label(__('Work phone'))->tel()->maxLength(30),
                    TextInput::make('home_phone')->label(__('Home phone'))->tel()->maxLength(30),
                    TextInput::make('whatsapp')->label(__('WhatsApp'))->tel()->maxLength(30),
                    TextInput::make('website')->label(__('Website'))->maxLength(150),
                    TextInput::make('nationality')->label(__('Nationality'))->maxLength(60)->default('Indonesia'),
                ]),
            Section::make(__('Employment'))
                ->columns(3)
                ->schema([
                    NumberFields::make(TransactionType::Employee, 'Employee ID'),
                    TextInput::make('position')->label(__('Position'))->maxLength(100),
                    DatePicker::make('join_date')->label(__('Join date'))->native(false)->displayFormat(Format::DATE_INPUT),
                    Select::make('branch_id')->label(__('fields.branch'))->relationship('branch', 'name')->preload()->native(false),
                    Toggle::make('is_salesman')->label(__('Salesperson: may be named on sales documents'))->inline(false),
                    self::activeToggle()->inline(false),
                    Textarea::make('notes')->label(__('fields.memo'))->rows(2)->columnSpanFull(),
                ]),
            Tabs::make('employee')->tabs([
                Tab::make(__('Address'))->schema([AddressFields::make('', 'Home address')]),
                Tab::make(__('Income tax'))->schema([
                    Toggle::make('withhold_income_tax')->label(__('Withhold income tax (Art. 21)'))->live(),
                    Grid::make(2)
                        ->visible(fn (Get $get) => (bool) $get('withhold_income_tax'))
                        ->schema([
                            TextInput::make('npwp_no')->label(__('Tax ID (NPWP)'))->maxLength(30),
                            Select::make('work_status')->label(__('Employment status'))->options(WorkStatus::class)->native(false),
                            Select::make('tax_status')->label(__('Non-taxable income status (PTKP)'))->options(PtkpStatus::class)->native(false),
                            Grid::make(2)->schema([
                                Select::make('start_month_payment')->label(__('Tax counted from month'))
                                    ->options(collect(range(1, 12))->mapWithKeys(fn (int $m) => [$m => date('F', mktime(0, 0, 0, $m, 1))])->all())->native(false),
                                Select::make('start_year_payment')->label(__('year'))
                                    ->options(collect(range((int) date('Y') - 5, (int) date('Y') + 1))->mapWithKeys(fn (int $y) => [$y => (string) $y])->all())->native(false),
                            ]),
                            TextInput::make('previous_income')->label(__('Income earned before joining'))->prefix(Format::symbol())->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0),
                            TextInput::make('previous_tax')->label(__('Tax withheld before joining'))->prefix(Format::symbol())->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0),
                        ]),
                ]),
                Tab::make(__('Salary account'))->schema([
                    Grid::make(3)->schema([
                        Select::make('bank_id')->label(__('Bank'))->relationship('bank', 'name')->preload()->searchable()->native(false),
                        TextInput::make('bank_account')->label(__('Account number'))->maxLength(50),
                        TextInput::make('bank_account_name')->label(__('Account holder'))->maxLength(150),
                    ]),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('fields.name'))->searchable()->sortable()->weight('medium'),
                TextColumn::make('position')->label(__('Position'))->placeholder('—'),
                TextColumn::make('email')->label(__('Email'))->placeholder('—'),
                TextColumn::make('mobile_phone')->label(__('Mobile'))->placeholder('—'),
                TextColumn::make('number')->label(__('Employee ID'))->fontFamily('mono')->searchable(),
                TextColumn::make('tax_status')->label(__('PTKP'))->placeholder('—')->formatStateUsing(fn ($state) => $state instanceof PtkpStatus ? $state->value : ''),
                TextColumn::make('work_status')->label(__('Employment'))->placeholder('—'),
                IconColumn::make('is_salesman')->label(__('Sales'))->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                self::activeFilter(),
                TernaryFilter::make('is_salesman')->label(__('Salesperson')),
                SelectFilter::make('work_status')->label(__('Employment status'))->options(WorkStatus::class),
            ])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
