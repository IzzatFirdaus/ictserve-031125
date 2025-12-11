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
        $translation = match ($this) {
            self::PENDING => __('loan.approval_status.pending'),
            self::APPROVED => __('loan.approval_status.approved'),
            self::REJECTED => __('loan.approval_status.rejected'),
        };

        return is_string($translation) ? $translation : $this->value;
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
