<?php

declare(strict_types=1);

namespace App\Filament\Pages\Inventory;

use App\Domain\Access\MenuKey;
use App\Domain\Inventory\StockQuery;
use App\Domain\Shared\Format;
use App\Filament\Support\ErpPage;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemCost;
use App\Models\Inventory\Warehouse;
use App\Models\Purchasing\Vendor;
use Brick\Math\BigDecimal;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

/** Minimum Stock: the items at or below their minimum, by vendor and warehouse; the reorder worklist. */
class MinimumStock extends ErpPage implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.inventory.minimum-stock';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    public ?array $filters = [];

    public static function menuKey(): MenuKey
    {
        return MenuKey::MinimumStock;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(3)->schema([
                Select::make('vendor_id')->label(__('Vendor'))->options(fn () => Vendor::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))->searchable()->live()->native(false),
                Select::make('warehouse_id')->label(__('Warehouse'))->options(fn () => Warehouse::query()->where('is_system', false)->where('is_active', true)->orderBy('name')->pluck('name', 'id'))->live()->native(false)->placeholder(__('All warehouses')),
                TextInput::make('search')->label(__('Item name or code'))->live(onBlur: true),
            ]),
        ])->statePath('filters');
    }

    public function updatedFilters(): void
    {
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->rows())
            ->columns([
                TextColumn::make('vendor')->label(__('Vendor')),
                TextColumn::make('name')->label(__('Item name'))->weight('medium'),
                TextColumn::make('number')->label(__('Item code'))->fontFamily('mono'),
                TextColumn::make('unit')->label(__('Unit')),
                TextColumn::make('on_hand')->label(__('Available'))->alignEnd(),
                TextColumn::make('on_order')->label(__('On order'))->alignEnd(),
                TextColumn::make('requested')->label(__('Requested'))->alignEnd(),
                TextColumn::make('min_stock')->label(__('Minimum'))->alignEnd(),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('Nothing below its minimum'))
            ->emptyStateDescription(__('Items whose stock is at or under the minimum set on the item appear here.'));
    }

    /** @return Collection<int, array<string, mixed>> */
    private function rows(): Collection
    {
        $warehouseId = $this->filters['warehouse_id'] ?? null;
        $search = trim((string) ($this->filters['search'] ?? ''));
        $onHand = $warehouseId
            ? ItemCost::query()->where('warehouse_id', $warehouseId)->pluck('qty_on_hand', 'item_id')->map(fn ($v) => (string) $v)->all()
            : StockQuery::onHandMap();

        return Item::query()->active()
            ->with(['unit1', 'preferredVendor'])
            ->where('item_type', 'inventory')
            ->where('min_stock', '>', 0)
            ->when($this->filters['vendor_id'] ?? null, fn ($q, $v) => $q->where('preferred_vendor_id', $v))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('name', 'ilike', "%{$search}%")->orWhere('number', 'ilike', "%{$search}%")))
            ->orderBy('number')
            ->get()
            ->filter(fn (Item $item) => BigDecimal::of($onHand[$item->id] ?? '0')->isLessThanOrEqualTo((string) $item->min_stock))
            ->map(fn (Item $item) => [
                'id' => $item->id,
                'vendor' => $item->preferredVendor?->name ?? '—',
                'name' => $item->name,
                'number' => $item->number,
                'unit' => $item->unit1->name,
                'on_hand' => Format::quantity($onHand[$item->id] ?? '0'),
                'on_order' => Format::quantity(0),
                'requested' => Format::quantity(0),
                'min_stock' => Format::quantity((string) $item->min_stock),
            ])->values();
    }
}
