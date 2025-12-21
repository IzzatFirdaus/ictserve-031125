<?php

declare(strict_types=1);

namespace App\Filament\Traits;

/**
 * Widget Metadata Trait
 *
 * Provides standardized metadata for dashboard widgets following
 * ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R2 (Widget Organization), R10 (Role-Based Access)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D12 §4 MyDS Design System
 *
 * @version 3.6.1
 */
trait WidgetMetadata
{
    /**
     * Get widget category for organization
     * Override in widget class if needed
     */
    public static function getWidgetCategory(): string
    {
        $reflection = new \ReflectionClass(static::class);

        // Auto-detect based on parent class
        if ($reflection->isSubclassOf('Filament\Widgets\StatsOverviewWidget')) {
            return 'header';
        }

        if ($reflection->isSubclassOf('Filament\Widgets\ChartWidget')) {
            return 'charts';
        }

        return 'content';
    }

    /**
     * Get widget refresh rate in seconds
     * Override in widget class if needed
     */
    public static function getRefreshRate(): int
    {
        $category = static::getWidgetCategory();

        return match ($category) {
            'header' => 60,    // 1 minute for stats
            'charts' => 300,   // 5 minutes for charts
            'content' => 120,  // 2 minutes for content
            default => 300,
        };
    }

    /**
     * Get widget cache TTL in seconds
     * Override in widget class if needed
     * Note: This method may conflict with CacheableWidget trait
     */
    public static function getWidgetCacheTtl(): int
    {
        $category = static::getWidgetCategory();

        return match ($category) {
            'header' => 300,   // 5 minutes for stats
            'charts' => 900,   // 15 minutes for charts
            'content' => 600,  // 10 minutes for content
            default => 600,
        };
    }

    /**
     * Get widget roles for access control
     * Override in widget class if needed
     */
    public static function getWidgetRoles(): array
    {
        $className = class_basename(static::class);

        // Sensitive widgets - superuser only
        if (
            str_contains($className, 'Audit') ||
            str_contains($className, 'Security') ||
            str_contains($className, 'Sensitive') ||
            str_contains($className, 'System')
        ) {
            return ['superuser'];
        }

        // Admin widgets
        if (
            str_contains($className, 'Admin') ||
            str_contains($className, 'Performance') ||
            str_contains($className, 'Health') ||
            str_contains($className, 'Horizon')
        ) {
            return ['admin', 'superuser'];
        }

        // Default - all roles
        return ['staff', 'admin', 'superuser'];
    }

    /**
     * Get widget configuration metadata
     */
    public static function getWidgetConfiguration(): array
    {
        return [
            'category' => static::getWidgetCategory(),
            'sort_order' => static::$sort ?? 1,
            'is_active' => true,
            'roles' => static::getWidgetRoles(),
            'refresh_rate' => static::getRefreshRate(),
            'cache_ttl' => static::getWidgetCacheTtl(),
        ];
    }

    /**
     * Check if widget is accessible by current user
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        $allowedRoles = static::getWidgetRoles();

        // Check if user has any of the allowed roles
        foreach ($allowedRoles as $role) {
            if ($user->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get widget documentation reference
     * Override in widget class to specify D00-D18 references
     */
    public static function getDocumentationReference(): string
    {
        return 'D04 §3.2 Dashboard widgets';
    }

    /**
     * Check if widget is WCAG 2.2 AA compliant
     * Override in widget class if specific compliance checks needed
     */
    public static function isWcagCompliant(): bool
    {
        return true; // Default assumption - override if specific checks needed
    }
}
