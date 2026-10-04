<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Resources\Pages\CreateRecord;

/** A document's create page: numbered from its series, posted once its lines are saved. */
abstract class CreateDocument extends CreateRecord
{
    use CreatesNumberedRecord;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $this->assignNumber($data);
    }

    protected function afterCreate(): void
    {
        $this->refreshTotals();
        DocumentPages::afterCreated($this->record);
    }

    protected function refreshTotals(): void
    {
        if (method_exists($this->record, 'refreshTotal')) {
            $this->record->refreshTotal();
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
