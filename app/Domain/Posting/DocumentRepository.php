<?php

declare(strict_types=1);

namespace App\Domain\Posting;

use App\Domain\Audit\Auditor;
use App\Domain\Posting\Contracts\Postable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * The write path of every document: guard, persist, post, record the
 * revision and the audit entry, all in one transaction. The Filament
 * document pages call these hooks around their own saving.
 */
final class DocumentRepository
{
    public function __construct(
        private readonly PostingService $postings,
        private readonly DocumentGuard $guard,
        private readonly Revisions $revisions,
    ) {}

    /** After a new document and its lines are in the database. */
    public function created(Postable&Model $document): void
    {
        DB::transaction(function () use ($document): void {
            $document->refresh();
            $this->postings->post($document);
            $this->revisions->record($document, 'created', null, $document->snapshot());
            Auditor::log('created', $document, $document->postingNumber(), [], $document->postingDate()->toDateString());
        });
    }

    /** Before an existing document is changed: the snapshot to compare against, or an exception. */
    public function beforeUpdate(Postable&Model $document, ?CarbonInterface $newDate = null): array
    {
        $this->guard->assertMutable($document, $newDate);

        return $document->snapshot();
    }

    /** After the header and lines are saved: re-post and record what changed. */
    public function updated(Postable&Model $document, array $before): void
    {
        DB::transaction(function () use ($document, $before): void {
            $document->refresh();
            $this->postings->post($document);
            $after = $document->snapshot();
            $this->revisions->record($document, 'updated', $before, $after);
            Auditor::log('updated', $document, $document->postingNumber(), ['before' => $before['header'] ?? null, 'after' => $after['header'] ?? null], $document->postingDate()->toDateString());
        });
    }

    public function delete(Postable&Model $document): void
    {
        DB::transaction(function () use ($document): void {
            $this->guard->assertMutable($document);
            $before = $document->snapshot();
            $this->postings->unpost($document);
            $this->revisions->record($document, 'deleted', $before, null);
            Auditor::log('deleted', $document, $document->postingNumber(), ['before' => $before['header'] ?? null], $document->postingDate()->toDateString());
            $document->delete();
        });
    }

    public function lockReason(Postable&Model $document): ?string
    {
        return $this->guard->lockReason($document);
    }
}
