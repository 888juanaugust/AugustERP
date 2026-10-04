<?php

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

/** One user's exception to their groups: allow or deny one right on one screen. */
class UserRightOverride extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return ['allowed' => 'boolean'];
    }
}
