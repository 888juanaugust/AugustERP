<?php

namespace Database\Seeders;

use App\Domain\Access\Hak;
use App\Domain\Access\HakKhusus;
use App\Domain\Access\MenuKey;
use App\Filament\Modul;
use App\Models\Settings\AccessGroup;
use Illuminate\Database\Seeder;

/**
 * The seven groups the business runs with. Each gets rights per screen; the
 * owner adjusts them on the Access Groups screen. "Administrator" is a group
 * too, for operators who need everything without the administrator account type.
 */
class AccessGroupSeeder extends Seeder
{
    private const ALL = ['view', 'create', 'update', 'delete', 'print'];

    private const READ = ['view', 'print'];

    private const WORK = ['view', 'create', 'update', 'print'];

    public function run(): void
    {
        $byModule = fn (Modul ...$moduls) => array_values(array_filter(
            MenuKey::cases(),
            fn (MenuKey $k) => $k->isReplicated() && in_array($k->modul(), $moduls, true),
        ));

        $groups = [
            'Administrator' => [
                'rights' => $this->grant(MenuKey::cases(), self::ALL),
                'special' => HakKhusus::cases(),
            ],
            'Owner' => [
                'rights' => $this->grant(MenuKey::cases(), self::ALL),
                'special' => HakKhusus::cases(),
            ],
            'Finance' => [
                'rights' => $this->grant($byModule(Modul::GeneralLedger, Modul::CashBank, Modul::Tax, Modul::Reports, Modul::FixedAssets), self::ALL)
                    + $this->grant(array_values(array_filter($byModule(Modul::Sales, Modul::Purchasing), fn (MenuKey $k) => ! in_array($k, [MenuKey::PriceAndDiscountAdjustments, MenuKey::PriceCategories], true))), self::WORK)
                    + $this->grant([MenuKey::Currencies, MenuKey::TaxCodes, MenuKey::PaymentTerms, MenuKey::Employees, MenuKey::SalaryComponents, MenuKey::MonthEndProcess, MenuKey::RecurringTransactions, MenuKey::MemorizedTransactions, MenuKey::Contacts, MenuKey::Calendar, MenuKey::ActivityLog], self::ALL)
                    + $this->grant([MenuKey::Customers, MenuKey::Vendors, MenuKey::ItemsAndServices, MenuKey::StockByWarehouse], self::READ),
                'special' => [HakKhusus::SeeCreditData, HakKhusus::OverrideCreditLimit, HakKhusus::BackdateTransactions, HakKhusus::ApproveTransactions, HakKhusus::ExportData, HakKhusus::SeeCost],
            ],
            'Sales' => [
                'rights' => $this->grant([MenuKey::SalesQuotations, MenuKey::SalesOrders, MenuKey::SalesReturns, MenuKey::CheckIns], self::WORK)
                    + $this->grant([MenuKey::Customers, MenuKey::SalesInvoices, MenuKey::DeliveryOrders, MenuKey::SalesReceipts, MenuKey::ItemsAndServices, MenuKey::StockByWarehouse, MenuKey::OrderFulfilment, MenuKey::PriceCategories, MenuKey::SalesTargets, MenuKey::SalesmanCommissions, MenuKey::Calendar, MenuKey::Contacts], self::READ),
                'special' => [],
            ],
            'Marketing' => [
                'rights' => $this->grant($byModule(Modul::Sales), self::READ)
                    + $this->grant([MenuKey::SalesOrders, MenuKey::SalesQuotations, MenuKey::Customers, MenuKey::CustomerCategories, MenuKey::PriceCategories, MenuKey::PriceAndDiscountAdjustments, MenuKey::SalesTargets], self::ALL)
                    + $this->grant([MenuKey::ItemsAndServices, MenuKey::StockByWarehouse, MenuKey::OrderFulfilment, MenuKey::Calendar, MenuKey::Contacts], self::READ),
                'special' => [HakKhusus::SeeCreditData, HakKhusus::ApproveTransactions],
            ],
            'Inventory' => [
                'rights' => $this->grant($byModule(Modul::Inventory), self::ALL)
                    + $this->grant([MenuKey::PurchaseRequisitions, MenuKey::GoodsReceipts, MenuKey::PurchaseReturns, MenuKey::VendorPrices, MenuKey::SalesReturns], self::WORK)
                    + $this->grant([MenuKey::PurchaseOrders, MenuKey::Vendors, MenuKey::SalesOrders, MenuKey::DeliveryOrders], self::READ),
                'special' => [HakKhusus::SeeCost],
            ],
            'Warehouse' => [
                'rights' => $this->grant([MenuKey::DeliveryOrders, MenuKey::GoodsReceipts, MenuKey::ItemTransfers, MenuKey::StockOpnameResults], self::WORK)
                    + $this->grant([MenuKey::SalesOrders, MenuKey::PurchaseOrders, MenuKey::OrderFulfilment, MenuKey::StockByWarehouse, MenuKey::MinimumStock, MenuKey::ItemsAndServices, MenuKey::Warehouses, MenuKey::StockOpnameOrders], self::READ),
                'special' => [],
            ],
        ];

        foreach ($groups as $name => $spec) {
            $group = AccessGroup::query()->firstOrCreate(['name' => $name], ['restriction_type' => 'preferences']);
            if ($group->rights()->exists()) {
                continue; // already shaped by the owner
            }
            $group->syncRights($spec['rights']);
            $group->syncSpecialRights(array_map(fn (HakKhusus $r) => $r->value, $spec['special']));
        }
    }

    /**
     * @param  list<MenuKey>  $keys
     * @param  list<string>  $rights
     * @return array<string, list<string>>
     */
    private function grant(array $keys, array $rights): array
    {
        $out = [];
        foreach ($keys as $key) {
            if ($key->isReplicated()) {
                $out[$key->value] = array_values(array_filter($rights, fn (string $r) => Hak::tryFrom($r) !== null));
            }
        }

        return $out;
    }
}
