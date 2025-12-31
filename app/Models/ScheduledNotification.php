<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Scheduled Notification Model
 *
 * Represents a notification scheduled for future delivery.
 * Supports one-time and recurring notifications.
 *
 * @property int $id
 * @property string $schedule_id
 * @property int $user_id
 * @property string $notification_class
 * @property array $notification_data
 * @property string|null $notification_type
 * @property string $priority
 * @property array|null $channels
 * @property \Carbon\Carbon $scheduled_at
 * @property \Carbon\Carbon|null $sent_at
 * @property \Carbon\Carbon|null $cancelled_at
 * @property string $status
 * @property string|null $error_message
 * @property int $retry_count
 * @property bool $is_recurring
 * @property string|null $recurrence_pattern
 * @property \Carbon\Carbon|null $next_occurrence_at
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 */
class ScheduledNotification extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_SENT = 'sent';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_FAILED = 'failed';

    public const RECURRENCE_DAILY = 'daily';

    public const RECURRENCE_WEEKLY = 'weekly';

    public const RECURRENCE_MONTHLY = 'monthly';

    protected $fillable = [
        'schedule_id',
        'user_id',
        'notification_class',
        'notification_data',
        'notification_type',
        'priority',
        'channels',
        'scheduled_at',
        'sent_at',
        'cancelled_at',
        'status',
        'error_message',
        'retry_count',
        'is_recurring',
        'recurrence_pattern',
        'next_occurrence_at',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'notification_data' => 'array',
            'channels' => 'array',
            'metadata' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'next_occurrence_at' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ScheduledNotification $notification): void {
            if (empty($notification->schedule_id)) {
                $notification->schedule_id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ScheduledNotification>
     */
    public function scopePending(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ScheduledNotification>
     */
    public function scopeDue(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('scheduled_at', '<=', now())
            ->where('status', self::STATUS_PENDING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<ScheduledNotification>
     */
    public function scopeForUser(\Illuminate\Database\Eloquent\Builder $query, int $userId): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('user_id', $userId);
    }
}
