<?php

declare(strict_types=1);

namespace App\Modules\Inventory;

use App\Domain\Access\MenuKey;
use App\Domain\Inventory\StockLedger;
use App\Models\Inventory\InventoryAdjustment;
use App\Models\Inventory\Item;
use App\Models\Inventory\ItemBrand;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\ItemTransfer;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\StockOpnameOrder;
use App\Models\Inventory\StockOpnameResult;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Modules\BaseModule;
use App\Modules\ModuleContext;

/** Inventory: items, warehouses, the stock ledger and its documents. Core. */
final class InventoryModule extends BaseModule
{
    public static function key(): string
    {
        return 'inventory';
    }

    public static function menuKeys(): array
    {
        return [
            MenuKey::ItemTransfers, MenuKey::InventoryAdjustments, MenuKey::StockOpnameOrders, MenuKey::StockOpnameResults, MenuKey::ItemsAndServices,
            MenuKey::Warehouses, MenuKey::Units, MenuKey::ItemCategories, MenuKey::ItemBrands, MenuKey::OrderFulfilment, MenuKey::StockByWarehouse, MenuKey::MinimumStock,
        ];
    }

    public static function morphMap(): array
    {
        return [
            'item' => Item::class,
            'item_category' => ItemCategory::class,
            'item_brand' => ItemBrand::class,
            'unit' => Unit::class,
            'warehouse' => Warehouse::class,
            'stock_movement' => StockMovement::class,
            'inventory_adjustment' => InventoryAdjustment::class,
            'item_transfer' => ItemTransfer::class,
            'stock_opname_order' => StockOpnameOrder::class,
            'stock_opname_result' => StockOpnameResult::class,
        ];
    }

    public static function boot(ModuleContext $context): void
    {
        // The stock ledger writes the movements every posting declares.
        $context->postings->extend(fn ($posting, $builder) => $context->app->make(StockLedger::class)->write($posting, $builder));
        $context->postings->onUnpost(fn ($posting) => $context->app->make(StockLedger::class)->unwrite($posting));
    }
}
