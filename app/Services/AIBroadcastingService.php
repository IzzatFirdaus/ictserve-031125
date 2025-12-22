<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\AICacheStatsUpdate;
use App\Events\AIErrorOccurred;
use App\Events\AIPerformanceAlert;
use App\Events\AIPerformanceUpdate;
use App\Events\AIProcessingCompleted;
use App\Events\AIProcessingStarted;
use App\Events\AIResourceUsageUpdate;
use App\Events\AIServiceDegraded;
use App\Events\AIServiceRestored;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Service untuk broadcasting AI events dan integrasi Laravel Pulse
 *
 * Perkhidmatan ini menguruskan penghantaran notifikasi masa nyata untuk
 * operasi AI termasuk pemprosesan, prestasi, dan amaran sistem.
 *
 * @version 3.6.0
 *
 * @compliance D16 Broadcasting Setup v3.6.0, D11 Technical Design v3.6.0
 *
 * @requirements 11.1, 11.2, 11.3 - Real-time AI notifications
 */
class AIBroadcastingService
{
    /**
     * Tahap degradasi semasa (1-4)
     */
    private int $currentDegradationTier = 1;

    /**
     * Masa degradasi bermula
     */
    private ?float $degradationStartTime = null;

    /**
     * Ambang prestasi untuk amaran
     */
    private array $thresholds;

    public function __construct()
    {
        $this->thresholds = [
            'cpu_high' => config('ai-broadcasting.thresholds.cpu', 80),
            'memory_high' => config('ai-broadcasting.thresholds.memory', 90),
            'response_slow' => config('ai-broadcasting.thresholds.response_time', 5),
            'queue_backlog' => config('ai-broadcasting.thresholds.queue_size', 100),
            'cache_miss_high' => config('ai-broadcasting.thresholds.cache_miss_rate', 50),
        ];

        // Restore degradation state from cache
        $this->currentDegradationTier = (int) Cache::get('ai:degradation_tier', 1);
        $this->degradationStartTime = Cache::get('ai:degradation_start_time');
    }

    /**
     * Broadcast AI processing started event.
     */
    

/**
 * @param array<string, mixed> $metadata
 */
public function broadcastProcessingStarted(
        string $operationType,
        array $metadata = [],
        ?string $requestId = null
    ): string {
        $requestId = $requestId ?? (string) Str::uuid();

        try {
            event(new AIProcessingStarted($operationType, $metadata, $requestId));

            Log::channel('ai')->info('AI processing started broadcast', [
                'operation_type' => $operationType,
                'request_id' => $requestId,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast AI processing started', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
            ]);
        }

        return $requestId;
    }

    /**
     * Broadcast AI processing completed event.
     */
    

/**
 * @param array<string, mixed> $result
 */
public function broadcastProcessingCompleted(
        string $operationType,
        array $result = [],
        float $processingTime = 0.0,
        ?string $requestId = null
    ): void {
        try {
            event(new AIProcessingCompleted($operationType, $result, $processingTime, $requestId));

            // Check if response time exceeds threshold
            if ($processingTime > $this->thresholds['response_slow']) {
                $this->broadcastPerformanceAlert('response_slow', [
                    'response_time' => $processingTime,
                    'operation_type' => $operationType,
                ]);
            }

            Log::channel('ai')->info('AI processing completed broadcast', [
                'operation_type' => $operationType,
                'processing_time' => $processingTime,
                'request_id' => $requestId,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast AI processing completed', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
            ]);
        }
    }

    /**
     * Broadcast AI error event.
     */
    

/**
 * @param array<string, mixed> $context
 */
public function broadcastError(
        string $errorType,
        string $message,
        string $severity = 'medium',
        array $context = [],
        ?string $requestId = null
    ): void {
        try {
            event(new AIErrorOccurred($errorType, $message, $severity, $context, $requestId));

            Log::channel('ai')->error('AI error broadcast', [
                'error_type' => $errorType,
                'severity' => $severity,
                'request_id' => $requestId,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast AI error', [
                'error' => $e->getMessage(),
                'request_id' => $requestId,
            ]);
        }
    }

