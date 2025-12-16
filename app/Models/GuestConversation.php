<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Model GuestConversation untuk sejarah perbualan tetamu
 * 
 * Menyokong Account Linking untuk True Hybrid Architecture
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property string $session_id ID sesi tetamu
 * @property string|null $email E-mel tetamu (opsyen)
 * @property array $conversation_history Sejarah perbualan
 * @property int|null $claimed_by_user_id Pengguna yang menuntut
 * @property \Carbon\Carbon|null $claimed_at Masa dituntut
 * @property \Carbon\Carbon $expires_at Masa tamat tempoh
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $claimedByUser
 * @property-read bool $is_active
 * @property-read bool $is_claimed
 * @property-read int $message_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation byEmail(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation bySession(string $sessionId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation claimed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation expired()
 * @method static \Database\Factories\GuestConversationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation unclaimed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereClaimedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereClaimedByUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereConversationHistory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|GuestConversation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GuestConversation extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory; // owen-it untuk compliance audit
    use LogsActivity; // spatie untuk operational logging

    /**
     * Nama jadual pangkalan data
     */
    protected $table = 'guest_conversations';

    /**
     * Atribut yang boleh diisi secara massal
     */
    protected $fillable = [
        'session_id',
        'email',
        'conversation_history',
        'claimed_by_user_id',
        'claimed_at',
        'expires_at',
    ];

    /**
     * Casting atribut ke jenis data yang sesuai
     */
    protected function casts(): array
    {
        return [
            'conversation_history' => 'array',
            'claimed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Atribut yang perlu dilog untuk activity logging (spatie)
     */
    protected static $logAttributes = [
        'session_id',
        'email',
        'claimed_by_user_id',
        'claimed_at',
    ];

    /**
     * Nama log untuk activity logging
     */
    protected static $logName = 'guest_conversation';

    /**
     * Log hanya perubahan atribut yang kotor
     */
    protected static $logOnlyDirty = true;

    /**
     * Hubungan dengan model User yang menuntut perbualan
     */
    public function claimedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by_user_id')->withDefault();
    }

    /**
     * Scope untuk perbualan yang belum dituntut
     */
    public function scopeUnclaimed($query)
    {
        return $query->whereNull('claimed_by_user_id');
    }

    /**
     * Scope untuk perbualan yang sudah dituntut
     */
    public function scopeClaimed($query)
    {
        return $query->whereNotNull('claimed_by_user_id');
    }

    /**
     * Scope untuk perbualan yang masih aktif (belum tamat tempoh)
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope untuk perbualan yang sudah tamat tempoh
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope untuk mencari mengikut e-mel
     */
    public function scopeByEmail($query, string $email)
    {
        return $query->where('email', $email);
    }

    /**
     * Scope untuk mencari mengikut sesi
     */
    public function scopeBySession($query, string $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Accessor untuk memeriksa sama ada perbualan sudah dituntut
     */
    public function getIsClaimedAttribute(): bool
    {
        return ! is_null($this->claimed_by_user_id);
    }

    /**
     * Accessor untuk memeriksa sama ada perbualan masih aktif
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->expires_at->isFuture();
    }

    /**
     * Accessor untuk mendapatkan bilangan mesej dalam perbualan
     */
    public function getMessageCountAttribute(): int
    {
        return count($this->conversation_history ?? []);
    }

    /**
     * Kaedah untuk menuntut perbualan oleh pengguna authenticated
     */
    public function claimByUser(User $user): bool
    {
        if ($this->is_claimed || $this->expires_at->isPast()) {
            return false;
        }

        $this->update([
            'claimed_by_user_id' => $user->id,
            'claimed_at' => now(),
        ]);

        return true;
    }

    /**
     * Kaedah untuk menambah mesej ke sejarah perbualan
     */
    public function addMessage(string $role, string $content): void
    {
        $history = $this->conversation_history ?? [];
        $history[] = [
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toISOString(),
        ];

        $this->update(['conversation_history' => $history]);
    }

    /**
     * Kaedah untuk melanjutkan masa tamat tempoh (30 minit dari sekarang)
     */
    public function extendExpiry(): void
    {
        $this->update(['expires_at' => now()->addMinutes(30)]);
    }

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
            ->logOnly([
                'session_id',
                'email',
                'claimed_by_user_id',
                'claimed_at',
            ])
            ->logOnlyDirty()
            ->useLogName('guest_conversation')
            ->setDescriptionForEvent(fn (string $eventName) => "Guest conversation {$eventName}");
    }
}
