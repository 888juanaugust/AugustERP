<?php

declare(strict_types=1);

namespace App\Filament\Pages\Inventory;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class OrderFulfilment extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::OrderFulfilment;
    }

    protected function explanation(): string
    {
        return 'Sales orders not yet delivered, with what can ship from stock, arrive with the sales module.';
    }
}
