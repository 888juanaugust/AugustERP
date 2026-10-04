<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Users;

use App\Domain\Access\MenuKey;
use App\Filament\Resources\Settings\Users\Pages\CreateUser;
use App\Filament\Resources\Settings\Users\Pages\EditUser;
use App\Filament\Resources\Settings\Users\Pages\ListUsers;
use App\Filament\Support\ErpResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

/** The Users screen: staff accounts, their access type, groups and branches. */
class UserResource extends ErpResource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $modelLabel = 'User';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Users;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->columns(2)
                ->schema([
                    TextInput::make('name')->label('Name')->required()->maxLength(100),
                    TextInput::make('email')->label('Email')->email()->required()->maxLength(150)->unique(ignoreRecord: true),
                    TextInput::make('phone')->label('Mobile number')->tel()->maxLength(30),
                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation) => $operation === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->minLength(8)
                        ->helperText(fn (string $operation) => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                    Radio::make('access_type')
                        ->label('Access type')
                        ->options([
                            'operator' => 'Operator: limited to the rights of their access groups',
                            'administrator' => 'Administrator: every screen, every right',
                        ])
                        ->default('operator')
                        ->required(),
                    Toggle::make('is_active')->label(__('fields.is_active'))->default(true)->inline(false),
                ]),
            Tabs::make('access')->tabs([
                Tab::make('Access groups')->schema([
                    CheckboxList::make('accessGroups')
                        ->label('Groups')
                        ->relationship('accessGroups', 'name', fn ($query) => $query->orderBy('name'))
                        ->columns(3),
                ]),
                Tab::make('Branches')->schema([
                    CheckboxList::make('branches')
                        ->label('May work in these branches')
                        ->helperText('A branch open to all users needs no entry here.')
                        ->relationship('branches', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                        ->columns(3),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->searchable()->sortable(),
                TextColumn::make('phone')->label('Mobile number')->placeholder('—'),
                TextColumn::make('email')->label('Email')->searchable(),
                IconColumn::make('two_factor')->label('2FA')->state(fn (User $record) => $record->hasTwoFactor())->boolean(),
                TextColumn::make('access_type')
                    ->label('Access type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => $state === 'administrator' ? 'primary' : 'gray'),
                IconColumn::make('is_active')->label(__('fields.is_active'))->boolean(),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('access_type')->label('Access type')->options(['operator' => 'Operator', 'administrator' => 'Administrator']),
                TernaryFilter::make('is_active')->label('Active'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (User $record) => $record->is(auth()->user())),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
