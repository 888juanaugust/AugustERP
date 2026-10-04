<?php

declare(strict_types=1);

namespace App\Filament\Pages\Sales;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class ECommerceLinks extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::ECommerceLinks;
    }

    protected function explanation(): string
    {
        return 'Marketplace links were an integration service of the original product and are not replicated; a sales order can be entered for any customer.';
    }
}
