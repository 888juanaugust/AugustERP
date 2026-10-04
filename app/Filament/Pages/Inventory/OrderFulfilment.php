<?php

declare(strict_types=1);

namespace App\Filament\Pages\Inventory;

use App\Domain\Access\MenuKey;
use App\Domain\Inventory\StockQuery;
use App\Domain\Shared\Format;
use App\Filament\Resources\Sales\Deliveries\DeliveryResource;
use App\Filament\Support\ErpPage;
use App\Models\Sales\SalesOrder;
use Brick\Math\BigDecimal;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

/** Order Fulfilment: approved sales orders not yet delivered, what shipped and what stock can ship now. */
class OrderFulfilment extends ErpPage implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.inventory.order-fulfilment';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function menuKey(): MenuKey
    {
        return MenuKey::OrderFulfilment;
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn () => $this->rows())
            ->columns([
                TextColumn::make('customer')->label(__('fields.customer'))->weight('medium'),
                TextColumn::make('number')->label(__('Order No.'))->fontFamily('mono'),
                TextColumn::make('trans_date')->label(__('fields.trans_date')),
                TextColumn::make('ship_date')->label(__('fields.ship_date')),
                TextColumn::make('delivered')->label(__('Delivered'))->alignEnd(),
                TextColumn::make('deliverable')->label(__('Can ship now'))->alignEnd()->color(fn ($state) => str_starts_with((string) $state, '0') ? 'danger' : 'success'),
            ])
            ->recordActions([
                Action::make('deliver')->label(__('Deliver'))->icon('heroicon-m-truck')
                    ->visible(fn () => DeliveryResource::canCreate())
                    ->url(fn (array $record) => DeliveryResource::getUrl('create', ['source' => $record['id']])),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('Every approved order has shipped'))
            ->emptyStateDescription(__('Approved orders with lines still to deliver appear here.'));
    }

    /** @return Collection<int, array<string, mixed>> */
    private function rows(): Collection
    {
        $onHand = StockQuery::onHandMap();

        return SalesOrder::query()->with(['customer', 'lines'])
            ->where('approval_status', SalesOrder::APPROVED)
            ->whereIn('status', ['pending', 'partial'])
            ->orderBy('ship_date')->orderBy('trans_date')
            ->get()
            ->map(function (SalesOrder $order) use ($onHand) {
                $ordered = BigDecimal::zero();
                $processed = BigDecimal::zero();
                $deliverable = BigDecimal::zero();
                foreach ($order->lines as $line) {
                    $ordered = $ordered->plus((string) $line->base_quantity);
                    $processed = $processed->plus((string) $line->processed_quantity);
                    $remaining = BigDecimal::of($line->remainingQuantity());
                    $stock = BigDecimal::of($onHand[$line->item_id] ?? '0');
                    $deliverable = $deliverable->plus($remaining->isLessThan($stock) ? $remaining : ($stock->isNegative() ? BigDecimal::zero() : $stock));
                }

                return [
                    'id' => $order->id,
                    'customer' => $order->customer->name,
                    'number' => $order->number,
                    'trans_date' => Format::date($order->trans_date),
                    'ship_date' => Format::date($order->ship_date),
                    'delivered' => Format::quantity((string) $processed).' / '.Format::quantity((string) $ordered),
                    'deliverable' => Format::quantity((string) $deliverable).' of '.Format::quantity((string) $ordered->minus($processed)),
                ];
            })->values();
    }
}
