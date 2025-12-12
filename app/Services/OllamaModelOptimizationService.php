<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\OllamaClientContract;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Perkhidmatan Pengoptimuman Model Ollama untuk ICTServe v3.6.0
 *
 * Perkhidmatan ini menguruskan pengoptimuman prestasi model AI termasuk:
 * - Konfigurasi model quantized (Q4_K_M) untuk pengeluaran
 * - Pemanasan model pada permulaan aplikasi
 * - Fungsi keep-alive untuk prestasi konsisten
 * - Pemantauan sumber dan pencetus penskalaan automatik
 *
 * @version 3.6.0
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @compliance D11 Technical Design Documentation v3.6.0
 *
 * @requirements 8.1, 8.5
 */
class OllamaModelOptimizationService
{
    private const WARM_UP_CACHE_KEY = 'ollama:model:warmed_up';

    private const KEEP_ALIVE_INTERVAL = 300;

    private const PERFORMANCE_TARGETS = [
        'lcp' => 2500,
        'fid' => 100,
        'cls' => 0.1,
        'response_p50' => 2000,
        'response_p95' => 5000,
        'response_p99' => 8000,
    ];

    private array $config;

    public function __construct(
        private readonly OllamaClientContract $ollamaClient,
        private readonly OllamaCacheService $cacheService
    ) {
        $this->config = config('ollama', []);
    }

    /**
     * Pemanasan model pada permulaan aplikasi
     * Warm up model on application start
     */
    public function warmUpModel(): array
    {
        $startTime = microtime(true);
        $result = [
            'success' => false,
            'model' => $this->getActiveModel(),
            'duration_ms' => 0,
            'message' => '',
        ];

        try {
            if ($this->isModelWarmedUp()) {
                $result['success'] = true;
                $result['message'] = 'Model sudah dipanaskan / Model already warmed up';

                return $result;
            }

            $warmUpPrompt = 'Sila sahkan anda bersedia untuk menerima pertanyaan. / Please confirm you are ready to receive queries.';

            $response = $this->ollamaClient->generate([
                'model' => $this->getActiveModel(),
                'prompt' => $warmUpPrompt,
                'stream' => false,
                'options' => [
                    'num_predict' => 10,
                ],
            ]);

            if (isset($response['response'])) {
                Cache::put(self::WARM_UP_CACHE_KEY, true, 3600);
                $result['success'] = true;
                $result['message'] = 'Model berjaya dipanaskan / Model warmed up successfully';

                Log::info('Ollama model warmed up', [
                    'model' => $this->getActiveModel(),
                    'duration_ms' => round((microtime(true) - $startTime) * 1000, 2),
                ]);
            }
        } catch (\Exception $e) {
            $result['message'] = 'Gagal memanaskan model: '.$e->getMessage();
            Log::error('Failed to warm up Ollama model', [
                'error' => $e->getMessage(),
                'model' => $this->getActiveModel(),
            ]);
        }

        $result['duration_ms'] = round((microtime(true) - $startTime) * 1000, 2);

        return $result;
    }

    /**
     * Semak sama ada model sudah dipanaskan
     * Check if model is already warmed up
     */
    public function isModelWarmedUp(): bool
    {
        return (bool) Cache::get(self::WARM_UP_CACHE_KEY, false);
    }

