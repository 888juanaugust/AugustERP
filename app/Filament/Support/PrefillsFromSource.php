<?php

declare(strict_types=1);

namespace App\Filament\Support;

use App\Domain\Access\BranchLimit;
use App\Domain\Access\Hak;
use App\Domain\Access\HakAkses;
use App\Domain\Access\MenuRegistry;
use App\Domain\Approval\ApprovalEngine;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * The standard's "Proses": a create page opened with ?source=ID starts
 * from that upstream document, lines pulled and pointing back at it, in
 * the source's currency.
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
            // Only a document the user may see: in their branches, with the view right on its screen.
            $source = BranchLimit::apply($this->sourceModel()::query(), auth()->user())->find($id);
            $screen = $source !== null ? app(MenuRegistry::class)->menuKeyForModel($source::class) : null;
            if ($screen !== null && ! app(HakAkses::class)->allows(auth()->user(), $screen, Hak::View)) {
                $source = null;
            }
            if ($source !== null && ! app(ApprovalEngine::class)->isApproved($source)) {
                Notification::make()->title(__(':number is not approved; nothing can be made from it yet.', ['number' => $source->getAttribute('number')]))->warning()->send();
                $source = null;
            }
            if ($source !== null) {
                $data = $this->dataFromSource($source);
                $lines = $data['lines'] ?? null;
                unset($data['lines']);
                $state = $this->form->getRawState();
                $this->form->fill(array_merge($state, CurrencyFields::fromSource($source, $state['trans_date'] ?? null), $data));
                if ($lines !== null) {
                    $this->data['lines'] = DocumentPages::keyedRows($lines);
                }
            }
        }
    }
}
