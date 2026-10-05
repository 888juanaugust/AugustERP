<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Costing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Re-posts a long list of documents in date order, receipts first, as one batch of the recoster: the documents
 * those re-posts reach join the same pass instead of starting jobs of their own. One transaction: it all lands, or
 * the job fails whole (and is retried). Idempotent: posting a document that already reflects the current average
 * changes nothing but its revision number.
 */
final class RecostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param  list<array{type: string, id: int, date: string, in: bool}>  $documents */
    public function __construct(public readonly array $documents) {}

    public function handle(Recoster $recoster): void
    {
        DB::transaction(fn () => $recoster->runBatch($this->documents));
    }
}
