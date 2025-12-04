<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

/**
 * Performance Monitoring Service Interface for ICTServe v3.5.0
 *
 * Provides real-time application performance monitoring using Laravel Pulse.
 * Tracks slow queries, queue job metrics, request patterns, and server health.
 *
 * @see D03 §8.2 Performance monitoring requirements
 * @see Laravel Pulse Documentation v1.3.0
 * @see Requirements 36.2, 36.3, 36.4, 36.5, 36.7, 36.8
 */
interface PerformanceMonitoringServiceInterface
{
    /**
     * Get slow database queries exceeding the threshold
     *
     * Retrieves queries that exceed the specified threshold from Laravel Pulse.
     * Per Requirement 36.2: Track slow database queries (>500ms threshold)
     * with query details, frequency, and execution time.
     *
     * @param  int  $thresholdMs  Threshold in milliseconds (default: 500ms per D03 §8.2)
     * @return Collection Collection of slow query records with query, time, count, location
     */
    public function getSlowQueries(int $thresholdMs = 500): Collection;

    /**
     * Get queue job performance metrics
     *
     * Retrieves queue job metrics including processing time, failure rates,
     * and retry patterns from Laravel Pulse.
     * Per Requirement 36.3: Monitor queue job performance per D17.
     *
     * @return array{
     *     total_jobs: int,
     *     processed_jobs: int,
     *     failed_jobs: int,
     *     pending_jobs: int,
     *     average_processing_time_ms: float,
     *     failure_rate_percent: float,
     *     jobs_by_queue: array<string, int>,
     *     slow_jobs: array
     * }
     */
    public function getQueueJobMetrics(): array;

    /**
     * Get user request metrics
     *
     * Retrieves request patterns including response times, memory usage,
     * and cache hit rates from Laravel Pulse.
     * Per Requirement 36.4: Track user request patterns per D03 §8.2.
     *
     * @return array{
     *     total_requests: int,
     *     average_response_time_ms: float,
     *     slow_requests_count: int,
     *     requests_by_user: array,
     *     cache_hit_rate_percent: float,
     *     memory_usage_mb: float
     * }
     */
    public function getRequestMetrics(): array;

    /**
     * Get server health metrics
     *
     * Retrieves server health metrics including CPU usage, memory consumption,
     * and disk space utilization from Laravel Pulse.
     * Per Requirement 36.5: Provide server health metrics per D03 §8.2.
     *
     * @return array{
     *     servers: array<string, array{
     *         cpu_percent: float,
     *         memory_used_mb: float,
     *         memory_total_mb: float,
     *         memory_percent: float,
     *         disk_used_gb: float,
     *         disk_total_gb: float,
     *         disk_percent: float,
     *         last_seen_at: string
     *     }>,
     *     overall_health: string
     * }
     */
    public function getServerHealthMetrics(): array;

    /**
     * Check if any performance thresholds are exceeded
     *
     * Evaluates current metrics against configured thresholds and returns
     * any exceeded thresholds for alerting.
     * Per Requirement 36.8: Trigger alerts when thresholds exceeded per D17 §5.
     *
     * @return array<int, array{
     *     metric: string,
     *     current_value: float,
     *     threshold: float,
     *     severity: string,
     *     message: string
     * }>
     */
    public function checkPerformanceThresholds(): array;

    /**
     * Trigger a performance alert
     *
     * Sends alert via configured notification channels when a performance
     * threshold is exceeded.
     * Per Requirement 36.8: Trigger alerts via configured notification channels per D17 §5.
     *
     * @param  string  $metric  The metric name that exceeded threshold
     * @param  float  $value  The current value of the metric
     * @param  float  $threshold  The threshold that was exceeded
     */
    public function triggerPerformanceAlert(string $metric, float $value, float $threshold): void;

    /**
     * Prune old Pulse data
     *
     * Removes Pulse data older than the specified retention period.
     * Per Requirement 36.7: Retain Pulse data for 7 days with automatic pruning.
     *
     * @param  int  $retentionDays  Number of days to retain data (default: 7 per D03 §8.2)
     * @return int Number of records pruned
     */
    public function pruneOldData(int $retentionDays = 7): int;
}
