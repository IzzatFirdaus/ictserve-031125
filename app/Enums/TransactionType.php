<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Transaction Type Enum
 *
 * Defines types of loan transactions for audit trail.
 *
 * @see D03-FR-010.2 Comprehensive audit logging
 * @see D04 §2.3 Enum definitions
 */
enum TransactionType: string
{
    case ISSUE = 'issue';
    case RETURN = 'return';
    case EXTEND = 'extend';
    case RECALL = 'recall';

    /**
     * Get bilingual label for transaction type
     */
    public function label(): string
    {
        $key = match ($this) {
            self::ISSUE => 'loan.transaction.issue',
            self::RETURN => 'loan.transaction.return',
            self::EXTEND => 'loan.transaction.extend',
            self::RECALL => 'loan.transaction.recall',
        };
        
        return trans($key);
    }

    /**
     * Get icon for transaction type
     */
    public function icon(): string
    {
        return match ($this) {
            self::ISSUE => 'heroicon-o-arrow-right-circle',
            self::RETURN => 'heroicon-o-arrow-left-circle',
            self::EXTEND => 'heroicon-o-clock',
            self::RECALL => 'heroicon-o-exclamation-circle',
        };
    }

    /**
     * Get WCAG 2.2 AA compliant color for transaction type
     */
    public function color(): string
    {
        return match ($this) {
            self::ISSUE => 'blue',
            self::RETURN => 'green',
            self::EXTEND => 'orange',
            self::RECALL => 'red',
        };
    }
}
