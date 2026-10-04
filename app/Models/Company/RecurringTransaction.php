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

    /** @return array<string, string> document kind → label */
    public static function types(): array
    {
        return ['journal_voucher' => __('Journal voucher'), 'cash_payment' => __('Payment'), 'cash_receipt' => __('Receipt')];
    }

    /** @return array<string, string> frequency → label */
    public static function frequencies(): array
    {
        return ['weekly' => __('Every week'), 'monthly' => __('Every month'), 'yearly' => __('Every year')];
    }

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
