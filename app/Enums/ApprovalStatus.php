<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Approval Status Enum
 *
 * Defines approval workflow states for loan applications (Bahagian 5)
 */
enum ApprovalStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Get bilingual label for approval status
     */
    public function label(): string
    {
        $key = match ($this) {
            self::PENDING => 'loan.approval_status.pending',
            self::APPROVED => 'loan.approval_status.approved',
            self::REJECTED => 'loan.approval_status.rejected',
        };
        
        return trans($key);
    }

    /**
     * Get color class for UI display (WCAG 2.2 AA compliant)
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
