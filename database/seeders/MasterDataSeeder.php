<?php

namespace Database\Seeders;

use App\Models\GeneralLedger\Account;
use App\Models\Inventory\ItemBrand;
use App\Models\Inventory\ItemCategory;
use App\Models\Inventory\Unit;
use App\Models\Inventory\Warehouse;
use App\Models\Purchasing\VendorCategory;
use App\Models\Purchasing\VendorType;
use App\Models\Sales\CustomerCategory;
use App\Models\Sales\PriceCategory;
use Illuminate\Database\Seeder;

/** The masters a fresh company starts with: units, the brands and categories carried, one warehouse, default categories. */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['PCS', 'UM.0018'], ['SET', 'UM.0021'], ['CTN', 'UM.0005'], ['DOZEN', 'UM.0007']] as [$name, $tax]) {
            Unit::query()->firstOrCreate(['name' => $name], ['unit_tax_code' => $tax]);
        }

        foreach (['YUHOLI', 'OSBORN', 'ASTRO', 'STAVO', 'STAVIX', 'SERVO', 'BDAX'] as $brand) {
            ItemBrand::query()->firstOrCreate(['name' => $brand]);
        }

        $id = fn (string $no): ?int => Account::query()->where('no', $no)->value('id');
        foreach (['Hydraulic Parts', 'Suspension Parts', 'Electric Parts', 'Bearing Parts'] as $i => $category) {
            ItemCategory::query()->firstOrCreate(['name' => $category, 'parent_id' => null], [
                'is_default' => $i === 0,
                'inventory_account_id' => $id('1300'),
                'sales_account_id' => $id('4100'),
                'cogs_account_id' => $id('5100'),
                'sales_return_account_id' => $id('4200'),
                'purchase_return_account_id' => $id('1300'),
            ]);
        }

        CustomerCategory::query()->firstOrCreate(['name' => 'General', 'parent_id' => null], ['is_default' => true]);
        foreach (['Workshop', 'Parts store', 'Distributor'] as $name) {
            CustomerCategory::query()->firstOrCreate(['name' => $name, 'parent_id' => null]);
        }

        VendorCategory::query()->firstOrCreate(['name' => 'General', 'parent_id' => null], ['is_default' => true]);
        foreach (['Supplier', 'Service provider', 'Expedition', 'Other'] as $name) {
            VendorType::query()->firstOrCreate(['name' => $name]);
        }

        PriceCategory::query()->firstOrCreate(['name' => 'General'], ['is_default' => true, 'notes' => 'The price every customer gets unless given another level.']);

        Warehouse::query()->firstOrCreate(['name' => 'Main Warehouse'], [
            'is_default' => true,
            'used_all_user' => true,
            'is_active' => true,
        ]);
    }
}
