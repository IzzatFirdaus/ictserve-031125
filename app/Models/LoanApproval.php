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
 *
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
 */
class LoanApproval extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\LoanApprovalFactory> */
    use HasFactory;

    // TODO: Add LogsActivity trait when spatie/laravel-activitylog is installed
    // use Spatie\Activitylog\Traits\LogsActivity;
    use \OwenIt\Auditing\Auditable;

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
     */
    protected static $logAttributes = [
        'loan_application_id',
        'approver_email',
        'decision',
        'decision_at',
    ];

    protected static $logName = 'loan_approval';

    protected static $logOnlyDirty = true;

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
