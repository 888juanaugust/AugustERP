<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Access\MenuKey;
use Filament\Pages\Page;
use Filament\Panel;
use UnitEnum;

/** A standalone screen (settings, inquiry, report): placed and named like a resource. */
abstract class ErpPage extends Page
{
    abstract public static function menuKey(): MenuKey;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::menuKey()->modul();
    }

    public static function getNavigationSort(): ?int
    {
        return static::menuKey()->sort();
    }

    public static function getNavigationLabel(): string
    {
        return static::menuKey()->label();
    }

    public function getTitle(): string
    {
        return static::menuKey()->label();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return static::menuKey()->slug();
    }
}
