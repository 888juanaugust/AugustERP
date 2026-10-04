<?php

namespace Database\Seeders;

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
            FixedAssetSeeder::class,
        ]);
    }
}
