<?php

declare(strict_types=1);

namespace App\Filament\Pages\GeneralLedger;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class BudgetMonitor extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::BudgetMonitor;
    }

    protected function explanation(): string
    {
        return 'Budget against actuals per account arrives with Budgets, in the reporting phase.';
    }
}
