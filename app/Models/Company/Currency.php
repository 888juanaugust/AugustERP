<?php

namespace App\Models\Company;

use App\Domain\Audit\HasAuditReference;
use App\Domain\Audit\RecordsActivity;
use App\Domain\Shared\Format;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model implements HasAuditReference
{
    use RecordsActivity;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saved(fn () => Format::forgetSymbol());
    }

    protected function casts(): array
    {
        return [
            'is_base' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function auditReference(): string
    {
        return $this->code;
    }
}
