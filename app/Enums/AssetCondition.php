<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Asset Condition Enum
 *
 * Defines physical condition states for assets.
 *
 * @see D03-FR-003.3 Asset return processing
 * @see D04 §2.3 Enum definitions
 */
enum AssetCondition: string
{
    case EXCELLENT = 'excellent';
    case GOOD = 'good';
    case FAIR = 'fair';
    case POOR = 'poor';
    case DAMAGED = 'damaged';

    /**
     * Get bilingual label for condition
     */
    public function label(): string
    {
        return match ($this) {
            self::EXCELLENT => __('asset.condition.excellent'),
            self::GOOD => __('asset.condition.good'),
            self::FAIR => __('asset.condition.fair'),
            self::POOR => __('asset.condition.poor'),
            self::DAMAGED => __('asset.condition.damaged'),
        };
    }

    /**
     * Get WCAG 2.2 AA compliant color for condition
     */
    public function color(): string
    {
        return match ($this) {
            self::EXCELLENT => 'green',
            self::GOOD => 'blue',
            self::FAIR => 'yellow',
            self::POOR => 'orange',
            self::DAMAGED => 'red',
        };
    }

    /**
     * Check if condition requires maintenance
     */
    public function requiresMaintenance(): bool
    {
        return in_array($this, [
            self::POOR,
            self::DAMAGED,
        ]);
    }

    /**
     * Check if condition allows asset to be loaned
     */
    public function canBeLoan(): bool
    {
        return in_array($this, [
            self::EXCELLENT,
            self::GOOD,
            self::FAIR,
        ]);
    }

    /**
     * Get condition score (1-5, higher is better)
     */
    public function score(): int
    {
        return match ($this) {
            self::EXCELLENT => 5,
            self::GOOD => 4,
            self::FAIR => 3,
            self::POOR => 2,
            self::DAMAGED => 1,
        };
    }
}
