<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Users\Pages;

use App\Domain\Access\HakAkses;
use App\Filament\Resources\Settings\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function afterSave(): void
    {
        app(HakAkses::class)->forget();
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->hidden(fn () => $this->record->is(auth()->user()))];
    }
}
