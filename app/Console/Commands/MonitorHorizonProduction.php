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

        $data = [
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
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
                if (! is_string($queue) || ! is_array($metrics)) {
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
 * @param array<string, mixed> $data
 */
private function analyzeHealthStatus(array $data): array
    {
        $issues = [];
        $warnings = [];

        // Check master supervisors
        if ($data['masters']['total'] === 0) {
            $issues[] = 'No Horizon master supervisors found';
        } elseif ($data['masters']['running'] < $data['masters']['total']) {
            $issues[] = sprintf(
                'Only %d of %d master supervisors are running',
                $data['masters']['running'],
                $data['masters']['total']
            );
        }

        // Check queue health
        foreach ($data['queues'] as $queue => $metrics) {
            if (isset($metrics['wait_time']) && $metrics['wait_time'] > 60) {
                $issues[] = "Queue {$queue} has high wait time: {$metrics['wait_time']}s";
            }

            if (isset($metrics['failed_jobs']) && $metrics['failed_jobs'] > 10) {
                $issues[] = "Queue {$queue} has too many failed jobs: {$metrics['failed_jobs']}";
            }

            if (isset($metrics['wait_time']) && $metrics['wait_time'] > 30) {
                $warnings[] = "Queue {$queue} wait time approaching threshold: {$metrics['wait_time']}s";
            }
        }

        // Check system resources
        $memoryUsage = $data['performance']['memory_usage'];
        $memoryLimit = $this->parseMemoryLimit($data['performance']['memory_limit']);

        if ($memoryLimit > 0 && ($memoryUsage / $memoryLimit) > 0.9) {
            $issues[] = 'High memory usage: '.round(($memoryUsage / $memoryLimit) * 100, 1).'%';
        } elseif ($memoryLimit > 0 && ($memoryUsage / $memoryLimit) > 0.8) {
            $warnings[] = 'Elevated memory usage: '.round(($memoryUsage / $memoryLimit) * 100, 1).'%';
        }

        // Check Redis status
        if (! $data['system']['redis_status']) {
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
     *   issues: list<string>,
     *   warnings: list<string>,
     *   summary: array{status: string}
     * } $healthStatus
     * @param  array{timestamp: string, environment: string, ...}  $monitoringData
     */
    

/**
 * @param array<string, mixed> $monitoringData
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

            $message .= "\nEnvironment: {$monitoringData['environment']}\n";
            $message .= "Timestamp: {$monitoringData['timestamp']}\n";
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
 * @param array<string, mixed> $healthStatus
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
 * @param array<string, mixed> $health
 */
private function outputConsole(array $data, array $health, bool $detailed): void
    {
        // Overall status
        $status = $health['summary']['status'];
        $statusColor = match ($status) {
            'healthy' => 'info',
            'warning' => 'comment',
            'unhealthy' => 'error',
            default => 'line',
        };

        $this->$statusColor('🔍 Horizon Status: '.strtoupper($status));
        $this->line("📅 Timestamp: {$data['timestamp']}");
        $this->line("🌍 Environment: {$data['environment']}");
        $this->newLine();

        // Master supervisors
        $this->line('👥 Master Supervisors:');
        $this->line("   Total: {$data['masters']['total']}");
        $this->line("   Running: {$data['masters']['running']}");

        if ($detailed) {
            foreach ($data['masters']['details'] as $master) {
                $status = $master['running'] ? '✅' : '❌';
                $this->line("   {$status} {$master['name']} ({$master['processes']} processes)");
            }
        }
        $this->newLine();

        // Queue status
        $this->line('📋 Queue Status:');
        foreach ($data['queues'] as $queue => $metrics) {
            $waitTime = $metrics['wait_time'] ?? 0;
            $failedJobs = $metrics['failed_jobs'] ?? 0;

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
        if (! empty($health['issues'])) {
            $this->error('❌ Issues Found:');
            foreach ($health['issues'] as $issue) {
                $this->error("   • {$issue}");
            }
            $this->newLine();
        }

        if (! empty($health['warnings'])) {
            $this->comment('⚠️  Warnings:');
            foreach ($health['warnings'] as $warning) {
                $this->comment("   • {$warning}");
            }
            $this->newLine();
        }

        if ($health['overall_healthy'] && empty($health['warnings'])) {
            $this->info('✅ All systems healthy!');
        }

        // Detailed system info
        if ($detailed) {
            $this->line('💻 System Information:');
            $this->line("   PHP: {$data['system']['php_version']}");
            $this->line("   Laravel: {$data['system']['laravel_version']}");
            $this->line("   Horizon: {$data['system']['horizon_version']}");
            $this->line('   Redis: '.($data['system']['redis_status'] ? 'Connected' : 'Disconnected'));
            $this->line('   Memory: '.$this->formatBytes($data['performance']['memory_usage']));
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
 * @param array<string, mixed> $monitoringData
 */
private function logMonitoringResults(array $healthStatus, array $monitoringData): void
    {
        $logLevel = $healthStatus['overall_healthy'] ? 'info' : 'warning';

        Log::$logLevel('Horizon production monitoring completed', [
            'status' => $healthStatus['summary']['status'],
            'issues' => count($healthStatus['issues']),
            'warnings' => count($healthStatus['warnings']),
            'masters_running' => $monitoringData['masters']['running'],
            'masters_total' => $monitoringData['masters']['total'],
            'environment' => $monitoringData['environment'],
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
