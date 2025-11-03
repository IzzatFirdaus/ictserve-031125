<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Asset Status Enum
 *
 * Defines all possible states for assets in the system.
 *
 * @see D03-FR-003.1 Asset inventory management
 * @see D04 §2.3 Enum definitions
 */
enum AssetStatus: string
{
    case AVAILABLE = 'available';
    case LOANED = 'loaned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';
    case DAMAGED = 'damaged';

    /**
     * Get bilingual label for status
     */
    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => __('asset.status.available'),
            self::LOANED => __('asset.status.loaned'),
            self::MAINTENANCE => __('asset.status.maintenance'),
            self::RETIRED => __('asset.status.retired'),
            self::DAMAGED => __('asset.status.damaged'),
        };
    }

    /**
     * Get WCAG 2.2 AA compliant color for status
     */
    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'green',
            self::LOANED => 'blue',
            self::MAINTENANCE => 'orange',
            self::RETIRED => 'gray',
            self::DAMAGED => 'red',
        };
    }

    /**
     * Check if asset can be loaned in this status
     */
    public function canBeLoan(): bool
    {
        return $this === self::AVAILABLE;
    }

    /**
     * Check if status requires maintenance attention
     */
    public function requiresMaintenance(): bool
    {
        return in_array($this, [
            self::MAINTENANCE,
            self::DAMAGED,
        ]);
    }
}
