<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\TransactionApprovers;

use App\Domain\Access\MenuKey;
use App\Domain\Numbering\TransactionType;
use App\Filament\Resources\Settings\TransactionApprovers\Pages\ManageTransactionApprovers;
use App\Filament\Support\Columns\Rupiah;
use App\Filament\Support\MasterResource;
use App\Filament\Support\PricedDocumentForm;
use App\Models\Company\TransactionApprover;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Transaction Approvers: which documents need approval, from whom, by whom, under which rule. The marketing approval of sales orders is the seeded rule. */
class TransactionApproverResource extends MasterResource
{
    protected static ?string $model = TransactionApprover::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $modelLabel = 'Approval rule';

    protected static ?string $recordTitleAttribute = 'transaction_type';

    public static function menuKey(): MenuKey
    {
        return MenuKey::TransactionApprovers;
    }

    /** @return array<string, string> transaction type value → label */
    public static function documentOptions(): array
    {
        return collect(TransactionType::cases())->mapWithKeys(fn (TransactionType $type) => [$type->value => $type->getLabel()])->sort()->all();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('transaction_type')->label('Document')->options(self::documentOptions())->required()->searchable()->native(false),
            PricedDocumentForm::money('min_amount', 'From amount')->helperText('Documents below this amount need no approval.'),
            Select::make('rule')->label('Condition')->options(TransactionApprover::RULES)->default('any_one')->required()->native(false),
            Select::make('branch_id')->label('Branch')->relationship('branch', 'name')->preload()->placeholder('Every branch')->nullable()->native(false),
            Fieldset::make('Who needs approval')->columns(1)->schema([
                Select::make('requesters')->label('Users')->multiple()->relationship('requesters', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                    ->preload()->searchable()->helperText('Nobody chosen means every user.'),
            ]),
            Fieldset::make('Who approves')->columns(1)->schema([
                Select::make('groups')->label('Access groups')->multiple()->relationship('groups', 'name', fn ($query) => $query->orderBy('name'))->preload(),
                Select::make('approvers')->label('Users')->multiple()->relationship('approvers', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))->preload()->searchable(),
            ]),
            self::activeToggle(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['groups', 'approvers', 'requesters', 'branch']))
            ->columns([
                TextColumn::make('transaction_type')->label('Document')->sortable()
                    ->formatStateUsing(fn (string $state): string => TransactionType::tryFrom($state)?->getLabel() ?? $state),
                Rupiah::make('min_amount')->label('From amount'),
                TextColumn::make('approved_by')->label('Approved by')->limit(60)
                    ->state(fn ($record): string => $record->groups->pluck('name')->merge($record->approvers->pluck('name'))->join(', ')),
                TextColumn::make('requested_by')->label('Requested by')->limit(60)
                    ->state(fn ($record): string => $record->requesters->isEmpty() ? 'Everyone' : $record->requesters->pluck('name')->join(', ')),
                TextColumn::make('branch.name')->label('Branch')->placeholder('Every branch'),
                TextColumn::make('rule')->label('Condition')
                    ->formatStateUsing(fn (string $state): string => TransactionApprover::RULES[$state] ?? $state),
                self::activeColumn(),
            ])
            ->defaultSort('transaction_type')
            ->filters([
                self::activeFilter(),
                SelectFilter::make('transaction_type')->label('Document')->options(self::documentOptions()),
                SelectFilter::make('branch_id')->label('Branch')->relationship('branch', 'name'),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTransactionApprovers::route('/')];
    }
}
