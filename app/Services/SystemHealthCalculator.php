<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * System Health Calculator Service
 *
 * Calculates overall system health score using weighted components:
 * - SLA Compliance: 30%
 * - AI Services: 30%
 * - Database: 20%
 * - Queue: 20%
 *
 * @see D03-FR-021 System health monitoring
 * @see Requirements 21.1-21.7
 */
class SystemHealthCalculator
{
    /**
     * Cache TTL in seconds
     */
    private const CACHE_TTL = 30;

    /**
     * Component weights for health calculation
     */
    private const WEIGHTS = [
        'sla_compliance' => 0.30,
        'ai_services' => 0.30,
        'database' => 0.20,
        'queue' => 0.20,
    ];

    public function __construct(
        private readonly SLABreachDetector $slaBreachDetector,
        private readonly AIHealthChecker $aiHealthChecker
    ) {}

    /**
     * Calculate overall system health.
     *
     * @return array<string, mixed>
     */
    public function calculateHealth(): array
    {
        return Cache::remember('system_health:overall', self::CACHE_TTL, function () {
            return $this->performHealthCalculation();
        });
    }

    /**
     * Force refresh health calculation.
     *
     * @return array<string, mixed>
     */
    public function forceRefresh(): array
    {
        Cache::forget('system_health:overall');
        $this->aiHealthChecker->forceRefresh();

        return $this->calculateHealth();
    }

