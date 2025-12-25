<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\HelpdeskTicketObserver;
use App\Traits\HasAuditTrail;
use App\Traits\OptimizedQueries;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * HelpdeskTicket Model - PKS 5.2.1 Compliant (SSO-Only Architecture)
 *
 * Supports ONLY authenticated submissions with mandatory user_id per PKS 5.2.1.
 * Integrates with asset loan system for cross-module functionality.
 * NO GUEST ACCESS - All submissions require SSO authentication.
 *
 * @see D03 Software Requirements Specification - Requirement 1, 8, 25
 * @see D04 Software Design Document - PKS-Compliant SSO-Only Architecture
 * @see D09 Database Documentation - helpdesk_tickets table
 *
 * @property int $id
 * @property string $ticket_number
 * @property string $form_reference_code Official MOTAC form reference code
 * @property int $user_id MANDATORY - NOT NULL per PKS 5.2.1
 * @property string|null $job_grade
 * @property string|null $staff_id
 * @property int|null $division_id
 * @property int $category_id
 * @property string $priority
 * @property string $subject
 * @property string $description
 * @property string|null $damage_type
 * @property bool $declaration_accepted
 * @property string|null $source
 * @property string|null $internal_notes
 * @property string $status
 * @property int|null $assigned_to_division
 * @property string|null $assigned_to_agency
 * @property int|null $assigned_to_user
 * @property int|null $asset_id
 * @property int|null $related_loan_application_id
 * @property \Illuminate\Support\Carbon|null $sla_response_due_at
 * @property \Illuminate\Support\Carbon|null $sla_resolution_due_at
 * @property \Illuminate\Support\Carbon|null $responded_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $first_response_at
 * @property int $escalation_level
 * @property \Illuminate\Support\Carbon|null $escalation_notified_at
 * @property \Illuminate\Support\Carbon|null $sla_breached_at
 * @property string|null $sla_breach_type
 * @property \Illuminate\Support\Carbon|null $sla_paused_at
 * @property string|null $sla_pause_reason
 * @property int $sla_total_paused_hours
 * @property string|null $closure_reason
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon|null $assigned_at
 * @property string|null $admin_notes
 * @property string|null $resolution_notes
 * @property string|null $anonymized_at
 * @property string|null $claimed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $sla_due_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortalActivity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Asset|null $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LoanApplication> $assetLoanApplications
 * @property-read int|null $asset_loan_applications_count
 * @property-read \App\Models\Division|null $assignedDivision
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskAttachment> $attachments
 * @property-read int|null $attachments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $auditTrail
 * @property-read int|null $audit_trail_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\TicketCategory $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\HelpdeskComment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CrossModuleIntegration> $crossModuleIntegrations
 * @property-read int|null $cross_module_integrations_count
 * @property-read \App\Models\Division|null $division
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\InternalComment> $internalComments
 * @property-read int|null $internal_comments_count
 * @property-read \App\Models\HelpdeskComment|null $latestComment
 * @property-read \App\Models\Asset|null $relatedAsset
 * @property-read \App\Models\User|null $user
 *
 * @method static Builder<static>|HelpdeskTicket bySLA(string $slaStatus)
 * @method static \Database\Factories\HelpdeskTicketFactory factory($count = null, $state = [])
 * @method static Builder<static>|HelpdeskTicket forUser(\App\Models\User $user)
 * @method static Builder<static>|HelpdeskTicket newModelQuery()
 * @method static Builder<static>|HelpdeskTicket newQuery()
 * @method static Builder<static>|HelpdeskTicket onlyTrashed()
 * @method static Builder<static>|HelpdeskTicket optimizedCount()
 * @method static Builder<static>|HelpdeskTicket optimizedPagination(int $perPage = 25)
 * @method static Builder<static>|HelpdeskTicket query()
 * @method static Builder<static>|HelpdeskTicket whereAdminNotes($value)
 * @method static Builder<static>|HelpdeskTicket whereAnonymizedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereAssetId($value)
 * @method static Builder<static>|HelpdeskTicket whereAssignedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereAssignedToAgency($value)
 * @method static Builder<static>|HelpdeskTicket whereAssignedToDivision($value)
 * @method static Builder<static>|HelpdeskTicket whereAssignedToUser($value)
 * @method static Builder<static>|HelpdeskTicket whereCategoryId($value)
 * @method static Builder<static>|HelpdeskTicket whereClaimedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereClosedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereClosureReason($value)
 * @method static Builder<static>|HelpdeskTicket whereCreatedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereDamageType($value)
 * @method static Builder<static>|HelpdeskTicket whereDeclarationAccepted($value)
 * @method static Builder<static>|HelpdeskTicket whereDeletedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereDescription($value)
 * @method static Builder<static>|HelpdeskTicket whereDivisionId($value)
 * @method static Builder<static>|HelpdeskTicket whereEscalationLevel($value)
 * @method static Builder<static>|HelpdeskTicket whereEscalationNotifiedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereFirstResponseAt($value)
 * @method static Builder<static>|HelpdeskTicket whereFormReferenceCode($value)
 * @method static Builder<static>|HelpdeskTicket whereId($value)
 * @method static Builder<static>|HelpdeskTicket whereInternalNotes($value)
 * @method static Builder<static>|HelpdeskTicket whereJobGrade($value)
 * @method static Builder<static>|HelpdeskTicket wherePriority($value)
 * @method static Builder<static>|HelpdeskTicket whereRelatedLoanApplicationId($value)
 * @method static Builder<static>|HelpdeskTicket whereResolutionNotes($value)
 * @method static Builder<static>|HelpdeskTicket whereResolvedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereRespondedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaBreachType($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaBreachedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaDueAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaPauseReason($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaPausedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaResolutionDueAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaResponseDueAt($value)
 * @method static Builder<static>|HelpdeskTicket whereSlaTotalPausedHours($value)
 * @method static Builder<static>|HelpdeskTicket whereSource($value)
 * @method static Builder<static>|HelpdeskTicket whereStaffId($value)
 * @method static Builder<static>|HelpdeskTicket whereStatus($value)
 * @method static Builder<static>|HelpdeskTicket whereSubject($value)
 * @method static Builder<static>|HelpdeskTicket whereTicketNumber($value)
 * @method static Builder<static>|HelpdeskTicket whereUpdatedAt($value)
 * @method static Builder<static>|HelpdeskTicket whereUserId($value)
 * @method static Builder<static>|HelpdeskTicket withCommonRelations()
 * @method static Builder<static>|HelpdeskTicket withTrashed(bool $withTrashed = true)
 * @method static Builder<static>|HelpdeskTicket withoutTrashed()
 *
 * @mixin \Eloquent
 */
#[ObservedBy([HelpdeskTicketObserver::class])]
class HelpdeskTicket extends Model implements Auditable
{
    use HasAuditTrail;

    /** @use HasFactory<\Database\Factories\HelpdeskTicketFactory> */
    use HasFactory;

    use LogsActivity;
    use OptimizedQueries;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id', // MANDATORY - NOT NULL per PKS 5.2.1
        'form_reference_code', // Official form code PK.(S).MOTAC.07.(L1)

        // Ticket details
        'staff_id',
        'division_id',
        'job_grade',
        'category_id',
        'priority',
        'subject',
        'description',
        'status',
        'damage_type',
        'declaration_accepted',

        // Assignment fields
        'assigned_to_division',
        'assigned_to_agency',
        'assigned_to_user',

        // Cross-module integration
        'asset_id',
        'related_loan_application_id',

        // SLA tracking
        'sla_due_at',
        'sla_response_due_at',
        'sla_resolution_due_at',
        'responded_at',
        'resolved_at',
        'closed_at',
        'assigned_at',
        'first_response_at',

        // Advanced SLA tracking
        'escalation_level',
        'escalation_notified_at',
        'sla_breached_at',
        'sla_breach_type',
        'sla_paused_at',
        'sla_pause_reason',
        'sla_total_paused_hours',
        'closure_reason',

        // Notes
        'admin_notes',
        'internal_notes',
        'resolution_notes',

        // Source tracking
        'source',
    ];

    /**
     * Spatie Activity Log configuration
     *
     * @see D09 §4.7 - Activity Log Requirements
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'ticket_number',
                'status',
                'priority',
                'category_id',
                'assigned_to_user',
                'resolved_at',
                'closed_at',
            ])
            ->logOnlyDirty()
            ->useLogName('helpdesk')
            ->setDescriptionForEvent(fn (string $eventName) => "Helpdesk ticket {$eventName}");
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sla_due_at' => 'datetime',
            'sla_response_due_at' => 'datetime',
            'sla_resolution_due_at' => 'datetime',
            'responded_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'assigned_at' => 'datetime',
            'first_response_at' => 'datetime',
            'escalation_notified_at' => 'datetime',
            'sla_breached_at' => 'datetime',
            'sla_paused_at' => 'datetime',
            'sla_total_paused_hours' => 'integer',
            'escalation_level' => 'integer',
            'declaration_accepted' => 'boolean',
        ];
    }

    public function setPriorityAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['priority'] = null;

            return;
        }

        $this->attributes['priority'] = strtolower($value);
    }

    // PKS 5.2.1 COMPLIANT - Relationships
    /** @return BelongsTo<User, HelpdeskTicket> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Division, HelpdeskTicket> */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /** @return BelongsTo<TicketCategory, HelpdeskTicket> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /** @return BelongsTo<Asset, HelpdeskTicket> */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /** @return BelongsTo<Division, HelpdeskTicket> */
    public function assignedDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'assigned_to_division');
    }

    /** @return BelongsTo<User, HelpdeskTicket> */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user');
    }

    /** @return HasMany<HelpdeskComment, HelpdeskTicket> */
    public function comments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class);
    }

    /** @return HasOne<HelpdeskComment, HelpdeskTicket> */
    public function latestComment(): HasOne
    {
        return $this->hasOne(HelpdeskComment::class)->latestOfMany();
    }

    /** @return HasMany<HelpdeskAttachment, HelpdeskTicket> */
    public function attachments(): HasMany
    {
        return $this->hasMany(HelpdeskAttachment::class);
    }

    /** @return MorphMany<PortalActivity, HelpdeskTicket> */
    public function activities(): MorphMany
    {
        return $this->morphMany(PortalActivity::class, 'subject')->latest();
    }

    /**
     * Internal staff-only comments
     *
     * @return MorphMany<InternalComment, HelpdeskTicket>
     */
    public function internalComments(): MorphMany
    {
        return $this->morphMany(InternalComment::class, 'commentable')->latest();
    }

    /**
     * Cross-module integration: Related asset
     */
    public function relatedAsset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * Cross-module integration: Asset loan applications through asset
     */
    public function assetLoanApplications(): HasManyThrough
    {
        return $this->hasManyThrough(
            LoanApplication::class,
            Asset::class,
            'id',           // Foreign key on assets table
            'asset_id',     // Foreign key on loan_applications table
            'asset_id',     // Local key on helpdesk_tickets table
            'id'            // Local key on assets table
        );
    }

    /**
     * Cross-module integration records
     */
    public function crossModuleIntegrations(): HasMany
    {
        return $this->hasMany(CrossModuleIntegration::class, 'helpdesk_ticket_id');
    }

    // PKS 5.2.1 COMPLIANT - Helper methods

    /**
     * Get submitter name (authenticated user only)
     */
    public function getSubmitterName(): string
    {
        return $this->user->name;
    }

    /**
     * Get submitter email (authenticated user only)
     */
    public function getSubmitterEmail(): string
    {
        return $this->user->email;
    }

    /**
     * Get submitter identifier for tracking (authenticated user only)
     */
    public function getSubmitterIdentifier(): string
    {
        return "user:{$this->user_id}";
    }

    /**
     * Get submitter grade (authenticated user only)
     */
    public function getSubmitterGrade(): ?string
    {
        return $this->user->grade;
    }

    /**
     * Get submitter division (authenticated user only)
     */
    public function getSubmitterDivision(): ?string
    {
        return $this->user->division;
    }

    // CROSS-MODULE HELPER METHODS

    /**
     * Check if ticket has related asset
     */
    public function hasRelatedAsset(): bool
    {
        return ! is_null($this->asset_id);
    }

    /**
     * Check if this is a maintenance ticket
     */
    public function isMaintenanceTicket(): bool
    {
        return $this->category_id &&
            $this->category->name === 'maintenance' &&
            $this->hasRelatedAsset();
    }

    // UTILITY METHODS

    /**
     * Generate ticket number in format HD[YYYY][000001-999999]
     */
    public static function generateTicketNumber(): string
    {
        $year = now()->year;

        // Always query database to avoid race conditions
        $sequence = static::whereYear('created_at', $year)
            ->count() + 1;

        return 'HD'.$year.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate SLA due dates based on category
     */
    public function calculateSLADueDates(): void
    {
        if ($this->category) {
            $this->sla_response_due_at = now()->addHours($this->category->sla_response_hours);
            $this->sla_resolution_due_at = now()->addHours($this->category->sla_resolution_hours);
            $this->save();
        }
    }

    /**
     * Get eager load relationships for query optimization
     */
    protected function getEagerLoadRelationships(): array
    {
        return [
            'user',
            'assignedUser',
            'division',
            'category',
            'relatedAsset',
            'comments',
            'attachments',
        ];
    }

    // PKS 5.2.1 COMPLIANT - Query Scopes

    /**
     * Scope to filter tickets for a specific user (authenticated submissions only)
     *
     * @param  Builder<HelpdeskTicket>  $query
     * @return Builder<HelpdeskTicket>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to filter tickets by SLA status
     *
     * @param  Builder<HelpdeskTicket>  $query
     * @return Builder<HelpdeskTicket>
     */
    public function scopeBySLA(Builder $query, string $slaStatus): Builder
    {
        $now = now();

        return match ($slaStatus) {
            'breached' => $query->where('sla_breached_at', '!=', null),
            'at_risk' => $query->whereNull('sla_breached_at')
                ->where(function (Builder $q) use ($now): void {
                    $q->where('sla_response_due_at', '<=', $now->copy()->addHours(2))
                        ->orWhere('sla_resolution_due_at', '<=', $now->copy()->addHours(4));
                }),
            'on_track' => $query->whereNull('sla_breached_at')
                ->where('sla_response_due_at', '>', $now->copy()->addHours(2))
                ->where('sla_resolution_due_at', '>', $now->copy()->addHours(4)),
            default => $query,
        };
    }

    /**
     * Generate ticket number in HD-YYYYMM-XXXX format
     */
    public static function generateTicketNumberV3(): string
    {
        $yearMonth = now()->format('Ym');
        $sequence = static::whereRaw("ticket_number LIKE 'HD-{$yearMonth}-%'")
            ->count() + 1;

        return sprintf('HD-%s-%04d', $yearMonth, $sequence);
    }
}
