<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DRP Test Result Model
 *
 * PKS Business Continuity (Requirement 29) - DRP Test Result Tracking
 *
 * Tracks all DRP test results including:
 * - Tabletop exercises
 * - Walkthrough tests
 * - Simulation tests
 * - Full failover tests
 *
 * @property int $id
 * @property string $test_id
 * @property string $test_type
 * @property \Carbon\Carbon $test_date
 * @property int $conducted_by
 * @property string $status
 * @property int|null $rto_achieved_minutes
 * @property float|null $rpo_achieved_hours
 * @property array|null $participants
 * @property array|null $findings
 * @property array|null $recommendations
 * @property array|null $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.5
 */
class DrpTestResult extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'test_id',
        'test_type',
        'test_date',
        'conducted_by',
        'status',
        'rto_achieved_minutes',
        'rpo_achieved_hours',
        'participants',
        'findings',
        'recommendations',
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
            'test_date' => 'datetime',
            'participants' => 'array',
            'findings' => 'array',
            'recommendations' => 'array',
            'metadata' => 'array',
            'rto_achieved_minutes' => 'integer',
            'rpo_achieved_hours' => 'float',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the user who conducted the test.
     */
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    /**
     * Get test types with Bahasa Melayu labels.
     *
     * @return array<string, string>
     */
    public static function getTestTypes(): array
    {
        return [
            'tabletop' => 'Ujian Meja',
            'walkthrough' => 'Ujian Walkthrough',
            'simulation' => 'Ujian Simulasi',
            'full' => 'Ujian Penuh',
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
            'passed' => 'Lulus',
            'failed' => 'Gagal',
            'partial' => 'Separa Lulus',
        ];
    }

    /**
     * Scope for passed tests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePassed($query)
    {
        return $query->where('status', 'passed');
    }

    /**
     * Scope for failed tests.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for tests by type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('test_type', $type);
    }

    /**
     * Scope for tests in a specific year.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInYear($query, int $year)
    {
        return $query->whereYear('test_date', $year);
    }

    /**
     * Get the test type label in Bahasa Melayu.
     */
    public function getTestTypeLabelAttribute(): string
    {
        return self::getTestTypes()[$this->test_type] ?? $this->test_type;
    }

    /**
     * Get the status label in Bahasa Melayu.
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Check if RTO target was met (4 hours = 240 minutes).
     */
    public function isRtoCompliant(): bool
    {
        if ($this->rto_achieved_minutes === null) {
            return false;
        }

        return $this->rto_achieved_minutes <= 240; // 4 hours in minutes
    }

    /**
     * Check if RPO target was met (24 hours).
     */
    public function isRpoCompliant(): bool
    {
        if ($this->rpo_achieved_hours === null) {
            return false;
        }

        return $this->rpo_achieved_hours <= 24;
    }

    /**
     * Check if test is fully PKS compliant.
     */
    public function isPksCompliant(): bool
    {
        return $this->status === 'passed' && $this->isRtoCompliant() && $this->isRpoCompliant();
    }
}
