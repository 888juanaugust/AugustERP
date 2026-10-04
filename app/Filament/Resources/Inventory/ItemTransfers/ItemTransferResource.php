<?php

declare(strict_types=1);

namespace App\Filament\Resources\Inventory\ItemTransfers;

use App\Domain\Access\MenuKey;
use App\Domain\Inventory\TransferReceiver;
use App\Domain\Numbering\TransactionType;
use App\Domain\Shared\Format;
use App\Filament\Resources\Inventory\ItemTransfers\Pages\CreateItemTransfer;
use App\Filament\Resources\Inventory\ItemTransfers\Pages\EditItemTransfer;
use App\Filament\Resources\Inventory\ItemTransfers\Pages\ListItemTransfers;
use App\Filament\Support\Columns\Tanggal;
use App\Filament\Support\DocumentListFilters;
use App\Filament\Support\ErpResource;
use App\Filament\Support\LineItemFields;
use App\Filament\Support\NumberFields;
use App\Models\Company\Branch;
use App\Models\Inventory\ItemTransfer;
use App\Models\Inventory\Warehouse;
use Brick\Math\BigDecimal;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Item Transfers: a send puts goods in transit; "Receive" books them into the destination. */
class ItemTransferResource extends ErpResource
{
    protected static ?string $model = ItemTransfer::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static ?string $modelLabel = 'Item transfer';

    protected static ?string $recordTitleAttribute = 'number';

    public static function menuKey(): MenuKey
    {
        return MenuKey::ItemTransfers;
    }

    public static function form(Schema $schema): Schema
    {
        $warehouses = fn () => Warehouse::query()->visibleTo(auth()->user())->where('is_system', false)->where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return $schema->components([
            Section::make()
                ->columns(3)
                ->schema([
                    Select::make('item_transfer_type')->label('Process')->options(['send' => 'Send goods', 'receive' => 'Receive goods'])->default('send')->disabled()->dehydrated()->native(false),
                    Select::make('warehouse_id')->label('From warehouse')->options($warehouses)->required()->native(false)->default(fn () => Warehouse::default()?->id),
                    Select::make('reference_warehouse_id')->label('To warehouse')->options($warehouses)->required()->native(false)->different('warehouse_id'),
                    DatePicker::make('trans_date')->label(__('fields.trans_date'))->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                    NumberFields::make(TransactionType::ItemTransfer, 'Transfer No.'),
                    Select::make('branch_id')->label(__('fields.branch'))->relationship('branch', 'name')->preload()->native(false)
                        ->default(fn () => Branch::default()?->id),
                ]),
            Tabs::make('transfer')->tabs([
                Tab::make(__('fields.lines'))->schema([
                    Repeater::make('lines')
                        ->hiddenLabel()
                        ->relationship()
                        ->orderColumn('sort')
                        ->table([
                            TableColumn::make('Item'),
                            TableColumn::make('Quantity')->alignment(Alignment::End),
                            TableColumn::make('Unit'),
                            TableColumn::make('Memo'),
                        ])
                        ->schema([
                            LineItemFields::item(stockedOnly: true),
                            LineItemFields::quantity()->minValue(0.0001),
                            LineItemFields::unit(),
                            TextInput::make('memo')->maxLength(255),
                            LineItemFields::baseQuantity(),
                        ])
                        ->minItems(1)
                        ->defaultItems(1)
                        ->addActionLabel('Add line')
                        ->mutateRelationshipDataBeforeCreateUsing(fn (array $data) => LineItemFields::fillBaseQuantities([$data])[0])
                        ->mutateRelationshipDataBeforeSaveUsing(fn (array $data) => LineItemFields::fillBaseQuantities([$data])[0])
                        ->disabled(fn (?ItemTransfer $record) => $record !== null && ! $record->isSend()),
                ]),
                Tab::make(__('fields.other_info'))->schema([
                    Textarea::make('description')->label(__('fields.description'))->rows(3),
                ]),
            ]),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['warehouse', 'referenceWarehouse']))
            ->columns([
                TextColumn::make('number')->label('Number')->searchable()->sortable()->fontFamily('mono'),
                Tanggal::make('trans_date')->label(__('fields.trans_date')),
                TextColumn::make('item_transfer_type')->label('Process')->badge()->formatStateUsing(fn (string $state) => $state === 'send' ? 'Send' : 'Receive')->color(fn (string $state) => $state === 'send' ? 'info' : 'success'),
                TextColumn::make('referenceWarehouse.name')->label('To / from'),
                TextColumn::make('warehouse.name')->label('Warehouse'),
                TextColumn::make('description')->label(__('fields.description'))->limit(40)->placeholder('—'),
                TextColumn::make('status')->label('Delivery status')->badge()->formatStateUsing(fn (string $state) => __('status.fulfilment.'.$state))
                    ->color(fn (string $state) => match ($state) {
                        'processed' => 'success', 'partial' => 'warning', 'closed' => 'gray', default => 'info'
                    }),
            ])
            ->defaultSort('trans_date', 'desc')
            ->filters([
                DocumentListFilters::dateRange(),
                SelectFilter::make('item_transfer_type')->label('Process')->options(['send' => 'Send', 'receive' => 'Receive']),
                SelectFilter::make('status')->label('Delivery status')->options(['pending' => 'Pending', 'partial' => 'Partial', 'processed' => 'Processed']),
                SelectFilter::make('warehouse_id')->label('Warehouse')->relationship('warehouse', 'name'),
            ])
            ->recordActions([
                EditAction::make()->visible(fn (ItemTransfer $r) => $r->isSend()),
                self::receiveAction(),
            ]);
    }

