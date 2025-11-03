<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Loan Priority Enum
 *
 * Defines priority levels for loan applications.
 *
 * @see D04 §2.3 Enum definitions
 */
enum LoanPriority: string
{
    case LOW = 'low';
    case NORMAL = 'normal';
    case HIGH = 'high';
    case URGENT = 'urgent';

    /**
     * Get bilingual label for priority
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => __('loan.priority.low'),
            self::NORMAL => __('loan.priority.normal'),
            self::HIGH => __('loan.priority.high'),
            self::URGENT => __('loan.priority.urgent'),
        };
    }

    /**
     * Get WCAG 2.2 AA compliant color for priority
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'gray',
            self::NORMAL => 'blue',
            self::HIGH => 'orange',
            self::URGENT => 'red',
        };
    }

    /**
     * Get sort order for priority (higher number = higher priority)
     */
    public function sortOrder(): int
    {
        return match ($this) {
            self::LOW => 1,
            self::NORMAL => 2,
            self::HIGH => 3,
            self::URGENT => 4,
        };
    }
}
