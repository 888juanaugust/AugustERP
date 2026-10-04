<?php

declare(strict_types=1);

namespace App\Domain\Shared\Enums;

use Filament\Support\Contracts\HasLabel;

/** The four item types of the reference system. */
enum ItemType: string implements HasLabel
{
    case Inventory = 'inventory';
    case NonInventory = 'non_inventory';
    case Service = 'service';
    case Group = 'group';

    public function getLabel(): string
    {
        return match ($this) {
            self::Inventory => 'Inventory item',
            self::NonInventory => 'Non-inventory item',
            self::Service => 'Service',
            self::Group => 'Group / bundle',
        };
    }

    /** Whether stock is kept for it. */
    public function isStocked(): bool
    {
        return $this === self::Inventory;
    }
}
