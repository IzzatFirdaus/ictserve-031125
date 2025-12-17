<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Loan Status Enum with Cross-Module Integration
 *
 * Defines all possible states for loan applications with WCAG compliant color mapping.
 *
 * @see D03-FR-001.5 WCAG 2.2 AA compliance
 * @see D03-FR-016.1 Cross-module integration
 * @see D04 §2.3 Enum definitions
 */
enum LoanStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case UNDER_REVIEW = 'under_review';
    case PENDING_APPROVAL = 'pending_approval';
    case PENDING_INFO = 'pending_info';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case READY_ISSUANCE = 'ready_issuance';
    case ISSUED = 'issued';
    case IN_USE = 'in_use';
    case RETURN_DUE = 'return_due';
    case RETURNING = 'returning';
    case RETURNED = 'returned';
    case COMPLETED = 'completed';
    case OVERDUE = 'overdue';
    case MAINTENANCE_REQUIRED = 'maintenance_required';

    /**
     * Get bilingual label for status
     */
    public function label(): string
    {
        $key = match ($this) {
            self::DRAFT => 'loan.status.draft',
            self::SUBMITTED => 'loan.status.submitted',
            self::UNDER_REVIEW => 'loan.status.under_review',
            self::PENDING_APPROVAL => 'loan.status.pending_approval',
            self::PENDING_INFO => 'loan.status.pending_info',
            self::APPROVED => 'loan.status.approved',
            self::REJECTED => 'loan.status.rejected',
            self::READY_ISSUANCE => 'loan.status.ready_issuance',
            self::ISSUED => 'loan.status.issued',
            self::IN_USE => 'loan.status.in_use',
            self::RETURN_DUE => 'loan.status.return_due',
            self::RETURNING => 'loan.status.returning',
            self::RETURNED => 'loan.status.returned',
            self::COMPLETED => 'loan.status.completed',
            self::OVERDUE => 'loan.status.overdue',
            self::MAINTENANCE_REQUIRED => 'loan.status.maintenance_required',
        };

        return trans($key);
    }

    /**
     * Get WCAG 2.2 AA compliant color for status
     *
     * @see D03-FR-015.2 Compliant color palette
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::UNDER_REVIEW => 'yellow',
            self::PENDING_APPROVAL => 'yellow',
            self::PENDING_INFO => 'orange',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::READY_ISSUANCE => 'purple',
            self::ISSUED => 'indigo',
            self::IN_USE => 'teal',
            self::RETURN_DUE => 'amber',
            self::RETURNING => 'lime',
            self::RETURNED => 'emerald',
            self::COMPLETED => 'green',
            self::OVERDUE => 'red',
            self::MAINTENANCE_REQUIRED => 'orange',
        };
    }

    /**
     * Check if status requires helpdesk integration
     *
     * @see D03-FR-016.1 Cross-module integration
     */
    public function requiresHelpdeskIntegration(): bool
    {
        return in_array($this, [
            self::MAINTENANCE_REQUIRED,
            self::RETURNED,
            self::OVERDUE,
        ]);
    }

    /**
     * Check if status is terminal (no further changes expected)
     */
    public function isTerminal(): bool
    {
        return in_array($this, [
            self::COMPLETED,
            self::REJECTED,
        ]);
    }

    /**
     * Check if status is active (loan in progress)
     */
    public function isActive(): bool
    {
        return in_array($this, [
            self::ISSUED,
            self::IN_USE,
            self::RETURN_DUE,
            self::RETURNING,
        ]);
    }

    /**
     * Get all statuses that require admin action
     *
     * @return array<self>
     */
    public static function requiresAdminAction(): array
    {
        return [
            self::APPROVED,
            self::PENDING_APPROVAL,
            self::READY_ISSUANCE,
            self::RETURNING,
            self::MAINTENANCE_REQUIRED,
        ];
    }
}
