<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Costing;

use App\Domain\Posting\PostingService;
use App\Models\GeneralLedger\Posting;
use App\Models\Inventory\StockMovement;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

/**
 * After a movement lands on a date with later movements of the same item and
 * warehouse, every later document touching that pair is re-posted in date
 * order, receipts first, so their issue costs follow the new average. Runs
 * inside the posting transaction up to a limit; beyond it, as a queued job.
 */
final class Recoster
{
    public const SYNC_LIMIT = 50;

    private bool $running = false;

    /** @var array<string, array{type: string, id: int, date: string, in: bool}> */
    private array $queue = [];

    /** @var array<string, true> */
    private array $done = [];

    public function __construct(private readonly PostingService $postings) {}

    /** Queues the documents with movements after $date on the pair, except the posting that just wrote. */
    public function schedule(int $itemId, int $warehouseId, DateTimeInterface|string $date, ?int $excludePostingId = null, bool $currentHasReceipt = true): void
    {
        $date = Carbon::parse($date)->toDateString();

        $later = StockMovement::query()->active()
            ->where('item_id', $itemId)
            ->where('warehouse_id', $warehouseId)
            ->when($excludePostingId, fn ($q) => $q->where('posting_id', '!=', $excludePostingId))
            ->where(fn ($q) => $q
                ->where('trans_date', '>', $date)
                ->when($currentHasReceipt, fn ($q) => $q->orWhere(fn ($q) => $q->where('trans_date', $date)->where('direction', StockMovement::OUT))))
            ->get(['posting_id', 'trans_date', 'direction'])
            ->groupBy('posting_id');

        if ($later->isEmpty()) {
            return;
        }

        $postings = Posting::query()->whereIn('id', $later->keys())->get();
        foreach ($postings as $posting) {
            $key = "{$posting->document_type}:{$posting->document_id}";
            if (isset($this->done[$key])) {
                continue;
            }
            $movements = $later[$posting->id];
            $this->queue[$key] = [
                'type' => $posting->document_type,
                'id' => (int) $posting->document_id,
                'date' => $posting->trans_date->toDateString(),
                'in' => $movements->contains('direction', StockMovement::IN),
            ];
        }

        if (! $this->running) {
            $this->run();
        }
    }

    private function run(): void
    {
        $this->running = true;
        try {
            if (count($this->queue) > self::SYNC_LIMIT) {
                RecostJob::dispatch(array_values($this->queue));
                $this->queue = [];

                return;
            }

            while ($this->queue !== []) {
                uasort($this->queue, fn ($a, $b) => [$a['date'], $a['in'] ? 0 : 1] <=> [$b['date'], $b['in'] ? 0 : 1]);
                $key = array_key_first($this->queue);
                $entry = $this->queue[$key];
                unset($this->queue[$key]);
                $this->done[$key] = true;

                $document = self::resolve($entry['type'], $entry['id']);
                if ($document !== null) {
                    $this->postings->post($document, auth()->id());
                }
            }
        } finally {
            $this->running = false;
            $this->done = [];
        }
    }

    public static function resolve(string $type, int $id): ?Model
    {
        $class = Relation::getMorphedModel($type);

        return $class !== null ? $class::query()->find($id) : null;
    }

    public function isRunning(): bool
    {
        return $this->running;
    }
}
