<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Costing;

use App\Domain\Posting\PostingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Re-posts a long list of documents in date order, receipts first. Idempotent:
 * posting a document that already reflects the current average changes nothing
 * but its revision number.
 */
final class RecostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param  list<array{type: string, id: int, date: string, in: bool}>  $documents */
    public function __construct(public readonly array $documents) {}

    public function handle(PostingService $postings): void
    {
        $documents = $this->documents;
        usort($documents, fn ($a, $b) => [$a['date'], $a['in'] ? 0 : 1] <=> [$b['date'], $b['in'] ? 0 : 1]);

        foreach ($documents as $entry) {
            $document = Recoster::resolve($entry['type'], $entry['id']);
            if ($document !== null) {
                $postings->post($document, null);
            }
        }
    }
}
