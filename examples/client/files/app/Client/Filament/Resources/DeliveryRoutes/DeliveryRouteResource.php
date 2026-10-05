<?php

declare(strict_types=1);

namespace App\Client\Filament\Resources\DeliveryRoutes;

use App\Client\Filament\Resources\DeliveryRoutes\Pages\ManageDeliveryRoutes;
use App\Client\Models\DeliveryRoute;
use App\Client\Screens\ClientScreen;
use App\Filament\Support\MasterResource;
use App\Models\Company\Employee;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Delivery Routes: a client screen built like the template's own masters, guarded by its own rights. */
class DeliveryRouteResource extends MasterResource
{
    protected static ?string $model = DeliveryRoute::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static ?string $modelLabel = 'Delivery route';

    public static function menuKey(): ClientScreen
    {
        return ClientScreen::DeliveryRoutes;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('fields.name'))->required()->maxLength(100)->unique(ignoreRecord: true),
            TextInput::make('area')->label(__('Area'))->maxLength(100),
            Select::make('driver_id')->label(__('Usual driver'))->options(fn () => Employee::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))->searchable()->native(false),
            Textarea::make('notes')->label(__('fields.memo'))->rows(2),
            self::activeToggle(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('fields.name'))->searchable()->sortable(),
                TextColumn::make('area')->label(__('Area'))->placeholder('—'),
                TextColumn::make('driver.name')->label(__('Usual driver'))->placeholder('—'),
                self::activeColumn(),
            ])
            ->defaultSort('name')
            ->filters([self::activeFilter()])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageDeliveryRoutes::route('/')];
    }
}
