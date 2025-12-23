<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\HorizonMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Monitor Horizon Health Command
 *
 * Monitors Laravel Horizon health and sends alerts for issues.
 * Implements Requirements 23.4, 23.5 for automated monitoring.
 *
 * @see Requirements 23.4, 23.5, 23.8
 */
class MonitorHorizonHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:monitor-health 
                            {--alert : Send alerts for detected issues}
                            {--json : Output results as JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor Horizon health and send alerts for queue issues';

    /**
     * Execute the console command
     */
    public function handle(HorizonMonitoringService $monitoring): int
    {
        $this->info('Monitoring Horizon health...');

        try {
            $healthStatus = $monitoring->checkHealthAndAlert();

            if ($this->option('json')) {
                $json = json_encode($healthStatus, JSON_PRETTY_PRINT);
                if ($json !== false) {
                    $this->line($json);
                }

                return 0;
            }

            $this->displayHealthStatus($healthStatus);

            $allHealthy = collect($healthStatus)->every(fn ($status) => $status['healthy']);

            if ($allHealthy) {
                $this->info('✅ All Horizon components are healthy');

                return 0;
            } else {
                $this->error('❌ Some Horizon components have issues');

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Failed to check Horizon health: '.$e->getMessage());
            Log::error('Horizon health check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 1;
        }
    }

    /**
     * Display health status in console
     *
     * @param array<string, mixed> $healthStatus
     */
    private function displayHealthStatus(array $healthStatus): void
    {
        $this->newLine();
        $this->info('Horizon Health Status Report');
        $this->info('================================');

        // Supervisors
        $supervisors = $healthStatus['supervisors'] ?? [];
        $supervisorsHealthy = $supervisors['healthy'] ?? false;
        $supervisorsTotal = $supervisors['total_supervisors'] ?? 0;
        $supervisorsUnhealthy = $supervisors['unhealthy_supervisors'] ?? 0;
        $status = $supervisorsHealthy ? '✅' : '❌';
        $this->line("{$status} Supervisors: {$supervisorsTotal} total, {$supervisorsUnhealthy} unhealthy");

        if (! $supervisorsHealthy && ! empty($supervisors['details'])) {
            foreach ($supervisors['details'] as $detail) {
                $this->line("   - {$detail['name']}: {$detail['status']}");
            }
        }

        // Queue wait times
        $queues = $healthStatus['queues'] ?? [];
        $queuesHealthy = $queues['healthy'] ?? false;
        $status = $queuesHealthy ? '✅' : '❌';
        $this->line("{$status} Queue Wait Times");

        if (! $queuesHealthy && ! empty($queues['issues'])) {
            foreach ($queues['issues'] as $issue) {
                $severity = ($issue['severity'] ?? '') === 'critical' ? '🔴' : '🟡';
                $this->line("   {$severity} {$issue['queue']}: {$issue['wait_time']}s (threshold: {$issue['threshold']}s)");
            }
        }

        // Failed jobs
        $failedJobs = $healthStatus['failed_jobs'] ?? [];
        $failedJobsHealthy = $failedJobs['healthy'] ?? false;
        $failedCount = $failedJobs['failed_count'] ?? 0;
        $failedThreshold = $failedJobs['threshold'] ?? 0;
        $status = $failedJobsHealthy ? '✅' : '❌';
        $this->line("{$status} Failed Jobs: {$failedCount} (threshold: {$failedThreshold})");

        // Worker processes
        $workers = $healthStatus['worker_processes'] ?? [];
        $workersHealthy = $workers['healthy'] ?? false;
        $activeProcesses = $workers['active_processes'] ?? 0;
        $totalProcesses = $workers['total_processes'] ?? 0;
        $healthyRatio = round((float) ($workers['healthy_ratio'] ?? 0) * 100, 1);
        $status = $workersHealthy ? '✅' : '❌';
        $this->line("{$status} Worker Processes: {$activeProcesses}/{$totalProcesses} active ({$healthyRatio}%)");

        $this->newLine();
    }
}
