<?php

namespace App\Models\Company;

use App\Domain\Audit\RecordsActivity;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A template that becomes a document on its date: a journal, a payment or a receipt that repeats. */
class RecurringTransaction extends Model
{
    use RecordsActivity;

    public const TYPES = ['journal_voucher' => 'Journal voucher', 'cash_payment' => 'Payment', 'cash_receipt' => 'Receipt'];

    public const FREQUENCIES = ['weekly' => 'Every week', 'monthly' => 'Every month', 'yearly' => 'Every year'];

    protected $guarded = [];

    protected function casts(): array
    {
        return ['template' => 'array', 'next_run_on' => 'date', 'last_run_on' => 'date', 'end_on' => 'date', 'run_count' => 'integer'];
    }

    public function scopeDue(Builder $query, \DateTimeInterface|string|null $on = null): Builder
    {
        return $query->where('status', 'active')->where('next_run_on', '<=', CarbonImmutable::parse($on ?? today())->toDateString());
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function nextAfter(CarbonImmutable $date): CarbonImmutable
    {
        return match ($this->frequency) {
            'weekly' => $date->addWeek(),
            'yearly' => $date->addYear(),
            default => $date->addMonthNoOverflow(),
        };
    }
}
