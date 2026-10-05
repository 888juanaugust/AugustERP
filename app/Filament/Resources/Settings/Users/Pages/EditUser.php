<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Users\Pages;

use App\Domain\Access\HakAkses;
use App\Filament\Resources\Settings\Users\UserActions;
use App\Filament\Resources\Settings\Users\UserResource;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** @var array{groups: list<string>, branches: list<string>} */
    private array $membershipsBefore = ['groups' => [], 'branches' => []];

    protected function beforeSave(): void
    {
        $this->membershipsBefore = $this->getRecord()->memberships();
    }

    protected function afterSave(): void
    {
        $this->getRecord()->logMembershipChange($this->membershipsBefore);
        app(HakAkses::class)->forget();
    }

    protected function getHeaderActions(): array
    {
        return [UserActions::deactivate(), UserActions::reactivate()];
    }
}
