<?php

declare(strict_types=1);

namespace App\Filament\Resources\Company\Currencies;

use App\Domain\Access\MenuKey;
use App\Filament\Resources\Company\Currencies\Pages\ManageCurrencies;
use App\Filament\Support\MasterResource;
use App\Models\Company\Currency;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrencyResource extends MasterResource
{
    protected static ?string $model = Currency::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCurrencyDollar;

    protected static ?string $modelLabel = 'Currency';

    protected static ?string $recordTitleAttribute = 'code';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Currencies;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')->label(__('Code'))->required()->length(3)->alpha()->unique(ignoreRecord: true)->extraInputAttributes(['style' => 'text-transform: uppercase'])
                ->dehydrateStateUsing(fn (?string $state) => strtoupper((string) $state)),
            TextInput::make('symbol')->label(__('Symbol'))->required()->maxLength(10),
            TextInput::make('name')->label(__('Name'))->required()->maxLength(80),
            TextInput::make('country')->label(__('Country'))->maxLength(80),
            Toggle::make('is_base')->label(__('Base currency'))->helperText(__('Books are kept in the base currency.'))->inline(false),
            self::activeToggle()->inline(false),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('symbol')->label(__('Symbol')),
                TextColumn::make('code')->label(__('Code'))->searchable()->sortable(),
                TextColumn::make('name')->label(__('Country / Name'))->state(fn (Currency $r) => $r->country ? "{$r->country} · {$r->name}" : $r->name)->searchable(),
                IconColumn::make('is_base')->label(__('Base'))->boolean(),
                self::activeColumn(),
            ])
            ->defaultSort('code')
            ->filters([self::activeFilter()])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()->hidden(fn (Currency $r) => $r->is_base)]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageCurrencies::route('/')];
    }
}
