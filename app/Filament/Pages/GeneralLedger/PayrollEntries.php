<?php

declare(strict_types=1);

namespace App\Filament\Pages\GeneralLedger;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class PayrollEntries extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::PayrollEntries;
    }

    protected function explanation(): string
    {
        return 'Payroll journals per employee with gross pay, income tax and net pay follow the Cash & Bank phase.';
    }
}
