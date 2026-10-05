<?php

declare(strict_types=1);

namespace App\Filament\Resources\GeneralLedger\PayrollEntries;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Domain\Shared\Money;
use App\Filament\Resources\GeneralLedger\PayrollEntries\Pages\CreatePayrollEntry;
use App\Filament\Resources\GeneralLedger\PayrollEntries\Pages\EditPayrollEntry;
use App\Filament\Resources\GeneralLedger\PayrollEntries\Pages\ListPayrollEntries;
use App\Filament\Support\BranchFields;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\DocumentPages;
use App\Filament\Support\ErpResource;
use App\Filament\Support\LineTotals;
use App\Filament\Support\Months;
use App\Filament\Support\NumberFields;
use App\Filament\Support\PayAction;
use App\Filament\Support\PricedDocumentForm;
use App\Models\Company\Employee;
use App\Models\Company\PayrollEntry;
use App\Models\Company\SalaryComponent;
use App\Models\GeneralLedger\Account;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

/**
 * Payroll Entries: the journal of one pay period, employee by employee; gross
 * pay to expense, tax withheld to the tax office, net to the employees until
 * paid. Payroll itself is outside the system.
 */
class PayrollEntryResource extends ErpResource
{
    protected static ?string $model = PayrollEntry::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'Payroll entry';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::PayrollEntries;
    }

    private static function payableOptions(): array
    {
        return Account::options(AccountType::OtherCurrentLiability, AccountType::AccountsPayable);
    }

    /** Net pay follows gross pay and the tax withheld as either is typed. */
    private static function recomputeNet(Set $set, Get $get): void
    {
        $set('net_amount', max(0, Money::parse($get('gross_amount')) - Money::parse($get('income_tax'))));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                Select::make('payment_type')->label(__('Payment type'))->options(['monthly' => __('Monthly'), 'non_monthly' => __('Non-monthly')])->default('monthly')->required()->native(false),
                Select::make('period_month')->label(__('Period month'))->options(Months::options())->required()->native(false)->default(today()->month),
                TextInput::make('period_year')->label(__('Period year'))->numeric()->required()->minValue(2000)->maxValue(2100)->default(today()->year),
                NumberFields::make(TransactionType::PayrollEntry, 'Entry No.'),
                DatePicker::make('trans_date')->label(__('Date'))->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                DatePicker::make('due_date')->label(__('Due date'))->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                Placeholder::make('totals')->label(__('Net to pay'))->content(fn (Get $get): string => Format::rupiah(LineTotals::sum($get('lines'), 'net_amount'))),
            ]),
            Tabs::make('payroll')->tabs([
                Tab::make(__('Employees'))->schema([
                    Action::make('pullEmployees')
                        ->label(__('Pull every active employee'))
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('gray')
                        ->visible(fn (Get $get): bool => array_filter((array) $get('lines'), fn ($line) => ! empty($line['employee_id'])) === [])
                        ->action(function (Set $set): void {
                            $rows = Employee::query()->where('is_active', true)->orderBy('name')->get()
                                ->map(fn (Employee $employee): array => [
                                    'employee_id' => $employee->id,
                                    'salary_component_id' => null,
                                    'gross_amount' => 0,
                                    'income_tax' => 0,
                                    'net_amount' => 0,
                                    'memo' => null,
                                ])
                                ->all();
                            $set('lines', DocumentPages::keyedRows($rows));
                            Notification::make()->title(__(':count employee(s) pulled', ['count' => count($rows)]))->success()->send();
                        }),
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make(__('Employee')),
                            TableColumn::make(__('Component')),
                            TableColumn::make(__('Gross pay'))->alignment(Alignment::End),
                            TableColumn::make(__('Income tax'))->alignment(Alignment::End),
                            TableColumn::make(__('Net pay'))->alignment(Alignment::End),
                        ])
                        ->schema([
                            Select::make('employee_id')->options(fn () => Employee::query()->orderBy('name')->pluck('name', 'id')->all())->searchable()->required()->native(false),
                            Select::make('salary_component_id')->options(fn () => SalaryComponent::query()->active()->orderBy('name')->pluck('name', 'id')->all())->native(false)->nullable()->placeholder(__('Basic salary account')),
                            PricedDocumentForm::money('gross_amount', 'Gross pay')->required()->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recomputeNet($set, $get)),
                            PricedDocumentForm::money('income_tax', 'Income tax')->live(onBlur: true)
                                ->afterStateUpdated(fn (Set $set, Get $get) => self::recomputeNet($set, $get)),
                            PricedDocumentForm::money('net_amount', 'Net pay')->required(),
                        ])
                        ->minItems(1)
                        ->defaultItems(1)
                        ->live()
                        ->addActionLabel('Add employee'),
                ]),
                Tab::make(__('Other info'))->schema([
                    Select::make('expense_payable_account_id')->label(__('Payable account'))->options(fn () => self::payableOptions())->searchable()->required()->native(false)
                        ->default(fn () => Account::query()->where('no', '2230')->value('id')),
                    Select::make('tax_payable_account_id')->label(__('Income tax payable'))->options(fn () => self::payableOptions())->searchable()->native(false)
                        ->default(fn () => Account::query()->where('no', '2220')->value('id')),
                    BranchFields::select(__('Branch'), defaulted: false),
                    Textarea::make('description')->label(__('Notes'))->rows(2)->maxLength(255),
                ])->columns(2),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label(__('Number'))->searchable()->sortable()->fontFamily('mono'),
                Tanggal::make('trans_date')->label(__('Date')),
                Tanggal::make('due_date')->label(__('Due')),
                TextColumn::make('period')->label(__('Period'))->state(fn (PayrollEntry $record): string => $record->periodLabel()),
                TextColumn::make('payment_type')->label(__('Payment type'))->badge()->color('gray')
                    ->formatStateUsing(fn ($state): string => $state === 'non_monthly' ? 'Non-monthly' : 'Monthly'),
                TextColumn::make('payment_status')->label(__('Status'))->badge()
                    ->formatStateUsing(fn ($state): string => ucfirst((string) $state))
                    ->color(fn ($state): string => match ($state) {
                        'paid' => 'success',
                        'partial' => 'info',
                        default => 'warning',
                    }),
                TextColumn::make('description')->label(__('Notes'))->limit(40)->placeholder('—'),
                Rupiah::make('total')->label(__('Net pay')),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('period_month')->label(__('Period month'))->options(Months::options()),
                SelectFilter::make('payment_status')->label(__('Status'))->options(['unpaid' => __('Unpaid'), 'partial' => __('Partial'), 'paid' => __('Paid')]),
            ])
            ->recordActions([EditAction::make(), PayAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollEntries::route('/'),
            'create' => CreatePayrollEntry::route('/create'),
            'edit' => EditPayrollEntry::route('/{record}/edit'),
        ];
    }
}