    /**
     * Perform the actual health calculation.
     *
     * @return array<string, mixed>
     */
    private function performHealthCalculation(): array
    {
        try {
            $components = [
                'sla_compliance' => $this->calculateSLACompliance(),
                'ai_services' => $this->calculateAIServicesHealth(),
                'database' => $this->calculateDatabaseHealth(),
                'queue' => $this->calculateQueueHealth(),
            ];

            // Calculate weighted average
            $overallScore = $this->calculateWeightedScore($components);

            return [
                'overall_score' => round($overallScore, 1),
                'status' => $this->determineStatus($overallScore),
                'components' => $components,
                'last_calculated' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            Log::error('System health calculation failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'overall_score' => 0,
                'status' => 'unknown',
                'components' => [],
                'last_calculated' => now()->toIso8601String(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate weighted score from components.
     *
     * @param  array<string, array<string, mixed>>  $components
     */
    private function calculateWeightedScore(array $components): float
    {
        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($components as $key => $data) {
            $weight = self::WEIGHTS[$key] ?? 0;

            // Skip components that are not configured (excluded from calculation)
            if (($data['excluded'] ?? false) === true) {
                continue;
            }

            $score = $data['score'] ?? 0;
            $weightedSum += $score * $weight;
            $totalWeight += $weight;
        }

        // Normalize if some components are excluded
        if ($totalWeight > 0 && $totalWeight < 1.0) {
            return $weightedSum / $totalWeight * 100 / 100;
        }

        return $weightedSum;
    }

    /**
     * Calculate SLA compliance score.
     *
     * @return array<string, mixed>
     */
    private function calculateSLACompliance(): array
    {
        try {
            $complianceRate = $this->slaBreachDetector->getComplianceRate('month');
            $breachedCount = $this->slaBreachDetector->getBreachedCount();
            $atRiskCount = $this->slaBreachDetector->getAtRiskTickets()->count();

            return [
                'score' => $complianceRate,
                'status' => $this->determineStatus($complianceRate),
                'metrics' => [
                    'compliance_rate' => $complianceRate,
                    'breached_tickets' => $breachedCount,
                    'at_risk_tickets' => $atRiskCount,
                ],
                'excluded' => false,
            ];
        } catch (\Exception $e) {
            Log::warning('SLA compliance calculation failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'score' => 0,
                'status' => 'unknown',
                'metrics' => [],
                'excluded' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate AI services health score.
     *
     * @return array<string, mixed>
     */
    private function calculateAIServicesHealth(): array
    {
        try {
            $aiHealth = $this->aiHealthChecker->getOverallHealth();
            $ollamaStatus = $aiHealth['ollama']['status'] ?? 'unknown';
            $bedrockStatus = $aiHealth['bedrock']['status'] ?? 'unknown';

            // Check if both services are not configured
            if ($ollamaStatus === 'not_configured' && $bedrockStatus === 'not_configured') {
                return [
                    'score' => 0,
                    'status' => 'not_configured',
                    'metrics' => [
                        'ollama' => $ollamaStatus,
                        'bedrock' => $bedrockStatus,
                    ],
                    'excluded' => true, // Exclude from weighted calculation
                ];
            }

            // Calculate score based on available services
            $scores = [];

            if ($ollamaStatus !== 'not_configured') {
                $scores[] = $this->statusToScore($ollamaStatus);
            }

            if ($bedrockStatus !== 'not_configured') {
                $scores[] = $this->statusToScore($bedrockStatus);
            }

            $averageScore = \count($scores) > 0 ? array_sum($scores) / \count($scores) : 0;

            return [
                'score' => $averageScore,
                'status' => $this->determineStatus($averageScore),
                'metrics' => [
                    'ollama' => $ollamaStatus,
                    'bedrock' => $bedrockStatus,
                    'overall' => $aiHealth['overall_status'] ?? 'unknown',
                ],
                'excluded' => false,
            ];
        } catch (\Exception $e) {
            Log::warning('AI services health calculation failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'score' => 0,
                'status' => 'unknown',
                'metrics' => [],
                'excluded' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Calculate database health score.
     *
     * @return array<string, mixed>
     */
    private function calculateDatabaseHealth(): array
    {
        try {
            $startTime = microtime(true);
            DB::select('SELECT 1');
            $responseTime = (microtime(true) - $startTime) * 1000;

            // Score based on response time
            $score = match (true) {
                $responseTime < 50 => 100,
                $responseTime < 100 => 90,
                $responseTime < 200 => 80,
                $responseTime < 500 => 60,
                $responseTime < 1000 => 40,
                default => 20,
            };

            return [
                'score' => $score,
                'status' => $this->determineStatus($score),
                'metrics' => [
                    'response_time_ms' => round($responseTime, 2),
                    'connection' => 'active',
                ],
                'excluded' => false,
            ];
        } catch (\Exception $e) {
            Log::warning('Database health check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'score' => 0,
                'status' => 'critical',
                'metrics' => [
                    'connection' => 'failed',
                    'error' => $e->getMessage(),
                ],
                'excluded' => false,
            ];
        }
    }

    /**
     * Calculate queue health score.
     *
     * @return array<string, mixed>
     */
    private function calculateQueueHealth(): array
    {
        try {
            // Check queue size and failed jobs
            $failedJobs = DB::table('failed_jobs')->count();
            $pendingJobs = DB::table('jobs')->count();

            // Score based on failed jobs and queue size
            $failedScore = match (true) {
                $failedJobs === 0 => 100,
                $failedJobs < 5 => 80,
                $failedJobs < 20 => 60,
                $failedJobs < 50 => 40,
                default => 20,
            };

            $pendingScore = match (true) {
                $pendingJobs < 10 => 100,
                $pendingJobs < 50 => 90,
                $pendingJobs < 100 => 70,
                $pendingJobs < 500 => 50,
                default => 30,
            };

            $score = ($failedScore + $pendingScore) / 2;

            return [
                'score' => $score,
                'status' => $this->determineStatus($score),
                'metrics' => [
                    'failed_jobs' => $failedJobs,
                    'pending_jobs' => $pendingJobs,
                ],
                'excluded' => false,
            ];
        } catch (\Exception $e) {
            Log::warning('Queue health check failed', [
                'error' => $e->getMessage(),
            ]);

            return [
                'score' => 50, // Assume moderate health if can't check
                'status' => 'warning',
                'metrics' => [
                    'error' => $e->getMessage(),
                ],
                'excluded' => false,
            ];
        }
    }

    /**
     * Convert status string to numeric score.
     */
    private function statusToScore(string $status): float
    {
        return match ($status) {
            'healthy', 'excellent' => 100,
            'good' => 85,
            'warning', 'fair' => 60,
            'critical', 'poor' => 20,
            'inactive' => 0,
            default => 50,
        };
    }

    /**
     * Determine status label from score.
     */
    private function determineStatus(float $score): string
    {
        if ($score >= 80) {
            return 'healthy';
        }

        if ($score >= 50) {
            return 'warning';
        }

        return 'critical';
    }

    /**
     * Get health score only (for quick checks).
     */
    public function getHealthScore(): float
    {
        $health = $this->calculateHealth();

        return $health['overall_score'] ?? 0;
    }

    /**
     * Get health status only (for quick checks).
     */
    public function getHealthStatus(): string
    {
        $health = $this->calculateHealth();

        return $health['status'] ?? 'unknown';
    }
}
