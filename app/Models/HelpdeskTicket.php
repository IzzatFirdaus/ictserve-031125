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

/**
 * HelpdeskTicket Model - Enhanced with Hybrid Architecture Support
 *
 * Supports both guest submissions (no user_id) and authenticated submissions (with user_id).
 * Integrates with asset loan system for cross-module functionality.
 *
 * @see D03 Software Requirements Specification - Requirement 1, 2
 * @see D04 Software Design Document - Hybrid Architecture
 * @see D09 Database Documentation - helpdesk_tickets table
 *
 * @property string|null $guest_email
 */
#[ObservedBy([HelpdeskTicketObserver::class])]
class HelpdeskTicket extends Model implements Auditable
{
    use HasAuditTrail;

    /** @use HasFactory<\Database\Factories\HelpdeskTicketFactory> */
    use HasFactory;

    use OptimizedQueries;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'status_token_hash', // v3.5.0 True Hybrid - SHA-512 hash for guest status checking
        'form_reference_code', // v3.5.0 - Official form code PK.(S).MOTAC.07.(L1)

        // Enhanced guest submission fields for hybrid architecture
        'guest_name',
        'guest_email',
        'guest_phone',
        'guest_staff_id',
        'guest_grade',
        'guest_division',

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
    public function getActivitylogOptions(): \Spatie\Activitylog\LogOptions
    {
        return \Spatie\Activitylog\LogOptions::defaults()
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

    // HYBRID SUPPORT - Relationships
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

    // HYBRID SUPPORT - Helper methods

    /**
     * Check if this is a guest submission (no user_id)
     */
    public function isGuestSubmission(): bool
    {
        return is_null($this->user_id);
    }

    /**
     * Check if this is an authenticated submission (has user_id)
     */
    public function isAuthenticatedSubmission(): bool
    {
        return ! is_null($this->user_id);
    }

    /**
     * Get submitter name (guest or authenticated)
     */
    public function getSubmitterName(): string
    {
        return $this->isGuestSubmission() ? $this->guest_name : $this->user->name;
    }

    /**
     * Get submitter email (guest or authenticated)
     */
    public function getSubmitterEmail(): string
    {
        return $this->isGuestSubmission() ? $this->guest_email : $this->user->email;
    }

    /**
     * Get submitter identifier for tracking
     */
    public function getSubmitterIdentifier(): string
    {
        return $this->isGuestSubmission()
            ? "guest:{$this->guest_email}"
            : "user:{$this->user_id}";
    }

    /**
     * Get submitter grade (guest or authenticated)
     */
    public function getSubmitterGrade(): ?string
    {
        return $this->isGuestSubmission() ? $this->guest_grade : $this->user->grade;
    }

    /**
     * Get submitter division (guest or authenticated)
     */
    public function getSubmitterDivision(): ?string
    {
        return $this->isGuestSubmission() ? $this->guest_division : $this->user->division;
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

    /**
     * Check if ticket can be claimed by authenticated user
     */
    public function canBeClaimedBy(User $user): bool
    {
        return $this->isGuestSubmission() &&
            $this->guest_email === $user->email;
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

    // v3.5.0 True Hybrid Architecture - Query Scopes

    /**
     * Scope to filter tickets for a specific user (authenticated submissions)
     *
     * @param  Builder<HelpdeskTicket>  $query
     * @return Builder<HelpdeskTicket>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to find ticket by status token hash
     *
     * @param  Builder<HelpdeskTicket>  $query
     * @return Builder<HelpdeskTicket>
     */
    public function scopeByStatusToken(Builder $query, string $tokenHash): Builder
    {
        return $query->where('status_token_hash', $tokenHash);
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

    // v3.5.0 True Hybrid Architecture - Token Methods

    /**
     * Generate and set status token hash (SHA-512)
     */
    public function generateStatusToken(): string
    {
        $token = bin2hex(random_bytes(32)); // 64 character token
        $this->status_token_hash = hash('sha512', $token);
        $this->save();

        return $token; // Return plain token for sending to user
    }

    /**
     * Verify status token
     */
    public static function findByStatusToken(string $token): ?self
    {
        $hash = hash('sha512', $token);

        return static::where('status_token_hash', $hash)->first();
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
