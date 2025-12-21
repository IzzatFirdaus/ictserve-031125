<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Laravel\Pulse\Facades\Pulse;

/**
 * Pulse Widget Integration Service
 *
 * Provides centralized integration between Laravel Pulse metrics
 * and ICTServe dashboard widgets with performance optimization.
 *
 * Features:
 * - Centralized Pulse data access with caching
 * - Performance threshold monitoring
 * - Alert generation for critical metrics
 * - Data aggregation and formatting
 * - Error handling and fallback values
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R17 (Performance Standards)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D17 Queue Management - Laravel Pulse integration
 *
 * @version 3.6.1
 */
class PulseWidgetIntegration
{
    /**
     * Performance thresholds for alerting
     */
    protected const THRESHOLDS = [
        'response_time' => [
            'good' => 500,      // < 500ms
            'warning' => 1000,  // 500ms - 1s
            'critical' => 2000, // > 1s
        ],
        'error_rate' => [
            'good' => 1,        // < 1%
            'warning' => 5,     // 1% - 5%
            'critical' => 10,   // > 5%
        ],
        'slow_queries' => [
            'good' => 0,        // No slow queries
            'warning' => 5,     // 1-5 slow queries
            'critical' => 10,   // > 5 slow queries
        ],
        'queue_failure_rate' => [
            'good' => 0,        // No failures
            'warning' => 5,     // < 5% failure rate
            'critical' => 10,   // > 5% failure rate
        ],
    ];

    /**
     * Cache TTL for Pulse data (2 minutes)
     */
    protected const CACHE_TTL = 120;

