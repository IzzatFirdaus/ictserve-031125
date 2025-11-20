<?php

declare(strict_types=1);

// name: UserNotificationPreference
// description: Granular notification preferences for authenticated users
// author: dev-team@motac.gov.my
// trace: SRS-FR-004; D04 §4.4; D11 §9; Requirements 3.2
// last-updated: 2025-11-06

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    /** @use HasFactory<\Database\Factories\UserNotificationPreferenceFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'preference_key',
        'preference_value',
    ];

    protected function casts(): array
    {
        return [
            'preference_value' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who owns the preference
     *
     * @return BelongsTo<User, UserNotificationPreference>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get preferences for a specific user
     *
     * @param  Builder<UserNotificationPreference>  $query
     * @return Builder<UserNotificationPreference>
     */
    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get enabled preferences
     */
    public function scopeEnabled($query)
    {
        return $query->where('preference_value', true);
    }

    /**
     * Scope: Get disabled preferences
     */
    public function scopeDisabled($query)
    {
        return $query->where('preference_value', false);
    }

    /**
     * Static: Available preference keys
     */
    public static function availableKeys(): array
    {
        return [
            'ticket_status_updates',
            'loan_approval_notifications',
            'overdue_reminders',
            'system_announcements',
            'ticket_assignments',
            'comment_replies',
        ];
    }
}
