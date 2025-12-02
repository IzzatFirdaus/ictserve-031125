<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ApprovalDelegation Model
 *
 * Manages temporary delegation of approval authority from one approver to another.
 * Supports Grade 41+ approvers delegating their approval responsibilities during
 * leave periods or temporary unavailability.
 *
 * @property int $id
 * @property int $original_approver_id
 * @property int $delegated_approver_id
 * @property \Carbon\Carbon $start_date
 * @property \Carbon\Carbon $end_date
 * @property string $reason
 * @property bool $is_active
 * @property int $created_by
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ApprovalDelegation extends Model
{
    use HasFactory;

    protected $fillable = [
        'original_approver_id',
        'delegated_approver_id',
        'start_date',
        'end_date',
        'reason',
        'is_active',
        'created_by',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the original approver (the one delegating authority)
     */
    public function originalApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'original_approver_id');
    }

    /**
     * Get the delegated approver (the one receiving authority)
     */
    public function delegatedApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delegated_approver_id');
    }

    /**
     * Get the user who created the delegation
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if delegation is currently active (within date range and not deactivated)
     */
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = now();

        return $now->gte($this->start_date) && $now->lte($this->end_date);
    }

    /**
     * Get active delegation for a specific approver
     */
    public static function getActiveDelegationFor(int $approverId): ?self
    {
        return static::where('original_approver_id', $approverId)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get all active delegations where user is the delegated approver
     */
    public static function getActiveDelegationsToUser(int $userId): Collection
    {
        return static::where('delegated_approver_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['originalApprover'])
            ->get();
    }

    /**
     * Deactivate delegation
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Check if delegation overlaps with existing delegations for the same original approver
     */
    public function hasOverlap(): bool
    {
        $query = static::where('original_approver_id', $this->original_approver_id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereBetween('start_date', [$this->start_date, $this->end_date])
                    ->orWhereBetween('end_date', [$this->start_date, $this->end_date])
                    ->orWhere(function ($q2) {
                        $q2->where('start_date', '<=', $this->start_date)
                            ->where('end_date', '>=', $this->end_date);
                    });
            });

        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        return $query->exists();
    }

    /**
     * Scope for active delegations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Scope for upcoming delegations
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->where('start_date', '>', now());
    }

    /**
     * Scope for expired delegations
     */
    public function scopeExpired($query)
    {
        return $query->where('is_active', true)
            ->where('end_date', '<', now());
    }
}
