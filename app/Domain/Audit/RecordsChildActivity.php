<?php

declare(strict_types=1);

namespace App\Domain\Audit;

use Illuminate\Database\Eloquent\Model;

/**
 * For the rows a master holds in a grid (a vendor's bank accounts, an item's units, prices, components and minimum
 * stocks): each row added, changed or removed is written to the Activity Log on the master, with the row's values
 * before and after, so the master's history shows it.
 */
trait RecordsChildActivity
{
    /** The master the row belongs to. */
    abstract public function auditParent(): ?Model;

    public static function bootRecordsChildActivity(): void
    {
        static::created(fn (Model $row) => $row->logOnParent('line_added', ['after' => $row->auditValues($row->getAttributes())]));

        static::updated(function (Model $row): void {
            $changes = collect($row->getChanges())->except(['created_at', 'updated_at', ...$row->getHidden()]);
            if ($changes->isNotEmpty()) {
                $row->logOnParent('line_changed', ['before' => collect($row->getOriginal())->only($changes->keys())->all(), 'after' => $changes->all()]);
            }
        });

        static::deleted(fn (Model $row) => $row->logOnParent('line_removed', ['before' => $row->auditValues($row->getOriginal())]));
    }

    /** @param  array<string, mixed>  $values */
    private function auditValues(array $values): array
    {
        return collect($values)->except(['created_at', 'updated_at', ...$this->getHidden()])->all();
    }

    /** @param  array<string, mixed>  $meta */
    private function logOnParent(string $action, array $meta): void
    {
        Auditor::log($action, $this->auditParent() ?? $this, null, ['part' => $this->getTable(), 'row' => $this->getKey()] + $meta);
    }
}
