<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\HelpdeskTicketObserver;
use App\Traits\HasAuditTrail;
use App\Traits\OptimizedQueries;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
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
    use HasFactory;
    use OptimizedQueries;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',

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
        'category_id',
        'priority',
        'subject',
        'description',
        'status',
        'damage_type',

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

        // Notes
        'admin_notes',
        'internal_notes',
        'resolution_notes',
    ];

    protected $casts = [
        'sla_response_due_at' => 'datetime',
        'sla_resolution_due_at' => 'datetime',
        'responded_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'assigned_at' => 'datetime',
    ];

    // HYBRID SUPPORT - Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'assigned_to_division');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(HelpdeskComment::class);
    }

    public function latestComment(): HasOne
    {
        return $this->hasOne(HelpdeskComment::class)->latestOfMany();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(HelpdeskAttachment::class);
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(PortalActivity::class, 'subject')->latest();
    }

    /**
     * Internal staff-only comments
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
}
