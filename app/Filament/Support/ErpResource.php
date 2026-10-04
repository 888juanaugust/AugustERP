<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Access\MenuKey;
use Filament\Panel;
use Filament\Resources\Resource;
use UnitEnum;

/**
 * Every resource of the product: it is one screen of the reference system's
 * menu (its MenuKey), sits in that screen's module at that screen's position,
 * and is named in English from lang/en/menu.php. Access is decided by the
 * access matrix (phase 1), not by per-model policies.
 */
abstract class ErpResource extends Resource
{
    protected static bool $shouldCheckPolicyExistence = false;

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

    public static function getModelLabel(): string
    {
        return static::$modelLabel ?? static::menuKey()->label();
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel ?? static::menuKey()->label();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return static::menuKey()->slug();
    }

    public static function getRecordTitleAttribute(): ?string
    {
        return static::$recordTitleAttribute ?? 'name';
    }
}
