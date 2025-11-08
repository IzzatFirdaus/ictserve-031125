<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Email Log Model
 *
 * Tracks email delivery status, retry attempts, and performance metrics.
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
    ];

    protected $casts = [
        'data' => 'array',
        'delivered_at' => 'datetime',
        'last_retry_at' => 'datetime',
        'retry_attempts' => 'integer',
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
}
