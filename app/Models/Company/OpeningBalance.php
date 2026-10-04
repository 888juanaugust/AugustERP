<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** A receivable or payable carried in from before the data start date. */
class OpeningBalance extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'trans_date' => 'date',
            'amount' => 'integer',
        ];
    }

    public function party(): MorphTo
    {
        return $this->morphTo();
    }

    public function paymentTerm(): BelongsTo
    {
        return $this->belongsTo(PaymentTerm::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
