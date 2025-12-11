<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

/**
 * Model AutoReplyDraft untuk sistem AI Ollama
 *
 * Per Requirements 3.1, 3.2, 3.3, 3.4: Auto-reply draft management dengan approval workflow
 * Selaras dengan D09 Database Documentation v3.6.0 (Dual Audit System)
 *
 * @property int $id
 * @property string $replyable_type
 * @property int $replyable_id
 * @property string $draft_content
 * @property int|null $template_id
 * @property string $status
 * @property int $generated_by
 * @property int|null $approved_by
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model $replyable
 * @property-read \App\Models\AutoReplyTemplate|null $template
 * @property-read \App\Models\User $generator
 * @property-read \App\Models\User|null $approver
 */
class AutoReplyDraft extends Model implements AuditableContract
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * Draft statuses
     */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SENT = 'sent';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'replyable_type',
        'replyable_id',
        'draft_content',
        'template_id',
        'status',
        'generated_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Polymorphic relationship dengan tickets/loan applications
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function replyable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Hubungan dengan AutoReplyTemplate
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(AutoReplyTemplate::class, 'template_id');
    }

    /**
     * Hubungan dengan User yang menjana draft
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Hubungan dengan User yang meluluskan draft
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope untuk draft dengan status tertentu
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk draft yang menunggu semakan
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    /**
     * Scope untuk draft yang diluluskan
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope untuk draft yang ditolak
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    /**
     * Semak sama ada draft menunggu semakan
     *
     * @return bool
     */
    public function isPendingReview(): bool
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }

    /**
     * Semak sama ada draft diluluskan
     *
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Semak sama ada draft ditolak
     *
     * @return bool
     */
    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Semak sama ada draft telah dihantar
     *
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Hantar draft untuk semakan
     *
     * @return bool
     */
    public function submitForReview(): bool
    {
        return $this->update(['status' => self::STATUS_PENDING_REVIEW]);
    }

    /**
     * Luluskan draft
     *
     * @param \App\Models\User $approver
     * @return bool
     */
    public function approve(User $approver): bool
    {
        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Tolak draft
     *
     * @param \App\Models\User $approver
     * @param string $reason
     * @return bool
     */
    public function reject(User $approver, string $reason): bool
    {
        return $this->update([
            'status' => self::STATUS_REJECTED,
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Tandakan draft sebagai telah dihantar
     *
     * @return bool
     */
    public function markAsSent(): bool
    {
        return $this->update(['status' => self::STATUS_SENT]);
    }

    /**
     * Dapatkan preview kandungan draft (200 karakter pertama)
     *
     * @return string
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->draft_content) > 200
            ? substr($this->draft_content, 0, 200) . '...'
            : $this->draft_content;
    }

    /**
     * Dapatkan status badge color untuk UI
     *
     * @return string
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PENDING_REVIEW => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_SENT => 'blue',
            default => 'gray',
        };
    }
}
