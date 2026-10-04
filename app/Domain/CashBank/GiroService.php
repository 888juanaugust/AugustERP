<?php

declare(strict_types=1);

namespace App\Domain\CashBank;

use App\Domain\Audit\Auditor;
use App\Domain\CashBank\Contracts\GiroSource;
use App\Domain\Posting\Contracts\Postable;
use App\Domain\Posting\PostingService;
use App\Models\CashBank\Giro;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * The giro register (K-06). A document settled by cheque registers a giro
 * when it is saved; the giro clears on the day the bank honours it, posting
 * the bank leg then; a bounced giro withdraws its document's effects.
 */
final class GiroService
{
    public function __construct(private readonly PostingService $postings) {}

    /** Keeps the register in step with a saved document; the document's own posting follows the giro's state. */
    public function sync(GiroSource&Model $document): ?Giro
    {
        $details = $document->giroDetails();
        $existing = $document->giro()->first();

        if ($details === null) {
            if ($existing !== null && $existing->isOutstanding()) {
                $existing->delete();
            }

            return null;
        }

        if ($existing !== null && ! $existing->isOutstanding()) {
            return $existing; // settled giros keep their record; the guard blocks the document anyway
        }

        $bankAccountId = (int) $document->getAttribute('bank_account_id');

        return $document->giro()->updateOrCreate([], [
            'direction' => $details->direction,
            'number' => $details->number,
            'bank_account_id' => $bankAccountId,
            'party_id' => $details->partyId,
            'party_name' => $details->partyName,
            'trans_date' => $document->getAttribute('trans_date'),
            'due_date' => $details->dueDate,
            'amount' => $details->amount,
            'status' => Giro::OUTSTANDING,
        ]);
    }

    /** The bank honoured the giro: it leaves giros receivable/payable for the bank account on that day. */
    public function clear(Giro $giro, CarbonImmutable|string $on, ?int $userId = null): void
    {
        if (! $giro->isOutstanding()) {
            throw new RuntimeException("Giro {$giro->number} is {$giro->status}; only an outstanding giro clears.");
        }
        $on = CarbonImmutable::parse($on);
        if ($on->lt($giro->trans_date)) {
            throw new RuntimeException("Giro {$giro->number} cannot clear before it was received.");
        }

        DB::transaction(function () use ($giro, $on, $userId): void {
            $giro->forceFill(['status' => Giro::CLEARED, 'settled_on' => $on, 'settled_by' => $userId ?? auth()->id()])->save();
            $this->postings->post($giro, $userId);
            Auditor::log('giro_cleared', $giro, $giro->number, ['on' => $on->toDateString(), 'amount' => $giro->amount], $on->toDateString());
        });
    }

    /** The bank refused the giro: the receipt or payment it settled is withdrawn, so what it settled is open again. */
    public function bounce(Giro $giro, CarbonImmutable|string $on, ?string $reason = null, ?int $userId = null): void
    {
        if (! $giro->isOutstanding()) {
            throw new RuntimeException("Giro {$giro->number} is {$giro->status}; only an outstanding giro bounces.");
        }
        $on = CarbonImmutable::parse($on);

        DB::transaction(function () use ($giro, $on, $reason, $userId): void {
            $giro->forceFill(['status' => Giro::BOUNCED, 'settled_on' => $on, 'settled_by' => $userId ?? auth()->id()])->save();
            $source = $giro->source;
            if ($source instanceof Postable) {
                $this->postings->unpost($source, $userId);
            }
            Auditor::log('giro_bounced', $giro, $giro->number, ['on' => $on->toDateString(), 'reason' => $reason, 'amount' => $giro->amount], $on->toDateString());
        });
    }
}
