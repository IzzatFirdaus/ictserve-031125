<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Disaster Recovery Log Model
 *
 * PKS Business Continuity (Requirement 29) - DR Event Logging
 *
 * Tracks all DR-related events including:
 * - Health checks
 * - Failover tests
 * - Actual failovers
 * - Replication status changes
 *
 * @property int $id
 * @property string $event_id
 * @property string $event_type
 * @property int|null $user_id
 * @property string|null $reason
 * @property string $status
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.2, 29.3, 29.4
 */
class DisasterRecoveryLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',
        'event_type',
        'user_id',
        'reason',
        'status',
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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who triggered the event.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get event types with Bahasa Melayu labels.
     *
     * @return array<string, string>
     */
    public static function getEventTypes(): array
    {
        return [
            'health_check' => 'Semakan Kesihatan',
            'failover_test' => 'Ujian Failover',
            'failover_initiated' => 'Failover Dimulakan',
            'failover_completed' => 'Failover Selesai',
            'failback_initiated' => 'Failback Dimulakan',
            'failback_completed' => 'Failback Selesai',
            'replication_error' => 'Ralat Replikasi',
            'replication_restored' => 'Replikasi Dipulihkan',
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
            'healthy' => 'Sihat',
            'degraded' => 'Merosot',
            'failed' => 'Gagal',
            'syncing' => 'Menyegerak',
            'unknown' => 'Tidak Diketahui',
            'initiated' => 'Dimulakan',
            'completed' => 'Selesai',
            'passed' => 'Lulus',
        ];
    }

    /**
     * Scope for health checks.
     */
    public function scopeHealthChecks($query)
    {
        return $query->where('event_type', 'health_check');
    }

    /**
     * Scope for failover tests.
     */
    public function scopeFailoverTests($query)
    {
        return $query->where('event_type', 'failover_test');
    }

    /**
     * Scope for actual failovers.
     */
    public function scopeFailovers($query)
    {
        return $query->whereIn('event_type', ['failover_initiated', 'failover_completed']);
    }

    /**
     * Get the event type label in Bahasa Melayu.
     */
    public function getEventTypeLabelAttribute(): string
    {
        return self::getEventTypes()[$this->event_type] ?? $this->event_type;
    }

    /**
     * Get the status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }
}
