<?php

declare(strict_types=1);

namespace App\Domain\Audit;

use Illuminate\Database\Eloquent\Model;

/**
 * Writes created / updated / deleted entries to the Activity Log for a model,
 * with the changed columns before and after. Masters and settings use it;
 * documents log through the posting layer instead.
 */
trait RecordsActivity
{
    public static function bootRecordsActivity(): void
    {
        static::created(fn (Model $model) => Auditor::log('created', $model));

        static::updated(function (Model $model): void {
            $changes = collect($model->getChanges())->except(['updated_at', 'created_at', 'remember_token', 'password']);
            if ($changes->isEmpty()) {
                return;
            }
            $before = collect($model->getOriginal())->only($changes->keys());

            Auditor::log('updated', $model, null, ['before' => $before->all(), 'after' => $changes->all()]);
        });

        static::deleted(fn (Model $model) => Auditor::log('deleted', $model, null, ['before' => collect($model->getOriginal())->except(['password', 'remember_token'])->all()]));
    }

    public function auditReference(): string
    {
        return (string) ($this->getAttribute('number') ?? $this->getAttribute('name') ?? $this->getKey());
    }
}
