<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

use function random_int;

/**
 * Enhanced Loan Application Model with ICTServe Integration
 *
 * Supports hybrid architecture (guest + authenticated), email-based approval workflows,
 * and cross-module integration with helpdesk system.
 *
 * @see D03-FR-001.2 Hybrid architecture support
 * @see D03-FR-002.1 Email approval workflow
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §2.2 Model relationships
 *
 * @property int $id
 * @property string $application_number
 * @property int|null $user_id
 * @property string $applicant_name
 * @property string $applicant_email
 * @property string $applicant_phone
 * @property string $staff_id
 * @property string $grade
 * @property int $division_id
 * @property string $purpose
 * @property string $location
 * @property string $return_location
 * @property \Carbon\Carbon $loan_start_date
 * @property \Carbon\Carbon $loan_end_date
 * @property LoanStatus $status
 * @property LoanPriority $priority
 * @property float $total_value
 * @property string|null $approver_email
 * @property string|null $approved_by_name
 * @property \Carbon\Carbon|null $approved_at
 * @property string|null $approval_token
 * @property \Carbon\Carbon|null $approval_token_expires_at
 * @property string|null $approval_method
 * @property string|null $approval_remarks
 * @property string|null $rejected_reason
 * @property string|null $special_instructions
 * @property array<string, mixed>|null $related_helpdesk_tickets
 * @property bool $maintenance_required
 */
