<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Security Incident Log Model
 *
 * PKS CSIRT Integration (Requirement 28) - Incident Response Logging
 *
 * Records all actions taken during incident response for audit trail.
 *
 * @property int $id
 * @property int $security_incident_id
 * @property int|null $user_id
 * @property string $action_type
 * @property string $description
 * @property array<string, mixed>|null $metadata
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read SecurityIncident $incident
 * @property-read User|null $user
 *
 * @see D03-FR-028.3 (Incident log retention)
 *
 * @trace Requirements 28.3
 */
class SecurityIncidentLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'security_incident_id',
        'user_id',
        'action_type',
        'description',
        'metadata',
        'ip_address',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    // Action type constants
    public const ACTION_CREATED = 'created';

    public const ACTION_STATUS_CHANGED = 'status_changed';

    public const ACTION_ASSIGNED = 'assigned';

    public const ACTION_ESCALATED = 'escalated';

    public const ACTION_CSIRT_NOTIFIED = 'csirt_notified';

    public const ACTION_NACSA_REPORTED = 'nacsa_reported';

    public const ACTION_MYCERT_REPORTED = 'mycert_reported';

    public const ACTION_CONTAINED = 'contained';

    public const ACTION_RESPONSE_ADDED = 'response_added';

    public const ACTION_RESOLVED = 'resolved';

    public const ACTION_CLOSED = 'closed';

    public const ACTION_COMMENT_ADDED = 'comment_added';

    public const ACTION_EVIDENCE_ADDED = 'evidence_added';

    /**
     * Parent incident
     *
     * @return BelongsTo<SecurityIncident, self>
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(SecurityIncident::class, 'security_incident_id');
    }

    /**
     * User who performed the action
     *
     * @return BelongsTo<User, self>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a log entry
     *
     * @param  array<string, mixed>|null  $metadata
     */
    public static function log(
        int $incidentId,
        string $actionType,
        string $description,
        ?array $metadata = null,
        ?int $userId = null
    ): self {
        return self::create([
            'security_incident_id' => $incidentId,
            'user_id' => $userId ?? auth()->id(),
            'action_type' => $actionType,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Get action type labels in Bahasa Melayu
     *
     * @return array<string, string>
     */
    public static function getActionTypes(): array
    {
        return [
            self::ACTION_CREATED => 'Insiden Dicipta',
            self::ACTION_STATUS_CHANGED => 'Status Dikemaskini',
            self::ACTION_ASSIGNED => 'Ditugaskan',
            self::ACTION_ESCALATED => 'Dieskalasi',
            self::ACTION_CSIRT_NOTIFIED => 'CSIRT Dimaklumkan',
            self::ACTION_NACSA_REPORTED => 'Dilaporkan ke NACSA',
            self::ACTION_MYCERT_REPORTED => 'Dilaporkan ke MyCERT',
            self::ACTION_CONTAINED => 'Dibendung',
            self::ACTION_RESPONSE_ADDED => 'Tindakan Ditambah',
            self::ACTION_RESOLVED => 'Diselesaikan',
            self::ACTION_CLOSED => 'Ditutup',
            self::ACTION_COMMENT_ADDED => 'Komen Ditambah',
            self::ACTION_EVIDENCE_ADDED => 'Bukti Ditambah',
        ];
    }
}
