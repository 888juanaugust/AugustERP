<?php

namespace Database\Seeders;

use App\Modules\ModuleRegistry;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            BranchSeeder::class,
            CurrencySeeder::class,
            BankSeeder::class,
            ChartOfAccountsSeeder::class,
            TaxCodeSeeder::class,
            PaymentTermSeeder::class,
            FobSeeder::class,
            DocumentSeriesSeeder::class,
            AccessGroupSeeder::class,
            MasterDataSeeder::class,
            CompanyExtrasSeeder::class,
        ]);

        // Each module that is switched on brings its own defaults.
        foreach (app(ModuleRegistry::class)->enabled() as $module) {
            $this->call($module::defaultSeeders());
        }
    }
}
