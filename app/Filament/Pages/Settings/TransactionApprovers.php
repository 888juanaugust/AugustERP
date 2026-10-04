<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Domain\Access\MenuKey;
use App\Filament\Support\PlaceholderPage;

class TransactionApprovers extends PlaceholderPage
{
    public static function menuKey(): MenuKey
    {
        return MenuKey::TransactionApprovers;
    }

    protected function explanation(): string
    {
        return 'Approval rules per transaction type and approver arrive with the sales module; until then every sales order waits for a marketing approval when that business rule is on.';
    }
}
