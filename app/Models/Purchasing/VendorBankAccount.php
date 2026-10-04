<?php

namespace App\Models\Purchasing;

use App\Models\Company\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankAccount extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
