<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Laravel\Pulse\Facades\Pulse;

/**
 * Pulse Widget Integration Service
 *
 * Provides centralized integration between Laravel Pulse metrics
 * and Filament dashboard widgets with caching and error handling.
 *
 * Features:
 * - Centralized Pulse data access with caching
 * - Error handling and fallback values
 * - Performance optimization with configurable TTL
 * - Metric aggregation and calculation utilities
 * - Alert threshold management
 * - Integration with notification system
 *
 * @trace Requirements: R9 (Laravel Pulse Integration), R18 (Pulse Dashboard Integration)
 *
 * @see D04 §3.2 Dashboard widgets
 * @see D17 Queue Management - Laravel Pulse integration
 *
 * @version 3.6.1
 */
class PulseWidgetIntegration
{
    /**
     * Default cache TTL for Pulse metrics (2 minutes)
     */
    protected int $defaultCacheTtl = 120;

    /**
     * Performance thresholds for alerts
     *
     * @var array<string, array<string, float|int>>
     */
    protected array $thresholds = [
        'response_time' => [
            'warning' => 500,  // 500ms
            'critical' => 1000, // 1s
        ],
        'error_rate' => [
            'warning' => 1.0,   // 1%
            'critical' => 5.0,  // 5%
        ],
        'slow_queries' => [
            'warning' => 5,     // 5 queries
            'critical' => 10,   // 10 queries
        ],
        'queue_failure_rate' => [
            'warning' => 2.0,   // 2%
            'critical' => 5.0,  // 5%
        ],
    ];

    /**
     * Get performance metrics summary
     *
     * @return array<string, mixed>
     */
    public function getPerformanceMetrics(string $period = '1 hour'): array
    {
        $cacheKey = "pulse_performance_metrics_{$period}";

        /** @var array<string, mixed> $result */
        $result = Cache::remember($cacheKey, $this->defaultCacheTtl, function () use ($period): array {
            $since = $this->parsePeriod($period);

            return [
                'response_time' => $this->getAverageResponseTime($since),
                'slow_queries' => $this->getSlowQueriesCount($since),
                'error_rate' => $this->getErrorRate($since),
                'queue_health' => $this->getQueueHealth($since),
                'server_metrics' => $this->getServerMetrics($since),
                'cache_performance' => $this->getCachePerformance($since),
            ];
        });

        return $result;
    }

