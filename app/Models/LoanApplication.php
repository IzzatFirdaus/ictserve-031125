<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\LoanPriority;
use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;

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
 * @property string|null $rejected_reason
 * @property string|null $special_instructions
 * @property array|null $related_helpdesk_tickets
 * @property bool $maintenance_required
 */
class LoanApplication extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $fillable = [
        'application_number',
        'user_id',
        // Guest applicant fields (always populated)
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'staff_id',
        'grade',
        'division_id',
        // Application details
        'purpose',
        'location',
        'return_location',
        'loan_start_date',
        'loan_end_date',
        'status',
        'priority',
        'total_value',
        // Email approval workflow
        'approver_email',
        'approved_by_name',
        'approved_at',
        'approval_token',
        'approval_token_expires_at',
        'rejected_reason',
        'special_instructions',
        // Cross-module integration
        'related_helpdesk_tickets',
        'maintenance_required',
    ];

    protected $casts = [
        'loan_start_date' => 'date',
        'loan_end_date' => 'date',
        'approved_at' => 'datetime',
        'approval_token_expires_at' => 'datetime',
        'status' => LoanStatus::class,
        'priority' => LoanPriority::class,
        'total_value' => 'decimal:2',
        'related_helpdesk_tickets' => 'array',
        'maintenance_required' => 'boolean',
    ];

    // ICTServe Integration Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function loanItems(): HasMany
    {
        return $this->hasMany(LoanItem::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(LoanTransaction::class);
    }

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
        $year = now()->year;
        $month = now()->format('m');
        $sequence = static::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

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
}
