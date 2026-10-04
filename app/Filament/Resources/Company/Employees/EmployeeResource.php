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
            Section::make('Personal data')
                ->columns(3)
                ->schema([
                    Select::make('salutation')->label('Salutation')->options(['Mr' => 'Mr', 'Mrs' => 'Mrs', 'Ms' => 'Ms'])->native(false),
                    TextInput::make('name')->label('Full name')->required()->maxLength(150)->columnSpan(2),
                    TextInput::make('nik_no')->label('National ID (NIK)')->maxLength(30),
                    TextInput::make('email')->label('Email')->email()->maxLength(150),
                    TextInput::make('mobile_phone')->label('Mobile')->tel()->maxLength(30),
                    TextInput::make('work_phone')->label('Work phone')->tel()->maxLength(30),
                    TextInput::make('home_phone')->label('Home phone')->tel()->maxLength(30),
                    TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(30),
                    TextInput::make('website')->label('Website')->maxLength(150),
                    TextInput::make('nationality')->label('Nationality')->maxLength(60)->default('Indonesia'),
                ]),
            Section::make('Employment')
                ->columns(3)
                ->schema([
                    NumberFields::make(TransactionType::Employee, 'Employee ID'),
                    TextInput::make('position')->label('Position')->maxLength(100),
                    DatePicker::make('join_date')->label('Join date')->native(false)->displayFormat(Format::DATE_INPUT),
                    Select::make('branch_id')->label(__('fields.branch'))->relationship('branch', 'name')->preload()->native(false),
                    Toggle::make('is_salesman')->label('Salesperson: may be named on sales documents')->inline(false),
                    self::activeToggle()->inline(false),
                    Textarea::make('notes')->label(__('fields.memo'))->rows(2)->columnSpanFull(),
                ]),
            Tabs::make('employee')->tabs([
                Tab::make('Address')->schema([AddressFields::make('', 'Home address')]),
                Tab::make('Income tax')->schema([
                    Toggle::make('withhold_income_tax')->label('Withhold income tax (Art. 21)')->live(),
                    Grid::make(2)
                        ->visible(fn (Get $get) => (bool) $get('withhold_income_tax'))
                        ->schema([
                            TextInput::make('npwp_no')->label('Tax ID (NPWP)')->maxLength(30),
                            Select::make('work_status')->label('Employment status')->options(WorkStatus::class)->native(false),
                            Select::make('tax_status')->label('Non-taxable income status (PTKP)')->options(PtkpStatus::class)->native(false),
                            Grid::make(2)->schema([
                                Select::make('start_month_payment')->label('Tax counted from month')
                                    ->options(collect(range(1, 12))->mapWithKeys(fn (int $m) => [$m => date('F', mktime(0, 0, 0, $m, 1))])->all())->native(false),
                                Select::make('start_year_payment')->label('year')
                                    ->options(collect(range((int) date('Y') - 5, (int) date('Y') + 1))->mapWithKeys(fn (int $y) => [$y => (string) $y])->all())->native(false),
                            ]),
                            TextInput::make('previous_income')->label('Income earned before joining')->prefix(Format::symbol())->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0),
                            TextInput::make('previous_tax')->label('Tax withheld before joining')->prefix(Format::symbol())->mask(RawJs::make('$money($input, \',\', \'.\', 0)'))->stripCharacters('.')->numeric()->default(0),
                        ]),
                ]),
                Tab::make('Salary account')->schema([
                    Grid::make(3)->schema([
                        Select::make('bank_id')->label('Bank')->relationship('bank', 'name')->preload()->searchable()->native(false),
                        TextInput::make('bank_account')->label('Account number')->maxLength(50),
                        TextInput::make('bank_account_name')->label('Account holder')->maxLength(150),
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
                TextColumn::make('position')->label('Position')->placeholder('—'),
                TextColumn::make('email')->label('Email')->placeholder('—'),
                TextColumn::make('mobile_phone')->label('Mobile')->placeholder('—'),
                TextColumn::make('number')->label('Employee ID')->fontFamily('mono')->searchable(),
                TextColumn::make('tax_status')->label('PTKP')->placeholder('—')->formatStateUsing(fn ($state) => $state instanceof PtkpStatus ? $state->value : ''),
                TextColumn::make('work_status')->label('Employment')->placeholder('—'),
                IconColumn::make('is_salesman')->label('Sales')->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                self::activeFilter(),
                TernaryFilter::make('is_salesman')->label('Salesperson'),
                SelectFilter::make('work_status')->label('Employment status')->options(WorkStatus::class),
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
