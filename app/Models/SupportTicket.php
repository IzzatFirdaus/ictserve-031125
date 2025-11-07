<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Support Ticket Model
 *
 * Represents in-app support messages from portal users.
 *
 * @version 1.0.0
 *
 * @since 2025-11-06
 *
 * @author ICTServe Development Team
 *
 * Requirements:
 * - Requirement 12.4: Support ticket tracking
 * - D09: Database documentation and audit trail
 *
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class SupportTicket extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'support_tickets';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the support ticket.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachments for the support ticket.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SupportTicketAttachment::class);
    }

    /**
     * Get the responses for the support ticket.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(SupportTicketResponse::class);
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Get ticket number
     */
    public function getTicketNumberAttribute(): string
    {
        return 'SUP'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
