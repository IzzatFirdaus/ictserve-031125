<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Helpdesk Ticket Status Enum
 *
 * Defines all possible states for helpdesk tickets with WCAG compliant color mapping.
 *
 * @see D03-FR-001.5 WCAG 2.2 AA compliance
 * @see D04 §2.3 Enum definitions
 */
enum HelpdeskTicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case PENDING_INFO = 'pending_info';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';

    /**
     * Get bilingual label for status (Bahasa Melayu)
     */
    public function label(): string
    {
        return match ($this) {
            self::OPEN => __('helpdesk.status.open'),
            self::IN_PROGRESS => __('helpdesk.status.in_progress'),
            self::PENDING_INFO => __('helpdesk.status.pending_info'),
            self::RESOLVED => __('helpdesk.status.resolved'),
            self::CLOSED => __('helpdesk.status.closed'),
            self::CANCELLED => __('helpdesk.status.cancelled'),
        };
    }

    /**
     * Get WCAG 2.2 AA compliant Filament color for status
     */
    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'info',
            self::IN_PROGRESS => 'warning',
            self::PENDING_INFO => 'gray',
            self::RESOLVED => 'success',
            self::CLOSED => 'gray',
            self::CANCELLED => 'danger',
        };
    }

    /**
     * Check if status is active (ticket still needs attention)
     */
    public function isActive(): bool
    {
        return \in_array($this, [
            self::OPEN,
            self::IN_PROGRESS,
            self::PENDING_INFO,
        ], true);
    }

    /**
     * Check if status is terminal (no further changes expected)
     */
    public function isTerminal(): bool
    {
        return \in_array($this, [
            self::RESOLVED,
            self::CLOSED,
            self::CANCELLED,
        ], true);
    }

    /**
     * Get all active status values as strings
     *
     * @return array<string>
     */
    public static function activeStatuses(): array
    {
        return [
            self::OPEN->value,
            self::IN_PROGRESS->value,
            self::PENDING_INFO->value,
        ];
    }

    /**
     * Get all terminal status values as strings
     *
     * @return array<string>
     */
    public static function terminalStatuses(): array
    {
        return [
            self::RESOLVED->value,
            self::CLOSED->value,
            self::CANCELLED->value,
        ];
    }
}
