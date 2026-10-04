<?php

declare(strict_types=1);

namespace App\Filament\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * The standard's "Proses": a create page opened with ?source=ID starts
 * from that upstream document, lines pulled and pointing back at it.
 */
trait PrefillsFromSource
{
    abstract protected function sourceModel(): string;

    /** @return array<string, mixed> form data from the source document */
    abstract protected function dataFromSource(Model $source): array;

    public function mount(): void
    {
        parent::mount();

        $id = request()->integer('source');
        if ($id > 0) {
            $source = $this->sourceModel()::query()->find($id);
            if ($source !== null) {
                $data = $this->dataFromSource($source);
                $lines = $data['lines'] ?? null;
                unset($data['lines']);
                $this->form->fill(array_merge($this->form->getRawState(), $data));
                if ($lines !== null) {
                    $this->data['lines'] = DocumentPages::keyedRows($lines);
                }
            }
        }
    }
}
