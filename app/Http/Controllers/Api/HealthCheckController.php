<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Monitoring\PerformanceMonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

/**
 * Health Check Controller
 *
 * Provides health check endpoints for monitoring and load balancing.
 *
 * @see D11 Technical Design Documentation - Monitoring
 * @see docs/deployment-checklist.md - Health Check Script
 *
 * @requirements R08 Performance Optimization, 6.5.4 Monitoring and Alerting
 *
 * @version 1.0.0
 */
class HealthCheckController extends Controller
{
    public function __construct(
        private readonly PerformanceMonitoringService $performanceService
    ) {}

    /**
     * Basic health check - returns 200 if application is running.
     */
    public function basic(): JsonResponse
    {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Detailed health check - checks all dependencies.
     */
    public function detailed(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
            'storage' => $this->checkStorage(),
            'performance' => $this->checkPerformance(),
        ];

        $allHealthy = ! in_array(false, array_column($checks, 'healthy'), true);

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'degraded',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '3.0.0'),
            'environment' => config('app.env'),
        ], $allHealthy ? 200 : 503);
    }

    /**
     * Performance metrics endpoint.
     */
    public function performance(): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'metrics' => $this->performanceService->getPerformanceSummary(),
            'healthy' => $this->performanceService->isHealthy(),
        ]);
    }

    /**
     * Check database connectivity.
     */
    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $latency = (microtime(true) - $start) * 1000;

            return [
                'healthy' => true,
                'latency_ms' => round($latency, 2),
                'connection' => config('database.default'),
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Database connection failed',
            ];
        }
    }

    /**
     * Check cache connectivity.
     */
    private function checkCache(): array
    {
        try {
            $start = microtime(true);
            $key = 'health_check_'.uniqid();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            $latency = (microtime(true) - $start) * 1000;

            return [
                'healthy' => $value === 'test',
                'latency_ms' => round($latency, 2),
                'driver' => config('cache.default'),
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Cache check failed',
            ];
        }
    }

    /**
     * Check queue connectivity.
     */
    private function checkQueue(): array
    {
        try {
            $connection = config('queue.default');

            // For database queue, check the jobs table
            if ($connection === 'database') {
                DB::table('jobs')->count();
            }

            return [
                'healthy' => true,
                'connection' => $connection,
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Queue check failed',
            ];
        }
    }

    /**
     * Check storage accessibility.
     */
    private function checkStorage(): array
    {
        try {
            $storagePath = storage_path('app');
            $writable = is_writable($storagePath);
            $freeSpace = disk_free_space($storagePath);
            $totalSpace = disk_total_space($storagePath);
            $usedPercent = round((1 - ($freeSpace / $totalSpace)) * 100, 2);

            return [
                'healthy' => $writable && $usedPercent < 90,
                'writable' => $writable,
                'used_percent' => $usedPercent,
                'free_gb' => round($freeSpace / 1073741824, 2),
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Storage check failed',
            ];
        }
    }

    /**
     * Check performance metrics.
     */
    private function checkPerformance(): array
    {
        return [
            'healthy' => $this->performanceService->isHealthy(),
            'summary' => $this->performanceService->getPerformanceSummary(),
        ];
    }

    /**
     * AI services health check endpoint.
     *
     * Checks connectivity to both AWS Bedrock and Ollama services.
     *
     * trace: D03-SRS-AI-009, D18-§6.1 (AI Health Monitoring)
     */
    public function aiServices(): JsonResponse
    {
        $checks = [
            'ollama' => $this->checkOllama(),
            'bedrock' => $this->checkBedrock(),
        ];

        $ollamaHealthy = $checks['ollama']['healthy'] ?? false;
        $bedrockHealthy = $checks['bedrock']['healthy'] ?? false;

        // System is healthy if at least one AI service is available (hybrid fallback)
        $overallHealthy = $ollamaHealthy || $bedrockHealthy;

        return response()->json([
            'status' => $overallHealthy ? 'healthy' : 'degraded',
            'services' => $checks,
            'timestamp' => now()->toIso8601String(),
            'architecture' => 'hybrid', // True Hybrid Architecture
        ], $overallHealthy ? 200 : 503);
    }

    /**
     * Check Ollama local LLM service.
     */
    private function checkOllama(): array
    {
        try {
            $start = microtime(true);
            $ollamaUrl = config('ollama-laravel.url', 'http://127.0.0.1:11434');

            $client = new \GuzzleHttp\Client(['timeout' => 5]);
            $response = $client->get($ollamaUrl.'/api/tags');
            $latency = (microtime(true) - $start) * 1000;

            $data = json_decode($response->getBody()->getContents(), true);
            $models = $data['models'] ?? [];

            return [
                'healthy' => true,
                'latency_ms' => round($latency, 2),
                'models_available' => count($models),
                'url' => $ollamaUrl,
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Ollama service unavailable',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check AWS Bedrock service.
     */
    private function checkBedrock(): array
    {
        try {
            $enabled = config('bedrock.enabled', false);

            if (! $enabled) {
                return [
                    'healthy' => false,
                    'error' => 'Bedrock is disabled in configuration',
                ];
            }

            $hasCredentials = ! empty(config('bedrock.aws.key')) && ! empty(config('bedrock.aws.secret'));
            $modelId = config('bedrock.model_id');

            return [
                'healthy' => $hasCredentials,
                'credentials_configured' => $hasCredentials,
                'model_id' => $modelId,
                'region' => config('bedrock.aws.region', 'us-east-1'),
            ];
        } catch (\Exception $e) {
            return [
                'healthy' => false,
                'error' => 'Bedrock configuration check failed',
                'message' => $e->getMessage(),
            ];
        }
    }
}
