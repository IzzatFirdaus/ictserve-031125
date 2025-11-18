<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Email Log Model
 *
 * Tracks email delivery status, retry attempts, performance metrics, and
 * unified notification system integration (multi-channel tracking).
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $recipient_email
 * @property string $subject
 * @property string $email_type
 * @property string $status
 * @property array|null $data
 * @property int $retry_attempts
 * @property \Carbon\Carbon|null $delivered_at
 * @property \Carbon\Carbon|null $last_retry_at
 * @property string|null $error_message
 * @property array|null $channels Multi-channel dispatch tracking
 * @property string|null $notification_type From config/notifications.php
 * @property string|null $priority critical/high/normal/low
 * @property \Carbon\Carbon|null $next_retry_at Scheduled retry time
 * @property string|null $final_status delivered/permanently_failed/bounced/rejected
 * @property bool $preference_bypassed User preference override flag
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'mailable_class',
        'email_type',
        'status',
        'message_id',
        'status_message',
        'meta',
        'data',
        'retry_attempts',
        'queued_at',
        'sent_at',
        'failed_at',
        'delivered_at',
        'last_retry_at',
        'error_message',
        // Unified notification system fields
        'channels',
        'notification_type',
        'priority',
        'next_retry_at',
        'final_status',
        'preference_bypassed',
    ];

    protected $casts = [
        'data' => 'array',
        'meta' => 'array',
        'delivered_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'retry_attempts' => 'integer',
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        // Unified notification system fields
        'channels' => 'array',
        'next_retry_at' => 'datetime',
        'preference_bypassed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRetryable($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_attempts', '<', 3);
    }

    /**
     * Scope for emails with specific notification type
     */
    public function scopeOfType($query, string $notificationType)
    {
        return $query->where('notification_type', $notificationType);
    }

    /**
     * Scope for emails with specific priority
     */
    public function scopeWithPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for emails that reached permanent final status
     */
    public function scopePermanentlyFailed($query)
    {
        return $query->where('final_status', 'permanently_failed');
    }

    /**
     * Scope for emails that bypassed user preferences
     */
    public function scopePreferenceBypassed($query)
    {
        return $query->where('preference_bypassed', true);
    }

    public function markAsSent(?string $messageId): void
    {
        // Strip angle brackets from Message-ID if present
        $cleanMessageId = $messageId ? trim($messageId, '<>') : null;

        $this->update([
            'status' => 'sent',
            'message_id' => $cleanMessageId,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'status_message' => $errorMessage,
            'failed_at' => now(),
        ]);
    }

    /**
     * Mark email as permanently failed after all retries exhausted
     */
    public function markAsPermanentlyFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'permanently_failed',
            'final_status' => 'permanently_failed',
            'status_message' => $errorMessage,
            'failed_at' => now(),
            'next_retry_at' => null, // Clear retry schedule
        ]);
    }
}
