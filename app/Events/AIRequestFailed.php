<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an AI request fails.
 *
 * Used by Laravel Pulse AIServiceMetrics recorder for failure tracking.
 *
 * trace: D03-SRS-AI-019, D18-§6.1 (AI Performance Monitoring)
 */
class AIRequestFailed
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $service  The AI service that failed ('bedrock' or 'ollama')
     * @param  int  $responseTimeMs  Response time in milliseconds before failure
     * @param  string  $errorMessage  The error message
     * @param  string|null  $modelId  The model identifier attempted
     * @param  string|null  $queryType  Query routing type
     */
    public function __construct(
        public string $service,
        public int $responseTimeMs,
        public string $errorMessage,
        public ?string $modelId = null,
        public ?string $queryType = null,
    ) {}
}