    /**
     * Get performance summary for dashboard widgets
     */
    public function getPerformanceSummary(string $period = '1 hour'): array
    {
        $cacheKey = "pulse_performance_summary_{$period}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($period) {
            $since = $this->parsePeriod($period);

            return [
                'response_time' => $this->getResponseTimeMetrics($since),
                'error_rate' => $this->getErrorRateMetrics($since),
                'slow_queries' => $this->getSlowQueryMetrics($since),
                'queue_health' => $this->getQueueHealthMetrics($since),
                'server_health' => $this->getServerHealthMetrics($since),
                'alerts' => $this->generateAlerts($since),
                'timestamp' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get response time metrics
     */
    protected function getResponseTimeMetrics(\DateTimeInterface $since): array
    {
        try {
            // Use Pulse aggregate method instead of values
            $entries = Pulse::aggregate('slow_request', 'avg', $since);

            if ($entries->isEmpty()) {
                return [
                    'average' => 0,
                    'max' => 0,
                    'min' => 0,
                    'count' => 0,
                    'status' => 'good',
                ];
            }

            $average = $entries->avg('value') ?? 0;
            $max = $entries->max('value') ?? 0;
            $min = $entries->min('value') ?? 0;
            $count = $entries->count();

            return [
                'average' => round($average, 2),
                'max' => $max,
                'min' => $min,
                'count' => $count,
                'status' => $this->getResponseTimeStatus($average),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get response time metrics from Pulse', [
                'error' => $e->getMessage(),
            ]);

            return [
                'average' => 0,
                'max' => 0,
                'min' => 0,
                'count' => 0,
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Get error rate metrics
     */
    protected function getErrorRateMetrics(\DateTimeInterface $since): array
    {
        try {
            $totalRequests = Pulse::aggregate('user_request', 'count', $since)->sum('count') ?? 0;
            $errorRequests = Pulse::aggregate('exception', 'count', $since)->sum('count') ?? 0;

            $errorRate = $totalRequests > 0 ? ($errorRequests / $totalRequests) * 100 : 0;

            return [
                'rate' => round($errorRate, 2),
                'total_requests' => $totalRequests,
                'error_requests' => $errorRequests,
                'status' => $this->getErrorRateStatus($errorRate),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get error rate metrics from Pulse', [
                'error' => $e->getMessage(),
            ]);

            return [
                'rate' => 0,
                'total_requests' => 0,
                'error_requests' => 0,
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Get slow query metrics
     */
    protected function getSlowQueryMetrics(\DateTimeInterface $since): array
    {
        try {
            $slowQueries = Pulse::aggregate('slow_query', ['count', 'avg'], $since);

            $count = $slowQueries->sum('count') ?? 0;
            $averageTime = $slowQueries->avg('avg') ?? 0;

            return [
                'count' => $count,
                'average_time' => round($averageTime, 2),
                'status' => $this->getSlowQueryStatus($count),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get slow query metrics from Pulse', [
                'error' => $e->getMessage(),
            ]);

            return [
                'count' => 0,
                'average_time' => 0,
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Get queue health metrics
     */
    protected function getQueueHealthMetrics(\DateTimeInterface $since): array
    {
        try {
            $totalJobs = Pulse::aggregate('queue', 'count', $since)->sum('count') ?? 0;
            $failedJobs = Pulse::aggregate('slow_job', 'count', $since)->sum('count') ?? 0;

            $failureRate = $totalJobs > 0 ? ($failedJobs / $totalJobs) * 100 : 0;

            return [
                'total_jobs' => $totalJobs,
                'failed_jobs' => $failedJobs,
                'failure_rate' => round($failureRate, 2),
                'status' => $this->getQueueHealthStatus($failureRate),
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get queue health metrics from Pulse', [
                'error' => $e->getMessage(),
            ]);

            return [
                'total_jobs' => 0,
                'failed_jobs' => 0,
                'failure_rate' => 0,
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Get server health metrics
     */
    protected function getServerHealthMetrics(\DateTimeInterface $since): array
    {
        try {
            $serverMetrics = Pulse::aggregate('server', 'count', $since);

            // Basic server health check
            $isHealthy = $serverMetrics->isNotEmpty();

            return [
                'is_healthy' => $isHealthy,
                'last_check' => $serverMetrics->max('timestamp'),
                'status' => $isHealthy ? 'good' : 'critical',
            ];
        } catch (\Exception $e) {
            \Log::warning('Failed to get server health metrics from Pulse', [
                'error' => $e->getMessage(),
            ]);

            return [
                'is_healthy' => false,
                'last_check' => null,
                'status' => 'unknown',
            ];
        }
    }

    /**
     * Generate alerts based on performance thresholds
     */
    protected function generateAlerts(\DateTimeInterface $since): array
    {
        $alerts = [];

        $responseTime = $this->getResponseTimeMetrics($since);
        if ($responseTime['status'] === 'critical') {
            $alerts[] = [
                'type' => 'response_time',
                'level' => 'critical',
                'message' => 'Masa respons purata melebihi 2 saat',
                'value' => $responseTime['average'],
            ];
        }

        $errorRate = $this->getErrorRateMetrics($since);
        if ($errorRate['status'] === 'critical') {
            $alerts[] = [
                'type' => 'error_rate',
                'level' => 'critical',
                'message' => 'Kadar ralat melebihi 10%',
                'value' => $errorRate['rate'],
            ];
        }

        $slowQueries = $this->getSlowQueryMetrics($since);
        if ($slowQueries['status'] === 'critical') {
            $alerts[] = [
                'type' => 'slow_queries',
                'level' => 'critical',
                'message' => 'Terlalu banyak query perlahan',
                'value' => $slowQueries['count'],
            ];
        }

        return $alerts;
    }

    /**
     * Parse period string to DateTime
     */
    protected function parsePeriod(string $period): \DateTimeInterface
    {
        return match ($period) {
            '15 minutes' => now()->subMinutes(15),
            '30 minutes' => now()->subMinutes(30),
            '1 hour' => now()->subHour(),
            '6 hours' => now()->subHours(6),
            '24 hours' => now()->subDay(),
            '7 days' => now()->subWeek(),
            default => now()->subHour(),
        };
    }

    /**
     * Get response time status based on thresholds
     */
    protected function getResponseTimeStatus(float $time): string
    {
        if ($time < self::THRESHOLDS['response_time']['good']) {
            return 'good';
        }

        if ($time < self::THRESHOLDS['response_time']['warning']) {
            return 'warning';
        }

        return 'critical';
    }

    /**
     * Get error rate status based on thresholds
     */
    protected function getErrorRateStatus(float $rate): string
    {
        if ($rate < self::THRESHOLDS['error_rate']['good']) {
            return 'good';
        }

        if ($rate < self::THRESHOLDS['error_rate']['warning']) {
            return 'warning';
        }

        return 'critical';
    }

    /**
     * Get slow query status based on thresholds
     */
    protected function getSlowQueryStatus(int $count): string
    {
        if ($count <= self::THRESHOLDS['slow_queries']['good']) {
            return 'good';
        }

        if ($count <= self::THRESHOLDS['slow_queries']['warning']) {
            return 'warning';
        }

        return 'critical';
    }

    /**
     * Get queue health status based on failure rate
     */
    protected function getQueueHealthStatus(float $failureRate): string
    {
        if ($failureRate <= self::THRESHOLDS['queue_failure_rate']['good']) {
            return 'good';
        }

        if ($failureRate <= self::THRESHOLDS['queue_failure_rate']['warning']) {
            return 'warning';
        }

        return 'critical';
    }
}
