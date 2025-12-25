<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Security Incident Model
 *
 * PKS CSIRT Integration (Requirement 28) - Security Incident Management
 *
 * Records all security incidents for CSIRT coordination, NACSA/MyCERT reporting,
 * and 7-year retention compliance.
 *
 * @property int $id
 * @property string $incident_number
 * @property string $type
 * @property string $severity
 * @property string $status
 * @property string $title
 * @property string $description
 * @property int|null $detected_by_user_id
 * @property int|null $assigned_to_user_id
 * @property string|null $source_ip
 * @property string|null $target_system
 * @property array<string, mixed>|null $affected_assets
 * @property array<string, mixed>|null $detection_rules_triggered
 * @property array<string, mixed>|null $indicators_of_compromise
 * @property \Illuminate\Support\Carbon $detected_at
 * @property \Illuminate\Support\Carbon|null $escalated_at
 * @property \Illuminate\Support\Carbon|null $csirt_notified_at
 * @property \Illuminate\Support\Carbon|null $nacsa_reported_at
 * @property \Illuminate\Support\Carbon|null $mycert_reported_at
 * @property \Illuminate\Support\Carbon|null $contained_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string|null $nacsa_report_id
 * @property string|null $mycert_report_id
 * @property string|null $resolution_summary
 * @property string|null $lessons_learned
 * @property array<string, mixed>|null $timeline
 * @property array<string, mixed>|null $response_actions
 * @property bool $requires_escalation
 * @property bool $is_false_positive
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User|null $detectedByUser
 * @property-read User|null $assignedToUser
 *
 * @see D03-FR-028 (CSIRT Integration)
 *
 * @trace Requirements 28.1, 28.2, 28.3, 28.4, 28.5
 */
