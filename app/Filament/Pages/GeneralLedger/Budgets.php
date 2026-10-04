<?php

declare(strict_types=1);

namespace App\Filament\Pages\GeneralLedger;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class Budgets extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::Budgets;
    }

    protected function explanation(): string
    {
        return 'Budgets per account and month, with the Budget Monitor and Budget Transfers, follow the reporting phase.';
    }
}
