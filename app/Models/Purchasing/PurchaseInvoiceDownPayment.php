<?php

namespace App\Models\Purchasing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A down payment deducted on an invoice. */
class PurchaseInvoiceDownPayment extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount' => 'integer'];
    }

    public function downPayment(): BelongsTo
    {
        return $this->belongsTo(PurchaseDownPayment::class, 'purchase_down_payment_id');
    }
}