class SecurityIncident extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_number',
        'type',
        'severity',
        'status',
        'title',
        'description',
        'detected_by_user_id',
        'assigned_to_user_id',
        'source_ip',
        'target_system',
        'affected_assets',
        'detection_rules_triggered',
        'indicators_of_compromise',
        'detected_at',
        'escalated_at',
        'csirt_notified_at',
        'nacsa_reported_at',
        'mycert_reported_at',
        'contained_at',
        'resolved_at',
        'closed_at',
        'nacsa_report_id',
        'mycert_report_id',
        'resolution_summary',
        'lessons_learned',
        'timeline',
        'response_actions',
        'requires_escalation',
        'is_false_positive',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'affected_assets' => 'array',
            'detection_rules_triggered' => 'array',
            'indicators_of_compromise' => 'array',
            'timeline' => 'array',
            'response_actions' => 'array',
            'detected_at' => 'datetime',
            'escalated_at' => 'datetime',
            'csirt_notified_at' => 'datetime',
            'nacsa_reported_at' => 'datetime',
            'mycert_reported_at' => 'datetime',
            'contained_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'requires_escalation' => 'boolean',
            'is_false_positive' => 'boolean',
        ];
    }

    // Incident type constants (PKS 28.1)
    public const TYPE_UNAUTHORIZED_ACCESS = 'unauthorized_access';

    public const TYPE_DATA_BREACH = 'data_breach';

    public const TYPE_MALWARE = 'malware';

    public const TYPE_BRUTE_FORCE = 'brute_force';

    public const TYPE_PHISHING = 'phishing';

    public const TYPE_DOS_ATTACK = 'dos_attack';

    public const TYPE_PRIVILEGE_ESCALATION = 'privilege_escalation';

    public const TYPE_DATA_EXFILTRATION = 'data_exfiltration';

    public const TYPE_ANOMALY = 'anomaly';

    public const TYPE_POLICY_VIOLATION = 'policy_violation';

    public const TYPE_OTHER = 'other';

    // Severity constants (PKS 28.5)
    public const SEVERITY_CRITICAL = 'critical';

    public const SEVERITY_HIGH = 'high';

    public const SEVERITY_MEDIUM = 'medium';

    public const SEVERITY_LOW = 'low';

    public const SEVERITY_INFO = 'info';

    // Status constants
    public const STATUS_DETECTED = 'detected';

    public const STATUS_INVESTIGATING = 'investigating';

    public const STATUS_ESCALATED = 'escalated';

    public const STATUS_CONTAINED = 'contained';

    public const STATUS_ERADICATING = 'eradicating';

    public const STATUS_RECOVERING = 'recovering';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_FALSE_POSITIVE = 'false_positive';

    // CSIRT escalation SLA in minutes (PKS 28.4)
    public const CSIRT_ESCALATION_SLA_MINUTES = 15;

    /**
     * User who detected the incident
     *
     * @return BelongsTo<User, self>
     */
    public function detectedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'detected_by_user_id');
    }

    /**
     * User assigned to handle the incident
     *
     * @return BelongsTo<User, self>
     */
    public function assignedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Incident response logs
     *
     * @return HasMany<SecurityIncidentLog, self>
     */
    public function logs(): HasMany
    {
        return $this->hasMany(SecurityIncidentLog::class);
    }

    /**
     * Generate unique incident number
     */
    public static function generateIncidentNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;

        return 'SEC'.$year.str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new security incident
     *
     * @param  array<string, mixed>  $data
     */
    public static function createIncident(array $data): self
    {
        $incident = self::create([
            'incident_number' => self::generateIncidentNumber(),
            'type' => $data['type'] ?? self::TYPE_OTHER,
            'severity' => $data['severity'] ?? self::SEVERITY_MEDIUM,
            'status' => self::STATUS_DETECTED,
            'title' => $data['title'] ?? 'Security Incident Detected',
            'description' => $data['description'] ?? '',
            'detected_by_user_id' => $data['detected_by_user_id'] ?? auth()->id(),
            'source_ip' => $data['source_ip'] ?? request()->ip(),
            'target_system' => $data['target_system'] ?? null,
            'affected_assets' => $data['affected_assets'] ?? null,
            'detection_rules_triggered' => $data['detection_rules_triggered'] ?? null,
            'indicators_of_compromise' => $data['indicators_of_compromise'] ?? null,
            'detected_at' => now(),
            'requires_escalation' => in_array($data['severity'] ?? self::SEVERITY_MEDIUM, [
                self::SEVERITY_CRITICAL,
                self::SEVERITY_HIGH,
            ]),
            'timeline' => [
                [
                    'timestamp' => now()->toIso8601String(),
                    'action' => 'Incident detected',
                    'user_id' => auth()->id(),
                    'details' => $data['description'] ?? '',
                ],
            ],
        ]);

        return $incident;
    }

    /**
     * Add timeline entry
     *
     * @param  array<string, mixed>  $entry
     */
    public function addTimelineEntry(string $action, ?string $details = null, ?int $userId = null): void
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = [
            'timestamp' => now()->toIso8601String(),
            'action' => $action,
            'user_id' => $userId ?? auth()->id(),
            'details' => $details,
        ];
        $this->update(['timeline' => $timeline]);
    }

    /**
     * Add response action
     *
     * @param  array<string, mixed>  $action
     */
    public function addResponseAction(string $actionType, string $description, ?int $userId = null): void
    {
        $actions = $this->response_actions ?? [];
        $actions[] = [
            'timestamp' => now()->toIso8601String(),
            'action_type' => $actionType,
            'description' => $description,
            'performed_by' => $userId ?? auth()->id(),
        ];
        $this->update(['response_actions' => $actions]);
    }

    /**
     * Escalate to CSIRT MOTAC (PKS 28.4)
     */
    public function escalateToCSIRT(): void
    {
        $this->update([
            'status' => self::STATUS_ESCALATED,
            'escalated_at' => now(),
            'csirt_notified_at' => now(),
        ]);
        $this->addTimelineEntry('Escalated to CSIRT MOTAC', 'Incident escalated per PKS 28.4 requirements');
    }

    /**
     * Check if CSIRT escalation SLA is breached
     */
    public function isCSIRTSLABreached(): bool
    {
        if (! $this->requires_escalation) {
            return false;
        }

        if ($this->csirt_notified_at !== null) {
            return false;
        }

        return $this->detected_at->addMinutes(self::CSIRT_ESCALATION_SLA_MINUTES)->isPast();
    }

    /**
     * Get time remaining for CSIRT escalation SLA
     */
    public function getCSIRTSLATimeRemaining(): ?int
    {
        if (! $this->requires_escalation || $this->csirt_notified_at !== null) {
            return null;
        }

        $deadline = $this->detected_at->addMinutes(self::CSIRT_ESCALATION_SLA_MINUTES);

        return (int) max(0, now()->diffInMinutes($deadline, false));
    }

    /**
     * Mark as reported to NACSA (PKS 28.2)
     */
    public function markNACSAReported(string $reportId): void
    {
        $this->update([
            'nacsa_reported_at' => now(),
            'nacsa_report_id' => $reportId,
        ]);
        $this->addTimelineEntry('Reported to NACSA', "Report ID: {$reportId}");
    }

    /**
     * Mark as reported to MyCERT (PKS 28.2)
     */
    public function markMyCERTReported(string $reportId): void
    {
        $this->update([
            'mycert_reported_at' => now(),
            'mycert_report_id' => $reportId,
        ]);
        $this->addTimelineEntry('Reported to MyCERT', "Report ID: {$reportId}");
    }

    /**
     * Mark as contained
     */
    public function markContained(?string $details = null): void
    {
        $this->update([
            'status' => self::STATUS_CONTAINED,
            'contained_at' => now(),
        ]);
        $this->addTimelineEntry('Incident contained', $details);
    }

    /**
     * Mark as resolved
     */
    public function markResolved(string $summary, ?string $lessonsLearned = null): void
    {
        $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
            'resolution_summary' => $summary,
            'lessons_learned' => $lessonsLearned,
        ]);
        $this->addTimelineEntry('Incident resolved', $summary);
    }

    /**
     * Close incident
     */
    public function close(): void
    {
        $this->update([
            'status' => self::STATUS_CLOSED,
            'closed_at' => now(),
        ]);
        $this->addTimelineEntry('Incident closed');
    }

    /**
     * Mark as false positive
     */
    public function markFalsePositive(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_FALSE_POSITIVE,
            'is_false_positive' => true,
            'resolution_summary' => "False positive: {$reason}",
            'resolved_at' => now(),
            'closed_at' => now(),
        ]);
        $this->addTimelineEntry('Marked as false positive', $reason);
    }

    /**
     * Scope: Critical and high severity
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeCriticalOrHigh(Builder $query): Builder
    {
        return $query->whereIn('severity', [self::SEVERITY_CRITICAL, self::SEVERITY_HIGH]);
    }

    /**
     * Scope: Requires escalation
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeRequiresEscalation(Builder $query): Builder
    {
        return $query->where('requires_escalation', true)
            ->whereNull('csirt_notified_at');
    }

    /**
     * Scope: Open incidents
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED,
            self::STATUS_FALSE_POSITIVE,
        ]);
    }

    /**
     * Scope: By type
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Within date range
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('detected_at', [$from, $to]);
    }

    /**
     * Get summary statistics
     *
     * @return array<string, mixed>
     */
    public static function getSummaryStats(string $from, string $to): array
    {
        $query = self::query()->whereBetween('detected_at', [$from, $to]);

        return [
            'total_incidents' => (clone $query)->count(),
            'open_incidents' => (clone $query)->open()->count(),
            'critical_count' => (clone $query)->where('severity', self::SEVERITY_CRITICAL)->count(),
            'high_count' => (clone $query)->where('severity', self::SEVERITY_HIGH)->count(),
            'escalated_count' => (clone $query)->whereNotNull('escalated_at')->count(),
            'nacsa_reported' => (clone $query)->whereNotNull('nacsa_reported_at')->count(),
            'mycert_reported' => (clone $query)->whereNotNull('mycert_reported_at')->count(),
            'false_positives' => (clone $query)->where('is_false_positive', true)->count(),
            'avg_resolution_time_hours' => (clone $query)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, detected_at, resolved_at)) as avg_hours')
                ->value('avg_hours') ?? 0,
            'by_type' => (clone $query)
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'by_severity' => (clone $query)
                ->selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity')
                ->toArray(),
        ];
    }

    /**
     * Get all incident types
     *
     * @return array<string, string>
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_UNAUTHORIZED_ACCESS => 'Akses Tanpa Kebenaran',
            self::TYPE_DATA_BREACH => 'Pelanggaran Data',
            self::TYPE_MALWARE => 'Perisian Hasad',
            self::TYPE_BRUTE_FORCE => 'Serangan Brute Force',
            self::TYPE_PHISHING => 'Phishing',
            self::TYPE_DOS_ATTACK => 'Serangan DoS/DDoS',
            self::TYPE_PRIVILEGE_ESCALATION => 'Peningkatan Keistimewaan',
            self::TYPE_DATA_EXFILTRATION => 'Eksfiltrasi Data',
            self::TYPE_ANOMALY => 'Anomali',
            self::TYPE_POLICY_VIOLATION => 'Pelanggaran Polisi',
            self::TYPE_OTHER => 'Lain-lain',
        ];
    }

    /**
     * Get all severity levels
     *
     * @return array<string, string>
     */
    public static function getSeverities(): array
    {
        return [
            self::SEVERITY_CRITICAL => 'Kritikal',
            self::SEVERITY_HIGH => 'Tinggi',
            self::SEVERITY_MEDIUM => 'Sederhana',
            self::SEVERITY_LOW => 'Rendah',
            self::SEVERITY_INFO => 'Maklumat',
        ];
    }

    /**
     * Get all statuses
     *
     * @return array<string, string>
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_DETECTED => 'Dikesan',
            self::STATUS_INVESTIGATING => 'Dalam Siasatan',
            self::STATUS_ESCALATED => 'Dieskalasi',
            self::STATUS_CONTAINED => 'Dibendung',
            self::STATUS_ERADICATING => 'Dalam Pembasmian',
            self::STATUS_RECOVERING => 'Dalam Pemulihan',
            self::STATUS_RESOLVED => 'Diselesaikan',
            self::STATUS_CLOSED => 'Ditutup',
            self::STATUS_FALSE_POSITIVE => 'Positif Palsu',
        ];
    }
}
