<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\HorizonMonitoringService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

/**
 * Horizon Health Widget (Widget Status Kesihatan Horizon)
 *
 * Displays Laravel Horizon queue health status in the Filament admin dashboard.
 * Monitors queue metrics, supervisor status, failed jobs, and provides real-time
 * queue health diagnostics.
 *
 * Features:
 * - Queue supervisor status monitoring
 * - Failed job count tracking
 * - Queue throughput metrics
 * - Worker status display
 * - Real-time health alerts
 *
 * @trace D17-§3 (Queue Management with Horizon)
 * @trace D11-§9 (Laravel Horizon Integration)
 * @trace D04-§3.2 (Dashboard Widgets Architecture)
 * @trace D12-§7 (System Monitoring UI)
 *
 * @see \App\Services\HorizonMonitoringService
 * @see \App\Filament\Traits\WidgetMetadata
 */
class HorizonHealthWidget extends Widget
{
    use WidgetMetadata;

    protected string $view = 'filament.widgets.horizon-health-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 10;

    /**
     * Widget roles - restricted access
     */
    public static function getWidgetRoles(): array
    {
        return ['admin', 'superuser'];
    }

    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return 'D11 §9 Laravel Horizon integration, D04 §3.2 Dashboard widgets';
    }

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
