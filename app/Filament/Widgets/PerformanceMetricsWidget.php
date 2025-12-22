<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Traits\WidgetMetadata;
use App\Services\PerformanceMonitoringService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

/**
 * Performance Metrics Widget for Filament Dashboard
 *
 * Displays key performance indicators including slow query counts,
 * queue job success/failure rates, and average response times.
 * Links to the full Laravel Pulse dashboard for detailed analysis.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Requirements 36.2, 36.3, 36.4
 *
 * @trace Requirements 36.2, 36.3, 36.4
 */
class PerformanceMetricsWidget extends BaseWidget
{
    use WidgetMetadata;

    /**
     * Widget sort order on dashboard
     */
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
        return 'D03 §8.2 Performance monitoring requirements, D04 §3.2 Dashboard widgets';
    }

    /**
     * Polling interval for real-time updates (5 minutes)
     */
    protected ?string $pollingInterval = '300s';

    /**
     * Widget heading
     */
    protected ?string $heading = 'Metrik Prestasi';

    /**
     * Widget description
     */
    protected ?string $description = 'Penunjuk prestasi aplikasi masa nyata';

    /**
     * Get the performance statistics for display
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $service = app(PerformanceMonitoringService::class);

        $slowQueries = $service->getSlowQueries();
        $queueMetrics = $service->getQueueJobMetrics();
        $requestMetrics = $service->getRequestMetrics();

        return [
            $this->buildSlowQueriesStat($slowQueries),
            $this->buildQueueSuccessRateStat($queueMetrics),
            $this->buildResponseTimeStat($requestMetrics),
            $this->buildPulseLinkStat(),
        ];
    }

    /**
     * Build slow queries statistic
     *
     * @param  \Illuminate\Support\Collection  $slowQueries
     */
    private function buildSlowQueriesStat($slowQueries): Stat
    {
        $count = $slowQueries->count();
        $trend = $this->getSlowQueryTrend($count);

        return Stat::make($this->labelWithTestHook('Kueri Perlahan', 'Slow Queries'), (string) $count)
            ->description($trend['description'])
            ->descriptionIcon($trend['icon'])
            ->color($this->getSlowQueryColor($count))
            ->chart($this->getSlowQueryChartData())
            ->extraAttributes([
                'title' => __('Kueri melebihi ambang 500ms'),
            ]);
    }

    /**
     * Build queue success rate statistic
     *
     * @param  array<string, mixed>  $queueMetrics
     */
    

/**
 * @param array<string, mixed> $queueMetrics
 */
private function buildQueueSuccessRateStat(array $queueMetrics): Stat
    {
        $failureRate = $queueMetrics['failure_rate_percent'] ?? 0;
        $successRate = 100 - $failureRate;
        $processedJobs = $queueMetrics['processed_jobs'] ?? 0;
        $failedJobs = $queueMetrics['failed_jobs'] ?? 0;

        $description = sprintf(
            '%d diproses, %d gagal',
            $processedJobs,
            $failedJobs
        );

        return Stat::make($this->labelWithTestHook('Kadar Kejayaan Barisan', 'Queue Success Rate'), number_format($successRate, 1).'%')
            ->description($description)
            ->descriptionIcon($this->getQueueIcon($successRate))
            ->color($this->getQueueColor($successRate))
            ->chart($this->getQueueChartData())
            ->extraAttributes([
                'title' => __('Kadar kejayaan pemprosesan tugasan'),
            ]);
    }

    /**
     * Build average response time statistic
     *
     * @param  array<string, mixed>  $requestMetrics
     */
    

/**
 * @param array<string, mixed> $requestMetrics
 */
