<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Services\HorizonMonitoringService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

/**
 * Horizon Health Widget
 *
 * Displays Laravel Horizon health status in the Filament admin dashboard.
 * Shows queue metrics, supervisor status, and failed job counts.
 *
 * @see Requirements 23.1, 23.4, 23.8
 */
class HorizonHealthWidget extends Widget
{
    protected string $view = 'filament.widgets.horizon-health-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    /**
     * Get widget data with caching
     */
    protected function getViewData(): array
    {
        return Cache::remember('horizon_health_widget_data', 60, function () {
            try {
                $monitoring = app(HorizonMonitoringService::class);

                return [
                    'health_status' => $monitoring->checkHealthAndAlert(),
                    'queue_statistics' => $monitoring->getQueueStatistics(),
                    'pulse_metrics' => $monitoring->getMetricsForPulse(),
                    'last_updated' => now()->format('d/m/Y H:i:s'),
                ];
            } catch (\Exception $e) {
                return [
                    'error' => $e->getMessage(),
                    'health_status' => [],
                    'queue_statistics' => [],
                    'pulse_metrics' => [],
                    'last_updated' => now()->format('d/m/Y H:i:s'),
                ];
            }
        });
    }

    /**
     * Check if widget should be visible to current user
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Only show to admin and superuser roles
        return $user->hasRole(['admin', 'superuser']) ||
            in_array($user->role, ['admin', 'superuser']);
    }
}
