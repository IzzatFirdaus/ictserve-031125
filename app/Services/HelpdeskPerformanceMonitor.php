<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Helpdesk Performance Monitor
 *
 * Monitors performance metrics for the helpdesk module including:
 * - Core Web Vitals (LCP, FID, CLS)
 * - Email queue performance (60-second SLA)
 * - Database query performance
 * - Component render times
 *
 * @see D11 Technical Design Documentation - Performance Monitoring
 * @see Requirements 9.1, 9.4 - Performance monitoring requirements
 */
class HelpdeskPerformanceMonitor
{
    /**
     * Core Web Vitals thresholds
     */
    protected array $coreWebVitalsThresholds = [
        'lcp' => 2500, // Largest Contentful Paint (ms)
        'fid' => 100,  // First Input Delay (ms)
        'cls' => 0.1,  // Cumulative Layout Shift
        'ttfb' => 600, // Time to First Byte (ms)
    ];

    /**
     * Email SLA threshold (60 seconds)
     */
    protected int $emailSlaThreshold = 60;

    /**
     * Record Core Web Vitals metric
     */
    public function recordCoreWebVital(string $metric, float $value, string $page): void
    {
        $cacheKey = "cwv.{$metric}.{$page}";

        // Store last 100 measurements
        $measurements = Cache::get($cacheKey, []);
        $measurements[] = [
            'value' => $value,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 100
        if (count($measurements) > 100) {
            $measurements = array_slice($measurements, -100);
        }

        Cache::put($cacheKey, $measurements, now()->addDays(7));

        // Check if threshold exceeded
        $threshold = $this->coreWebVitalsThresholds[strtolower($metric)] ?? null;
        if ($threshold && $value > $threshold) {
            $this->alertPerformanceDegradation($metric, $value, $threshold, $page);
        }
    }

    /**
     * Record email queue performance
     */
    public function recordEmailQueueTime(string $emailType, float $queueTimeSeconds): void
    {
        $cacheKey = "email_queue.{$emailType}";

        $measurements = Cache::get($cacheKey, []);
        $measurements[] = [
            'queue_time' => $queueTimeSeconds,
            'timestamp' => now()->toIso8601String(),
            'sla_met' => $queueTimeSeconds <= $this->emailSlaThreshold,
        ];

        // Keep only last 100
        if (count($measurements) > 100) {
            $measurements = array_slice($measurements, -100);
        }

        Cache::put($cacheKey, $measurements, now()->addDays(7));

        // Alert if SLA breached
        if ($queueTimeSeconds > $this->emailSlaThreshold) {
            $this->alertEmailSlaBreached($emailType, $queueTimeSeconds);
        }
    }

    /**
     * Record database query performance
     */
    public function recordQueryPerformance(string $query, float $executionTimeMs): void
    {
        // Only record slow queries (>100ms)
        if ($executionTimeMs < 100) {
            return;
        }

        $cacheKey = 'slow_queries.helpdesk';

        $slowQueries = Cache::get($cacheKey, []);
        $slowQueries[] = [
            'query' => substr($query, 0, 200), // Truncate for storage
            'execution_time' => $executionTimeMs,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 50 slow queries
        if (count($slowQueries) > 50) {
            $slowQueries = array_slice($slowQueries, -50);
        }

        Cache::put($cacheKey, $slowQueries, now()->addDays(7));

        // Alert if very slow (>1000ms)
        if ($executionTimeMs > 1000) {
            Log::warning('Very slow helpdesk query detected', [
                'query' => substr($query, 0, 200),
                'execution_time_ms' => $executionTimeMs,
            ]);
        }
    }

    /**
     * Record Livewire component render time
     */
    public function recordComponentRenderTime(string $component, float $renderTimeMs): void
    {
        $cacheKey = "component_render.{$component}";

        $measurements = Cache::get($cacheKey, []);
        $measurements[] = [
            'render_time' => $renderTimeMs,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 50
        if (count($measurements) > 50) {
            $measurements = array_slice($measurements, -50);
        }

        Cache::put($cacheKey, $measurements, now()->addDays(7));

        // Alert if render time exceeds 500ms
        if ($renderTimeMs > 500) {
            Log::warning('Slow component render detected', [
                'component' => $component,
                'render_time_ms' => $renderTimeMs,
            ]);
        }
    }

    /**
     * Get Core Web Vitals statistics
     *
     * @return array{average: float, p95: float, measurements_count: int, threshold: float, threshold_met: bool}
     */
    public function getCoreWebVitalsStats(string $metric, string $page): array
    {
        $cacheKey = "cwv.{$metric}.{$page}";
        $measurements = Cache::get($cacheKey, []);

        if (empty($measurements)) {
            return [
                'average' => 0,
                'p95' => 0,
                'measurements_count' => 0,
                'threshold' => $this->coreWebVitalsThresholds[strtolower($metric)] ?? 0,
                'threshold_met' => true,
            ];
        }

        $values = array_column($measurements, 'value');
        $average = array_sum($values) / count($values);

        // Calculate 95th percentile
        sort($values);
        $p95Index = (int) ceil(count($values) * 0.95) - 1;
        $p95 = $values[$p95Index] ?? 0;

        $threshold = $this->coreWebVitalsThresholds[strtolower($metric)] ?? 0;

        return [
            'average' => round($average, 2),
            'p95' => round($p95, 2),
            'measurements_count' => count($measurements),
            'threshold' => $threshold,
            'threshold_met' => $p95 <= $threshold,
        ];
    }

    /**
     * Get email queue performance statistics
     *
     * @return array{average_queue_time: float, sla_compliance_rate: float, total_emails: int}
     */
    public function getEmailQueueStats(string $emailType): array
    {
        $cacheKey = "email_queue.{$emailType}";
        $measurements = Cache::get($cacheKey, []);

        if (empty($measurements)) {
            return [
                'average_queue_time' => 0,
                'sla_compliance_rate' => 100,
                'total_emails' => 0,
            ];
        }

        $queueTimes = array_column($measurements, 'queue_time');
        $slaMet = array_filter($measurements, fn ($m) => $m['sla_met']);

        return [
            'average_queue_time' => round(array_sum($queueTimes) / count($queueTimes), 2),
            'sla_compliance_rate' => round((count($slaMet) / count($measurements)) * 100, 2),
            'total_emails' => count($measurements),
        ];
    }

    /**
     * Get slow queries summary
     *
     * @return array{total_slow_queries: int, average_execution_time: float, slowest_query: array|null}
     */
    public function getSlowQueriesStats(): array
    {
        $cacheKey = 'slow_queries.helpdesk';
        $slowQueries = Cache::get($cacheKey, []);

        if (empty($slowQueries)) {
            return [
                'total_slow_queries' => 0,
                'average_execution_time' => 0,
                'slowest_query' => null,
            ];
        }

        $executionTimes = array_column($slowQueries, 'execution_time');
        $slowest = $slowQueries[array_key_last($slowQueries)];

        return [
            'total_slow_queries' => count($slowQueries),
            'average_execution_time' => round(array_sum($executionTimes) / count($executionTimes), 2),
            'slowest_query' => $slowest,
        ];
    }

    /**
     * Get component render time statistics
     *
     * @return array{average_render_time: float, p95_render_time: float, measurements_count: int}
     */
    public function getComponentRenderStats(string $component): array
    {
        $cacheKey = "component_render.{$component}";
        $measurements = Cache::get($cacheKey, []);

        if (empty($measurements)) {
            return [
                'average_render_time' => 0,
                'p95_render_time' => 0,
                'measurements_count' => 0,
            ];
        }

        $renderTimes = array_column($measurements, 'render_time');
        $average = array_sum($renderTimes) / count($renderTimes);

        sort($renderTimes);
        $p95Index = (int) ceil(count($renderTimes) * 0.95) - 1;
        $p95 = $renderTimes[$p95Index] ?? 0;

        return [
            'average_render_time' => round($average, 2),
            'p95_render_time' => round($p95, 2),
            'measurements_count' => count($measurements),
        ];
    }

    /**
     * Get comprehensive performance dashboard data
     *
     * @return array{core_web_vitals: array, email_queue: array, database: array, components: array}
     */
    public function getPerformanceDashboard(): array
    {
        return [
            'core_web_vitals' => [
                'lcp' => $this->getCoreWebVitalsStats('lcp', 'helpdesk'),
                'fid' => $this->getCoreWebVitalsStats('fid', 'helpdesk'),
                'cls' => $this->getCoreWebVitalsStats('cls', 'helpdesk'),
                'ttfb' => $this->getCoreWebVitalsStats('ttfb', 'helpdesk'),
            ],
            'email_queue' => [
                'ticket_created' => $this->getEmailQueueStats('ticket_created'),
                'ticket_updated' => $this->getEmailQueueStats('ticket_updated'),
                'ticket_assigned' => $this->getEmailQueueStats('ticket_assigned'),
            ],
            'database' => $this->getSlowQueriesStats(),
            'components' => [
                'dashboard' => $this->getComponentRenderStats('Dashboard'),
                'my_tickets' => $this->getComponentRenderStats('MyTickets'),
                'submit_ticket' => $this->getComponentRenderStats('SubmitTicket'),
            ],
        ];
    }

    /**
     * Alert when performance degrades
     */
    protected function alertPerformanceDegradation(string $metric, float $value, float $threshold, string $page): void
    {
        Log::warning('Core Web Vital threshold exceeded', [
            'metric' => $metric,
            'value' => $value,
            'threshold' => $threshold,
            'page' => $page,
            'exceeded_by' => round($value - $threshold, 2),
        ]);

        // Store alert in cache for dashboard display
        $alertKey = 'performance_alerts.helpdesk';
        $alerts = Cache::get($alertKey, []);

        $alerts[] = [
            'type' => 'core_web_vital',
            'metric' => $metric,
            'value' => $value,
            'threshold' => $threshold,
            'page' => $page,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 20 alerts
        if (count($alerts) > 20) {
            $alerts = array_slice($alerts, -20);
        }

        Cache::put($alertKey, $alerts, now()->addDays(7));
    }

    /**
     * Alert when email SLA is breached
     */
    protected function alertEmailSlaBreached(string $emailType, float $queueTimeSeconds): void
    {
        Log::warning('Email SLA breached', [
            'email_type' => $emailType,
            'queue_time_seconds' => $queueTimeSeconds,
            'sla_threshold' => $this->emailSlaThreshold,
            'exceeded_by' => round($queueTimeSeconds - $this->emailSlaThreshold, 2),
        ]);

        // Store alert
        $alertKey = 'performance_alerts.helpdesk';
        $alerts = Cache::get($alertKey, []);

        $alerts[] = [
            'type' => 'email_sla',
            'email_type' => $emailType,
            'queue_time' => $queueTimeSeconds,
            'sla_threshold' => $this->emailSlaThreshold,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 20 alerts
        if (count($alerts) > 20) {
            $alerts = array_slice($alerts, -20);
        }

        Cache::put($alertKey, $alerts, now()->addDays(7));
    }

    /**
     * Get recent performance alerts
     *
     * @return array<int, array>
     */
    public function getRecentAlerts(int $limit = 10): array
    {
        $alertKey = 'performance_alerts.helpdesk';
        $alerts = Cache::get($alertKey, []);

        return array_slice($alerts, -$limit);
    }

    /**
     * Clear all performance data
     */
    public function clearPerformanceData(): void
    {
        $patterns = [
            'cwv.*',
            'email_queue.*',
            'slow_queries.*',
            'component_render.*',
            'performance_alerts.*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
