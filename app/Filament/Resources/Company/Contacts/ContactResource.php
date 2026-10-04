<?php

declare(strict_types=1);

namespace App\Filament\Resources\Company\Contacts;

use App\Domain\Access\MenuKey;
use App\Domain\Shared\Enums\ContactType;
use App\Filament\Resources\Company\Contacts\Pages\ManageContacts;
use App\Filament\Support\MasterResource;
use App\Models\Company\Contact;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactResource extends MasterResource
{
    protected static ?string $model = Contact::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $modelLabel = 'Contact';

    public static function menuKey(): MenuKey
    {
        return MenuKey::Contacts;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Full name')->required()->maxLength(150),
            Select::make('contact_type')->label('Type')->options(ContactType::class)->default(ContactType::Other)->required()->native(false),
            TextInput::make('company')->label('Company')->maxLength(150),
            TextInput::make('position')->label('Position')->maxLength(100),
            TextInput::make('email')->label('Email')->email()->maxLength(150),
            TextInput::make('mobile_phone')->label('Mobile')->tel()->maxLength(30),
            TextInput::make('work_phone')->label('Work phone')->tel()->maxLength(30),
            Textarea::make('notes')->label(__('fields.memo'))->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Full name')->searchable()->sortable(),
                TextColumn::make('contact_type')->label('Type')->badge()->color('gray'),
                TextColumn::make('company')->label('Company')->searchable()->placeholder('—'),
                TextColumn::make('email')->label('Email')->placeholder('—'),
                TextColumn::make('mobile_phone')->label('Mobile')->placeholder('—'),
            ])
            ->defaultSort('name')
            ->filters([SelectFilter::make('contact_type')->label('Type')->options(ContactType::class)])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageContacts::route('/')];
    }
}
