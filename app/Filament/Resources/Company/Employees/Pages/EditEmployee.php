<?php

declare(strict_types=1);

namespace App\Filament\Resources\Company\Employees\Pages;

use App\Filament\Resources\Company\Employees\EmployeeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