class LoanApplication extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanApplicationFactory> */
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use \Spatie\Activitylog\Traits\LogsActivity;

    /**
     * Flag used by factories to skip automatic loan item creation without persisting to the database.
     */
    public bool $skipLoanItemsCreation = false;

    protected $fillable = [
        'application_number',
        'user_id',
        'approval_token_hash', // v3.5.0 True Hybrid - SHA-512 hash for approval workflow
        'status_token_hash', // v3.5.0 True Hybrid - SHA-512 hash for guest status checking
        'form_reference_code', // v3.5.0 - Official form code PK.(S).MOTAC.07.(L3)
        // Guest applicant fields (always populated)
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'staff_id',
        'grade',
        'division_id',
        // Bahagian 1: Extended applicant info
        'applicant_position',
        'applicant_grade',
        'purpose',
        'location',
        'loan_start_date',
        'expected_return_date',
        // Bahagian 2: Responsible officer (conditional)
        'is_applicant_responsible',
        'responsible_officer_name',
        'responsible_officer_position',
        'responsible_officer_grade',
        'responsible_officer_phone',
        'responsible_officer_email',
        'responsible_officer_acknowledged_at',
        'sponsorship_token',
        'sponsorship_token_expires_at',
        // Delegation workflow (Task 1.1.6)
        'is_delegate',
        'responsible_officer_details',
        // Bahagian 4: Applicant declaration
        'applicant_declaration_date',
        'applicant_digital_signature',
        'terms_acknowledged',
        'declared_at',
        // Bahagian 5: Approval workflow
        'approver_id',
        'approved_by',
        'approval_status',
        'approval_date',
        'approver_digital_signature',
        'approval_notes',
        // Application details (existing)
        'return_location',
        'loan_end_date',
        'status',
        'priority',
        'total_value',
        // Email approval workflow (legacy)
        'approver_email',
        'approved_by_name',
        'approved_at',
        'approval_token',
        'approval_token_expires_at',
        'approval_method',
        'approval_remarks',
        'rejected_reason',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'special_instructions',
        // OTP Handshake
        'pickup_otp_hash',
        'pickup_otp_expires_at',
        'pickup_otp_attempts',
        'pickup_otp_generated_at',
        'pickup_otp_validated_at',
        'pickup_otp_validated_by',
        // Cross-module integration
        'related_helpdesk_tickets',
        'maintenance_required',
        'accessories',
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
                'application_number',
                'status',
                'priority',
                'approver_email',
                'approved_at',
                'rejected_reason',
            ])
            ->logOnlyDirty()
            ->useLogName('loan')
            ->setDescriptionForEvent(fn (string $eventName) => "Loan application {$eventName}");
    }

    protected $casts = [
        'loan_start_date' => 'date',
        'loan_end_date' => 'date',
        'expected_return_date' => 'date',
        'approved_at' => 'datetime',
        'approval_token_expires_at' => 'datetime',
        'applicant_declaration_date' => 'datetime',
        'approval_date' => 'datetime',
        'approval_method' => 'string',
        'approval_remarks' => 'string',
        'status' => LoanStatus::class,
        'priority' => LoanPriority::class,
        'total_value' => 'decimal:2',
        'related_helpdesk_tickets' => 'array',
        'maintenance_required' => 'boolean',
        'accessories' => 'array',
        'is_applicant_responsible' => 'boolean',
        'is_delegate' => 'boolean',
        'responsible_officer_details' => 'array',
        'terms_acknowledged' => 'boolean',
        'responsible_officer_acknowledged_at' => 'datetime',
        'sponsorship_token_expires_at' => 'datetime',
        'pickup_otp_expires_at' => 'datetime',
        'pickup_otp_generated_at' => 'datetime',
        'pickup_otp_validated_at' => 'datetime',
        'declared_at' => 'datetime',
    ];

    public function setStatusAttribute(null|string|LoanStatus $value): void
    {
        if ($value === null) {
            $this->attributes['status'] = null;

            return;
        }

        if ($value instanceof LoanStatus) {
            $this->attributes['status'] = $value->value;

            return;
        }

        $normalized = strtolower(trim($value));
        $normalized = str_replace(['-', ' '], '_', $normalized);

        if ($normalized === 'on_loan') {
            $normalized = LoanStatus::IN_USE->value;
        }

        $this->attributes['status'] = $normalized;
    }

    /** @var array<string, string> */
    protected $auditInclude = [
        'application_number',
        'user_id',
        'applicant_name',
        'applicant_email',
        'staff_id',
        'grade',
        'division_id',
        'loan_end_date',
        'status',
        'priority',
        'total_value',
        'approver_email',
        'approved_by_name',
        'approved_at',
        'rejected_reason',
        'special_instructions',
    ];

    // ICTServe Integration Relationships
    /** @return BelongsTo<User, LoanApplication> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Division, LoanApplication> */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /** @return BelongsTo<User, LoanApplication> */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /** @return HasMany<LoanItem, LoanApplication> */
    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    /**
     * Get the first asset through loan items (for backward compatibility with search)
     */
    /** @return HasOneThrough<Asset, LoanItem, LoanApplication> */
    public function asset(): HasOneThrough
    {
        return $this->hasOneThrough(
            Asset::class,
            LoanItem::class,
            'loan_application_id', // Foreign key on loan_items table
            'id',                   // Foreign key on assets table
            'id',                   // Local key on loan_applications table
            'asset_id'              // Local key on loan_items table
        )->select('assets.*');  // Explicitly select from assets to avoid ambiguous id
    }

    /**
     * Get all assets through loan items
     */
    /** @return HasManyThrough<Asset, LoanItem, LoanApplication> */
    public function assets(): HasManyThrough
    {
        return $this->hasManyThrough(
            Asset::class,
            LoanItem::class,
            'loan_application_id', // Foreign key on loan_items table
            'id',                   // Foreign key on assets table
            'id',                   // Local key on loan_applications table
            'asset_id'              // Local key on loan_items table
        );
    }

    /** @return MorphMany<PortalActivity, LoanApplication> */
    public function activities(): MorphMany
    {
        return $this->morphMany(PortalActivity::class, 'subject')->latest();
    }

    /**
     * Internal staff-only comments
     */
    /** @return MorphMany<InternalComment, LoanApplication> */
    public function internalComments(): MorphMany
    {
        return $this->morphMany(InternalComment::class, 'commentable')->latest();
    }

    /** @return HasMany<LoanItem, LoanApplication> */
    public function getItemsAttribute()
    {
        return $this->loanItems();
    }

    /** @return HasMany<LoanTransaction, LoanApplication> */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

    /** @return HasMany<HelpdeskTicket, LoanApplication> */
    public function helpdeskTickets(): HasMany
    {
        return $this->hasMany(HelpdeskTicket::class, 'related_loan_application_id');
    }

    // HYBRID SUPPORT - Helper methods
    public function isGuestSubmission(): bool
    {
        return $this->user_id === null;
    }

    public function isAuthenticatedSubmission(): bool
    {
        return $this->user_id !== null;
    }

    // DUAL APPROVAL - Token generation for email-based approval
    public function generateApprovalToken(): string
    {
        $this->approval_token = Str::random(64);
        $this->approval_token_expires_at = now()->addDays(7);
        $this->save();

        return $this->approval_token;
    }

    // DUAL APPROVAL - Token validation
    public function isTokenValid(string $token): bool
    {
        return $this->approval_token === $token
            && $this->approval_token_expires_at !== null
            && $this->approval_token_expires_at > now()
            && $this->status === LoanStatus::UNDER_REVIEW;
    }

    // Generate application number (LA[YYYY][MM][0001-9999])
    public static function generateApplicationNumber(): string
    {
        static $sequence = null;

        $year = now()->year;
        $month = now()->format('m');

        if ($sequence === null) {
            $sequence = static::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count() + 1;
        } else {
            $sequence++;
        }

        return sprintf('LA%s%s%04d', $year, $month, $sequence);
    }

    // Check if overdue
    public function isOverdue(): bool
    {
        return $this->status === LoanStatus::IN_USE
            && $this->loan_end_date < now()
            && $this->status !== LoanStatus::RETURNED;
    }

    // Check if requires helpdesk integration
    public function requiresHelpdeskIntegration(): bool
    {
        return $this->status->requiresHelpdeskIntegration();
    }

    // Calculate loan duration in days
    public function getLoanDurationDays(): int
    {
        return (int) $this->loan_start_date->diffInDays($this->loan_end_date);
    }

    /**
     * Calculate total value from loan items
     *
     * @return float Total value of all loan items
     */
    public function calculateTotalValue(): float
    {
        return (float) ($this->loanItems()
            ->join('assets', 'loan_items.asset_id', '=', 'assets.id')
            ->sum('assets.current_value') ?? 0.0);
    }

    // SPONSORSHIP WORKFLOW
    public function isSponsorshipAcknowledged(): bool
    {
        return $this->responsible_officer_acknowledged_at !== null;
    }

    public function requiresSponsorship(): bool
    {
        return ! $this->is_applicant_responsible;
    }

    // OTP HANDSHAKE
    public function isOtpValid(string $otp): bool
    {
        if ($this->pickup_otp_expires_at < now()) {
            return false;
        }

        return \Illuminate\Support\Facades\Hash::check($otp, $this->pickup_otp_hash);
    }

    public function incrementOtpAttempts(): void
    {
        $this->pickup_otp_attempts++;
        $this->save();
    }

    public function isOtpLocked(): bool
    {
        return $this->pickup_otp_attempts >= 3;
    }

    public function clearOtp(): void
    {
        $this->pickup_otp_hash = null;
        $this->pickup_otp_expires_at = null;
        $this->pickup_otp_attempts = 0;
        $this->pickup_otp_generated_at = null;
        $this->save();
    }

    public function generateOtp(): string
    {
        $otp = (string) random_int(100000, 999999);

        $this->pickup_otp_hash = \Illuminate\Support\Facades\Hash::make($otp);
        $this->pickup_otp_generated_at = now();
        $this->pickup_otp_expires_at = now()->addDays(3);
        $this->pickup_otp_attempts = 0;
        $this->save();

        return $otp;
    }

    // v3.5.0 True Hybrid Architecture - Query Scopes

    /**
     * Scope to filter applications for a specific user (authenticated submissions)
     *
     * @param  Builder<LoanApplication>  $query
     * @return Builder<LoanApplication>
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    /**
     * Scope to find application by approval token hash
     *
     * @param  Builder<LoanApplication>  $query
     * @return Builder<LoanApplication>
     */
    public function scopeByApprovalToken(Builder $query, string $tokenHash): Builder
    {
        return $query->where('approval_token_hash', $tokenHash)
            ->where('approval_token_expires_at', '>', now());
    }

    /**
     * Scope to find application by status token hash
     *
     * @param  Builder<LoanApplication>  $query
     * @return Builder<LoanApplication>
     */
    public function scopeByStatusToken(Builder $query, string $tokenHash): Builder
    {
        return $query->where('status_token_hash', $tokenHash);
    }

    // v3.5.0 True Hybrid Architecture - Token Methods

    /**
     * Generate and set approval token hash (SHA-512)
     */
    public function generateApprovalTokenV3(int $expiryHours = 72): string
    {
        $token = bin2hex(random_bytes(32)); // 64 character token
        $this->approval_token_hash = hash('sha512', $token);
        $this->approval_token_expires_at = now()->addHours($expiryHours);
        $this->save();

        return $token; // Return plain token for sending to approver
    }

    /**
     * Verify approval token
     */
    public static function findByApprovalToken(string $token): ?self
    {
        $hash = hash('sha512', $token);

        return static::where('approval_token_hash', $hash)
            ->where('approval_token_expires_at', '>', now())
            ->first();
    }

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
     * Generate application reference in LA-YYYYMM-XXXX format
     */
    public static function generateReferenceV3(): string
    {
        $yearMonth = now()->format('Ym');
        $sequence = static::whereRaw("application_number LIKE 'LA-{$yearMonth}-%'")
            ->count() + 1;

        return sprintf('LA-%s-%04d', $yearMonth, $sequence);
    }
}
