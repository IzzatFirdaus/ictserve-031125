<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when an AI request completes successfully.
 *
 * Used by Laravel Pulse AIServiceMetrics recorder for performance monitoring.
 *
 * trace: D03-SRS-AI-019, D18-§6.1 (AI Performance Monitoring)
 */
class AIRequestCompleted
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  string  $service  The AI service used ('bedrock' or 'ollama')
     * @param  int  $responseTimeMs  Response time in milliseconds
     * @param  string|null  $modelId  The model identifier used
     * @param  int|null  $inputTokens  Number of input tokens (Bedrock only)
     * @param  int|null  $outputTokens  Number of output tokens (Bedrock only)
     * @param  string|null  $queryType  Query routing type ('faq_specific', 'hybrid', 'complex_reasoning')
     */
    public function __construct(
        public string $service,
        public int $responseTimeMs,
        public ?string $modelId = null,
        public ?int $inputTokens = null,
        public ?int $outputTokens = null,
        public ?string $queryType = null,
    ) {}
}
