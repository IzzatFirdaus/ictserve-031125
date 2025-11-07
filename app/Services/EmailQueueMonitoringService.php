<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

/**
 * Email Queue Monitoring Service
 *
 * Monitors email queue status, tracks job processing, and provides queue health metrics.
 * Supports queue management operations and performance monitoring.
 *
 * Requirements: 18.2, D03-FR-014.2
 *
 * @see D04 §12.1 Email queue monitoring
 */
class EmailQueueMonitoringService
{
    private const CACHE_TTL = 60; // 1 minute for real-time monitoring

    private const QUEUE_NAMES = ['default', 'emails', 'notifications'];

    /**
     * Get queue monitoring dashboard statistics
     *
     * @return array<string, mixed>
     */
    public function getQueueStats(): array
    {
        return Cache::remember('email_queue_stats', self::CACHE_TTL, function () {
            $stats = [];

            foreach (self::QUEUE_NAMES as $queueName) {
                $stats[$queueName] = [
                    'pending' => $this->getPendingJobsCount($queueName),
                    'processing' => $this->getProcessingJobsCount($queueName),
                    'failed' => $this->getFailedJobsCount($queueName),
                    'completed_today' => $this->getCompletedJobsToday($queueName),
                    'average_processing_time' => $this->getAverageProcessingTime($queueName),
                    'health_status' => $this->getQueueHealthStatus($queueName),
                ];
            }

            return [
                'queues' => $stats,
                'total_pending' => array_sum(array_column($stats, 'pending')),
                'total_failed' => array_sum(array_column($stats, 'failed')),
                'overall_health' => $this->getOverallQueueHealth($stats),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get failed jobs with details
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getFailedJobs(int $limit = 50): Collection
    {
        return collect(DB::table('failed_jobs')
            ->select([
                'id',
                'uuid',
                'connection',
                'queue',
                'payload',
                'exception',
                'failed_at',
            ])
            ->orderBy('failed_at', 'desc')
            ->limit($limit)
            ->get())
            ->map(function ($job) {
                $payload = json_decode($job->payload, true);

                return [
                    'id' => $job->id,
                    'uuid' => $job->uuid,
                    'queue' => $job->queue,
                    'job_class' => $payload['displayName'] ?? 'Unknown',
                    'attempts' => $payload['attempts'] ?? 0,
                    'exception' => $this->formatException($job->exception),
                    'failed_at' => Carbon::parse($job->failed_at),
                    'can_retry' => $this->canRetryJob($job),
                ];
            });
    }

    /**
     * Get queue processing trends
     *
     * @return array<string, array<string, mixed>>
     */
    public function getProcessingTrends(int $days = 7): array
    {
        return Cache::remember("queue_trends_{$days}d", 300, function () use ($days) {
            $trends = [];
            $startDate = Carbon::now()->subDays($days);

            for ($i = 0; $i < $days; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateKey = $date->format('Y-m-d');

                $dayStats = DB::table('jobs')
                    ->whereDate('created_at', $date)
                    ->selectRaw('
                        COUNT(*) as total_jobs,
                        AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_processing_time
                    ')
                    ->first();

                $failedStats = DB::table('failed_jobs')
                    ->whereDate('failed_at', $date)
                    ->count();

                $trends[$dateKey] = [
                    'date' => $date->format('M j'),
                    'total_jobs' => $dayStats->total_jobs ?? 0,
                    'failed_jobs' => $failedStats,
                    'success_rate' => $dayStats->total_jobs > 0 ?
                        round((($dayStats->total_jobs - $failedStats) / $dayStats->total_jobs) * 100, 1) : 100,
                    'avg_processing_time' => round($dayStats->avg_processing_time ?? 0, 1),
                ];
            }

            return $trends;
        });
    }

    /**
     * Retry failed job
     */
    public function retryFailedJob(string $jobId): bool
    {
        try {
            $failedJob = DB::table('failed_jobs')->where('id', $jobId)->first();

            if (! $failedJob) {
                return false;
            }

            // Retry the job using Laravel's queue system
            Queue::retry($failedJob->uuid);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to retry queue job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Bulk retry failed jobs
     *
     * @param  array<string>  $jobIds
     * @return array<string, int>
     */
    public function bulkRetryFailedJobs(array $jobIds): array
    {
        $results = ['success' => 0, 'failed' => 0];

        foreach ($jobIds as $jobId) {
            if ($this->retryFailedJob($jobId)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        // Clear cache after bulk operation
        $this->clearCache();

        return $results;
    }

    /**
     * Delete failed job
     */
    public function deleteFailedJob(string $jobId): bool
    {
        try {
            $deleted = DB::table('failed_jobs')->where('id', $jobId)->delete();

            if ($deleted) {
                $this->clearCache();
            }

            return $deleted > 0;
        } catch (\Exception $e) {
            \Log::error('Failed to delete queue job', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get queue worker status
     *
     * @return array<string, mixed>
     */
    public function getWorkerStatus(): array
    {
        // Check if queue workers are running by looking at recent job processing
        $recentActivity = DB::table('jobs')
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->count();

        $lastProcessedJob = DB::table('jobs')
            ->orderBy('updated_at', 'desc')
            ->first();

        return [
            'workers_active' => $recentActivity > 0,
            'recent_activity_count' => $recentActivity,
            'last_job_processed' => $lastProcessedJob ?
                Carbon::parse($lastProcessedJob->updated_at) : null,
            'estimated_workers' => $this->estimateActiveWorkers(),
            'queue_lag' => $this->calculateQueueLag(),
        ];
    }

    /**
     * Get pending jobs count for queue
     */
    private function getPendingJobsCount(string $queueName): int
    {
        return DB::table('jobs')
            ->where('queue', $queueName)
            ->where('reserved_at', null)
            ->count();
    }

    /**
     * Get processing jobs count for queue
     */
    private function getProcessingJobsCount(string $queueName): int
    {
        return DB::table('jobs')
            ->where('queue', $queueName)
            ->whereNotNull('reserved_at')
            ->count();
    }

    /**
     * Get failed jobs count for queue
     */
    private function getFailedJobsCount(string $queueName): int
    {
        return DB::table('failed_jobs')
            ->where('queue', $queueName)
            ->count();
    }

    /**
     * Get completed jobs count for today
     */
    private function getCompletedJobsToday(string $queueName): int
    {
        // Estimate completed jobs by looking at job history
        return DB::table('jobs')
            ->where('queue', $queueName)
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    /**
     * Get average processing time for queue
     */
    private function getAverageProcessingTime(string $queueName): float
    {
        $avgTime = DB::table('jobs')
            ->where('queue', $queueName)
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time')
            ->value('avg_time');

        return round($avgTime ?? 0, 1);
    }

    /**
     * Get queue health status
     */
    private function getQueueHealthStatus(string $queueName): string
    {
        $pending = $this->getPendingJobsCount($queueName);
        $failed = $this->getFailedJobsCount($queueName);
        $avgTime = $this->getAverageProcessingTime($queueName);

        if ($failed > 10 || $avgTime > 300) {
            return 'critical';
        }

        if ($pending > 100 || $failed > 5 || $avgTime > 120) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Get overall queue health
     *
     * @param  array<string, array<string, mixed>>  $queueStats
     */
    private function getOverallQueueHealth(array $queueStats): string
    {
        $healthStatuses = array_column($queueStats, 'health_status');

        if (in_array('critical', $healthStatuses)) {
            return 'critical';
        }

        if (in_array('warning', $healthStatuses)) {
            return 'warning';
        }

        return 'healthy';
    }

    /**
     * Format exception message for display
     */
    private function formatException(string $exception): string
    {
        // Extract the main error message from the exception
        $lines = explode("\n", $exception);
        $mainError = $lines[0] ?? 'Unknown error';

        // Limit length for display
        return strlen($mainError) > 200 ?
            substr($mainError, 0, 200).'...' : $mainError;
    }

    /**
     * Check if job can be retried
     */
    private function canRetryJob(object $job): bool
    {
        $payload = json_decode($job->payload, true);
        $attempts = $payload['attempts'] ?? 0;
        $maxAttempts = $payload['maxTries'] ?? 3;

        return $attempts < $maxAttempts;
    }

    /**
     * Estimate number of active workers
     */
    private function estimateActiveWorkers(): int
    {
        // Estimate based on concurrent job processing
        $concurrentJobs = DB::table('jobs')
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '>=', Carbon::now()->subMinutes(1))
            ->count();

        return max(1, $concurrentJobs);
    }

    /**
     * Calculate queue lag in seconds
     */
    private function calculateQueueLag(): int
    {
        $oldestPendingJob = DB::table('jobs')
            ->where('reserved_at', null)
            ->orderBy('created_at')
            ->first();

        if (! $oldestPendingJob) {
            return 0;
        }

        return Carbon::now()->diffInSeconds(Carbon::parse($oldestPendingJob->created_at));
    }

    /**
     * Clear queue monitoring cache
     */
    public function clearCache(): void
    {
        Cache::forget('email_queue_stats');
        Cache::tags(['queue_trends'])->flush();
    }
}
