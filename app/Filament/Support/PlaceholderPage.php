<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * A screen of the reference system that exists in the menu but is built in a
 * later phase. Says so, instead of a dead link; ScreenRouteTest counts it as
 * not done.
 */
abstract class PlaceholderPage extends ErpPage
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    abstract protected function explanation(): string;

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make()
                ->heading(static::menuKey()->label().' is planned, not yet built')
                ->description($this->explanation())
                ->icon(Heroicon::OutlinedClock)
                ->color('info'),
            Text::make('Studied as "'.static::menuKey()->source().'" in the reference system; see docs/spec.')
                ->color('gray')
                ->size('sm'),
        ]);
    }
}
