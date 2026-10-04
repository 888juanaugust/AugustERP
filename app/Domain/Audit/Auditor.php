<?php

declare(strict_types=1);

namespace App\Domain\Audit;

use App\Models\Company\AuditLog;
use Illuminate\Database\Eloquent\Model;

/**
 * Writes the Activity Log. Every money-affecting action, every master change
 * and every preference change goes through here; the table is append-only.
 */
final class Auditor
{
    /** @param  array<string, mixed>  $meta */
    public static function log(
        string $action,
        ?Model $subject = null,
        ?string $reference = null,
        array $meta = [],
        ?string $transDate = null,
    ): AuditLog {
        $request = app()->runningInConsole() ? null : request();

        return AuditLog::query()->create([
            'user_id' => auth()->id(),
            'action' => $action,
            'document_type' => $subject?->getMorphClass(),
            'document_id' => $subject?->getKey(),
            'reference' => $reference ?? ($subject instanceof HasAuditReference ? $subject->auditReference() : null),
            'trans_date' => $transDate ?? ($subject?->getAttribute('trans_date') ? (string) $subject->getAttribute('trans_date')?->toDateString() : null),
            'ip' => $request?->ip(),
            'meta' => $meta === [] ? null : $meta,
        ]);
    }
}
