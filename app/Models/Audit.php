<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Models\Audit as BaseAudit;

/**
 * Enhanced Audit Model for ICTServe Compliance
 *
 * Extends Laravel Auditing with 7-year retention policy,
 * immutable storage, and enhanced search capabilities.
 *
 * @see D03-FR-010.2 Audit logging system
 * @see D09 Database Documentation - Audit requirements
 * @see D11 Technical Design - Compliance standards
 *
 * @property int $id
 * @property string $user_type
 * @property int|null $user_id
 * @property string $event
 * @property string $auditable_type
 * @property int $auditable_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $url
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $tags
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Audit extends BaseAudit
{
    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Prevent updates to audit records (immutable)
     */
    public function save(array $options = []): bool
    {
        // Only allow creation, not updates
        if ($this->exists) {
            throw new \Exception('Audit records are immutable and cannot be updated.');
        }

        return parent::save($options);
    }

    /**
     * Prevent deletion of audit records
     */
    public function delete(): ?bool
    {
        throw new \Exception('Audit records cannot be deleted to maintain compliance.');
    }

    /**
     * Get the user that performed the action
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the auditable model
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange(Builder $query, Carbon $startDate, Carbon $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by event type
     */
    public function scopeByEvent(Builder $query, string $event): Builder
    {
        return $query->where('event', $event);
    }

    /**
     * Scope for filtering by auditable type
     */
    public function scopeByAuditableType(Builder $query, string $type): Builder
    {
        return $query->where('auditable_type', $type);
    }

    /**
     * Scope for security-related events
     */
    public function scopeSecurityEvents(Builder $query): Builder
    {
        return $query->whereIn('event', ['created', 'updated', 'deleted'])
            ->whereIn('auditable_type', [
                'App\\Models\\User',
                'App\\Models\\LoanApplication',
                'App\\Models\\HelpdeskTicket',
            ]);
    }

    /**
     * Scope for records older than retention period
     */
    public function scopeExpired(Builder $query): Builder
    {
        $retentionYears = config('audit.retention.years', 7);
        $cutoffDate = now()->subYears($retentionYears);

        return $query->where('created_at', '<', $cutoffDate);
    }

    /**
     * Get formatted user information
     */
    public function getUserInfoAttribute(): string
    {
        if ($this->user) {
            return "{$this->user->name} ({$this->user->email})";
        }

        return 'System';
    }

    /**
     * Get formatted changes summary
     */
    public function getChangesSummaryAttribute(): string
    {
        $changes = [];

        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[] = "{$key}: '{$oldValue}' → '{$newValue}'";
                }
            }
        } elseif ($this->new_values) {
            foreach ($this->new_values as $key => $value) {
                $changes[] = "{$key}: '{$value}'";
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Check if audit record is within retention period
     */
    public function isWithinRetentionPeriod(): bool
    {
        $retentionYears = config('audit.retention.years', 7);
        $cutoffDate = now()->subYears($retentionYears);

        return $this->created_at >= $cutoffDate;
    }

    /**
     * Get audit statistics
     */
    public static function getStatistics(): array
    {
        return [
            'total_records' => static::count(),
            'records_last_30_days' => static::where('created_at', '>=', now()->subDays(30))->count(),
            'security_events_last_30_days' => static::securityEvents()
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'oldest_record' => static::oldest('created_at')->first()?->created_at,
            'newest_record' => static::latest('created_at')->first()?->created_at,
            'retention_cutoff' => now()->subYears(config('audit.retention.years', 7)),
            'expired_records' => static::expired()->count(),
        ];
    }

    /**
     * Search audit records
     */
    public static function search(array $criteria): Builder
    {
        $query = static::query();

        if (isset($criteria['user_id'])) {
            $query->byUser($criteria['user_id']);
        }

        if (isset($criteria['event'])) {
            $query->byEvent($criteria['event']);
        }

        if (isset($criteria['auditable_type'])) {
            $query->byAuditableType($criteria['auditable_type']);
        }

        if (isset($criteria['start_date']) && isset($criteria['end_date'])) {
            $query->dateRange(
                Carbon::parse($criteria['start_date']),
                Carbon::parse($criteria['end_date'])
            );
        }

        if (isset($criteria['ip_address'])) {
            $query->where('ip_address', $criteria['ip_address']);
        }

        return $query->latest('created_at');
    }
}