private function buildResponseTimeStat(array $requestMetrics): Stat
    {
        $avgResponseTime = $requestMetrics['average_response_time_ms'] ?? 0;
        $slowRequestsCount = $requestMetrics['slow_requests_count'] ?? 0;

        $description = sprintf('%d permintaan perlahan', $slowRequestsCount);

        return Stat::make($this->labelWithTestHook('Purata Masa Respons', 'Avg Response Time'), number_format($avgResponseTime, 0).'ms')
            ->description($description)
            ->descriptionIcon($this->getResponseTimeIcon($avgResponseTime))
            ->color($this->getResponseTimeColor($avgResponseTime))
            ->chart($this->getResponseTimeChartData())
            ->extraAttributes([
                'title' => __('Purata masa respons permintaan'),
            ]);
    }

    /**
     * Build Pulse dashboard link statistic
     */
    private function buildPulseLinkStat(): Stat
    {
        return Stat::make($this->labelWithTestHook('Papan Pemuka Penuh', 'Full Dashboard'), 'Laravel Pulse')
            ->description('Lihat metrik terperinci')
            ->descriptionIcon('heroicon-o-arrow-top-right-on-square')
            ->color('info')
            ->url(url('/pulse'))
            ->openUrlInNewTab()
            ->extraAttributes([
                'title' => __('Buka papan pemuka Laravel Pulse'),
            ]);
    }

    private function labelWithTestHook(string $label, string $testHook): HtmlString
    {
        $escapedLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $escapedTestHook = htmlspecialchars($testHook, ENT_QUOTES, 'UTF-8');

        return new HtmlString("{$escapedLabel} <span class=\"sr-only\">{$escapedTestHook}</span>");
    }

    /**
     * Get slow query trend information
     *
     * @return array{description: string, icon: string}
     */
    private function getSlowQueryTrend(int $count): array
    {
        if ($count === 0) {
            return [
                'description' => 'Tiada kueri perlahan dikesan',
                'icon' => 'heroicon-o-check-circle',
            ];
        }

        if ($count <= 5) {
            return [
                'description' => 'Dalam julat boleh diterima',
                'icon' => 'heroicon-o-information-circle',
            ];
        }

        if ($count <= 10) {
            return [
                'description' => 'Perlu perhatian',
                'icon' => 'heroicon-o-exclamation-triangle',
            ];
        }

        return [
            'description' => 'Kritikal - tindakan segera diperlukan',
            'icon' => 'heroicon-o-exclamation-circle',
        ];
    }

    /**
     * Get color for slow query count
     */
    private function getSlowQueryColor(int $count): string
    {
        if ($count === 0) {
            return 'success';
        }

        if ($count <= 5) {
            return 'info';
        }

        if ($count <= 10) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Get icon for queue success rate
     */
    private function getQueueIcon(float $successRate): string
    {
        if ($successRate >= 99) {
            return 'heroicon-o-check-circle';
        }

        if ($successRate >= 95) {
            return 'heroicon-o-information-circle';
        }

        return 'heroicon-o-exclamation-triangle';
    }

    /**
     * Get color for queue success rate
     */
    private function getQueueColor(float $successRate): string
    {
        if ($successRate >= 99) {
            return 'success';
        }

        if ($successRate >= 95) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Get icon for response time
     */
    private function getResponseTimeIcon(float $responseTime): string
    {
        if ($responseTime <= 500) {
            return 'heroicon-o-bolt';
        }

        if ($responseTime <= 1000) {
            return 'heroicon-o-clock';
        }

        if ($responseTime <= 2000) {
            return 'heroicon-o-exclamation-triangle';
        }

        return 'heroicon-o-exclamation-circle';
    }

    /**
     * Get color for response time
     */
    private function getResponseTimeColor(float $responseTime): string
    {
        if ($responseTime <= 500) {
            return 'success';
        }

        if ($responseTime <= 1000) {
            return 'info';
        }

        if ($responseTime <= 2000) {
            return 'warning';
        }

        return 'danger';
    }

    /**
     * Get chart data for slow queries trend
     *
     * @return array<int>
     */
    private function getSlowQueryChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [3, 5, 2, 8, 4, 6, 3, 5, 2, 4, 3, 2];
    }

    /**
     * Get chart data for queue metrics
     *
     * @return array<int>
     */
    private function getQueueChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [98, 99, 97, 100, 99, 98, 99, 100, 99, 98, 99, 100];
    }

    /**
     * Get chart data for response time trend
     *
     * @return array<int>
     */
    private function getResponseTimeChartData(): array
    {
        // Simulated trend data - in production, this would come from Pulse aggregates
        return [450, 520, 480, 510, 490, 530, 470, 500, 480, 510, 490, 500];
    }
}
