<?php

declare(strict_types=1);

namespace App\Domain\CashBank;

use App\Domain\Audit\Auditor;
use App\Models\CashBank\BankReconciliation;
use App\Models\CashBank\BankReconciliationItem;
use App\Models\CashBank\BankStatementLine;
use App\Models\GeneralLedger\JournalLine;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Bank reconciliation (K-05): the book lines of one bank account over one
 * period are cleared against the statement, by hand or matched to imported
 * statement lines; the reconciliation closes when the cleared balance meets
 * the statement's ending balance. A cleared line locks its document.
 */
final class Reconciler
{
    public const MATCH_WINDOW_DAYS = 3;

    public function open(int $bankAccountId, CarbonImmutable|string $start, CarbonImmutable|string $end, int $statementBalance = 0): BankReconciliation
    {
        $start = CarbonImmutable::parse($start)->toDateString();
        $end = CarbonImmutable::parse($end)->toDateString();
        if ($end < $start) {
            throw new RuntimeException('The period ends before it starts.');
        }

        $reconciliation = BankReconciliation::query()->firstOrCreate(
            ['bank_account_id' => $bankAccountId, 'start_date' => $start, 'end_date' => $end],
            ['statement_balance' => $statementBalance, 'status' => BankReconciliation::OPEN, 'created_by' => auth()->id()],
        );
        if (! $reconciliation->isClosed() && $reconciliation->statement_balance !== $statementBalance) {
            $reconciliation->forceFill(['statement_balance' => $statementBalance])->save();
        }

        return $reconciliation;
    }

    /** Book lines of the account up to the period's end: those not yet cleared, and those cleared in this reconciliation. */
    public function bookLines(BankReconciliation $reconciliation): Collection
    {
        return JournalLine::query()->active()
            ->with(['entry', 'posting'])
            ->where('account_id', $reconciliation->bank_account_id)
            ->where('trans_date', '<=', $reconciliation->end_date)
            ->where(fn (Builder $q) => $q
                ->whereDoesntHave('reconciliationItem')
                ->orWhereHas('reconciliationItem', fn (Builder $i) => $i->where('bank_reconciliation_id', $reconciliation->id)))
            ->orderBy('trans_date')->orderBy('id')
            ->get();
    }

    /** @param  list<int>  $journalLineIds */
    public function clear(BankReconciliation $reconciliation, array $journalLineIds, ?int $statementLineId = null, ?int $userId = null): int
    {
        $this->assertOpen($reconciliation);
        $cleared = 0;
        DB::transaction(function () use ($reconciliation, $journalLineIds, $statementLineId, $userId, &$cleared): void {
            foreach (array_unique($journalLineIds) as $id) {
                $line = JournalLine::query()->active()->where('account_id', $reconciliation->bank_account_id)->find($id);
                if ($line === null || $line->trans_date->gt($reconciliation->end_date) || $this->isCleared($line)) {
                    continue;
                }
                BankReconciliationItem::query()->create([
                    'bank_reconciliation_id' => $reconciliation->id,
                    'journal_line_id' => $line->id,
                    'bank_statement_line_id' => $statementLineId,
                    'cleared_on' => $reconciliation->end_date,
                    'cleared_by' => $userId ?? auth()->id(),
                    'created_at' => now(),
                ]);
                $cleared++;
            }
        });

        return $cleared;
    }

    /** Clears every book line that an unmatched statement line of the same amount explains, within a few days. */
    public function autoMatch(BankReconciliation $reconciliation, ?int $userId = null): int
    {
        $this->assertOpen($reconciliation);
        $matched = 0;
        DB::transaction(function () use ($reconciliation, $userId, &$matched): void {
            $statementLines = BankStatementLine::query()->unmatched()
                ->where('bank_account_id', $reconciliation->bank_account_id)
                ->where('trans_date', '<=', CarbonImmutable::parse($reconciliation->end_date)->addDays(self::MATCH_WINDOW_DAYS))
                ->orderBy('trans_date')->orderBy('id')
                ->get();
            $used = [];
            foreach ($this->bookLines($reconciliation) as $line) {
                if ($this->isCleared($line)) {
                    continue;
                }
                $signed = $line->debit - $line->credit;
                $candidate = $statementLines->first(fn (BankStatementLine $s) => ! isset($used[$s->id])
                    && $s->amount === $signed
                    && abs($s->trans_date->diffInDays($line->trans_date, false)) <= self::MATCH_WINDOW_DAYS);
                if ($candidate === null) {
                    continue;
                }
                $used[$candidate->id] = true;
                $matched += $this->clear($reconciliation, [$line->id], $candidate->id, $userId);
            }
        });

        return $matched;
    }

    /** @return array{book_balance: int, cleared_balance: int, uncleared: int, statement_balance: int, difference: int} */
    public function summary(BankReconciliation $reconciliation): array
    {
        $book = (int) JournalLine::query()->active()
            ->where('account_id', $reconciliation->bank_account_id)
            ->where('trans_date', '<=', $reconciliation->end_date)
            ->selectRaw('COALESCE(SUM(debit - credit), 0) AS net')->value('net');
        $cleared = (int) JournalLine::query()->active()
            ->where('account_id', $reconciliation->bank_account_id)
            ->where('trans_date', '<=', $reconciliation->end_date)
            ->whereHas('reconciliationItem')
            ->selectRaw('COALESCE(SUM(debit - credit), 0) AS net')->value('net');

        return [
            'book_balance' => $book,
            'cleared_balance' => $cleared,
            'uncleared' => $book - $cleared,
            'statement_balance' => (int) $reconciliation->statement_balance,
            'difference' => (int) $reconciliation->statement_balance - $cleared,
        ];
    }

    public function close(BankReconciliation $reconciliation, ?int $userId = null): void
    {
        $this->assertOpen($reconciliation);
        $summary = $this->summary($reconciliation);
        if ($summary['difference'] !== 0) {
            throw new RuntimeException('The cleared balance differs from the statement by '.number_format(abs($summary['difference']), 0, ',', '.').'; clear or correct before closing.');
        }
        $reconciliation->forceFill(['status' => BankReconciliation::CLOSED, 'closed_at' => now(), 'closed_by' => $userId ?? auth()->id()])->save();
        Auditor::log('bank_reconciled', $reconciliation, $reconciliation->bankAccount?->name, $summary, $reconciliation->end_date->toDateString());
    }

    public function isCleared(JournalLine|int $line): bool
    {
        $id = $line instanceof JournalLine ? $line->id : $line;

        return BankReconciliationItem::query()->where('journal_line_id', $id)->exists();
    }

    private function assertOpen(BankReconciliation $reconciliation): void
    {
        if ($reconciliation->isClosed()) {
            throw new RuntimeException('This reconciliation is closed.');
        }
    }
}
