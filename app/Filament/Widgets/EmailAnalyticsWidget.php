<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\Notifications\EmailAnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Email Analytics Widget
 *
 * Displays comprehensive email delivery analytics including delivery rates,
 * bounce rates, queue health, and alerting for delivery failures.
 *
 * Features:
 * - Delivery rate metrics
 * - Bounce rate monitoring
 * - Queue health status
 * - Failure alerting
 * - Real-time status updates
 *
 * @see D03 SRS-FR-008
 * @see D04 §6.2
 *
 * @requirements 10.1, 10.3, 10.5
 */
class EmailAnalyticsWidget extends BaseWidget
{
    use WidgetMetadata;

    protected static ?int $sort = 3;

    protected ?string $pollingInterval = '60s';

    public static function getDocumentationReference(): string
    {
        return 'D03 SRS-FR-008, D04 §6.2 Email Analytics';
    }

    protected function getStats(): array
    {
        $analyticsService = app(EmailAnalyticsService::class);

        $deliveryMetrics = $analyticsService->getDeliveryMetrics();
        $bounceMetrics = $analyticsService->getBounceMetrics();
        $queueHealth = $analyticsService->getQueueHealth();
        $alerts = $analyticsService->checkDeliveryAlerts();

        $deliveryRate = $deliveryMetrics['rates']['delivery_rate'];
        $bounceRate = $bounceMetrics['bounce_rate'];
        $healthStatus = $queueHealth['health_status'];

        return [
            Stat::make(__('notifications.analytics.delivery_rate'), $deliveryRate.'%')
                ->description(__('notifications.analytics.last_30_days'))
                ->descriptionIcon('heroicon-m-envelope')
                ->color($this->getDeliveryRateColor($deliveryRate))
                ->chart($this->getDeliveryTrend($analyticsService)),

            Stat::make(__('notifications.analytics.bounce_rate'), $bounceRate.'%')
                ->description($bounceMetrics['alert_triggered']
                    ? __('notifications.analytics.alert_threshold_exceeded')
                    : __('notifications.analytics.within_threshold'))
                ->descriptionIcon($bounceMetrics['alert_triggered']
                    ? 'heroicon-m-exclamation-triangle'
                    : 'heroicon-m-check-circle')
                ->color($bounceMetrics['alert_triggered'] ? 'danger' : 'success'),

            Stat::make(__('notifications.analytics.queue_health'), ucfirst($healthStatus))
                ->description(sprintf(
                    __('notifications.analytics.throughput_per_minute'),
                    $queueHealth['throughput_per_minute']
                ))
                ->descriptionIcon($this->getHealthIcon($healthStatus))
                ->color($this->getHealthColor($healthStatus)),

            Stat::make(__('notifications.analytics.total_sent'), number_format($deliveryMetrics['totals']['total']))
                ->description(sprintf(
                    __('notifications.analytics.delivered_count'),
                    number_format($deliveryMetrics['totals']['delivered'])
                ))
                ->descriptionIcon('heroicon-m-paper-airplane')
                ->color('primary'),
        ];
    }

    /**
     * Get color based on delivery rate.
     */
    private function getDeliveryRateColor(float $rate): string
    {
        return match (true) {
            $rate >= 95 => 'success',
            $rate >= 85 => 'warning',
            default => 'danger',
        };
    }

    /**
     * Get color based on queue health status.
     */
    private function getHealthColor(string $status): string
    {
        return match ($status) {
            'healthy' => 'success',
            'degraded' => 'warning',
            'warning' => 'warning',
            'critical' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get icon based on queue health status.
     */
    private function getHealthIcon(string $status): string
    {
        return match ($status) {
            'healthy' => 'heroicon-m-check-circle',
            'degraded' => 'heroicon-m-exclamation-circle',
            'warning' => 'heroicon-m-exclamation-triangle',
            'critical' => 'heroicon-m-x-circle',
            default => 'heroicon-m-question-mark-circle',
        };
    }

    /**
     * Get delivery trend data for chart.
     *
     * @return array<int, int>
     */
    private function getDeliveryTrend(EmailAnalyticsService $service): array
    {
        $dailyBreakdown = $service->getDailyBreakdown(now()->subDays(7), now());

        return $dailyBreakdown->pluck('delivered')->toArray();
    }
}
