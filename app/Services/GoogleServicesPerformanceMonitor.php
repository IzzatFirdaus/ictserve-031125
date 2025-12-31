<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GoogleServicesAuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Google Services Performance Monitor for ICTServe v3.6.1
 *
 * Provides performance monitoring and metrics collection for:
 * - SSO authentication timing
 * - Gmail API operations timing
 * - Success/failure rates
 * - Performance alerting
 *
 * @see Requirements 13.5, 17.2
 */
class GoogleServicesPerformanceMonitor
{
    /**
     * Cache key prefixes
     */
    private const CACHE_KEY_SSO_METRICS = 'google:perf:sso:metrics';

    private const CACHE_KEY_GMAIL_METRICS = 'google:perf:gmail:metrics';

    private const CACHE_KEY_TIMING_HISTORY = 'google:perf:timing:history';

    private const CACHE_KEY_ALERTS = 'google:perf:alerts';

    /**
     * Performance thresholds (in milliseconds)
     */
    private const THRESHOLD_SSO_AUTH_MS = 5000;      // 5 seconds per design doc

    private const THRESHOLD_GMAIL_SEND_MS = 10000;   // 10 seconds per design doc

    private const THRESHOLD_API_CALL_MS = 3000;      // 3 seconds

    /**
     * Cache TTL values (in seconds)
     */
    private const CACHE_TTL_METRICS = 60;

    private const CACHE_TTL_HISTORY = 3600;

    /**
     * Alert thresholds
     */
    private const ALERT_FAILURE_RATE_PERCENT = 10;

    private const ALERT_SLOW_OPERATION_COUNT = 5;

    public function __construct(
        private GoogleServicesCacheService $cacheService
    ) {}

    // =========================================================================
    // Timing Recording
    // =========================================================================

    /**
     * Record SSO authentication timing
     */
    public function recordSsoTiming(float $durationMs, bool $success, ?string $error = null): void
    {
        $this->recordTiming('sso', 'authentication', $durationMs, $success, $error);

        // Check for slow operation alert
        if ($durationMs > self::THRESHOLD_SSO_AUTH_MS) {
            $this->triggerSlowOperationAlert('sso', 'authentication', $durationMs);
        }
    }

    /**
     * Record Gmail API operation timing
     */
    public function recordGmailTiming(
        string $operation,
        float $durationMs,
        bool $success,
        ?string $error = null
    ): void {
        $this->recordTiming('gmail', $operation, $durationMs, $success, $error);

        // Check for slow operation alert
        $threshold = $operation === 'send_email' ? self::THRESHOLD_GMAIL_SEND_MS : self::THRESHOLD_API_CALL_MS;
        if ($durationMs > $threshold) {
            $this->triggerSlowOperationAlert('gmail', $operation, $durationMs);
        }
    }

    /**
     * Record generic timing
     */
    private function recordTiming(
        string $service,
        string $operation,
        float $durationMs,
        bool $success,
        ?string $error = null
    ): void {
        $key = self::CACHE_KEY_TIMING_HISTORY.":{$service}:{$operation}";
        $history = Cache::get($key, []);

        // Add new timing record
        $history[] = [
            'duration_ms' => $durationMs,
            'success' => $success,
            'error' => $error,
            'timestamp' => now()->toIso8601String(),
        ];

        // Keep only last 100 records
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }

        Cache::put($key, $history, self::CACHE_TTL_HISTORY);

        // Invalidate metrics cache to force recalculation
        $this->cacheService->invalidatePerformanceMetrics();

