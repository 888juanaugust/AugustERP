<?php

declare(strict_types=1);

namespace App\Filament\Resources\Sales\SalesDownPayments\Pages;

use App\Filament\Resources\Sales\SalesDownPayments\SalesDownPaymentResource;
use App\Filament\Support\ListDocuments;

class ListSalesDownPayments extends ListDocuments
{
    protected static string $resource = SalesDownPaymentResource::class;

    protected string $statusColumn = 'payment_status';

    protected function statuses(): array
    {
        return ['unpaid' => 'Unpaid', 'partial' => 'Partially paid', 'paid' => 'Paid'];
    }
}
