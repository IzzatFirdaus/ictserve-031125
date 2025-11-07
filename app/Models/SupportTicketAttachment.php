<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Support Ticket Attachment Model
 *
 * Represents file attachments for support tickets.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * @property int $id
 * @property int $support_ticket_id
 * @property string $filename
 * @property string $path
 * @property string $mime_type
 * @property int $size
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class SupportTicketAttachment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'support_ticket_attachments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'support_ticket_id',
        'filename',
        'path',
        'mime_type',
        'size',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the support ticket that owns the attachment.
     */
    public function supportTicket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class);
    }

    /**
     * Get human-readable file size
     */
    public function getHumanReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
