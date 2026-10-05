<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Filament\Resources\Pages\EditRecord;

/** A document's edit page: guarded before, re-posted and revisioned after; Approve and Reject when it waits for approval. */
abstract class EditDocument extends EditRecord
{
    private array $snapshot = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->snapshot = DocumentPages::beforeUpdate($this->record, $data);
        $data['updated_by'] = auth()->id();
        unset($data['series_id']);

        return $data;
    }

    protected function afterSave(): void
    {
        if (method_exists($this->record, 'refreshTotal')) {
            $this->record->refreshTotal();
        }
        DocumentPages::afterUpdated($this->record, $this->snapshot);
    }

    protected function getHeaderActions(): array
    {
        return [...ApprovalActions::make(), DocumentPages::deleteAction()];
    }
}
