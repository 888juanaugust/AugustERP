<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;

/**
 * A master-data screen: a list with the reference system's columns and its
 * "Non Aktif" filter, and a form. Small masters manage records in a slide-over;
 * the big ones (customers, vendors, items, employees, accounts) have full pages.
 */
abstract class MasterResource extends ErpResource
{
    protected static ?string $recordTitleAttribute = 'name';

    /** The reference system's "Non Aktif: Semua / Ya / Tidak" filter. */
    public static function activeFilter(): TernaryFilter
    {
        return TernaryFilter::make('is_active')
            ->label('Active')
            ->placeholder('All')
            ->trueLabel('Active only')
            ->falseLabel('Inactive only')
            ->default(true);
    }

    public static function activeColumn(): IconColumn
    {
        return IconColumn::make('is_active')->label(__('fields.is_active'))->boolean();
    }

    public static function activeToggle(): Toggle
    {
        return Toggle::make('is_active')->label(__('fields.is_active'))->default(true);
    }
}
