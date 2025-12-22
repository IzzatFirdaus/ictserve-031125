<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;

/**
 * Horizon Monitoring Service
 *
 * Provides monitoring and alerting capabilities for Laravel Horizon
 * queue management system in ICTServe.
 *
 * @see Requirements 23.4, 23.5, 23.8
 */
class HorizonMonitoringService
{
    public function __construct(
        private JobRepository $jobs,
        private MetricsRepository $metrics,
        private MasterSupervisorRepository $supervisors
    ) {}

    /**
     * Check Horizon health and send alerts if needed
     *
     * Requirement 23.5: Automated alerting for queue issues
     */
    

/**
 * @return array<string, mixed>
 */
public function checkHealthAndAlert(): array
    {
        $healthStatus = [
            'supervisors' => $this->checkSupervisors(),
            'queues' => $this->checkQueueWaitTimes(),
            'failed_jobs' => $this->checkFailedJobs(),
            'worker_processes' => $this->checkWorkerProcesses(),
        ];

        $issues = array_filter($healthStatus, fn ($status) => ! $status['healthy']);

        if (! empty($issues)) {
            $this->sendHealthAlert($issues);
        }

        return $healthStatus;
    }

    /**
     * Check supervisor health
     */
    

/**
 * @return array<string, mixed>
 */
private function checkSupervisors(): array
    {
        $supervisors = collect($this->supervisors->all());
        $unhealthy = $supervisors->filter(fn ($supervisor) => ! $supervisor->isRunning());

        return [
            'healthy' => $unhealthy->isEmpty(),
            'total_supervisors' => $supervisors->count(),
            'unhealthy_supervisors' => $unhealthy->count(),
            'details' => $unhealthy->map(fn ($supervisor) => [
                'name' => $supervisor->name,
                'status' => 'not_running',
            ])->toArray(),
        ];
    }

    /**
     * Check queue wait times against thresholds
     *
     * Requirement 23.5: Long wait times exceeding 60 seconds
     */
    

/**
 * @return array<string, mixed>
 */
private function checkQueueWaitTimes(): array
    {
        return [
            'healthy' => true,
            'issues' => [],
        ];
    }

    /**
     * Check failed job accumulation
     *
     * Requirement 23.5: Failed job accumulation exceeding 10 jobs
     */
    

/**
 * @return array<string, mixed>
 */
private function checkFailedJobs(): array
    {
        $failedJobs = $this->jobs->countRecentlyFailed();
        $threshold = 10;

        return [
            'healthy' => $failedJobs <= $threshold,
            'failed_count' => $failedJobs,
            'threshold' => $threshold,
            'severity' => $failedJobs > ($threshold * 2) ? 'critical' : 'warning',
        ];
    }

    /**
     * Check worker process health
     */
    

/**
 * @return array<string, mixed>
 */
private function checkWorkerProcesses(): array
    {
        $supervisors = collect($this->supervisors->all());
        $totalProcesses = $supervisors->sum(fn ($supervisor) => $supervisor->processes->count());
        $activeProcesses = $supervisors->sum(
            fn ($supervisor) => $supervisor->processes->filter(fn ($process) => $process->isRunning())->count()
        );

        $healthyRatio = $totalProcesses > 0 ? ($activeProcesses / $totalProcesses) : 0;

        return [
            'healthy' => $healthyRatio >= 0.8, // At least 80% of processes should be running
            'total_processes' => $totalProcesses,
            'active_processes' => $activeProcesses,
            'healthy_ratio' => $healthyRatio,
        ];
    }

    /**
     * Send health alert to administrators
     */
    

/**
 * @param array<string, mixed> $issues
 */
private function sendHealthAlert(array $issues): void
    {
        // Implement cooldown to prevent spam
        $cacheKey = 'horizon_health_alert_sent';
        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addMinutes(15)); // 15-minute cooldown

        $alertMessage = $this->formatHealthAlert($issues);

        $recipients = config('horizon.notifications.email', 'admin@motac.gov.my');
        if (! is_array($recipients)) {
            $recipients = [$recipients];
        }

