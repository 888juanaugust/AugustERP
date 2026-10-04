<?php

namespace Database\Seeders;

use App\Models\Company\Fob;
use Illuminate\Database\Seeder;

class FobSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Destination', 'Shipping point'] as $name) {
            Fob::query()->firstOrCreate(['name' => $name]);
        }
    }
}
