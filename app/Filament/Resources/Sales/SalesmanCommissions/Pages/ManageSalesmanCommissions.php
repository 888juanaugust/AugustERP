<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sales\SalesmanCommissions\Pages;

use App\Filament\Resources\Sales\SalesmanCommissions\SalesmanCommissionResource;
use App\Filament\Support\ManageMaster;

class ManageSalesmanCommissions extends ManageMaster
{
    protected static string $resource = SalesmanCommissionResource::class;
}
