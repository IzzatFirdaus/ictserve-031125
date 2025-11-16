<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Dashboard Service
 *
 * Centralized cache management for Filament dashboard widgets.
 * Provides cache invalidation methods for real-time updates.
 *
 * @trace D11 (Technical Design - Performance Optimization)
 */
class DashboardService
{
    /**
     * Clear all dashboard caches
     */
    public function clearAllCaches(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
        Cache::forget('dashboard:loan-stats');
        Cache::forget('dashboard:asset-stats');
    }

    /**
     * Clear helpdesk-related caches
     */
    public function clearHelpdeskCache(): void
    {
        Cache::forget('dashboard:helpdesk-stats');
    }

    /**
     * Clear loan-related caches
     */
    public function clearLoanCache(): void
    {
        Cache::forget('dashboard:loan-stats');
    }

    /**
     * Clear asset-related caches
     */
    public function clearAssetCache(): void
    {
        Cache::forget('dashboard:asset-stats');
    }
}
