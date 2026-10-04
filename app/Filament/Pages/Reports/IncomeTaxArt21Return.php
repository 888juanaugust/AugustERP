<?php

declare(strict_types=1);

namespace App\Filament\Pages\Reports;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class IncomeTaxArt21Return extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::IncomeTaxArt21Return;
    }

    protected function explanation(): string
    {
        return 'The Article 21 income tax return (form 1721) needs payroll, which is not built yet; the payroll phase brings it.';
    }
}
