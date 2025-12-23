<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\HorizonMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

/**
 * Production Horizon Monitoring Command
 *
 * Comprehensive monitoring command for production Horizon deployment
 * that checks health, performance, and sends alerts when needed.
 *
 * Requirements: 23.4, 23.5, 23.8
 */
class MonitorHorizonProduction extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'horizon:monitor-production 
                            {--alert : Send alerts for critical issues}
                            {--detailed : Include detailed metrics in output}
                            {--json : Output results in JSON format}';

    /**
     * The console command description.
     */
    protected $description = 'Monitor Horizon health and performance in production';

    /**
     * Execute the console command
     */
    public function handle(
        HorizonMonitoringService $monitoring,
        MasterSupervisorRepository $masterRepository
    ): int {
        $sendAlerts = (bool) $this->option('alert');
        $detailed = (bool) $this->option('detailed');
        $jsonOutput = (bool) $this->option('json');

        try {
            // Collect comprehensive monitoring data
            $monitoringData = $this->collectMonitoringData($monitoring, $masterRepository);

            // Analyze health status
            $healthStatus = $this->analyzeHealthStatus($monitoringData);

            // Send alerts if needed
            if ($sendAlerts && ! $healthStatus['overall_healthy']) {
                $this->sendHealthAlerts($healthStatus, $monitoringData);
            }

            // Output results
            if ($jsonOutput) {
                $this->outputJson($monitoringData, $healthStatus);
            } else {
                $this->outputConsole($monitoringData, $healthStatus, $detailed);
            }

            // Log monitoring results
            $this->logMonitoringResults($healthStatus, $monitoringData);

            return $healthStatus['overall_healthy'] ? 0 : 1;
        } catch (\Exception $e) {
            $this->error('Production monitoring failed: '.$e->getMessage());
            Log::error('Horizon production monitoring exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return 2;
        }
    }

    /**
     * Collect comprehensive monitoring data.
     *
     * @return array{
     *   timestamp: string,
     *   environment: string,
     *   masters: array{
     *     total: int,
     *     running: int,
     *     details: array<int, array{name: string, running: bool, processes: int}>
     *   },
     *   queues: array<string, array{wait_time: int|float, failed_jobs: int}>,
     *   performance: array{memory_usage: int, peak_memory: int, memory_limit: string, uptime: ?string},
     *   system: array{php_version: string, laravel_version: string, horizon_version: string, redis_status: bool}
     * }
     */
    private function collectMonitoringData(
        HorizonMonitoringService $monitoring,
        MasterSupervisorRepository $masterRepository
    ): array {
        $memoryLimit = ini_get('memory_limit');

        $environment = app()->environment();
        $environmentString = is_string($environment) ? $environment : 'unknown';

        $data = [
            'timestamp' => now()->toIso8601String(),
            'environment' => $environmentString,
            'masters' => [],
            'queues' => [],
            'performance' => [],
            'system' => [],
        ];

        // Master supervisor status
        $masters = collect($masterRepository->all());
        $masterDetails = $masters
            ->map(function (mixed $master): array {
                if (! is_object($master)) {
                    return [
                        'name' => 'unknown',
                        'running' => false,
                        'processes' => 0,
                    ];
                }

                $name = isset($master->name) ? (string) $master->name : 'unknown';
                $running = method_exists($master, 'isRunning') ? (bool) $master->isRunning() : false;

                $processesCount = 0;
                if (
                    isset($master->processes)
                    && is_object($master->processes)
                    && method_exists($master->processes, 'count')
                ) {
                    $processesCount = (int) $master->processes->count();
                }

                return [
                    'name' => $name,
                    'running' => $running,
                    'processes' => $processesCount,
                ];
            })
            ->values()
            ->all();

        $data['masters'] = [
            'total' => $masters->count(),
            'running' => $masters
                ->filter(fn (mixed $master): bool => is_object($master) && method_exists($master, 'isRunning') && $master->isRunning())
                ->count(),
            'details' => $masterDetails,
        ];

        // Queue metrics
        $queueStatistics = $monitoring->getQueueStatistics();
        $data['queues'] = collect($queueStatistics)
            ->mapWithKeys(function (mixed $metrics, mixed $queue): array {
                if (! is_array($metrics)) {
                    return [];
                }

                $waitTime = $metrics['wait_time'] ?? 0;
                $failedJobs = $metrics['failed'] ?? 0;

                return [
                    $queue => [
                        'wait_time' => is_int($waitTime) || is_float($waitTime) ? $waitTime : 0,
                        'failed_jobs' => is_int($failedJobs) ? $failedJobs : 0,
                    ],
                ];
            })
            ->all();

        // Performance metrics
        $data['performance'] = [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => is_string($memoryLimit) ? $memoryLimit : '-1',
            'uptime' => $this->getSystemUptime(),
        ];

        // System health
        $data['system'] = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'horizon_version' => $this->getHorizonVersion(),
            'redis_status' => $this->checkRedisStatus(),
        ];

        return $data;
    }

    /**
     * Analyze overall health status.
     *
     * @param array{
     *   timestamp: string,
     *   environment: string,
     *   masters: array{
     *     total: int,
     *     running: int,
     *     details: array<int, array{name: string, running: bool, processes: int}>
     *   },
     *   queues: array<string, array{wait_time: int|float, failed_jobs: int}>,
     *   performance: array{memory_usage: int, peak_memory: int, memory_limit: string, uptime: ?string},
     *   system: array{php_version: string, laravel_version: string, horizon_version: string, redis_status: bool}
     * } $data
     * @return array{
     *   overall_healthy: bool,
     *   has_warnings: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string, issue_count: int, warning_count: int}
     * }
     */

    /**
     * @param  array<string, mixed>  $data
     * @return array{
     *   overall_healthy: bool,
     *   has_warnings: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string, issue_count: int, warning_count: int}
     * }
     */
    private function analyzeHealthStatus(array $data): array
    {
        $issues = [];
        $warnings = [];

        // Check master supervisors
        /** @var array{total: int, running: int} $masters */
        $masters = is_array($data['masters'] ?? null) ? $data['masters'] : ['total' => 0, 'running' => 0];
        $mastersTotal = is_numeric($masters['total'] ?? 0) ? (int) $masters['total'] : 0;
        $mastersRunning = is_numeric($masters['running'] ?? 0) ? (int) $masters['running'] : 0;

        if ($mastersTotal === 0) {
            $issues[] = 'No Horizon master supervisors found';
        } elseif ($mastersRunning < $mastersTotal) {
            $issues[] = sprintf(
                'Only %d of %d master supervisors are running',
                $mastersRunning,
                $mastersTotal
            );
        }

        // Check queue health
        $queues = is_array($data['queues'] ?? null) ? $data['queues'] : [];
        foreach ($queues as $queue => $metrics) {
            if (! is_array($metrics)) {
                continue;
            }

            $waitTime = is_numeric($metrics['wait_time'] ?? 0) ? (float) $metrics['wait_time'] : 0;
            $failedJobs = is_numeric($metrics['failed_jobs'] ?? 0) ? (int) $metrics['failed_jobs'] : 0;

            if ($waitTime > 60) {
                $issues[] = "Queue {$queue} has high wait time: {$waitTime}s";
            }

            if ($failedJobs > 10) {
                $issues[] = "Queue {$queue} has too many failed jobs: {$failedJobs}";
            }

            if ($waitTime > 30 && $waitTime <= 60) {
                $warnings[] = "Queue {$queue} wait time approaching threshold: {$waitTime}s";
            }
        }

        // Check system resources
        /** @var array{memory_usage: int|float, memory_limit: string} $performance */
        $performance = is_array($data['performance'] ?? null) ? $data['performance'] : ['memory_usage' => 0, 'memory_limit' => '0'];
        $memoryUsage = is_numeric($performance['memory_usage'] ?? 0) ? (float) $performance['memory_usage'] : 0;
        $memoryLimitStr = is_string($performance['memory_limit'] ?? '') ? $performance['memory_limit'] : '0';
        $memoryLimit = $this->parseMemoryLimit($memoryLimitStr);

        if ($memoryLimit > 0 && ($memoryUsage / $memoryLimit) > 0.9) {
            $issues[] = 'High memory usage: '.round(($memoryUsage / $memoryLimit) * 100, 1).'%';
        } elseif ($memoryLimit > 0 && ($memoryUsage / $memoryLimit) > 0.8) {
            $warnings[] = 'Elevated memory usage: '.round(($memoryUsage / $memoryLimit) * 100, 1).'%';
        }

        // Check Redis status
        /** @var array{redis_status?: bool} $system */
        $system = is_array($data['system'] ?? null) ? $data['system'] : [];
        $redisStatus = $system['redis_status'] ?? false;
        if (! $redisStatus) {
            $issues[] = 'Redis connection failed';
        }

        return [
            'overall_healthy' => empty($issues),
            'has_warnings' => ! empty($warnings),
            'issues' => $issues,
            'warnings' => $warnings,
            'summary' => [
                'status' => empty($issues) ? (empty($warnings) ? 'healthy' : 'warning') : 'unhealthy',
                'issue_count' => count($issues),
                'warning_count' => count($warnings),
            ],
        ];
    }

    /**
     * Send health alerts for critical issues.
     *
     * @param array{
     *   overall_healthy: bool,
     *   has_warnings: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string, issue_count: int, warning_count: int}
     * } $healthStatus
     * @param  array<string, mixed>  $monitoringData
     */
    private function sendHealthAlerts(array $healthStatus, array $monitoringData): void
    {
        if (empty($healthStatus['issues'])) {
            return;
        }

        // Send email alert
        try {
            $recipients = config('horizon.notifications.email', 'admin@motac.gov.my');
            if (is_string($recipients)) {
                $recipients = [$recipients];
            } elseif (is_array($recipients)) {
                $recipients = array_values(array_filter($recipients, 'is_string'));
            } else {
                $recipients = ['admin@motac.gov.my'];
            }

            // Simple email alert (you may want to create a proper Mailable class)
            $subject = 'ICTServe Horizon Health Alert - '.ucfirst($healthStatus['summary']['status']);
            $message = "Horizon health issues detected:\n\n";

            foreach ($healthStatus['issues'] as $issue) {
                $message .= "• {$issue}\n";
            }

            if (! empty($healthStatus['warnings'])) {
                $message .= "\nWarnings:\n";
                foreach ($healthStatus['warnings'] as $warning) {
                    $message .= "• {$warning}\n";
                }
            }

            $environment = isset($monitoringData['environment']) && is_string($monitoringData['environment']) ? $monitoringData['environment'] : 'unknown';
            $timestamp = isset($monitoringData['timestamp']) && is_string($monitoringData['timestamp']) ? $monitoringData['timestamp'] : now()->toIso8601String();

            $message .= "\nEnvironment: {$environment}\n";
            $message .= "Timestamp: {$timestamp}\n";
            $message .= "\nPlease check the Horizon dashboard for more details.";

            foreach ($recipients as $recipient) {
                mail($recipient, $subject, $message);
            }

            Log::info('Horizon health alert sent', [
                'recipient' => $recipients,
                'issues' => count($healthStatus['issues']),
                'warnings' => count($healthStatus['warnings']),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send Horizon health alert', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Output results in JSON format.
     *
     * @param  array{
     *   timestamp: string,
     *   environment: string,
     *   masters: array{
     *     total: int,
     *     running: int,
     *     details: array<int, array{name: string, running: bool, processes: int}>
     *   },
     *   queues: array<string, array{wait_time: int|float, failed_jobs: int}>,
     *   performance: array{memory_usage: int, peak_memory: int, memory_limit: string, uptime: ?string},
     *   system: array{php_version: string, laravel_version: string, horizon_version: string, redis_status: bool}
     * }  $monitoringData
     * @param  array{
     *   overall_healthy: bool,
     *   has_warnings: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string, issue_count: int, warning_count: int}
     * }  $healthStatus
     */

    /**
     * @param  array<string, mixed>  $monitoringData
     * @param  array<string, mixed>  $healthStatus
     */
    private function outputJson(array $monitoringData, array $healthStatus): void
    {
        $output = [
            'monitoring_data' => $monitoringData,
            'health_status' => $healthStatus,
        ];

        $json = json_encode($output, JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->line('{}');

            return;
        }

        $this->line($json);
    }

    /**
     * Output results to console.
     *
     * @param  array{
     *   timestamp: string,
     *   environment: string,
     *   masters: array{
     *     total: int,
     *     running: int,
     *     details: array<int, array{name: string, running: bool, processes: int}>
     *   },
     *   queues: array<string, array{wait_time: int|float, failed_jobs: int}>,
     *   performance: array{memory_usage: int, peak_memory: int, memory_limit: string, uptime: ?string},
     *   system: array{php_version: string, laravel_version: string, horizon_version: string, redis_status: bool}
     * }  $data
     * @param  array{
     *   overall_healthy: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string}
     * }  $health
     */

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $health
     */
    private function outputConsole(array $data, array $health, bool $detailed): void
    {
        // Overall status
        /** @var array{status: string} $summary */
        $summary = is_array($health['summary'] ?? null) ? $health['summary'] : ['status' => 'unknown'];
        $status = is_string($summary['status'] ?? '') ? $summary['status'] : 'unknown';
        $statusColor = match ($status) {
            'healthy' => 'info',
            'warning' => 'comment',
            'unhealthy' => 'error',
            default => 'line',
        };

        $this->$statusColor('🔍 Horizon Status: '.strtoupper($status));

        $timestamp = isset($data['timestamp']) && is_string($data['timestamp']) ? $data['timestamp'] : now()->toIso8601String();
        $environment = isset($data['environment']) && is_string($data['environment']) ? $data['environment'] : 'unknown';

        $this->line("📅 Timestamp: {$timestamp}");
        $this->line("🌍 Environment: {$environment}");
        $this->newLine();

        // Master supervisors
        /** @var array{total: int, running: int, details: array<int, array{running: bool, name: string, processes: int}>} $masters */
        $masters = is_array($data['masters'] ?? null) ? $data['masters'] : ['total' => 0, 'running' => 0, 'details' => []];
        $mastersTotal = is_numeric($masters['total'] ?? 0) ? (int) $masters['total'] : 0;
        $mastersRunning = is_numeric($masters['running'] ?? 0) ? (int) $masters['running'] : 0;

        $this->line('👥 Master Supervisors:');
        $this->line("   Total: {$mastersTotal}");
        $this->line("   Running: {$mastersRunning}");

        if ($detailed && ! empty($masters['details']) && is_array($masters['details'])) {
            foreach ($masters['details'] as $master) {
                if (is_array($master)) {
                    $runningStatus = ($master['running'] ?? false) ? '✅' : '❌';
                    $this->line("   {$runningStatus} {$master['name']} ({$master['processes']} processes)");
                }
            }
        }
        $this->newLine();

        // Queue status
        $this->line('📋 Queue Status:');
        $queues = is_array($data['queues'] ?? null) ? $data['queues'] : [];
        foreach ($queues as $queue => $metrics) {
            if (! is_array($metrics)) {
                continue;
            }
            $waitTime = is_numeric($metrics['wait_time'] ?? 0) ? (float) $metrics['wait_time'] : 0;
            $failedJobs = is_numeric($metrics['failed_jobs'] ?? 0) ? (int) $metrics['failed_jobs'] : 0;

            $queueStatus = '✅';
            if ($waitTime > 60 || $failedJobs > 10) {
                $queueStatus = '❌';
            } elseif ($waitTime > 30 || $failedJobs > 5) {
                $queueStatus = '⚠️';
            }

            $this->line("   {$queueStatus} {$queue}: {$waitTime}s wait, {$failedJobs} failed");
        }
        $this->newLine();

        // Issues and warnings
        $issues = is_array($health['issues'] ?? null) ? $health['issues'] : [];
        $warnings = is_array($health['warnings'] ?? null) ? $health['warnings'] : [];
        $overallHealthy = $health['overall_healthy'] ?? false;

        if (! empty($issues)) {
            $this->error('❌ Issues Found:');
            foreach ($issues as $issue) {
                if (is_string($issue)) {
                    $this->error("   • {$issue}");
                }
            }
            $this->newLine();
        }

        if (! empty($warnings)) {
            $this->comment('⚠️  Warnings:');
            foreach ($warnings as $warning) {
                if (is_string($warning)) {
                    $this->comment("   • {$warning}");
                }
            }
            $this->newLine();
        }

        if ($overallHealthy && empty($warnings)) {
            $this->info('✅ All systems healthy!');
        }

        // Detailed system info
        if ($detailed) {
            $this->line('💻 System Information:');

            $system = is_array($data['system'] ?? null) ? $data['system'] : [];
            $performance = is_array($data['performance'] ?? null) ? $data['performance'] : [];

            $phpVersion = is_string($system['php_version'] ?? '') ? $system['php_version'] : 'Unknown';
            $laravelVersion = is_string($system['laravel_version'] ?? '') ? $system['laravel_version'] : 'Unknown';
            $horizonVersion = is_string($system['horizon_version'] ?? '') ? $system['horizon_version'] : 'Unknown';
            $redisStatus = (bool) ($system['redis_status'] ?? false);
            $memoryUsage = is_numeric($performance['memory_usage'] ?? 0) ? (int) $performance['memory_usage'] : 0;

            $this->line("   PHP: {$phpVersion}");
            $this->line("   Laravel: {$laravelVersion}");
            $this->line("   Horizon: {$horizonVersion}");
            $this->line('   Redis: '.($redisStatus ? 'Connected' : 'Disconnected'));
            $this->line('   Memory: '.$this->formatBytes($memoryUsage));
        }
    }

    /**
     * Log monitoring results.
     *
     * @param array{
     *   overall_healthy: bool,
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string}
     * } $healthStatus
     * @param  array{masters: array{running: int, total: int}, environment: string, ...}  $monitoringData
     */

    /**
     * @param  array<string, mixed>  $healthStatus
     * @param  array<string, mixed>  $monitoringData
     */
    private function logMonitoringResults(array $healthStatus, array $monitoringData): void
    {
        $overallHealthy = $healthStatus['overall_healthy'] ?? false;
        $logLevel = $overallHealthy ? 'info' : 'warning';

        /** @var array{status: string} $summary */
        $summary = is_array($healthStatus['summary'] ?? null) ? $healthStatus['summary'] : ['status' => 'unknown'];

        /** @var array{running: int, total: int} $mastersData */
        $mastersData = is_array($monitoringData['masters'] ?? null) ? $monitoringData['masters'] : ['running' => 0, 'total' => 0];
        $mastersRunning = isset($mastersData['running']) && is_int($mastersData['running']) ? $mastersData['running'] : 0;
        $mastersTotal = isset($mastersData['total']) && is_int($mastersData['total']) ? $mastersData['total'] : 0;

        Log::$logLevel('Horizon production monitoring completed', [
            'status' => $summary['status'],
            'issues' => count(is_array($healthStatus['issues'] ?? null) ? $healthStatus['issues'] : []),
            'warnings' => count(is_array($healthStatus['warnings'] ?? null) ? $healthStatus['warnings'] : []),
            'masters_running' => $mastersRunning,
            'masters_total' => $mastersTotal,
            'environment' => $monitoringData['environment'] ?? 'unknown',
        ]);
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime(): ?string
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                return null; // Windows uptime requires different approach
            }

            $uptime = file_get_contents('/proc/uptime');
            if ($uptime === false) {
                return null;
            }

            $seconds = (int) explode(' ', $uptime)[0];

            return gmdate('H:i:s', $seconds);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get Horizon version
     */
    private function getHorizonVersion(): string
    {
        try {
            $lockContents = file_get_contents(base_path('composer.lock'));
            if ($lockContents === false) {
                return 'unknown';
            }

            $composer = json_decode($lockContents, true);
            if (! is_array($composer) || ! isset($composer['packages']) || ! is_array($composer['packages'])) {
                return 'unknown';
            }

            foreach ($composer['packages'] as $package) {
                if (! is_array($package)) {
                    continue;
                }

                if (($package['name'] ?? null) === 'laravel/horizon') {
                    $version = $package['version'] ?? null;

                    return is_string($version) ? $version : 'unknown';
                }
            }

            return 'unknown';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    /**
     * Check Redis connection status
     */
    private function checkRedisStatus(): bool
    {
        try {
            $redis = app('redis');
            $response = $redis->ping();

            return $response === 'PONG' || $response === '+PONG';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse memory limit string to bytes
     */
    private function parseMemoryLimit(string $limit): int
    {
        if ($limit === '-1') {
            return 0; // No limit
        }

        $unit = strtolower(substr($limit, -1));
        $value = (int) substr($limit, 0, -1);

        return match ($unit) {
            'g' => $value * 1024 * 1024 * 1024,
            'm' => $value * 1024 * 1024,
            'k' => $value * 1024,
            default => (int) $limit,
        };
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;

        while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
            $bytes /= 1024;
            $unitIndex++;
        }

        return round($bytes, 2).' '.$units[$unitIndex];
    }
}
