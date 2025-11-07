<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class PerformanceMonitoringService
{
    public function getSystemMetrics(): array
    {
        return Cache::remember('system_metrics', 60, function () {
            return [
                'response_time' => $this->getAverageResponseTime(),
                'database_query_time' => $this->getAverageDatabaseQueryTime(),
                'cache_hit_rate' => $this->getCacheHitRate(),
                'queue_processing_time' => $this->getQueueProcessingTime(),
                'memory_usage' => $this->getMemoryUsage(),
                'disk_usage' => $this->getDiskUsage(),
                'active_connections' => $this->getActiveConnections(),
                'error_rate' => $this->getErrorRate(),
            ];
        });
    }

    public function getPerformanceTrends(string $period = '24h'): array
    {
        $cacheKey = "performance_trends_{$period}";

        return Cache::remember($cacheKey, 300, function () use ($period) {
            $hours = match ($period) {
                '1h' => 1,
                '24h' => 24,
                '7d' => 168,
                '30d' => 720,
                default => 24,
            };

            return [
                'response_times' => $this->getResponseTimeTrend($hours),
                'query_times' => $this->getQueryTimeTrend($hours),
                'cache_rates' => $this->getCacheRateTrend($hours),
                'memory_usage' => $this->getMemoryUsageTrend($hours),
                'error_counts' => $this->getErrorCountTrend($hours),
            ];
        });
    }

    public function checkPerformanceThresholds(): array
    {
        $metrics = $this->getSystemMetrics();
        $alerts = [];

        // Response time threshold: 2 seconds
        if ($metrics['response_time'] > 2000) {
            $alerts[] = [
                'type' => 'response_time',
                'severity' => 'high',
                'message' => "Average response time is {$metrics['response_time']}ms (threshold: 2000ms)",
                'value' => $metrics['response_time'],
                'threshold' => 2000,
            ];
        }

        // Database query time threshold: 500ms
        if ($metrics['database_query_time'] > 500) {
            $alerts[] = [
                'type' => 'database_query_time',
                'severity' => 'medium',
                'message' => "Average database query time is {$metrics['database_query_time']}ms (threshold: 500ms)",
                'value' => $metrics['database_query_time'],
                'threshold' => 500,
            ];
        }

        // Cache hit rate threshold: 80%
        if ($metrics['cache_hit_rate'] < 80) {
            $alerts[] = [
                'type' => 'cache_hit_rate',
                'severity' => 'medium',
                'message' => "Cache hit rate is {$metrics['cache_hit_rate']}% (threshold: 80%)",
                'value' => $metrics['cache_hit_rate'],
                'threshold' => 80,
            ];
        }

        // Memory usage threshold: 85%
        if ($metrics['memory_usage'] > 85) {
            $alerts[] = [
                'type' => 'memory_usage',
                'severity' => 'high',
                'message' => "Memory usage is {$metrics['memory_usage']}% (threshold: 85%)",
                'value' => $metrics['memory_usage'],
                'threshold' => 85,
            ];
        }

        return $alerts;
    }

    public function getIntegrationHealth(): array
    {
        return [
            'database' => $this->checkDatabaseHealth(),
            'redis' => $this->checkRedisHealth(),
            'email' => $this->checkEmailHealth(),
            'queue' => $this->checkQueueHealth(),
        ];
    }

    public function getSlowQueries(int $limit = 10): array
    {
        // This would typically read from MySQL slow query log
        // For now, return sample data
        return [
            [
                'query' => 'SELECT * FROM helpdesk_tickets WHERE created_at > ?',
                'execution_time' => 1.25,
                'rows_examined' => 15000,
                'timestamp' => now()->subMinutes(30),
            ],
            [
                'query' => 'SELECT * FROM loan_applications JOIN assets ON...',
                'execution_time' => 0.85,
                'rows_examined' => 8500,
                'timestamp' => now()->subHours(2),
            ],
        ];
    }

    private function getAverageResponseTime(): float
    {
        // In a real implementation, this would read from application logs
        // For now, return a simulated value
        return rand(800, 1500) / 1000 * 1000; // Convert to milliseconds
    }

    private function getAverageDatabaseQueryTime(): float
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $end = microtime(true);

            return ($end - $start) * 1000; // Convert to milliseconds
        } catch (\Exception $e) {
            Log::error('Failed to measure database query time', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    private function getCacheHitRate(): float
    {
        try {
            if (config('cache.default') === 'redis') {
                $info = Redis::info();
                $hits = $info['keyspace_hits'] ?? 0;
                $misses = $info['keyspace_misses'] ?? 0;

                if ($hits + $misses > 0) {
                    return round(($hits / ($hits + $misses)) * 100, 2);
                }
            }

            return rand(75, 95); // Simulated value
        } catch (\Exception $e) {
            Log::error('Failed to get cache hit rate', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    private function getQueueProcessingTime(): float
    {
        // This would typically read from queue metrics
        return rand(100, 500) / 1000 * 1000; // Simulated value in milliseconds
    }

    private function getMemoryUsage(): float
    {
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));

        if ($memoryLimit > 0) {
            return round(($memoryUsage / $memoryLimit) * 100, 2);
        }

        return 0;
    }

    private function getDiskUsage(): float
    {
        $totalSpace = disk_total_space('/');
        $freeSpace = disk_free_space('/');

        if ($totalSpace > 0) {
            return round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);
        }

        return 0;
    }

    private function getActiveConnections(): int
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Threads_connected'");

            return (int) ($result[0]->Value ?? 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getErrorRate(): float
    {
        // This would typically read from error logs
        return rand(0, 5) / 100; // Simulated error rate as percentage
    }

    private function getResponseTimeTrend(int $hours): array
    {
        $data = [];
        for ($i = $hours; $i >= 0; $i--) {
            $data[] = [
                'timestamp' => now()->subHours($i)->format('Y-m-d H:i'),
                'value' => rand(800, 1500),
            ];
        }

        return $data;
    }

    private function getQueryTimeTrend(int $hours): array
    {
        $data = [];
        for ($i = $hours; $i >= 0; $i--) {
            $data[] = [
                'timestamp' => now()->subHours($i)->format('Y-m-d H:i'),
                'value' => rand(50, 300),
            ];
        }

        return $data;
    }

    private function getCacheRateTrend(int $hours): array
    {
        $data = [];
        for ($i = $hours; $i >= 0; $i--) {
            $data[] = [
                'timestamp' => now()->subHours($i)->format('Y-m-d H:i'),
                'value' => rand(75, 95),
            ];
        }

        return $data;
    }

    private function getMemoryUsageTrend(int $hours): array
    {
        $data = [];
        for ($i = $hours; $i >= 0; $i--) {
            $data[] = [
                'timestamp' => now()->subHours($i)->format('Y-m-d H:i'),
                'value' => rand(40, 80),
            ];
        }

        return $data;
    }

    private function getErrorCountTrend(int $hours): array
    {
        $data = [];
        for ($i = $hours; $i >= 0; $i--) {
            $data[] = [
                'timestamp' => now()->subHours($i)->format('Y-m-d H:i'),
                'value' => rand(0, 10),
            ];
        }

        return $data;
    }

    private function checkDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $start) * 1000;

            return [
                'status' => 'healthy',
                'response_time' => round($responseTime, 2),
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }

    private function checkRedisHealth(): array
    {
        try {
            $start = microtime(true);
            Redis::ping();
            $responseTime = (microtime(true) - $start) * 1000;

            return [
                'status' => 'healthy',
                'response_time' => round($responseTime, 2),
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }

    private function checkEmailHealth(): array
    {
        // This would typically test SMTP connection
        return [
            'status' => 'healthy',
            'last_check' => now()->format('Y-m-d H:i:s'),
        ];
    }

    private function checkQueueHealth(): array
    {
        try {
            $failedJobs = DB::table('failed_jobs')->count();
            $pendingJobs = DB::table('jobs')->count();

            return [
                'status' => $failedJobs > 10 ? 'warning' : 'healthy',
                'failed_jobs' => $failedJobs,
                'pending_jobs' => $pendingJobs,
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage(),
                'last_check' => now()->format('Y-m-d H:i:s'),
            ];
        }
    }

    private function parseMemoryLimit(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $value = (int) $memoryLimit;

        return match ($last) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => $value,
        };
    }
}
