<?php

declare(strict_types=1);

namespace App\Client\Modules;

use App\Client\Models\DeliveryRoute;
use App\Client\Screens\ClientScreen;
use App\Client\Seeders\DeliveryRouteSeeder;
use App\Modules\BaseModule;

/** The client's delivery routes: the areas its drivers cover, named on deliveries by the dispatcher. Always on. */
final class DeliveryRoutesModule extends BaseModule
{
    public static function key(): string
    {
        return 'delivery-routes';
    }

    public static function menuKeys(): array
    {
        return [ClientScreen::DeliveryRoutes];
    }

    public static function morphMap(): array
    {
        return ['delivery_route' => DeliveryRoute::class];
    }

    public static function defaultSeeders(): array
    {
        return [DeliveryRouteSeeder::class];
    }
}
