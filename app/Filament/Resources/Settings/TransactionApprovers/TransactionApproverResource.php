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

/** Transaction Approvers: which documents need approval, from whom, by whom, under which rule. Sales orders consult these when the Sales Order Approval rule is on. */
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
            Select::make('transaction_type')->label(__('Document'))->options(self::documentOptions())->required()->searchable()->native(false),
            PricedDocumentForm::money('min_amount', 'From amount')->helperText(__('Documents below this amount need no approval.')),
            Select::make('rule')->label(__('Condition'))->options(TransactionApprover::rules())->default('any_one')->required()->native(false),
            Select::make('branch_id')->label(__('Branch'))->relationship('branch', 'name')->preload()->placeholder(__('Every branch'))->nullable()->native(false),
            Fieldset::make(__('Who needs approval'))->columns(1)->schema([
                Select::make('requesters')->label(__('Users'))->multiple()->relationship('requesters', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))
                    ->preload()->searchable()->helperText(__('Nobody chosen means every user.')),
            ]),
            Fieldset::make(__('Who approves'))->columns(1)->schema([
                Select::make('groups')->label(__('Access groups'))->multiple()->relationship('groups', 'name', fn ($query) => $query->orderBy('name'))->preload(),
                Select::make('approvers')->label(__('Users'))->multiple()->relationship('approvers', 'name', fn ($query) => $query->where('is_active', true)->orderBy('name'))->preload()->searchable(),
            ]),
            self::activeToggle(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['groups', 'approvers', 'requesters', 'branch']))
            ->columns([
                TextColumn::make('transaction_type')->label(__('Document'))->sortable()
                    ->formatStateUsing(fn (string $state): string => TransactionType::tryFrom($state)?->getLabel() ?? $state),
                Rupiah::make('min_amount')->label(__('From amount')),
                TextColumn::make('approved_by')->label(__('Approved by'))->limit(60)
                    ->state(fn ($record): string => $record->groups->pluck('name')->merge($record->approvers->pluck('name'))->join(', ')),
                TextColumn::make('requested_by')->label(__('Requested by'))->limit(60)
                    ->state(fn ($record): string => $record->requesters->isEmpty() ? 'Everyone' : $record->requesters->pluck('name')->join(', ')),
                TextColumn::make('branch.name')->label(__('Branch'))->placeholder(__('Every branch')),
                TextColumn::make('rule')->label(__('Condition'))
                    ->formatStateUsing(fn (string $state): string => TransactionApprover::rules()[$state] ?? $state),
                self::activeColumn(),
            ])
            ->defaultSort('transaction_type')
            ->filters([
                self::activeFilter(),
                SelectFilter::make('transaction_type')->label(__('Document'))->options(self::documentOptions()),
                SelectFilter::make('branch_id')->label(__('Branch'))->relationship('branch', 'name'),
            ])
            ->recordActions([EditAction::make()->slideOver(), DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => ManageTransactionApprovers::route('/')];
    }
}
