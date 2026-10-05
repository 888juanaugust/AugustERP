<?php

namespace App\Models\Purchasing;

use App\Domain\Audit\RecordsChildActivity;
use App\Models\Company\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorBankAccount extends Model
{
    use RecordsChildActivity;

    public $timestamps = false;

    protected $guarded = [];

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }

    public function auditParent(): ?Model
    {
        return Vendor::query()->find($this->getAttribute('vendor_id'));
    }
}
