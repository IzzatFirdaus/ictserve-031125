<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\AIHealthChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Restart AI Service Job
 *
 * Handles manual restart requests for AI services (Ollama and Bedrock).
 * Clears health check caches and forces a fresh health check.
 *
 * @see D03-FR-019 AI service health monitoring
 * @see Requirements 19.6
 */
class RestartAIServiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 120;

    /**
     * The backoff delays between retry attempts (exponential backoff)
     *
     * @var array<int>
     */
    public array $backoff = [5, 15, 30];

    /**
     * The service to restart ('ollama', 'bedrock', or 'all')
     */
    private string $service;

    /**
     * The user ID who initiated the restart
     */
    private ?int $userId;

    /**
     * Create a new job instance
     */
    public function __construct(string $service = 'all', ?int $userId = null)
    {
        $this->service = $service;
        $this->userId = $userId;
        $this->onQueue('ai');
    }

    /**
     * Execute the job
     */
    public function handle(AIHealthChecker $healthChecker): void
    {
        $startTime = microtime(true);

        Log::info('RestartAIServiceJob started', [
            'service' => $this->service,
            'user_id' => $this->userId,
            'attempt' => $this->attempts(),
        ]);

        try {
            // Force refresh health check cache
            $healthChecker->forceRefresh();

            // Perform fresh health checks based on service type
            $results = $this->performHealthChecks($healthChecker);

            $processingTime = microtime(true) - $startTime;

            Log::info('RestartAIServiceJob completed successfully', [
                'service' => $this->service,
                'user_id' => $this->userId,
                'results' => $results,
                'processing_time' => $processingTime,
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Perform health checks for the specified service(s)
     *
     * @return array<string, mixed>
     */
    private function performHealthChecks(AIHealthChecker $healthChecker): array
    {
        $results = [];

        if ($this->service === 'all' || $this->service === 'ollama') {
            $results['ollama'] = $healthChecker->checkOllamaHealth();
        }

        if ($this->service === 'all' || $this->service === 'bedrock') {
            $results['bedrock'] = $healthChecker->checkBedrockHealth();
        }

        return $results;
    }

    /**
     * Handle job failure
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('RestartAIServiceJob failed', [
            'service' => $this->service,
            'user_id' => $this->userId,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle permanent job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('RestartAIServiceJob permanently failed', [
            'service' => $this->service,
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'ai',
            'ai-restart',
            "service:{$this->service}",
            'priority:high',
        ];
    }
}
