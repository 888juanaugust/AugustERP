<?php

namespace App\Models\Company;

use App\Domain\Audit\HasAuditReference;
use App\Domain\Audit\RecordsActivity;
use App\Domain\Shared\Enums\PtkpStatus;
use App\Domain\Shared\Enums\WorkStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model implements HasAuditReference
{
    use RecordsActivity;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'is_salesman' => 'boolean',
            'withhold_income_tax' => 'boolean',
            'work_status' => WorkStatus::class,
            'tax_status' => PtkpStatus::class,
            'previous_income' => 'integer',
            'previous_tax' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function scopeSalesmen(Builder $query): Builder
    {
        return $query->where('is_salesman', true)->where('is_active', true);
    }

    public function auditReference(): string
    {
        return "{$this->number} {$this->name}";
    }
}
