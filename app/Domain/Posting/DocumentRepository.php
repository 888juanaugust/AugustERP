<?php

declare(strict_types=1);

namespace App\Domain\Posting;

use App\Domain\Audit\Auditor;
use App\Domain\CashBank\Contracts\GiroSource;
use App\Domain\CashBank\GiroService;
use App\Domain\Fulfilment\FulfilmentService;
use App\Domain\Posting\Contracts\AppliesEffects;
use App\Domain\Posting\Contracts\Postable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * The write path of every document: guard, save, post (when the document
 * posts), refresh the documents it pulled from, record the revision and the
 * audit entry, all in one transaction. The Filament document pages call these
 * hooks around their own saving.
 */
final class DocumentRepository
{
    public function __construct(
        private readonly PostingService $postings,
        private readonly DocumentGuard $guard,
        private readonly Revisions $revisions,
        private readonly FulfilmentService $fulfilment,
    ) {}

    /** After a new document and its lines are in the database. */
    public function created(Model $document): void
    {
        DB::transaction(function () use ($document): void {
            $document->refresh();
            $this->syncGiro($document);
            if ($document instanceof Postable) {
                $this->postings->post($document);
            }
            if ($document instanceof AppliesEffects) {
                $document->applyEffects();
            }
            $this->fulfilment->refreshUpstream($document);
            $this->revisions->record($document, 'created', null, $this->snapshot($document));
            Auditor::log('created', $document, $this->number($document), [], $this->date($document));
        });
    }

    /** Before an existing document is changed: the snapshot to compare against, or an exception. */
    public function beforeUpdate(Model $document, ?CarbonInterface $newDate = null): array
    {
        if ($document instanceof Postable) {
            $this->guard->assertMutable($document, $newDate);
        }

        return $this->snapshot($document) + ['sources' => $this->fulfilment->sourcesOf($document)];
    }

    /** After the header and lines are saved: re-post and record what changed. */
    public function updated(Model $document, array $before): void
    {
        DB::transaction(function () use ($document, $before): void {
            $document->refresh();
            $this->syncGiro($document);
            if ($document instanceof Postable) {
                $this->postings->post($document);
            }
            if ($document instanceof AppliesEffects) {
                $document->applyEffects();
            }
            $this->fulfilment->refreshUpstream($document, $before['sources'] ?? []);
            $after = $this->snapshot($document);
            unset($before['sources']);
            $this->revisions->record($document, 'updated', $before, $after);
            Auditor::log('updated', $document, $this->number($document), ['before' => $before['header'] ?? null, 'after' => $after['header'] ?? null], $this->date($document));
        });
    }

    public function delete(Model $document): void
    {
        DB::transaction(function () use ($document): void {
            if ($document instanceof Postable) {
                $this->guard->assertMutable($document);
            } else {
                (new Blockers\ReferencedBlocker)->blocks($document) && throw new Exceptions\DocumentLockedException("{$this->number($document)} cannot be deleted: another document has been made from it.");
            }
            $before = $this->snapshot($document);
            $sources = $this->fulfilment->sourcesOf($document);
            if ($document instanceof Postable) {
                $this->postings->unpost($document);
            }
            if ($document instanceof GiroSource) {
                $document->giro()->where('status', 'outstanding')->delete();
            }
            if ($document instanceof AppliesEffects) {
                $document->revertEffects();
            }
            $this->revisions->record($document, 'deleted', $before, null);
            Auditor::log('deleted', $document, $this->number($document), ['before' => $before['header'] ?? null], $this->date($document));
            $document->delete();
            $this->fulfilment->refreshUpstream($document, $sources);
        });
    }

    /** A receipt or payment by cheque registers its giro before it posts, so the posting follows the giro's state. */
    private function syncGiro(Model $document): void
    {
        if ($document instanceof GiroSource) {
            app(GiroService::class)->sync($document);
            $document->unsetRelation('giro');
        }
    }

    public function lockReason(Model $document): ?string
    {
        return $document instanceof Postable ? $this->guard->lockReason($document) : (new Blockers\ReferencedBlocker)->blocks($document);
    }

    private function snapshot(Model $document): array
    {
        if ($document instanceof Postable) {
            return $document->snapshot();
        }
        $header = collect($document->getAttributes())->except(['updated_at', 'created_at'])->all();
        $lines = method_exists($document, 'lines') ? $document->lines()->get()->map(fn ($l) => $l->getAttributes())->all() : [];

        return ['header' => $header, 'lines' => $lines];
    }

    private function number(Model $document): string
    {
        return $document instanceof Postable ? $document->postingNumber() : (string) $document->getAttribute('number');
    }

    private function date(Model $document): ?string
    {
        return $document->getAttribute('trans_date') ? Carbon::parse($document->getAttribute('trans_date'))->toDateString() : null;
    }
}
