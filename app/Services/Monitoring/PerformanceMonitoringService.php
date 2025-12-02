<?php

declare(strict_types=1);

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Performance Monitoring Service
 *
 * Tracks and reports Core Web Vitals and application performance metrics.
 * Provides alerting when metrics exceed configured thresholds.
 *
 * @see D12 UI/UX Design Guide - Performance Requirements
 * @see D13 UI/UX Frontend Framework - Performance Monitoring
 *
 * @requirements R08 Performance Optimization and Core Web Vitals
 *
 * @version 1.0.0
 */
class PerformanceMonitoringService
{
    /**
     * Core Web Vitals targets.
     */
    private array $targets;

    /**
     * Alert threshold multiplier.
     */
    private float $alertThreshold;

    public function __construct()
    {
        $this->targets = config('performance.core_web_vitals', [
            'lcp' => 2500,
            'fid' => 100,
            'cls' => 0.1,
            'ttfb' => 600,
        ]);

        $this->alertThreshold = config('performance.monitoring.alert_threshold', 1.5);
    }

    /**
     * Record a Core Web Vitals metric.
     */
    public function recordMetric(string $name, float $value, string $page, ?string $userId = null): void
    {
        $metric = [
            'name' => strtoupper($name),
            'value' => $value,
            'page' => $page,
            'user_id' => $userId,
            'timestamp' => now()->toIso8601String(),
            'rating' => $this->getRating($name, $value),
        ];

        // Store in cache for aggregation
        $cacheKey = "performance_metrics:{$name}:".now()->format('Y-m-d-H');
        $metrics = Cache::get($cacheKey, []);
        $metrics[] = $metric;
        Cache::put($cacheKey, $metrics, now()->addHours(24));

        // Check for alerts
        $this->checkAlert($name, $value, $page);

        // Log poor metrics
        if ($metric['rating'] === 'poor') {
            Log::channel('daily')->warning('Poor Core Web Vital detected', $metric);
        }
    }

    /**
     * Get the rating for a metric value.
     */
    public function getRating(string $name, float $value): string
    {
        $name = strtolower($name);
        $target = $this->targets[$name] ?? 0;

        if ($target === 0) {
            return 'unknown';
        }

        // CLS has different thresholds
        if ($name === 'cls') {
            if ($value <= 0.1) {
                return 'good';
            }
            if ($value <= 0.25) {
                return 'needs-improvement';
            }

            return 'poor';
        }

        // Time-based metrics (LCP, FID, TTFB)
        if ($value <= $target) {
            return 'good';
        }
        if ($value <= $target * 1.5) {
            return 'needs-improvement';
        }

        return 'poor';
    }

    /**
     * Check if an alert should be triggered.
     */
    private function checkAlert(string $name, float $value, string $page): void
    {
        $name = strtolower($name);
        $target = $this->targets[$name] ?? 0;

        if ($target === 0) {
            return;
        }

        $threshold = $target * $this->alertThreshold;

        if ($value > $threshold) {
            Log::channel('daily')->alert('Performance alert: {metric} exceeded threshold', [
                'metric' => strtoupper($name),
                'value' => $value,
                'threshold' => $threshold,
                'target' => $target,
                'page' => $page,
            ]);
        }
    }

    /**
     * Get aggregated metrics for a time period.
     */
    public function getAggregatedMetrics(string $name, int $hours = 24): array
    {
        $metrics = [];
        $now = now();

        for ($i = 0; $i < $hours; $i++) {
            $cacheKey = "performance_metrics:{$name}:".$now->copy()->subHours($i)->format('Y-m-d-H');
            $hourMetrics = Cache::get($cacheKey, []);
            $metrics = array_merge($metrics, $hourMetrics);
        }

        if (empty($metrics)) {
            return [
                'count' => 0,
                'average' => 0,
                'p75' => 0,
                'p95' => 0,
                'min' => 0,
                'max' => 0,
            ];
        }

        $values = array_column($metrics, 'value');
        sort($values);

        $count = count($values);

        return [
            'count' => $count,
            'average' => array_sum($values) / $count,
            'p75' => $values[(int) ($count * 0.75)] ?? 0,
            'p95' => $values[(int) ($count * 0.95)] ?? 0,
            'min' => min($values),
            'max' => max($values),
        ];
    }

    /**
     * Get performance summary for dashboard.
     */
    public function getPerformanceSummary(): array
    {
        return [
            'lcp' => $this->getAggregatedMetrics('lcp'),
            'fid' => $this->getAggregatedMetrics('fid'),
            'cls' => $this->getAggregatedMetrics('cls'),
            'ttfb' => $this->getAggregatedMetrics('ttfb'),
            'targets' => $this->targets,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Check if all Core Web Vitals are meeting targets.
     */
    public function isHealthy(): bool
    {
        foreach (['lcp', 'fid', 'cls', 'ttfb'] as $metric) {
            $aggregated = $this->getAggregatedMetrics($metric);

            if ($aggregated['count'] === 0) {
                continue;
            }

            $target = $this->targets[$metric];
            if ($aggregated['p75'] > $target) {
                return false;
            }
        }

        return true;
    }
}
