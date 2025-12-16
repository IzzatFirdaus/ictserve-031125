<?php

declare(strict_types=1);

namespace App\Pulse\Recorders;

use Carbon\CarbonImmutable;
use Illuminate\Config\Repository;
use Laravel\Pulse\Pulse;
use Laravel\Pulse\Recorders\Concerns\Ignores;
use Laravel\Pulse\Recorders\Concerns\Sampling;

/**
 * AI Service Metrics Recorder for Laravel Pulse.
 *
 * Tracks performance metrics for AI services (AWS Bedrock and Ollama)
 * including response times, token usage, model selection, and cost estimates.
 *
 * trace: D03-SRS-AI-019, D18-§6.1, D11-§8.1 (AI Performance Monitoring)
 *
 * @see https://laravel.com/docs/pulse/custom-recorders
 */
class AIServiceMetrics
{
    use Ignores;
    use Sampling;

    /**
     * The events to listen for.
     *
     * @var array<int, class-string>
     */
    public array $listen = [
        \App\Events\AIRequestCompleted::class,
        \App\Events\AIRequestFailed::class,
    ];

    public function __construct(
        protected Pulse $pulse,
        protected Repository $config
    ) {}

    /**
     * Record AI request metrics.
     */
    public function record(\App\Events\AIRequestCompleted|\App\Events\AIRequestFailed $event): void
    {
        if (! $this->shouldSample()) {
            return;
        }

        $timestamp = CarbonImmutable::now()->getTimestamp();

        // Record response time
        $this->pulse->record(
            type: 'ai_response_time',
            key: $event->service, // 'bedrock' or 'ollama'
            value: $event->responseTimeMs,
            timestamp: $timestamp
        )->max()->onlyBuckets();

        // Record token usage (for Bedrock)
        if ($event->service === 'bedrock' && isset($event->inputTokens, $event->outputTokens)) {
            $this->pulse->record(
                type: 'ai_tokens_input',
                key: $event->modelId ?? 'unknown',
                value: $event->inputTokens,
                timestamp: $timestamp
            )->sum()->onlyBuckets();

            $this->pulse->record(
                type: 'ai_tokens_output',
                key: $event->modelId ?? 'unknown',
                value: $event->outputTokens,
                timestamp: $timestamp
            )->sum()->onlyBuckets();
        }

        // Record model usage count
        $this->pulse->record(
            type: 'ai_model_usage',
            key: $event->modelId ?? $event->service,
            value: 1,
            timestamp: $timestamp
        )->count()->onlyBuckets();

        // Record query routing decisions
        if (isset($event->queryType)) {
            $this->pulse->record(
                type: 'ai_query_routing',
                key: $event->queryType, // 'faq_specific', 'hybrid', 'complex_reasoning'
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        // Record failures
        if ($event instanceof \App\Events\AIRequestFailed) {
            $this->pulse->record(
                type: 'ai_failures',
                key: $event->service,
                value: 1,
                timestamp: $timestamp
            )->count()->onlyBuckets();
        }

        // Estimate cost (Bedrock only)
        if ($event->service === 'bedrock' && isset($event->inputTokens, $event->outputTokens)) {
            $cost = $this->estimateCost($event->modelId, $event->inputTokens, $event->outputTokens);
            $this->pulse->record(
                type: 'ai_cost_estimate',
                key: $event->modelId ?? 'unknown',
                value: (int) ($cost * 1000000), // Store as micro-dollars
                timestamp: $timestamp
            )->sum()->onlyBuckets();
        }
    }

    /**
     * Estimate cost based on model and token usage.
     *
     * Pricing per 1M tokens (as of Dec 2025):
     * - Claude Opus 4.5: $15 input, $75 output
     * - Claude Sonnet 4.5: $3 input, $15 output
     * - Claude Haiku 4.5: $0.25 input, $1.25 output
     */
    protected function estimateCost(?string $modelId, int $inputTokens, int $outputTokens): float
    {
        $pricing = match (true) {
            str_contains($modelId ?? '', 'opus') => ['input' => 15.0, 'output' => 75.0],
            str_contains($modelId ?? '', 'sonnet') => ['input' => 3.0, 'output' => 15.0],
            str_contains($modelId ?? '', 'haiku') => ['input' => 0.25, 'output' => 1.25],
            default => ['input' => 3.0, 'output' => 15.0], // Default to Sonnet pricing
        };

        return (($inputTokens / 1000000) * $pricing['input']) +
               (($outputTokens / 1000000) * $pricing['output']);
    }
}
