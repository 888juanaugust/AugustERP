<?php

namespace App\Models\Company;

use App\Domain\Audit\RecordsActivity;
use App\Models\Settings\AccessGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Who must approve which documents, from what amount, under which rule (X-05). */
class TransactionApprover extends Model
{
    use RecordsActivity;

    /** @return array<string, string> rule value → label */
    public static function rules(): array
    {
        return [
            'any_one' => __('Any one of the approvers'),
            'at_least_two' => __('At least two approvers'),
            'all_in_order' => __('Every approver, in order'),
            'all_any_order' => __('Every approver, in any order'),
        ];
    }

    protected $guarded = [];

    protected function casts(): array
    {
        return ['min_amount' => 'integer', 'is_active' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /** Whose documents need the approval; nobody listed means everyone's. */
    public function requesters(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'transaction_approver_requesters');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(AccessGroup::class, 'transaction_approver_groups');
    }

    public function approvers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'transaction_approver_users');
    }

    /** Whether this user may approve under this rule: named, or in a named group. */
    public function allowsApprover(User $user): bool
    {
        return $this->approvers()->whereKey($user->id)->exists()
            || $this->groups()->whereHas('users', fn (Builder $q) => $q->whereKey($user->id))->exists();
    }
}
