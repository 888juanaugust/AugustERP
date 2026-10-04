<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesInvoiceDownPayment extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }

    public function downPayment(): BelongsTo
    {
        return $this->belongsTo(SalesDownPayment::class, 'sales_down_payment_id');
    }
}