    /**
     * Get average response time with error handling
     */
    public function getAverageResponseTime(\DateTimeInterface $since): float
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);
            $entries = Pulse::aggregate('slow_request', 'avg', $interval);

            return $entries->avg('avg') ?? 0.0;
        } catch (\Exception $e) {
            Log::warning('Failed to get average response time from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return 0.0;
        }
    }

    /**
     * Get slow queries count with error handling
     */
    public function getSlowQueriesCount(\DateTimeInterface $since): int
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);

            return Pulse::aggregate('slow_query', 'count', $interval)->sum('count') ?? 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get slow queries count from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return 0;
        }
    }

    /**
     * Get error rate with error handling
     */
    public function getErrorRate(\DateTimeInterface $since): float
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);
            $totalRequests = Pulse::aggregate('user_request', 'count', $interval)->sum('count') ?? 0;
            $errorRequests = Pulse::aggregate('exception', 'count', $interval)->sum('count') ?? 0;

            if ($totalRequests === 0) {
                return 0.0;
            }

            return ($errorRequests / $totalRequests) * 100;
        } catch (\Exception $e) {
            Log::warning('Failed to get error rate from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return 0.0;
        }
    }

    /**
     * Get queue health metrics
     *
     * @return array<string, mixed>
     */
    public function getQueueHealth(\DateTimeInterface $since): array
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);
            $totalJobs = Pulse::aggregate('queue', 'count', $interval)->sum('count') ?? 0;
            $failedJobs = Pulse::aggregate('slow_job', 'count', $interval)->sum('count') ?? 0;

            if ($totalJobs === 0) {
                return [
                    'status' => 'idle',
                    'total_jobs' => 0,
                    'failed_jobs' => 0,
                    'failure_rate' => 0.0,
                    'health_score' => 100,
                ];
            }

            $failureRate = ($failedJobs / $totalJobs) * 100;
            $healthScore = max(0, 100 - ($failureRate * 10)); // Reduce score by 10 points per 1% failure

            return [
                'status' => $this->getQueueStatus($failureRate),
                'total_jobs' => $totalJobs,
                'failed_jobs' => $failedJobs,
                'failure_rate' => $failureRate,
                'health_score' => $healthScore,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get queue health from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return [
                'status' => 'unknown',
                'total_jobs' => 0,
                'failed_jobs' => 0,
                'failure_rate' => 0.0,
                'health_score' => 0,
            ];
        }
    }

    /**
     * Get server metrics
     *
     * @return array<string, float>
     */
    public function getServerMetrics(\DateTimeInterface $since): array
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);
            $serverEntries = Pulse::aggregate('server', 'avg', $interval);

            return [
                'cpu_usage' => $serverEntries->where('type', 'cpu')->avg('avg') ?? 0.0,
                'memory_usage' => $serverEntries->where('type', 'memory')->avg('avg') ?? 0.0,
                'disk_usage' => $serverEntries->where('type', 'disk')->avg('avg') ?? 0.0,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get server metrics from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return [
                'cpu_usage' => 0.0,
                'memory_usage' => 0.0,
                'disk_usage' => 0.0,
            ];
        }
    }

    /**
     * Get cache performance metrics
     *
     * @return array<string, int|float>
     */
    public function getCachePerformance(\DateTimeInterface $since): array
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);
            $cacheEntries = Pulse::aggregate('cache', 'count', $interval);
            $hits = $cacheEntries->where('type', 'hit')->sum('count') ?? 0;
            $misses = $cacheEntries->where('type', 'miss')->sum('count') ?? 0;
            $total = $hits + $misses;

            $hitRate = $total > 0 ? ($hits / $total) * 100 : 0.0;

            return [
                'hits' => $hits,
                'misses' => $misses,
                'total' => $total,
                'hit_rate' => $hitRate,
            ];
        } catch (\Exception $e) {
            Log::warning('Failed to get cache performance from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return [
                'hits' => 0,
                'misses' => 0,
                'total' => 0,
                'hit_rate' => 0.0,
            ];
        }
    }

    /**
     * Get slow queries with details
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getSlowQueriesDetails(\DateTimeInterface $since, int $limit = 10): Collection
    {
        try {
            $interval = now()->diffAsCarbonInterval($since);

            // Note: Laravel Pulse doesn't have entries() method in current version
            // This would need to be implemented using available Pulse methods
            // For now, return empty collection as placeholder
            return collect();
        } catch (\Exception $e) {
            Log::warning('Failed to get slow queries details from Pulse', [
                'error' => $e->getMessage(),
                'since' => $since->format('Y-m-d H:i:s'),
            ]);

            return collect();
        }
    }

    /**
     * Check if any metrics exceed alert thresholds
     *
     * @return array<int, array<string, mixed>>
     */
    public function checkAlertThresholds(string $period = '1 hour'): array
    {
        $metrics = $this->getPerformanceMetrics($period);
        $alerts = [];

        // Check response time
        if ($metrics['response_time'] >= $this->thresholds['response_time']['critical']) {
            $alerts[] = [
                'type' => 'critical',
                'metric' => 'response_time',
                'value' => $metrics['response_time'],
                'threshold' => $this->thresholds['response_time']['critical'],
                'message' => 'Masa respons melebihi had kritikal',
            ];
        } elseif ($metrics['response_time'] >= $this->thresholds['response_time']['warning']) {
            $alerts[] = [
                'type' => 'warning',
                'metric' => 'response_time',
                'value' => $metrics['response_time'],
                'threshold' => $this->thresholds['response_time']['warning'],
                'message' => 'Masa respons melebihi had amaran',
            ];
        }

        // Check error rate
        if ($metrics['error_rate'] >= $this->thresholds['error_rate']['critical']) {
            $alerts[] = [
                'type' => 'critical',
                'metric' => 'error_rate',
                'value' => $metrics['error_rate'],
                'threshold' => $this->thresholds['error_rate']['critical'],
                'message' => 'Kadar ralat melebihi had kritikal',
            ];
        } elseif ($metrics['error_rate'] >= $this->thresholds['error_rate']['warning']) {
            $alerts[] = [
                'type' => 'warning',
                'metric' => 'error_rate',
                'value' => $metrics['error_rate'],
                'threshold' => $this->thresholds['error_rate']['warning'],
                'message' => 'Kadar ralat melebihi had amaran',
            ];
        }

        // Check slow queries
        if ($metrics['slow_queries'] >= $this->thresholds['slow_queries']['critical']) {
            $alerts[] = [
                'type' => 'critical',
                'metric' => 'slow_queries',
                'value' => $metrics['slow_queries'],
                'threshold' => $this->thresholds['slow_queries']['critical'],
                'message' => 'Terlalu banyak query perlahan',
            ];
        } elseif ($metrics['slow_queries'] >= $this->thresholds['slow_queries']['warning']) {
            $alerts[] = [
                'type' => 'warning',
                'metric' => 'slow_queries',
                'value' => $metrics['slow_queries'],
                'threshold' => $this->thresholds['slow_queries']['warning'],
                'message' => 'Query perlahan melebihi had normal',
            ];
        }

        // Check queue failure rate
        $queueHealth = (array) $metrics['queue_health'];
        $queueFailureRate = is_numeric($queueHealth['failure_rate'] ?? 0.0) ? (float) ($queueHealth['failure_rate'] ?? 0.0) : 0.0;
        if ($queueFailureRate >= $this->thresholds['queue_failure_rate']['critical']) {
            $alerts[] = [
                'type' => 'critical',
                'metric' => 'queue_failure_rate',
                'value' => $queueFailureRate,
                'threshold' => $this->thresholds['queue_failure_rate']['critical'],
                'message' => 'Kadar kegagalan queue melebihi had kritikal',
            ];
        } elseif ($queueFailureRate >= $this->thresholds['queue_failure_rate']['warning']) {
            $alerts[] = [
                'type' => 'warning',
                'metric' => 'queue_failure_rate',
                'value' => $queueFailureRate,
                'threshold' => $this->thresholds['queue_failure_rate']['warning'],
                'message' => 'Kadar kegagalan queue melebihi had amaran',
            ];
        }

        return $alerts;
    }

    /**
     * Get formatted metrics for display
     *
     * @return array<string, array<string, mixed>>
     */
    public function getFormattedMetrics(string $period = '1 hour'): array
    {
        $metrics = $this->getPerformanceMetrics($period);

        $responseTime = is_numeric($metrics['response_time']) ? (float) $metrics['response_time'] : 0.0;
        $errorRate = is_numeric($metrics['error_rate']) ? (float) $metrics['error_rate'] : 0.0;
        $slowQueries = is_numeric($metrics['slow_queries']) ? (int) $metrics['slow_queries'] : 0;
        $queueHealth = is_array($metrics['queue_health']) ? $metrics['queue_health'] : [];
        $queueFailureRate = is_numeric($queueHealth['failure_rate'] ?? 0.0) ? (float) ($queueHealth['failure_rate'] ?? 0.0) : 0.0;

        return [
            'response_time' => [
                'value' => $this->formatResponseTime($responseTime),
                'color' => $this->getResponseTimeColor($responseTime),
                'raw' => $responseTime,
            ],
            'error_rate' => [
                'value' => number_format($errorRate, 2).'%',
                'color' => $this->getErrorRateColor($errorRate),
                'raw' => $errorRate,
            ],
            'slow_queries' => [
                'value' => number_format($slowQueries),
                'color' => $this->getSlowQueriesColor($slowQueries),
                'raw' => $slowQueries,
            ],
            'queue_health' => [
                'value' => (string) ($queueHealth['status'] ?? 'unknown'),
                'color' => $this->getQueueHealthColor($queueFailureRate),
                'raw' => $queueHealth,
            ],
        ];
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
            '12 hours' => now()->subHours(12),
            '24 hours' => now()->subDay(),
            '7 days' => now()->subWeek(),
            default => now()->subHour(),
        };
    }

    /**
     * Get queue status based on failure rate
     */
    protected function getQueueStatus(float $failureRate): string
    {
        if ($failureRate === 0.0) {
            return 'healthy';
        }

        if ($failureRate < 2.0) {
            return 'good';
        }

        if ($failureRate < 5.0) {
            return 'warning';
        }

        return 'critical';
    }

    /**
     * Format response time for display
     */
    protected function formatResponseTime(float $time): string
    {
        if ($time < 1000) {
            return number_format($time, 0).'ms';
        }

        return number_format($time / 1000, 2).'s';
    }

    /**
     * Get color for response time based on thresholds
     */
    protected function getResponseTimeColor(float $time): string
    {
        if ($time >= $this->thresholds['response_time']['critical']) {
            return 'danger';
        }

        if ($time >= $this->thresholds['response_time']['warning']) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get color for error rate based on thresholds
     */
    protected function getErrorRateColor(float $rate): string
    {
        if ($rate >= $this->thresholds['error_rate']['critical']) {
            return 'danger';
        }

        if ($rate >= $this->thresholds['error_rate']['warning']) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get color for slow queries based on thresholds
     */
    protected function getSlowQueriesColor(int $count): string
    {
        if ($count >= $this->thresholds['slow_queries']['critical']) {
            return 'danger';
        }

        if ($count >= $this->thresholds['slow_queries']['warning']) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get color for queue health based on failure rate
     */
    protected function getQueueHealthColor(float $failureRate): string
    {
        if ($failureRate >= $this->thresholds['queue_failure_rate']['critical']) {
            return 'danger';
        }

        if ($failureRate >= $this->thresholds['queue_failure_rate']['warning']) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get alert threshold configuration
     *
     * @return array<string, array<string, float|int>>
     */
    public function getThresholds(): array
    {
        return $this->thresholds;
    }

    /**
     * Update alert thresholds
     *
     * @param  array<string, array<string, float|int>>  $thresholds
     */
    public function updateThresholds(array $thresholds): void
    {
        $this->thresholds = array_merge($this->thresholds, $thresholds);
    }

    /**
     * Clear cached metrics
     */
    public function clearCache(): void
    {
        $periods = ['15 minutes', '30 minutes', '1 hour', '6 hours', '12 hours', '24 hours', '7 days'];

        foreach ($periods as $period) {
            Cache::forget("pulse_performance_metrics_{$period}");
        }
    }
}
