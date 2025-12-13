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
    case RESERVED = 'reserved';
    case LOANED = 'loaned';
    case MAINTENANCE = 'maintenance';
    case RETIRED = 'retired';
    case DAMAGED = 'damaged';

    /**
     * Get bilingual label for status
     */
    public function label(): string
    {
        $key = match ($this) {
            self::AVAILABLE => 'asset.status.available',
            self::RESERVED => 'asset.status.reserved',
            self::LOANED => 'asset.status.loaned',
            self::MAINTENANCE => 'asset.status.maintenance',
            self::RETIRED => 'asset.status.retired',
            self::DAMAGED => 'asset.status.damaged',
        };
        
        return trans($key);
    }

    /**
     * Get WCAG 2.2 AA compliant color for status
     */
    public function color(): string
    {
        return match ($this) {
            self::AVAILABLE => 'green',
            self::RESERVED => 'yellow',
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
