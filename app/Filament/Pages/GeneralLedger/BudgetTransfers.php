<?php

declare(strict_types=1);

namespace App\Filament\Pages\GeneralLedger;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class BudgetTransfers extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::BudgetTransfers;
    }

    protected function explanation(): string
    {
        return 'Moving budget between accounts and months arrives with Budgets, in the reporting phase.';
    }
}
