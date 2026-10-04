<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sales\SalesTargets;

use App\Domain\Access\MenuKey;
use App\Domain\Shared\Format;
use App\Filament\Resources\Sales\SalesTargets\Pages\ManageSalesTargets;
use App\Filament\Support\ErpResource;
use App\Filament\Support\LineItemFields;
use App\Filament\Support\PricedDocumentForm;
use App\Models\Company\Employee;
use App\Models\Sales\SalesTarget;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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

/** Sales Targets: quantities and values to reach per item, category, salesperson or month, for a period and branch. */
class SalesTargetResource extends ErpResource
{
    protected static ?string $model = SalesTarget::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $modelLabel = 'Sales target';

    public static function menuKey(): MenuKey
    {
        return MenuKey::SalesTargets;
    }

    public static function form(Schema $schema): Schema
    {
        $types = ['per_item' => 'Per item', 'per_category' => 'Per item category', 'per_salesman' => 'Per salesperson', 'per_month' => 'Per month'];

        return $schema->components([
            Section::make()->columns(3)->schema([
                TextInput::make('name')->label('Target name')->required()->maxLength(100),
                Select::make('target_type')->label('Target type')->options($types)->default('per_salesman')->required()->native(false)->live(),
                Select::make('branch_id')->label('Branch sales')->relationship('branch', 'name')->preload()->native(false),
                DatePicker::make('from_date')->label('From')->native(false)->displayFormat(Format::DATE_INPUT)->default(fn () => today()->startOfYear()),
                DatePicker::make('to_date')->label('Until')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(fn () => today()->endOfYear()),
            ]),
            Tabs::make('target')->tabs([
                Tab::make('Targets')->schema([
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([TableColumn::make('For'), TableColumn::make('Quantity')->alignment(Alignment::End), TableColumn::make('Value')->alignment(Alignment::End)])
                        ->schema([
                            LineItemFields::item()->visible(fn (Get $get) => $get('../../target_type') === 'per_item')->required(false),
                            Select::make('item_category_id')->relationship('itemCategory', 'name')->native(false)->visible(fn (Get $get) => $get('../../target_type') === 'per_category'),
                            Select::make('salesman_id')->options(fn () => Employee::query()->salesmen()->orderBy('name')->pluck('name', 'id'))->native(false)->visible(fn (Get $get) => $get('../../target_type') === 'per_salesman'),
                            Select::make('month')->options(collect(range(1, 12))->mapWithKeys(fn (int $m) => [$m => date('F', mktime(0, 0, 0, $m, 1))])->all())->native(false)->visible(fn (Get $get) => $get('../../target_type') === 'per_month'),
                            TextInput::make('quantity')->numeric()->default(0),
                            PricedDocumentForm::money('value', 'Value'),
                        ])
                        ->minItems(1)->defaultItems(1)->addActionLabel('Add target'),
                ]),
                Tab::make('Notes')->schema([
                    Textarea::make('notes')->label(__('fields.memo'))->rows(3),
                    TextInput::make('analyst_name')->label('Analyst')->maxLength(100),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('branch'))
            ->columns([
                TextColumn::make('from_date')->label('From')->formatStateUsing(fn ($state) => Format::date($state))->placeholder('—'),
                TextColumn::make('to_date')->label('Until')->formatStateUsing(fn ($state) => Format::date($state))->sortable(),
                TextColumn::make('year')->label('Year')->state(fn (SalesTarget $r) => $r->to_date->year),
                TextColumn::make('name')->label('Target name')->searchable()->sortable()->weight('medium'),
                TextColumn::make('branch.name')->label(__('fields.branch'))->placeholder('All'),
                TextColumn::make('target_type')->label('Target type')->badge()->color('gray')->formatStateUsing(fn (string $state) => ucfirst(str_replace('_', ' ', $state))),
            ])
            ->defaultSort('to_date', 'desc')
            ->filters([SelectFilter::make('target_type')->label('Target type')->options(['per_item' => 'Per item', 'per_category' => 'Per item category', 'per_salesman' => 'Per salesperson', 'per_month' => 'Per month'])])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageSalesTargets::route('/'), 'create' => Pages\CreateSalesTarget::route('/create'), 'edit' => Pages\EditSalesTarget::route('/{record}/edit')];
    }
}