    /**
     * Hantar isyarat keep-alive untuk mengekalkan model dalam memori
     * Send keep-alive signal to keep model in memory
     */
    public function sendKeepAlive(): array
    {
        $result = [
            'success' => false,
            'model' => $this->getActiveModel(),
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $response = $this->ollamaClient->generate([
                'model' => $this->getActiveModel(),
                'prompt' => '',
                'keep_alive' => '5m',
            ]);

            $result['success'] = true;

            Log::debug('Keep-alive signal sent', [
                'model' => $this->getActiveModel(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Keep-alive signal failed', [
                'error' => $e->getMessage(),
                'model' => $this->getActiveModel(),
            ]);
        }

        return $result;
    }

    /**
     * Dapatkan model aktif (quantized untuk pengeluaran)
     * Get active model (quantized for production)
     */
    public function getActiveModel(): string
    {
        $useQuantized = $this->config['performance']['use_quantized_model'] ?? false;

        if ($useQuantized && app()->environment('production')) {
            return $this->config['performance']['quantized_model'] ?? 'llama3.1:q4_k_m';
        }

        return $this->config['model'] ?? 'llama3.1';
    }

    /**
     * Dapatkan status kesihatan model
     * Get model health status
     */
    public function getModelHealth(): array
    {
        $health = [
            'status' => 'unknown',
            'model' => $this->getActiveModel(),
            'warmed_up' => $this->isModelWarmedUp(),
            'server_available' => false,
            'response_time_ms' => null,
            'memory_usage' => null,
        ];

        try {
            $startTime = microtime(true);
            $serverHealth = $this->ollamaClient->healthCheck();
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            $health['server_available'] = $serverHealth['available'] ?? false;
            $health['response_time_ms'] = $responseTime;
            $health['status'] = $health['server_available'] ? 'healthy' : 'unavailable';

            if ($health['server_available']) {
                $models = $this->ollamaClient->models();
                $activeModel = $this->getActiveModel();

                $modelInfo = collect($models['models'] ?? [])->firstWhere('name', $activeModel);
                if ($modelInfo) {
                    $health['memory_usage'] = $modelInfo['size'] ?? null;
                }
            }
        } catch (\Exception $e) {
            $health['status'] = 'error';
            $health['error'] = $e->getMessage();
        }

        return $health;
    }

    /**
     * Dapatkan metrik prestasi
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        $cacheKey = 'ollama:performance:metrics';

        return Cache::remember($cacheKey, 60, function () {
            return [
                'targets' => self::PERFORMANCE_TARGETS,
                'current' => $this->measureCurrentPerformance(),
                'cache_stats' => $this->cacheService->getStats(),
                'model_health' => $this->getModelHealth(),
                'timestamp' => now()->toIso8601String(),
            ];
        });
    }

    /**
     * Ukur prestasi semasa
     * Measure current performance
     */
    protected function measureCurrentPerformance(): array
    {
        $metrics = [
            'response_times' => [],
            'p50' => null,
            'p95' => null,
            'p99' => null,
            'avg' => null,
            'meets_targets' => true,
        ];

        $cachedTimes = Cache::get('ollama:response_times', []);

        if (count($cachedTimes) > 0) {
            sort($cachedTimes);
            $count = count($cachedTimes);

            $metrics['p50'] = $cachedTimes[(int) floor($count * 0.5)] ?? null;
            $metrics['p95'] = $cachedTimes[(int) floor($count * 0.95)] ?? null;
            $metrics['p99'] = $cachedTimes[(int) floor($count * 0.99)] ?? null;
            $metrics['avg'] = round(array_sum($cachedTimes) / $count, 2);

            $metrics['meets_targets'] =
                ($metrics['p50'] ?? 0) <= self::PERFORMANCE_TARGETS['response_p50'] &&
                ($metrics['p95'] ?? 0) <= self::PERFORMANCE_TARGETS['response_p95'] &&
                ($metrics['p99'] ?? 0) <= self::PERFORMANCE_TARGETS['response_p99'];
        }

        return $metrics;
    }

    /**
     * Rekod masa respons untuk analisis prestasi
     * Record response time for performance analysis
     */
    public function recordResponseTime(float $responseTimeMs): void
    {
        $times = Cache::get('ollama:response_times', []);
        $times[] = $responseTimeMs;

        if (count($times) > 1000) {
            $times = array_slice($times, -1000);
        }

        Cache::put('ollama:response_times', $times, 3600);
    }

    /**
     * Semak sama ada penskalaan automatik diperlukan
     * Check if automatic scaling is needed
     */
    public function checkScalingTriggers(): array
    {
        $triggers = [
            'should_scale_up' => false,
            'should_scale_down' => false,
            'reasons' => [],
            'current_load' => $this->getCurrentLoad(),
        ];

        $load = $triggers['current_load'];

        if ($load['cpu_percent'] > 80) {
            $triggers['should_scale_up'] = true;
            $triggers['reasons'][] = 'CPU usage above 80%';
        }

        if ($load['memory_percent'] > 90) {
            $triggers['should_scale_up'] = true;
            $triggers['reasons'][] = 'Memory usage above 90%';
        }

        if ($load['queue_depth'] > 100) {
            $triggers['should_scale_up'] = true;
            $triggers['reasons'][] = 'Queue depth above 100';
        }

        if ($load['cpu_percent'] < 20 && $load['memory_percent'] < 40 && $load['queue_depth'] < 10) {
            $triggers['should_scale_down'] = true;
            $triggers['reasons'][] = 'Low resource utilization';
        }

        return $triggers;
    }

    /**
     * Dapatkan beban semasa sistem
     * Get current system load
     */
    protected function getCurrentLoad(): array
    {
        return [
            'cpu_percent' => $this->getCpuUsage(),
            'memory_percent' => $this->getMemoryUsage(),
            'queue_depth' => $this->getQueueDepth(),
            'active_connections' => $this->getActiveConnections(),
        ];
    }

    /**
     * Dapatkan penggunaan CPU
     * Get CPU usage
     */
    protected function getCpuUsage(): float
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return 0.0;
        }

        $load = sys_getloadavg();

        return $load ? round($load[0] * 100 / max(1, (int) shell_exec('nproc')), 2) : 0.0;
    }

    /**
     * Dapatkan penggunaan memori
     * Get memory usage
     */
    protected function getMemoryUsage(): float
    {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsed = memory_get_usage(true);

        $limit = $this->convertToBytes($memoryLimit);

        return $limit > 0 ? round(($memoryUsed / $limit) * 100, 2) : 0.0;
    }

    /**
     * Dapatkan kedalaman baris gilir
     * Get queue depth
     */
    protected function getQueueDepth(): int
    {
        try {
            return (int) Cache::get('ollama:queue:depth', 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Dapatkan sambungan aktif
     * Get active connections
     */
    protected function getActiveConnections(): int
    {
        try {
            return (int) Cache::get('ollama:active_connections', 0);
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Tukar rentetan memori kepada bytes
     * Convert memory string to bytes
     */
    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $numericValue = (int) $value;

        switch ($last) {
            case 'g':
                $numericValue *= 1024 * 1024 * 1024;
                break;
            case 'm':
                $numericValue *= 1024 * 1024;
                break;
            case 'k':
                $numericValue *= 1024;
                break;
        }

        return $numericValue;
    }

    /**
     * Optimumkan tetapan model untuk persekitaran semasa
     * Optimize model settings for current environment
     */
    public function optimizeForEnvironment(): array
    {
        $environment = app()->environment();
        $optimization = [
            'environment' => $environment,
            'model' => $this->getActiveModel(),
            'settings' => [],
        ];

        if ($environment === 'production') {
            $optimization['settings'] = [
                'use_quantized' => true,
                'keep_alive' => '5m',
                'num_ctx' => 2048,
                'num_batch' => 512,
                'num_thread' => 4,
            ];
        } else {
            $optimization['settings'] = [
                'use_quantized' => false,
                'keep_alive' => '1m',
                'num_ctx' => 4096,
                'num_batch' => 256,
                'num_thread' => 2,
            ];
        }

        return $optimization;
    }
}
