<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BedrockUsageLog;
use App\Models\MessageLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * AI Metrics Collector Service
 *
 * Collects and aggregates performance metrics from Ollama and AWS Bedrock
 * services for dashboard widget display. Provides real-time and historical
 * data with caching for optimal performance.
 *
 * trace: D18-§4.1 (AI Performance Monitoring), D03-SRS-AI-017 (Cost Optimization)
 * trace: D04-§6.4 (AI Architecture), D11-§8.1 (Performance Monitoring)
 *
 * @see docs/D18_AI_CHATBOT_OLLAMA_BEDROCK.md
 */
class AIMetricsCollector
{
    private const CACHE_TTL = 120; // 2 minutes as specified in requirements

    private const CACHE_PREFIX = 'ai_metrics';

    public function __construct(
        private OllamaClient $ollamaClient,
        private BedrockService $bedrockService
    ) {}

    /**
     * Get comprehensive AI performance metrics
     *
     * @return array<string, mixed>
     */
    public function getPerformanceMetrics(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.':performance',
            self::CACHE_TTL,
            fn () => $this->collectPerformanceMetrics()
        );
    }

    /**
     * Get AI cost metrics and optimization recommendations
     *
     * @return array<string, mixed>
     */
    public function getCostMetrics(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.':cost',
            self::CACHE_TTL,
            fn () => $this->collectCostMetrics()
        );
    }

    /**
     * Get AI system health status
     *
     * @return array<string, mixed>
     */
    public function getHealthMetrics(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.':health',
            self::CACHE_TTL,
            fn () => $this->collectHealthMetrics()
        );
    }

    /**
     * Collect performance metrics from both AI services
     *
     * @return array<string, mixed>
     */
    private function collectPerformanceMetrics(): array
    {
        try {
            $ollamaStats = $this->getOllamaPerformanceStats();
            $bedrockStats = $this->getBedrockPerformanceStats();

            return [
                'ollama' => $ollamaStats,
                'bedrock' => $bedrockStats,
                'combined' => $this->calculateCombinedStats($ollamaStats, $bedrockStats),
                'last_updated' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to collect AI performance metrics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->getDefaultPerformanceMetrics();
        }
    }

    /**
     * Get Ollama performance statistics
     *
     * @return array<string, mixed>
     */
    private function getOllamaPerformanceStats(): array
    {
        // Get recent message logs for Ollama
        $recentLogs = MessageLog::where('created_at', '>=', now()->subHours(24))
            ->whereNull('bedrock_model_used')
            ->get();

        $totalRequests = $recentLogs->count();
        $avgResponseTime = $totalRequests > 0
            ? $recentLogs->avg('response_time') ?? 0
            : 0;

        return [
            'total_requests_24h' => $totalRequests,
            'avg_response_time_ms' => round($avgResponseTime, 2),
            'success_rate' => $this->calculateSuccessRate($recentLogs),
            'requests_per_hour' => round($totalRequests / 24, 2),
            'status' => $this->determineOllamaStatus($avgResponseTime),
        ];
    }

    /**
     * Get Bedrock performance statistics
     *
     * @return array<string, mixed>
     */
    private function getBedrockPerformanceStats(): array
    {
        // Get recent Bedrock usage logs
        $recentLogs = BedrockUsageLog::where('created_at', '>=', now()->subHours(24))->get();
        $messageLogs = MessageLog::where('created_at', '>=', now()->subHours(24))
            ->whereNotNull('bedrock_model_used')
            ->get();

        $totalRequests = $messageLogs->count();
        $avgResponseTime = $totalRequests > 0
            ? $messageLogs->avg('response_time') ?? 0
            : 0;

        return [
            'total_requests_24h' => $totalRequests,
            'avg_response_time_ms' => round($avgResponseTime, 2),
            'success_rate' => $this->calculateSuccessRate($messageLogs),
            'requests_per_hour' => round($totalRequests / 24, 2),
            'total_tokens_24h' => $recentLogs->sum('input_tokens') + $recentLogs->sum('output_tokens'),
            'status' => $this->determineBedrockStatus($avgResponseTime),
        ];
    }

    /**
     * Collect cost metrics and optimization data
     *
     * @return array<string, mixed>
     */
    private function collectCostMetrics(): array
    {
        try {
            $today = now()->startOfDay();
            $yesterday = now()->subDay()->startOfDay();
            $thisMonth = now()->startOfMonth();

            // Get Bedrock costs
            $todayCost = BedrockUsageLog::whereDate('created_at', $today)
                ->sum('cost_usd');

            $yesterdayCost = BedrockUsageLog::whereDate('created_at', $yesterday)
                ->sum('cost_usd');

            $monthCost = BedrockUsageLog::whereDate('created_at', '>=', $thisMonth)
                ->sum('cost_usd');

            // Calculate cost trends
            $costTrend = $yesterdayCost > 0
                ? (($todayCost - $yesterdayCost) / $yesterdayCost) * 100
                : 0;

            // Get optimization recommendations
            $recommendations = $this->generateCostOptimizationRecommendations();

            return [
                'daily_cost_usd' => round($todayCost, 4),
                'yesterday_cost_usd' => round($yesterdayCost, 4),
                'monthly_cost_usd' => round($monthCost, 2),
                'cost_trend_percent' => round($costTrend, 1),
                'estimated_monthly_cost' => round($monthCost * (30 / now()->day), 2),
                'recommendations' => $recommendations,
                'last_updated' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to collect AI cost metrics', [
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultCostMetrics();
        }
    }

    /**
     * Collect health metrics for AI services
     *
     * @return array<string, mixed>
     */
    private function collectHealthMetrics(): array
    {
        try {
            $ollamaHealth = $this->checkOllamaHealth();
            $bedrockHealth = $this->checkBedrockHealth();

            return [
                'ollama' => $ollamaHealth,
                'bedrock' => $bedrockHealth,
                'overall_status' => $this->determineOverallHealth($ollamaHealth, $bedrockHealth),
                'last_health_check' => now()->toISOString(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to collect AI health metrics', [
                'error' => $e->getMessage(),
            ]);

            return $this->getDefaultHealthMetrics();
        }
    }

    /**
     * Calculate success rate from message logs
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $logs
     */
    private function calculateSuccessRate($logs): float
    {
        if ($logs->isEmpty()) {
            return 100.0;
        }

        $successfulLogs = $logs->where('status', 'success')->count();

        return round(($successfulLogs / $logs->count()) * 100, 1);
    }

    /**
     * Calculate combined statistics from both services
     *
     * @param  array<string, mixed>  $ollamaStats
     * @param  array<string, mixed>  $bedrockStats
     * @return array<string, mixed>
     */
    

/**
 * @param array<string, mixed> $bedrockStats
 */
private function calculateCombinedStats(array $ollamaStats, array $bedrockStats): array
    {
        $totalRequests = $ollamaStats['total_requests_24h'] + $bedrockStats['total_requests_24h'];

        if ($totalRequests === 0) {
            return [
                'total_requests_24h' => 0,
                'avg_response_time_ms' => 0,
                'success_rate' => 100.0,
                'ollama_percentage' => 0,
                'bedrock_percentage' => 0,
            ];
        }

        $weightedResponseTime = (
            ($ollamaStats['avg_response_time_ms'] * $ollamaStats['total_requests_24h']) +
            ($bedrockStats['avg_response_time_ms'] * $bedrockStats['total_requests_24h'])
        ) / $totalRequests;

        $weightedSuccessRate = (
            ($ollamaStats['success_rate'] * $ollamaStats['total_requests_24h']) +
            ($bedrockStats['success_rate'] * $bedrockStats['total_requests_24h'])
        ) / $totalRequests;

        return [
            'total_requests_24h' => $totalRequests,
            'avg_response_time_ms' => round($weightedResponseTime, 2),
            'success_rate' => round($weightedSuccessRate, 1),
            'ollama_percentage' => round(($ollamaStats['total_requests_24h'] / $totalRequests) * 100, 1),
            'bedrock_percentage' => round(($bedrockStats['total_requests_24h'] / $totalRequests) * 100, 1),
        ];
    }

    /**
     * Generate cost optimization recommendations
     *
     * @return array<string>
     */
    private function generateCostOptimizationRecommendations(): array
    {
        $recommendations = [];

        // Analyze usage patterns
        $bedrockUsage = BedrockUsageLog::where('created_at', '>=', now()->subDays(7))->get();
        $ollamaUsage = MessageLog::where('created_at', '>=', now()->subDays(7))
            ->whereNull('bedrock_model_used')
            ->count();

        $totalUsage = $bedrockUsage->count() + $ollamaUsage;

        if ($totalUsage > 0) {
            $bedrockPercentage = ($bedrockUsage->count() / $totalUsage) * 100;

            if ($bedrockPercentage > 70) {
                $recommendations[] = 'Pertimbangkan untuk menggunakan Ollama untuk pertanyaan mudah bagi menjimatkan kos';
            }

            if ($bedrockUsage->where('model_id', 'like', '%opus%')->count() > $bedrockUsage->count() * 0.5) {
                $recommendations[] = 'Gunakan model Sonnet atau Haiku untuk tugas yang kurang kompleks';
            }
        }

        if (empty($recommendations)) {
            $recommendations[] = 'Penggunaan AI telah dioptimumkan dengan baik';
        }

        return $recommendations;
    }

    /**
     * Check Ollama service health
     *
     * @return array<string, mixed>
     */
    private function checkOllamaHealth(): array
    {
        try {
            // Check recent error rate
            $recentErrors = MessageLog::where('created_at', '>=', now()->subHour())
                ->whereNull('bedrock_model_used')
                ->where('status', '!=', 'success')
                ->count();

            $recentTotal = MessageLog::where('created_at', '>=', now()->subHour())
                ->whereNull('bedrock_model_used')
                ->count();

            $errorRate = $recentTotal > 0 ? ($recentErrors / $recentTotal) * 100 : 0;

            return [
                'status' => $errorRate < 5 ? 'healthy' : ($errorRate < 15 ? 'warning' : 'critical'),
                'error_rate_percent' => round($errorRate, 1),
                'last_request' => MessageLog::whereNull('bedrock_model_used')
                    ->latest()
                    ->value('created_at')?->diffForHumans() ?? 'Tiada rekod',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'error_rate_percent' => 0,
                'last_request' => 'Tidak dapat dikesan',
            ];
        }
    }

    /**
     * Check Bedrock service health
     *
     * @return array<string, mixed>
     */
    private function checkBedrockHealth(): array
    {
        try {
            // Check recent error rate
            $recentErrors = MessageLog::where('created_at', '>=', now()->subHour())
                ->whereNotNull('bedrock_model_used')
                ->where('status', '!=', 'success')
                ->count();

            $recentTotal = MessageLog::where('created_at', '>=', now()->subHour())
                ->whereNotNull('bedrock_model_used')
                ->count();

            $errorRate = $recentTotal > 0 ? ($recentErrors / $recentTotal) * 100 : 0;

            return [
                'status' => $errorRate < 5 ? 'healthy' : ($errorRate < 15 ? 'warning' : 'critical'),
                'error_rate_percent' => round($errorRate, 1),
                'last_request' => MessageLog::whereNotNull('bedrock_model_used')
                    ->latest()
                    ->value('created_at')?->diffForHumans() ?? 'Tiada rekod',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unknown',
                'error_rate_percent' => 0,
                'last_request' => 'Tidak dapat dikesan',
            ];
        }
    }

    /**
     * Determine Ollama service status based on response time
     */
    private function determineOllamaStatus(float $responseTime): string
    {
        if ($responseTime === 0) {
            return 'inactive';
        }
        if ($responseTime < 1000) {
            return 'excellent';
        }
        if ($responseTime < 3000) {
            return 'good';
        }
        if ($responseTime < 5000) {
            return 'fair';
        }

        return 'poor';
    }

    /**
     * Determine Bedrock service status based on response time
     */
    private function determineBedrockStatus(float $responseTime): string
    {
        if ($responseTime === 0) {
            return 'inactive';
        }
        if ($responseTime < 2000) {
            return 'excellent';
        }
        if ($responseTime < 5000) {
            return 'good';
        }
        if ($responseTime < 8000) {
            return 'fair';
        }

        return 'poor';
    }

    /**
     * Determine overall system health
     *
     * @param  array<string, mixed>  $ollamaHealth
     * @param  array<string, mixed>  $bedrockHealth
     */
    

/**
 * @param array<string, mixed> $bedrockHealth
 */
private function determineOverallHealth(array $ollamaHealth, array $bedrockHealth): string
    {
        $statuses = [$ollamaHealth['status'], $bedrockHealth['status']];

        if (in_array('critical', $statuses)) {
            return 'critical';
        }
        if (in_array('warning', $statuses)) {
            return 'warning';
        }
        if (in_array('healthy', $statuses)) {
            return 'healthy';
        }

        return 'unknown';
    }

    /**
     * Get default performance metrics for error cases
     *
     * @return array<string, mixed>
     */
    private function getDefaultPerformanceMetrics(): array
    {
        return [
            'ollama' => [
                'total_requests_24h' => 0,
                'avg_response_time_ms' => 0,
                'success_rate' => 0,
                'requests_per_hour' => 0,
                'status' => 'unknown',
            ],
            'bedrock' => [
                'total_requests_24h' => 0,
                'avg_response_time_ms' => 0,
                'success_rate' => 0,
                'requests_per_hour' => 0,
                'total_tokens_24h' => 0,
                'status' => 'unknown',
            ],
            'combined' => [
                'total_requests_24h' => 0,
                'avg_response_time_ms' => 0,
                'success_rate' => 0,
                'ollama_percentage' => 0,
                'bedrock_percentage' => 0,
            ],
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Get default cost metrics for error cases
     *
     * @return array<string, mixed>
     */
    private function getDefaultCostMetrics(): array
    {
        return [
            'daily_cost_usd' => 0,
            'yesterday_cost_usd' => 0,
            'monthly_cost_usd' => 0,
            'cost_trend_percent' => 0,
            'estimated_monthly_cost' => 0,
            'recommendations' => ['Data kos tidak tersedia'],
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Get default health metrics for error cases
     *
     * @return array<string, mixed>
     */
    private function getDefaultHealthMetrics(): array
    {
        return [
            'ollama' => [
                'status' => 'unknown',
                'error_rate_percent' => 0,
                'last_request' => 'Tidak dapat dikesan',
            ],
            'bedrock' => [
                'status' => 'unknown',
                'error_rate_percent' => 0,
                'last_request' => 'Tidak dapat dikesan',
            ],
            'overall_status' => 'unknown',
            'last_health_check' => now()->toISOString(),
        ];
    }
}
