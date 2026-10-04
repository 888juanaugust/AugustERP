<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Users\Pages;

use App\Filament\Resources\Settings\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
}
