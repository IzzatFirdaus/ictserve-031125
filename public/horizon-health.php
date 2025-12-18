<?php

/**
 * ICTServe Horizon Health Check Endpoint
 *
 * Production-ready health check endpoint for Laravel Horizon
 * that can be used by load balancers and monitoring systems.
 *
 * Requirements: 23.1, 23.4
 * Usage: GET /horizon-health.php
 * Returns: HTTP 200 (healthy) or 503 (unhealthy) with JSON response
 */

declare(strict_types=1);

// Prevent direct access in non-production environments
if (! in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'HEAD'])) {
    http_response_code(405);
    header('Allow: GET, HEAD');
    exit('Method Not Allowed');
}

// Set JSON content type
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Bootstrap Laravel application
    require_once __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';

    // Initialize kernel
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    $healthData = [
        'status' => 'unknown',
        'timestamp' => date('c'),
        'checks' => [],
        'horizon' => [
            'running' => false,
            'supervisors' => 0,
            'processes' => 0,
        ],
        'queues' => [],
        'system' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
        ],
    ];

    // Check 1: Horizon Status
    $horizonOutput = [];
    $horizonReturnCode = 0;
    exec('php '.__DIR__.'/../artisan horizon:status 2>&1', $horizonOutput, $horizonReturnCode);

    $horizonRunning = $horizonReturnCode === 0 &&
        strpos(implode(' ', $horizonOutput), 'running') !== false;

    $healthData['checks']['horizon_status'] = [
        'healthy' => $horizonRunning,
        'message' => $horizonRunning ? 'Horizon is running' : 'Horizon is not running',
        'details' => $horizonOutput,
    ];

    // Check 2: Redis Connection
    try {
        $redis = app('redis');
        $redisPing = $redis->ping();
        $redisHealthy = $redisPing === 'PONG' || $redisPing === '+PONG';

        $healthData['checks']['redis_connection'] = [
            'healthy' => $redisHealthy,
            'message' => $redisHealthy ? 'Redis connection successful' : 'Redis connection failed',
            'response' => $redisPing,
        ];
    } catch (Exception $e) {
        $healthData['checks']['redis_connection'] = [
            'healthy' => false,
            'message' => 'Redis connection error: '.$e->getMessage(),
            'error' => $e->getMessage(),
        ];
    }

    // Check 3: Queue Metrics (if Horizon is running)
    if ($horizonRunning) {
        try {
            // Get queue metrics using Horizon's monitoring service
            $monitoringService = app(App\Services\HorizonMonitoringService::class);
            $queueMetrics = $monitoringService->getQueueMetrics();

            $healthData['queues'] = $queueMetrics;

            // Check for queue issues
            $queueIssues = [];
            foreach ($queueMetrics as $queue => $metrics) {
                if (isset($metrics['wait_time']) && $metrics['wait_time'] > 60) {
                    $queueIssues[] = "Queue {$queue} has high wait time: {$metrics['wait_time']}s";
                }
                if (isset($metrics['failed_jobs']) && $metrics['failed_jobs'] > 10) {
                    $queueIssues[] = "Queue {$queue} has too many failed jobs: {$metrics['failed_jobs']}";
                }
            }

            $healthData['checks']['queue_metrics'] = [
                'healthy' => empty($queueIssues),
                'message' => empty($queueIssues) ? 'All queues healthy' : 'Queue issues detected',
                'issues' => $queueIssues,
            ];
        } catch (Exception $e) {
            $healthData['checks']['queue_metrics'] = [
                'healthy' => false,
                'message' => 'Failed to get queue metrics: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    // Check 4: Supervisor Processes (if available)
    if ($horizonRunning) {
        try {
            $supervisorOutput = [];
            $supervisorReturnCode = 0;
            exec('supervisorctl status ictserve-horizon 2>&1', $supervisorOutput, $supervisorReturnCode);

            $supervisorHealthy = $supervisorReturnCode === 0 &&
                strpos(implode(' ', $supervisorOutput), 'RUNNING') !== false;

            $healthData['checks']['supervisor_status'] = [
                'healthy' => $supervisorHealthy,
                'message' => $supervisorHealthy ? 'Supervisor process running' : 'Supervisor process not running',
                'details' => $supervisorOutput,
            ];
        } catch (Exception $e) {
            $healthData['checks']['supervisor_status'] = [
                'healthy' => false,
                'message' => 'Supervisor check failed: '.$e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    // Determine overall health status
    $allChecksHealthy = true;
    foreach ($healthData['checks'] as $check) {
        if (! $check['healthy']) {
            $allChecksHealthy = false;
            break;
        }
    }

    if ($allChecksHealthy && $horizonRunning) {
        $healthData['status'] = 'healthy';
        http_response_code(200);
    } else {
        $healthData['status'] = 'unhealthy';
        http_response_code(503);
    }

    // Add summary
    $healthData['summary'] = [
        'overall_status' => $healthData['status'],
        'total_checks' => count($healthData['checks']),
        'passed_checks' => count(array_filter($healthData['checks'], fn ($check) => $check['healthy'])),
        'failed_checks' => count(array_filter($healthData['checks'], fn ($check) => ! $check['healthy'])),
    ];
} catch (Exception $e) {
    // Critical error - return 503
    http_response_code(503);
    $healthData = [
        'status' => 'error',
        'timestamp' => date('c'),
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
        'summary' => [
            'overall_status' => 'error',
            'message' => 'Health check failed due to system error',
        ],
    ];
} catch (Throwable $e) {
    // Fatal error - return 503
    http_response_code(503);
    $healthData = [
        'status' => 'fatal',
        'timestamp' => date('c'),
        'error' => $e->getMessage(),
        'summary' => [
            'overall_status' => 'fatal',
            'message' => 'Health check failed due to fatal error',
        ],
    ];
}

// Output JSON response
echo json_encode($healthData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
exit;
