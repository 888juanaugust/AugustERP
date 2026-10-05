<?php

declare(strict_types=1);

namespace App\Client\Screens;

use App\Domain\Access\ScreenKey;
use App\Domain\Access\ScreenKind;
use App\Filament\Modul;

/** This client's own screens. A value is stored in access rights, so it never changes once in use. */
enum ClientScreen: string implements ScreenKey
{
    case DeliveryRoutes = 'client__delivery-routes';

    public function modul(): Modul
    {
        return match ($this) {
            self::DeliveryRoutes => Modul::Sales,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::DeliveryRoutes => __('Delivery Routes'),
        };
    }

    public function sort(): int
    {
        return 500;
    }

    public function kind(): ScreenKind
    {
        return ScreenKind::Setup;
    }

    public function isReplicated(): bool
    {
        return true;
    }

    public function slug(): string
    {
        return str_replace('__', '/', $this->value);
    }
}
