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
 * Model ApprovalEmailToken untuk token kelulusan e-mel
 * 
 * Menyokong aliran kerja kelulusan auto-reply melalui e-mel
 * Mengintegrasikan Dual Audit System (owen-it + spatie)
 *
 * @property int $id
 * @property int $auto_reply_draft_id ID draf auto-reply
 * @property string $token Token kelulusan selamat
 * @property string $action Tindakan: approve atau reject
 * @property \Carbon\Carbon $expires_at Masa tamat tempoh
 * @property bool $used Status penggunaan token
 * @property \Carbon\Carbon|null $used_at Masa token digunakan
 * @property string|null $used_by_ip IP address pengguna
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\AutoReplyDraft $autoReplyDraft
 * @property-read string $action_label
 * @property-read bool $is_expired
 * @property-read bool $is_valid
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken byAction(string $action)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken byToken(string $token)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken expired()
 * @method static \Database\Factories\ApprovalEmailTokenFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken unused()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken used()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken valid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereAutoReplyDraftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApprovalEmailToken whereUsedByIp($value)
 * @mixin \Eloquent
 */
class ApprovalEmailToken extends Model implements AuditableContract
{
    use Auditable;
    use HasFactory; // owen-it untuk compliance audit
    use LogsActivity; // spatie untuk operational logging

    /**
     * Nama jadual pangkalan data
     */
    protected $table = 'approval_email_tokens';

    /**
     * Atribut yang boleh diisi secara massal
     */
    protected $fillable = [
        'auto_reply_draft_id',
        'token',
        'action',
        'expires_at',
        'used',
        'used_at',
        'used_by_ip',
    ];

    /**
     * Casting atribut ke jenis data yang sesuai
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    /**
     * Atribut yang perlu dilog untuk activity logging (spatie)
     */
    protected static $logAttributes = [
        'auto_reply_draft_id',
        'token',
        'action',
        'used',
        'used_at',
        'used_by_ip',
    ];

    /**
     * Nama log untuk activity logging
     */
    protected static $logName = 'approval_email_token';

    /**
     * Log hanya perubahan atribut yang kotor
     */
    protected static $logOnlyDirty = true;

    /**
     * Hubungan dengan model AutoReplyDraft
     */
    public function autoReplyDraft(): BelongsTo
    {
        return $this->belongsTo(AutoReplyDraft::class);
    }

    /**
     * Scope untuk token yang belum digunakan
     */
    public function scopeUnused($query)
    {
        return $query->where('used', false);
    }

    /**
     * Scope untuk token yang sudah digunakan
     */
    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    /**
     * Scope untuk token yang masih sah (belum tamat tempoh)
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
            ->where('used', false);
    }

    /**
     * Scope untuk token yang sudah tamat tempoh
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope untuk mencari mengikut token
     */
    public function scopeByToken($query, string $token)
    {
        return $query->where('token', $token);
    }

    /**
     * Scope untuk mencari mengikut tindakan
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Accessor untuk memeriksa sama ada token masih sah
     */
    public function getIsValidAttribute(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    /**
     * Accessor untuk memeriksa sama ada token sudah tamat tempoh
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Accessor untuk mendapatkan label tindakan dalam Bahasa Melayu
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'approve' => 'Luluskan',
            'reject' => 'Tolak',
            default => ucfirst($this->action),
        };
    }

    /**
     * Kaedah untuk menggunakan token
     */
    public function use(?string $ipAddress = null): bool
    {
        if (! $this->is_valid) {
            return false;
        }

        $this->update([
            'used' => true,
            'used_at' => now(),
            'used_by_ip' => $ipAddress,
        ]);

        return true;
    }

    /**
     * Kaedah statik untuk menjana token selamat
     */
    public static function generateSecureToken(): string
    {
        return hash('sha256', uniqid('approval_', true).microtime().random_bytes(32));
    }

    /**
     * Kaedah statik untuk mencipta token kelulusan
     */
    public static function createForDraft(
        AutoReplyDraft $draft,
        string $action,
        int $validityDays = 7
    ): self {
        return self::create([
            'auto_reply_draft_id' => $draft->id,
            'token' => self::generateSecureToken(),
            'action' => $action,
            'expires_at' => now()->addDays($validityDays),
        ]);
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
                'auto_reply_draft_id',
                'token',
                'action',
                'used',
                'used_at',
                'used_by_ip',
            ])
            ->logOnlyDirty()
            ->useLogName('approval_email_token')
            ->setDescriptionForEvent(fn (string $eventName) => "Approval email token {$eventName}");
    }
}
