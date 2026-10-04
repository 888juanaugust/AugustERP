<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class WithholdingSlips extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::WithholdingSlips;
    }

    protected function explanation(): string
    {
        return 'Withholding slips (1721-A1/A2) follow the payroll phase.';
    }
}
