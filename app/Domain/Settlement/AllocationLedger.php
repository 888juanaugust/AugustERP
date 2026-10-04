<?php

declare(strict_types=1);

namespace App\Domain\Settlement;

use App\Domain\Posting\PostingBuilder;
use App\Models\GeneralLedger\Posting;
use App\Models\Settlement\PaymentAllocation;
use Illuminate\Database\Eloquent\Relations\Relation;

/** Writes the allocations a payment's posting declared and refreshes the settled documents. Registered on the PostingService. */
final class AllocationLedger
{
    public function __construct(private readonly SettlementService $settlement) {}

    public function write(Posting $posting, PostingBuilder $builder): void
    {
        $touched = [];
        foreach ($builder->allocations() as $i => $a) {
            PaymentAllocation::query()->create([
                'posting_id' => $posting->id,
                'sort' => $i,
                'payment_type' => $posting->document_type,
                'payment_id' => $posting->document_id,
                'receivable_type' => $a['receivable_type'],
                'receivable_id' => $a['receivable_id'],
                'amount' => (int) $a['amount'],
                'discount' => (int) ($a['discount'] ?? 0),
                'discount_account_id' => $a['discount_account_id'] ?? null,
                'trans_date' => $posting->trans_date->toDateString(),
            ]);
            $touched["{$a['receivable_type']}:{$a['receivable_id']}"] = [$a['receivable_type'], (int) $a['receivable_id']];
        }
        $this->refresh($touched);
    }

    public function unwrite(Posting $posting): void
    {
        $touched = [];
        foreach (PaymentAllocation::query()->where('posting_id', $posting->id)->get(['receivable_type', 'receivable_id']) as $a) {
            $touched["{$a->receivable_type}:{$a->receivable_id}"] = [$a->receivable_type, (int) $a->receivable_id];
        }
        $this->refresh($touched);
    }

    private function refresh(array $touched): void
    {
        foreach ($touched as [$type, $id]) {
            $class = Relation::getMorphedModel($type) ?? $type;
            $doc = $class::query()->find($id);
            if ($doc !== null) {
                $this->settlement->refresh($doc);
            }
        }
    }
}