    /**
     * Broadcast AI error from exception.
     */
    public function broadcastErrorFromException(
        \Throwable $exception,
        string $operationType = 'unknown',
        ?string $requestId = null
    ): void {
        try {
            event(AIErrorOccurred::fromException($exception, $operationType, $requestId));
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast AI error from exception', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast performance alert.
     */
    

/**
 * @param array<string, mixed> $metrics
 */
public function broadcastPerformanceAlert(
        string $alertType,
        array $metrics = [],
        ?string $requestId = null
    ): void {
        try {
            event(new AIPerformanceAlert(
                $alertType,
                $metrics,
                $this->currentDegradationTier,
                $requestId
            ));

            Log::channel('ai')->warning('AI performance alert broadcast', [
                'alert_type' => $alertType,
                'degradation_tier' => $this->currentDegradationTier,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast performance alert', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast service degradation event.
     */
    public function broadcastServiceDegraded(
        int $newTier,
        string $reason = '',
        ?string $requestId = null
    ): void {
        $previousTier = $this->currentDegradationTier;

        if ($newTier <= $previousTier) {
            return; // Already at same or higher degradation level
        }

        $this->currentDegradationTier = $newTier;
        $this->degradationStartTime = microtime(true);

        // Persist to cache
        Cache::put('ai:degradation_tier', $newTier, now()->addHours(24));
        Cache::put('ai:degradation_start_time', $this->degradationStartTime, now()->addHours(24));

        try {
            event(new AIServiceDegraded($previousTier, $newTier, $reason, $requestId));

            Log::channel('ai')->warning('AI service degraded broadcast', [
                'previous_tier' => $previousTier,
                'new_tier' => $newTier,
                'reason' => $reason,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast service degradation', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast service restoration event.
     */
    public function broadcastServiceRestored(?string $requestId = null): void
    {
        $previousTier = $this->currentDegradationTier;

        if ($previousTier === 1) {
            return; // Already at normal operation
        }

        $downtimeDuration = $this->degradationStartTime
            ? microtime(true) - $this->degradationStartTime
            : 0.0;

        $this->currentDegradationTier = 1;
        $this->degradationStartTime = null;

        // Clear cache
        Cache::forget('ai:degradation_tier');
        Cache::forget('ai:degradation_start_time');

        try {
            event(new AIServiceRestored($previousTier, 1, $downtimeDuration, $requestId));

            Log::channel('ai')->info('AI service restored broadcast', [
                'previous_tier' => $previousTier,
                'downtime_duration' => $downtimeDuration,
            ]);
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast service restoration', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast real-time performance metrics.
     */
    

/**
 * @param array<string, mixed> $metrics
 */
public function broadcastPerformanceMetrics(array $metrics): void
    {
        try {
            event(new AIPerformanceUpdate($metrics));
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast performance metrics', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast cache statistics.
     */
    

/**
 * @param array<string, mixed> $stats
 */
public function broadcastCacheStats(array $stats): void
    {
        try {
            event(new AICacheStatsUpdate($stats));

            // Check cache miss rate threshold
            if (isset($stats['miss_rate']) && $stats['miss_rate'] > $this->thresholds['cache_miss_high']) {
                $this->broadcastPerformanceAlert('cache_miss_high', [
                    'cache_miss_rate' => $stats['miss_rate'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast cache stats', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Broadcast resource usage.
     */
    

/**
 * @param array<string, mixed> $usage
 */
public function broadcastResourceUsage(array $usage): void
    {
        try {
            event(new AIResourceUsageUpdate($usage, $this->currentDegradationTier));

            // Check resource thresholds
            if (isset($usage['cpu_percent']) && $usage['cpu_percent'] > $this->thresholds['cpu_high']) {
                $this->broadcastPerformanceAlert('cpu_high', [
                    'cpu_usage' => $usage['cpu_percent'],
                ]);
            }

            if (isset($usage['memory_percent']) && $usage['memory_percent'] > $this->thresholds['memory_high']) {
                $this->broadcastPerformanceAlert('memory_high', [
                    'memory_usage' => $usage['memory_percent'],
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('ai')->error('Failed to broadcast resource usage', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get current degradation tier.
     */
    public function getCurrentDegradationTier(): int
    {
        return $this->currentDegradationTier;
    }

    /**
     * Check if broadcasting is enabled.
     */
    public function isEnabled(): bool
    {
        return config('ai-broadcasting.monitoring.enabled', true)
            && config('broadcasting.default') !== 'null';
    }
}
