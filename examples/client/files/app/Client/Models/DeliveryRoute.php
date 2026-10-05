<?php

namespace App\Client\Models;

use App\Domain\Audit\HasAuditReference;
use App\Domain\Audit\RecordsActivity;
use App\Models\Company\Employee;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** An area the client's drivers cover, with its usual driver. */
class DeliveryRoute extends Model implements HasAuditReference
{
    use RecordsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function auditReference(): string
    {
        return (string) $this->name;
    }
}
