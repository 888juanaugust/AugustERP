<?php

declare(strict_types=1);

namespace App\Domain\Company;

use App\Domain\Posting\DocumentRepository;
use App\Models\Company\OpeningBalance;
use App\Models\Company\PaymentTerm;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

/**
 * Keeps a customer's or vendor's opening balances in step with the rows on
 * its form. Each row is a document of its own: created, changed and removed
 * through the document repository, so it posts, is audited, keeps its
 * revisions and is refused when settled or in a closed period. A row is
 * changed in place, never recreated, so its posting key stays the same.
 */
final class OpeningBalances
{
    private const FIELDS = ['document_date', 'due_date', 'amount', 'payment_term_id', 'number', 'description'];

    public function __construct(private readonly DocumentRepository $documents) {}

    /**
     * @param  array<string, array<string, mixed>>  $rows  the repeater's state: "record-{id}" keys for rows already saved
     *
     * @throws RuntimeException when a row may not change
     */
    public function sync(Model $party, array $rows): void
    {
        $existing = OpeningBalance::query()->where('party_type', $party->getMorphClass())->where('party_id', $party->getKey())->get()->keyBy('id');
        $kept = [];
        $sort = 0;
        foreach ($rows as $key => $row) {
            $values = $this->values($row, $party) + ['sort' => $sort++];
            $id = str_starts_with((string) $key, 'record-') ? (int) substr((string) $key, 7) : null;
            $balance = $id !== null ? $existing->get($id) : null;
            if ($balance === null) {
                $balance = OpeningBalance::query()->create($values + [
                    'party_type' => $party->getMorphClass(),
                    'party_id' => $party->getKey(),
                    'trans_date' => DataStart::openingDate(),
                    'branch_id' => $party->getAttribute('branch_id'),
                    'created_by' => auth()->id(),
                ]);
                $this->documents->created($balance);
                $kept[] = $balance->id;

                continue;
            }
            $kept[] = $balance->id;
            $balance->fill($values);
            if (! $balance->isDirty(self::FIELDS)) {
                $balance->saveQuietly(); // only the order changed

                continue;
            }
            $before = $this->documents->beforeUpdate($balance->fresh());
            $balance->forceFill(['updated_by' => auth()->id()])->save();
            $this->documents->updated($balance, $before);
        }
        foreach ($existing->except($kept) as $removed) {
            $this->documents->delete($removed);
        }
    }

    /** @param  array<string, mixed>  $row */
    private function values(array $row, Model $party): array
    {
        $documentDate = filled($row['document_date'] ?? null) ? CarbonImmutable::parse($row['document_date']) : CarbonImmutable::parse(DataStart::openingDate());
        // The row's payment term, else the customer's or vendor's own.
        $termId = filled($row['payment_term_id'] ?? null) ? $row['payment_term_id'] : $party->getAttribute('payment_term_id');
        $term = $termId ? PaymentTerm::query()->find($termId) : null;
        $amount = (int) ($row['amount'] ?? 0);
        if ($amount <= 0) {
            throw new RuntimeException(__('An opening balance needs an amount above zero.'));
        }
        $start = DataStart::date();
        if ($start !== null && $documentDate->gt($start)) {
            throw new RuntimeException(__('An opening balance comes from before the data start date, :date.', ['date' => $start->toDateString()]));
        }

        return [
            'document_date' => $documentDate->toDateString(),
            'due_date' => filled($row['due_date'] ?? null) ? CarbonImmutable::parse($row['due_date'])->toDateString() : $documentDate->addDays((int) ($term?->due_days ?? 0))->toDateString(),
            'amount' => $amount,
            'payment_term_id' => $term?->id,
            'number' => filled($row['number'] ?? null) ? mb_substr(trim((string) $row['number']), 0, 40) : null,
            'description' => filled($row['description'] ?? null) ? (string) $row['description'] : null,
        ];
    }
}