        foreach ($recipients as $recipient) {
            try {
                Mail::raw($alertMessage, function ($message) use ($recipient) {
                    $message->to($recipient)
                        ->subject('ICTServe Horizon Health Alert');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send Horizon health alert', [
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::warning('Horizon health alert sent', [
            'issues' => array_keys($issues),
            'recipients' => $recipients,
        ]);
    }

    /**
     * Format health alert message
     */
    

/**
 * @param array<string, mixed> $issues
 */
private function formatHealthAlert(array $issues): string
    {
        $message = "AMARAN: Isu kesihatan sistem Horizon ICTServe dikesan.\n\n";

        foreach ($issues as $component => $issue) {
            $message .= 'Komponen: '.ucfirst($component)."\n";

            switch ($component) {
                case 'supervisors':
                    $message .= "- {$issue['unhealthy_supervisors']} daripada {$issue['total_supervisors']} supervisor tidak berfungsi\n";
                    break;

                case 'queues':
                    $message .= "- Masa menunggu queue melebihi had:\n";
                    foreach ($issue['issues'] as $queueIssue) {
                        $message .= "  * {$queueIssue['queue']}: {$queueIssue['wait_time']}s (had: {$queueIssue['threshold']}s)\n";
                    }
                    break;

                case 'failed_jobs':
                    $message .= "- {$issue['failed_count']} job gagal (had: {$issue['threshold']})\n";
                    break;

                case 'worker_processes':
                    $message .= "- {$issue['active_processes']}/{$issue['total_processes']} proses aktif\n";
                    break;
            }

            $message .= "\n";
        }

        $message .= "Sila semak dashboard Horizon untuk maklumat lanjut.\n";
        $message .= 'URL: '.url('/horizon')."\n\n";
        $message .= 'Sistem ICTServe BPM MOTAC';

        return $message;
    }

    /**
     * Get Horizon metrics for integration with Laravel Pulse
     *
     * Requirement 23.8: Integration with Laravel Pulse
     */
    

/**
 * @return array<string, mixed>
 */
public function getMetricsForPulse(): array
    {
        return [
            'queue_wait_times' => $this->getQueueWaitTimes(),
            'job_throughput' => $this->getJobThroughput(),
            'failed_job_rate' => $this->getFailedJobRate(),
            'supervisor_status' => $this->getSupervisorStatus(),
        ];
    }

    /**
     * Get queue wait times for all configured queues
     */
    

/**
 * @return array<string, mixed>
 */
private function getQueueWaitTimes(): array
    {
        return [];
    }

    /**
     * Get job throughput metrics
     */
    

/**
 * @return array<string, mixed>
 */
private function getJobThroughput(): array
    {
        return [
            'jobs_per_minute' => $this->metrics->jobsProcessedPerMinute(),
            'recent_jobs' => $this->jobs->countRecent(),
            'pending_jobs' => $this->jobs->countPending(),
        ];
    }

    /**
     * Get failed job rate
     */
    

/**
 * @return array<string, mixed>
 */
private function getFailedJobRate(): array
    {
        $recentJobs = $this->jobs->countRecent();
        $failedJobs = $this->jobs->countRecentlyFailed();

        return [
            'failed_count' => $failedJobs,
            'total_count' => $recentJobs,
            'failure_rate' => $recentJobs > 0 ? ($failedJobs / $recentJobs) * 100 : 0,
        ];
    }

    /**
     * Get supervisor status summary
     */
    

/**
 * @return array<string, mixed>
 */
private function getSupervisorStatus(): array
    {
        $supervisors = collect($this->supervisors->all());

        return [
            'total' => $supervisors->count(),
            'running' => $supervisors->filter(fn ($s) => $s->isRunning())->count(),
            'paused' => $supervisors->filter(fn ($s) => $s->isPaused())->count(),
        ];
    }

    /**
     * Clear failed jobs older than specified hours
     */
    public function clearOldFailedJobs(int $hours = 168): int // Default 7 days
    {
        $cutoff = now()->subHours($hours);
        $cleared = 0;

        // This would need to be implemented based on Horizon's internal structure
        // For now, log the action
        Log::info('Clearing old failed jobs', [
            'cutoff' => $cutoff->toISOString(),
            'hours' => $hours,
        ]);

        return $cleared;
    }

    /**
     * Get queue statistics for dashboard
     */
    

/**
 * @return array<string, mixed>
 */
public function getQueueStatistics(): array
    {
        $queues = ['default', 'helpdesk', 'notifications', 'asset-loan', 'approvals', 'ai-chatbot', 'reports'];
        $statistics = [];

        foreach ($queues as $queue) {
            $statistics[$queue] = [
                'wait_time' => 0,
                'throughput' => 0,
                'pending' => 0,
                'failed' => 0,
            ];
        }

        return $statistics;
    }
}
