<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email',
        'recipient_name',
        'subject',
        'mailable_class',
        'status',
        'message_id',
        'status_message',
        'meta',
        'queued_at',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function markAsSent(?string $messageId = null): void
    {
        $cleanMessageId = $messageId ? trim($messageId, '<>') : null;

        $this->forceFill([
            'status' => 'sent',
            'message_id' => $cleanMessageId ?? $this->message_id,
            'sent_at' => now(),
            'status_message' => null,
        ])->save();
    }

    public function markAsFailed(string $statusMessage, ?string $messageId = null): void
    {
        $cleanMessageId = $messageId ? trim($messageId, '<>') : null;

        $this->forceFill([
            'status' => 'failed',
            'message_id' => $cleanMessageId ?? $this->message_id,
            'failed_at' => now(),
            'status_message' => $statusMessage,
        ])->save();
    }
}
