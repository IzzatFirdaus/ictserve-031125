<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User Notification Preferences Model
 *
 * @property int $id
 * @property int $user_id
 * @property bool $email_digest_enabled
 * @property string $email_digest_frequency
 * @property \Carbon\Carbon $email_digest_time
 * @property bool $quiet_hours_enabled
 * @property \Carbon\Carbon|null $quiet_hours_start
 * @property \Carbon\Carbon|null $quiet_hours_end
 * @property bool $browser_notifications_enabled
 * @property bool $sound_enabled
 * @property bool $group_notifications
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\User $user
 * @property string $preference_key
 * @property int $preference_value
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference wherePreferenceKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference wherePreferenceValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserNotificationPreference whereUserId($value)
 * @mixin \Eloquent
 */
class UserNotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_digest_enabled',
        'email_digest_frequency',
        'email_digest_time',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
        'browser_notifications_enabled',
        'sound_enabled',
        'group_notifications',
    ];

    protected $casts = [
        'email_digest_enabled' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
        'browser_notifications_enabled' => 'boolean',
        'sound_enabled' => 'boolean',
        'group_notifications' => 'boolean',
        'email_digest_time' => 'datetime:H:i',
        'quiet_hours_start' => 'datetime:H:i',
        'quiet_hours_end' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isInQuietHours(): bool
    {
        if (! $this->quiet_hours_enabled || ! $this->quiet_hours_start || ! $this->quiet_hours_end) {
            return false;
        }

        $now = now()->format('H:i');
        $start = $this->quiet_hours_start->format('H:i');
        $end = $this->quiet_hours_end->format('H:i');

        if ($start < $end) {
            return $now >= $start && $now <= $end;
        }

        // Handle overnight quiet hours (e.g., 22:00 to 06:00)
        return $now >= $start || $now <= $end;
    }
}