    /** The reference system's "Terima Barang": receive what is still in transit from a send. */
    public static function receiveAction(): Action
    {
        return Action::make('receive')
            ->label('Receive')
            ->icon('heroicon-m-inbox-arrow-down')
            ->color('success')
            ->visible(fn (ItemTransfer $record) => $record->isSend() && in_array($record->status, ['pending', 'partial'], true) && static::canCreate())
            ->modalHeading(fn (ItemTransfer $record) => "Receive {$record->number} into {$record->referenceWarehouse->name}")
            ->schema(fn (ItemTransfer $record) => [
                DatePicker::make('trans_date')->label('Receipt date')->required()->native(false)->displayFormat(Format::DATE_INPUT)->default(today()),
                Repeater::make('quantities')
                    ->label('Quantities received')
                    ->table([TableColumn::make('Item'), TableColumn::make('Still in transit'), TableColumn::make('Receive now')])
                    ->schema([
                        TextInput::make('item')->disabled()->dehydrated(false),
                        TextInput::make('remaining')->disabled()->dehydrated(false),
                        TextInput::make('quantity')->numeric()->minValue(0)->required(),
                        Hidden::make('line_id'),
                    ])
                    ->default($record->load('lines.item')->remainingLines()->map(fn ($l) => [
                        'line_id' => $l->id,
                        'item' => "{$l->item->number} · {$l->item->name}",
                        'remaining' => Format::quantity((string) BigDecimal::of((string) $l->base_quantity)->minus((string) $l->processed_quantity)),
                        'quantity' => (string) BigDecimal::of((string) $l->base_quantity)->minus((string) $l->processed_quantity)->toScale(4),
                    ])->values()->all())
                    ->addable(false)->deletable(false)->reorderable(false),
                Textarea::make('description')->label(__('fields.description'))->rows(2),
            ])
            ->action(function (ItemTransfer $record, array $data): void {
                try {
                    $quantities = collect($data['quantities'] ?? [])->mapWithKeys(fn ($row) => [(int) $row['line_id'] => $row['quantity']])->all();
                    $receipt = app(TransferReceiver::class)->receive($record, $quantities, CarbonImmutable::parse($data['trans_date']), null, $data['description'] ?? null);
                    Notification::make()->title("Received as {$receipt->number}")->success()->send();
                } catch (\RuntimeException $e) {
                    Notification::make()->title('Cannot receive')->body($e->getMessage())->danger()->persistent()->send();
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListItemTransfers::route('/'),
            'create' => CreateItemTransfer::route('/create'),
            'edit' => EditItemTransfer::route('/{record}/edit'),
        ];
    }
}