        Log::debug('Google Services: Timing recorded', [
            'service' => $service,
            'operation' => $operation,
            'duration_ms' => $durationMs,
            'success' => $success,
        ]);
    }

    // =========================================================================
    // Metrics Collection
    // =========================================================================

    /**
     * Get SSO performance metrics
     */
    public function getSsoMetrics(): array
    {
        return Cache::remember(self::CACHE_KEY_SSO_METRICS, self::CACHE_TTL_METRICS, function () {
            return $this->calculateServiceMetrics('sso');
        });
    }

    /**
     * Get Gmail performance metrics
     */
    public function getGmailMetrics(): array
    {
        return Cache::remember(self::CACHE_KEY_GMAIL_METRICS, self::CACHE_TTL_METRICS, function () {
            return $this->calculateServiceMetrics('gmail');
        });
    }

    /**
     * Get combined performance metrics
     */
    public function getAllMetrics(): array
    {
        $ssoMetrics = $this->getSsoMetrics();
        $gmailMetrics = $this->getGmailMetrics();
        $alerts = $this->getActiveAlerts();

        return [
            'sso' => $ssoMetrics,
            'gmail' => $gmailMetrics,
            'alerts' => $alerts,
            'thresholds' => [
                'sso_auth_ms' => self::THRESHOLD_SSO_AUTH_MS,
                'gmail_send_ms' => self::THRESHOLD_GMAIL_SEND_MS,
                'api_call_ms' => self::THRESHOLD_API_CALL_MS,
                'failure_rate_percent' => self::ALERT_FAILURE_RATE_PERCENT,
            ],
            'collected_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Calculate metrics for a service
     */
    private function calculateServiceMetrics(string $service): array
    {
        // Get metrics from audit log for last 24 hours
        $auditMetrics = $this->getAuditLogMetrics($service);

        // Get timing history
        $timingHistory = $this->getTimingHistory($service);

        // Calculate statistics
        $totalOperations = $auditMetrics['total'] ?? 0;
        $successfulOperations = $auditMetrics['successful'] ?? 0;
        $failedOperations = $auditMetrics['failed'] ?? 0;

        $successRate = $totalOperations > 0
            ? round(($successfulOperations / $totalOperations) * 100, 2)
            : 100;

        $failureRate = $totalOperations > 0
            ? round(($failedOperations / $totalOperations) * 100, 2)
            : 0;

        // Calculate timing statistics
        $timings = array_column($timingHistory, 'duration_ms');
        $avgDuration = count($timings) > 0 ? round(array_sum($timings) / count($timings), 2) : 0;
        $maxDuration = count($timings) > 0 ? max($timings) : 0;
        $minDuration = count($timings) > 0 ? min($timings) : 0;

        // Calculate percentiles
        $p95 = $this->calculatePercentile($timings, 95);
        $p99 = $this->calculatePercentile($timings, 99);

        // Count slow operations
        $threshold = $service === 'sso' ? self::THRESHOLD_SSO_AUTH_MS : self::THRESHOLD_GMAIL_SEND_MS;
        $slowOperations = count(array_filter($timings, fn ($t) => $t > $threshold));

        return [
            'total_operations' => $totalOperations,
            'successful_operations' => $successfulOperations,
            'failed_operations' => $failedOperations,
            'success_rate_percent' => $successRate,
            'failure_rate_percent' => $failureRate,
            'timing' => [
                'average_ms' => $avgDuration,
                'max_ms' => $maxDuration,
                'min_ms' => $minDuration,
                'p95_ms' => $p95,
                'p99_ms' => $p99,
            ],
            'slow_operations' => $slowOperations,
            'threshold_ms' => $threshold,
            'health_status' => $this->determineHealthStatus($failureRate, $slowOperations),
            'period' => '24h',
        ];
    }

    /**
     * Get metrics from audit log
     */
    private function getAuditLogMetrics(string $service): array
    {
        try {
            $serviceType = $service === 'sso'
                ? GoogleServicesAuditLog::SERVICE_SSO
                : GoogleServicesAuditLog::SERVICE_GMAIL;

            $query = GoogleServicesAuditLog::where('service_type', $serviceType)
                ->where('created_at', '>=', now()->subDay());

            return [
                'total' => (clone $query)->count(),
                'successful' => (clone $query)->where('success', true)->count(),
                'failed' => (clone $query)->where('success', false)->count(),
            ];
        } catch (\Exception $e) {
            Log::warning('Google Services: Failed to get audit log metrics', [
                'service' => $service,
                'error' => $e->getMessage(),
            ]);

            return ['total' => 0, 'successful' => 0, 'failed' => 0];
        }
    }

    /**
     * Get timing history for a service
     */
    private function getTimingHistory(string $service): array
    {
        $operations = $service === 'sso' ? ['authentication'] : ['send_email', 'authenticate', 'test_connectivity'];
        $history = [];

        foreach ($operations as $operation) {
            $key = self::CACHE_KEY_TIMING_HISTORY.":{$service}:{$operation}";
            $operationHistory = Cache::get($key, []);
            $history = array_merge($history, $operationHistory);
        }

        return $history;
    }

    /**
     * Calculate percentile
     */
    private function calculatePercentile(array $values, int $percentile): float
    {
        if (empty($values)) {
            return 0;
        }

        sort($values);
        $index = ceil(($percentile / 100) * count($values)) - 1;
        $index = max(0, min($index, count($values) - 1));

        return round($values[(int) $index], 2);
    }

    /**
     * Determine health status based on metrics
     */
    private function determineHealthStatus(float $failureRate, int $slowOperations): string
    {
        if ($failureRate >= self::ALERT_FAILURE_RATE_PERCENT || $slowOperations >= self::ALERT_SLOW_OPERATION_COUNT) {
            return 'critical';
        }

        if ($failureRate >= self::ALERT_FAILURE_RATE_PERCENT / 2 || $slowOperations >= self::ALERT_SLOW_OPERATION_COUNT / 2) {
            return 'warning';
        }

        return 'healthy';
    }

    // =========================================================================
    // Alerting
    // =========================================================================

    /**
     * Trigger slow operation alert
     */
    private function triggerSlowOperationAlert(string $service, string $operation, float $durationMs): void
    {
        $alertKey = self::CACHE_KEY_ALERTS.":{$service}:{$operation}:slow";
        $alerts = Cache::get(self::CACHE_KEY_ALERTS, []);

        $alert = [
            'type' => 'slow_operation',
            'service' => $service,
            'operation' => $operation,
            'duration_ms' => $durationMs,
            'threshold_ms' => $service === 'sso' ? self::THRESHOLD_SSO_AUTH_MS : self::THRESHOLD_GMAIL_SEND_MS,
            'timestamp' => now()->toIso8601String(),
        ];

        $alerts[] = $alert;

        // Keep only last 50 alerts
        if (count($alerts) > 50) {
            $alerts = array_slice($alerts, -50);
        }

        Cache::put(self::CACHE_KEY_ALERTS, $alerts, self::CACHE_TTL_HISTORY);

        Log::warning('Google Services: Slow operation detected', $alert);

        // Log to activity log for audit
        activity('google_services_performance')
            ->withProperties($alert)
            ->log("Slow {$service} operation: {$operation}");
    }

    /**
     * Get active alerts
     */
    public function getActiveAlerts(): array
    {
        $alerts = Cache::get(self::CACHE_KEY_ALERTS, []);

        // Filter to last hour only
        $oneHourAgo = now()->subHour()->toIso8601String();

        return array_filter($alerts, fn ($alert) => ($alert['timestamp'] ?? '') >= $oneHourAgo);
    }

    /**
     * Clear alerts
     */
    public function clearAlerts(): void
    {
        Cache::forget(self::CACHE_KEY_ALERTS);
    }

    // =========================================================================
    // Dashboard Data
    // =========================================================================

    /**
     * Get performance dashboard data
     */
    public function getDashboardData(): array
    {
        $metrics = $this->getAllMetrics();
        $trends = $this->getPerformanceTrends();

        return [
            'metrics' => $metrics,
            'trends' => $trends,
            'summary' => [
                'overall_health' => $this->getOverallHealth($metrics),
                'sso_health' => $metrics['sso']['health_status'] ?? 'unknown',
                'gmail_health' => $metrics['gmail']['health_status'] ?? 'unknown',
                'active_alerts_count' => count($metrics['alerts'] ?? []),
            ],
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get performance trends for charts
     */
    public function getPerformanceTrends(int $hours = 24): array
    {
        $ssoHistory = $this->getTimingHistory('sso');
        $gmailHistory = $this->getTimingHistory('gmail');

        return [
            'sso' => $this->aggregateTimingByHour($ssoHistory, $hours),
            'gmail' => $this->aggregateTimingByHour($gmailHistory, $hours),
        ];
    }

    /**
     * Aggregate timing data by hour
     */
    private function aggregateTimingByHour(array $history, int $hours): array
    {
        $aggregated = [];
        $cutoff = now()->subHours($hours);

        // Group by hour
        $grouped = [];
        foreach ($history as $record) {
            $timestamp = $record['timestamp'] ?? null;
            if (! $timestamp || $timestamp < $cutoff->toIso8601String()) {
                continue;
            }

            $hour = substr($timestamp, 0, 13); // YYYY-MM-DDTHH
            if (! isset($grouped[$hour])) {
                $grouped[$hour] = [];
            }
            $grouped[$hour][] = $record['duration_ms'];
        }

        // Calculate averages
        foreach ($grouped as $hour => $durations) {
            $aggregated[] = [
                'hour' => $hour,
                'average_ms' => round(array_sum($durations) / count($durations), 2),
                'count' => count($durations),
                'max_ms' => max($durations),
            ];
        }

        // Sort by hour
        usort($aggregated, fn ($a, $b) => $a['hour'] <=> $b['hour']);

        return $aggregated;
    }

    /**
     * Get overall health status
     */
    private function getOverallHealth(array $metrics): string
    {
        $ssoHealth = $metrics['sso']['health_status'] ?? 'unknown';
        $gmailHealth = $metrics['gmail']['health_status'] ?? 'unknown';
        $alertCount = count($metrics['alerts'] ?? []);

        if ($ssoHealth === 'critical' || $gmailHealth === 'critical' || $alertCount >= 5) {
            return 'critical';
        }

        if ($ssoHealth === 'warning' || $gmailHealth === 'warning' || $alertCount >= 2) {
            return 'warning';
        }

        return 'healthy';
    }

    // =========================================================================
    // Cache Management
    // =========================================================================

    /**
     * Clear all performance caches
     */
    public function clearCaches(): void
    {
        Cache::forget(self::CACHE_KEY_SSO_METRICS);
        Cache::forget(self::CACHE_KEY_GMAIL_METRICS);
        Cache::forget(self::CACHE_KEY_ALERTS);

        Log::info('Google Services: Performance caches cleared');
    }
}
