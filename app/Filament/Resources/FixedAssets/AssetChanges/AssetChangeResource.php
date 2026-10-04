<?php

declare(strict_types=1);

namespace App\Filament\Resources\FixedAssets\AssetChanges;

use App\Domain\Access\MenuKey;
use App\Domain\FixedAssets\DepreciationMethod;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Enums\AccountType;
use App\Domain\Shared\Format;
use App\Filament\Resources\FixedAssets\AssetChanges\Pages\CreateAssetChange;
use App\Filament\Resources\FixedAssets\AssetChanges\Pages\EditAssetChange;
use App\Filament\Resources\FixedAssets\AssetChanges\Pages\ListAssetChanges;
use App\Filament\Support\AssetFields;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\ErpResource;
use App\Filament\Support\LineTotals;
use App\Filament\Support\NumberFields;
use App\Filament\Support\PricedDocumentForm;
use App\Models\FixedAssets\AssetChange;
use App\Models\GeneralLedger\Account;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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

/** Asset Changes: new terms for an asset from the next depreciation on, and added costs or a revaluation posted to the asset account. */
class AssetChangeResource extends ErpResource
{
    protected static ?string $model = AssetChange::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static ?string $modelLabel = 'Asset change';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::AssetChanges;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                Select::make('change_type')->label('Kind of change')->options([AssetChange::DATA => 'Data', AssetChange::REVALUATION => 'Revaluation'])->default(AssetChange::DATA)->required()->native(false),
                AssetFields::select(),
                NumberFields::make(TransactionType::FixedAssetChange, 'Change No.'),
                DatePicker::make('trans_date')->label('Date')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                Select::make('new_depreciation_method')->label('New depreciation method')->options(DepreciationMethod::class)->native(false)->placeholder('Unchanged'),
                PricedDocumentForm::money('new_salvage_value', 'New salvage value')->default(null)->nullable(),
                TextInput::make('new_useful_life_months')->label('New useful life (months)')->numeric()->integer()->nullable(),
                Textarea::make('description')->label('What changed')->rows(2)->columnSpanFull(),
            ]),
            Tabs::make('change')->tabs([
                Tab::make('Expenditure')->schema([
                    Repeater::make('expenditures')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make('Account'),
                            TableColumn::make('Description'),
                            TableColumn::make('Amount')->alignment(Alignment::End),
                        ])
                        ->schema([
                            Select::make('account_id')->options(fn () => Account::options())->searchable()->required()->native(false),
                            TextInput::make('description')->maxLength(255),
                            PricedDocumentForm::money('amount', 'Amount')->required()->live(onBlur: true),
                        ])
                        ->defaultItems(0)->live()
                        ->addActionLabel('Add expenditure'),
                    Placeholder::make('amount_preview')->label('Added to the asset')->content(fn (Get $get) => Format::rupiah(LineTotals::sum($get('expenditures'), 'amount'))),
                ]),
                Tab::make('Other info')->schema([
                    Toggle::make('new_intangible')->label('Intangible asset')->default(null)->dehydrated(fn ($state) => $state !== null),
                    Select::make('branch_id')->label('Branch')->relationship('branch', 'name')->preload()->native(false),
                    Select::make('asset_account_id')->label('Asset account (new)')->options(fn () => Account::options(AccountType::FixedAsset, AccountType::OtherCurrentAsset))->searchable()->native(false)->placeholder('Unchanged'),
                    Toggle::make('new_fiscal')->label('Fiscal asset')->default(null)->dehydrated(fn ($state) => $state !== null),
                ])->columns(2),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['fixedAsset']))
            ->columns([
                TextColumn::make('number')->label('Number')->searchable()->sortable()->fontFamily('mono'),
                Tanggal::make('trans_date')->label('Date'),
                TextColumn::make('description')->label('Description')->limit(50)->placeholder('—'),
                TextColumn::make('fixedAsset.number')->label('Asset')->fontFamily('mono'),
                TextColumn::make('fixedAsset.name')->label('Asset name'),
                TextColumn::make('change_type')->label('Kind')->badge()->color('gray')->formatStateUsing(fn (string $state) => ucfirst($state)),
                Rupiah::make('amount')->label('Added'),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('change_type')->label('Kind of change')->options([AssetChange::DATA => 'Data', AssetChange::REVALUATION => 'Revaluation']),
            ])
            ->recordActions([EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssetChanges::route('/'),
            'create' => CreateAssetChange::route('/create'),
            'edit' => EditAssetChange::route('/{record}/edit'),
        ];
    }
}
