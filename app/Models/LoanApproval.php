<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * LoanApproval Model - v3.5.0 True Hybrid Architecture
 * 
 * Records approval decisions for loan applications with audit trail.
 * Supports email-based approval workflow with token validation.
 *
 * @see D03 Software Requirements Specification - Requirement 4.3
 * @see D04 Software Design Document - Approval Service
 * @see D09 Database Documentation - loan_approvals table
 * @property int $id
 * @property int $loan_application_id
 * @property string $approver_email
 * @property string $approver_grade
 * @property string $decision (APPROVED, REJECTED)
 * @property string|null $remarks
 * @property \Carbon\Carbon $decision_at
 * @property string $decision_ip_hash
 * @property string $token_hash
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Activitylog\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\LoanApplication $loanApplication
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereApproverEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereApproverGrade($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereDecision($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereDecisionAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereDecisionIpHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereLoanApplicationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereTokenHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LoanApproval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LoanApproval extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanApprovalFactory> */
    use HasFactory;

    use \OwenIt\Auditing\Auditable;
    use \Spatie\Activitylog\Traits\LogsActivity;

    protected $fillable = [
        'loan_application_id',
        'approver_email',
        'approver_grade',
        'decision',
        'remarks',
        'decision_at',
        'decision_ip_hash',
        'token_hash',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'decision_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /** @var array<int, string> */
    protected $auditInclude = [
        'loan_application_id',
        'approver_email',
        'approver_grade',
        'decision',
        'decision_at',
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
                'loan_application_id',
                'approver_email',
                'decision',
                'decision_at',
            ])
            ->logOnlyDirty()
            ->useLogName('loan')
            ->setDescriptionForEvent(fn (string $eventName) => "Loan approval {$eventName}");
    }

    // Relationships

    /**
     * Get the loan application this approval belongs to
     *
     * @return BelongsTo<LoanApplication, LoanApproval>
     */
    public function loanApplication(): BelongsTo
    {
        return $this->belongsTo(LoanApplication::class);
    }

    // Helper Methods

    /**
     * Check if decision is approved
     */
    public function isApproved(): bool
    {
        return $this->decision === 'APPROVED';
    }

    /**
     * Check if decision is rejected
     */
    public function isRejected(): bool
    {
        return $this->decision === 'REJECTED';
    }

    /**
     * Get decision label
     */
    public function getDecisionLabel(): string
    {
        return match ($this->decision) {
            'APPROVED' => __('loan.approval.approved'),
            'REJECTED' => __('loan.approval.rejected'),
            default => __('loan.approval.unknown'),
        };
    }
}
