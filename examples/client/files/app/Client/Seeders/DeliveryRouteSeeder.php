<?php

namespace App\Client\Seeders;

use App\Client\Models\DeliveryRoute;
use Illuminate\Database\Seeder;

/** The routes a new installation of this client starts with. */
class DeliveryRouteSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([['City centre', 'Central'], ['Industrial estate', 'East']] as [$name, $area]) {
            DeliveryRoute::query()->firstOrCreate(['name' => $name], ['area' => $area, 'is_active' => true]);
        }
    }
}
