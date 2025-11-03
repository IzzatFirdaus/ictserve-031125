<?php

declare(strict_types=1);

namespace App\Traits;

use OwenIt\Auditing\Contracts\Auditable;

/**
 * HasAuditTrail Trait
 *
 * Provides comprehensive audit trail functionality for models.
 * Integrates with Laravel Auditing package for 7-year retention compliance.
 *
 * @see D09 Database Documentation - Audit Requirements
 * @see D10 Source Code Documentation - Audit Trail Standards
 */
trait HasAuditTrail
{
    /**
     * Boot the trait and configure auditing
     */
    public static function bootHasAuditTrail(): void
    {
        // Ensure the model implements Auditable interface
        if (! in_array(Auditable::class, class_implements(static::class))) {
            throw new \RuntimeException(
                'Model must implement OwenIt\Auditing\Contracts\Auditable interface'
            );
        }
    }

    /**
     * Get the audit trail for this model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function auditTrail()
    {
        return $this->morphMany(\OwenIt\Auditing\Models\Audit::class, 'auditable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get recent audit entries
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentAuditTrail(int $limit = 10)
    {
        return $this->auditTrail()->limit($limit)->get();
    }

    /**
     * Get audit trail for a specific date range
     *
     * @param  \Carbon\Carbon  $startDate
     * @param  \Carbon\Carbon  $endDate
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditTrailForDateRange($startDate, $endDate)
    {
        return $this->auditTrail()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    /**
     * Get audit trail by event type
     *
     * @param  string  $event  (created, updated, deleted, restored)
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAuditTrailByEvent(string $event)
    {
        return $this->auditTrail()
            ->where('event', $event)
            ->get();
    }

    /**
     * Check if model has been audited
     */
    public function hasAuditTrail(): bool
    {
        return $this->auditTrail()->exists();
    }

    /**
     * Get the user who created this record
     *
     * @return \App\Models\User|null
     */
    public function getCreatedByUser()
    {
        $audit = $this->auditTrail()
            ->where('event', 'created')
            ->first();

        return $audit ? $audit->user : null;
    }

    /**
     * Get the user who last updated this record
     *
     * @return \App\Models\User|null
     */
    public function getLastUpdatedByUser()
    {
        $audit = $this->auditTrail()
            ->where('event', 'updated')
            ->first();

        return $audit ? $audit->user : null;
    }
}
