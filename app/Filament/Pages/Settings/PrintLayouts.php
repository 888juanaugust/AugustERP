<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class PrintLayouts extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::PrintLayouts;
    }

    protected function explanation(): string
    {
        return 'Documents print with the built-in layout until designable layouts land (roadmap phase 7).';
    }
}
