<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Failover Event Model
 *
 * PKS Business Continuity (Requirement 29) - Failover Event Tracking
 *
 * Tracks all failover events including:
 * - Automated failovers
 * - Manual failovers
 * - Failover tests
 * - Failback events
 *
 * @property int $id
 * @property string $event_id
 * @property string $type
 * @property string $status
 * @property int|null $triggered_by
 * @property string|null $reason
 * @property \Carbon\Carbon|null $started_at
 * @property \Carbon\Carbon|null $completed_at
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.3, 29.4
 */
class FailoverEvent extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'type',
        'status',
        'triggered_by',
        'reason',
        'started_at',
        'completed_at',
        'metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who triggered the event.
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    /**
     * Get event types with Bahasa Melayu labels.
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            'automated' => 'Automatik',
            'manual' => 'Manual',
            'test' => 'Ujian',
            'failback' => 'Failback',
        ];
    }

    /**
     * Get statuses with Bahasa Melayu labels.
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Berjalan',
            'completed' => 'Selesai',
            'failed' => 'Gagal',
            'rolled_back' => 'Dikembalikan',
            'passed' => 'Lulus',
        ];
    }

    /**
     * Scope for automated failovers.
     */
    public function scopeAutomated($query)
    {
        return $query->where('type', 'automated');
    }

    /**
     * Scope for manual failovers.
     */
    public function scopeManual($query)
    {
        return $query->where('type', 'manual');
    }

    /**
     * Scope for tests.
     */
    public function scopeTests($query)
    {
        return $query->where('type', 'test');
    }

    /**
     * Scope for successful events.
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['completed', 'passed']);
    }

    /**
     * Get the type label in Bahasa Melayu.
     */
    public function getTypeLabelAttribute(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }

    /**
     * Get the status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get duration in seconds.
     */
    public function getDurationSecondsAttribute(): ?int
    {
        if (! $this->started_at || ! $this->completed_at) {
            return null;
        }

        return $this->completed_at->diffInSeconds($this->started_at);
    }
}
