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
 * @since 2025-11-06
 * @author ICTServe Development Team
 * 
 * Requirements:
 * - Requirement 12.4: Support ticket tracking
 * - D09: Database documentation and audit trail
 * @property int $id
 * @property int $user_id
 * @property string $subject
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SupportTicketAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read string $ticket_number
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket closed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket open()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SupportTicket whereUserId($value)
 * @mixin \Eloquent
 */
class SupportTicket extends Model
{
    /** @use HasFactory<\Database\Factories\SupportTicketFactory> */
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
     *
     * @return array<string, string>
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
    /** @return BelongsTo<User, SupportTicket> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachments for the support ticket.
     */
    /** @return HasMany<SupportTicketAttachment, SupportTicket> */
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
