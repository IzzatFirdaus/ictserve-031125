<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BedrockModelConfig;
use App\Models\BedrockUsageLog;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Pulse\Facades\Pulse;

/**
 * AWS Bedrock Service for Claude AI integration.
 *
 * Provides access to Claude Opus 4.5, Sonnet 4.5, and Haiku 4.5 models
 * via AWS Bedrock with inference profile support, cost tracking, and
 * comprehensive error handling.
 *
 * trace: D03-SRS-AI-002 (Auto-Reply), D03-SRS-AI-003 (Document Analysis)
 * trace: D03-SRS-AI-012 (Multi-Model), D03-SRS-AI-017 (Cost Optimization)
 * trace: D18-§4.1 (AWS Bedrock Integration), D11-§8.1 (Performance Monitoring)
 *
 * @see docs/aws_bedrock/IMPLEMENTATION.md
 * @see docs/aws_bedrock/API_REFERENCE.md
 */
class BedrockService
{
    public function __construct(
        private BedrockRuntimeClient $client
    ) {}

    /**
     * @param  array<string, mixed>  $context
     */
    public function invoke(string $prompt, int $maxTokens = 1000, ?string $modelId = null, array $context = []): array
    {
        $requestId = is_string($context['request_id'] ?? null) ? (string) $context['request_id'] : (string) Str::uuid();
        $startedAt = microtime(true);

        try {
            $maxPromptChars = (int) config('bedrock.routing.max_prompt_chars', 10000);
            if ($maxPromptChars > 0 && $this->safeStrlen($prompt) > $maxPromptChars) {
                $this->logUsage(
                    requestId: $requestId,
                    modelId: (string) ($modelId ?? config('bedrock.model_id')),
                    inputTokens: 0,
                    outputTokens: 0,
                    costEstimate: null,
                    responseTimeMs: $this->durationMs($startedAt),
                    success: false,
                    errorMessage: 'Prompt terlalu panjang untuk pemprosesan Bedrock.',
                    context: $context,
                );

                return [
                    'success' => false,
                    'content' => 'Prompt terlalu panjang untuk pemprosesan Bedrock.',
                    'usage' => [],
                    'error_code' => 'PROMPT_TOO_LONG',
                    'error' => 'Prompt terlalu panjang untuk pemprosesan Bedrock.',
                ];
            }

            $effectiveModelId = (string) ($modelId ?? config('bedrock.model_id'));

            if (! str_starts_with($effectiveModelId, 'us.') && ! str_starts_with($effectiveModelId, 'global.')) {
                Log::warning('Model Bedrock tidak menggunakan inference profile yang sah.', [
                    'model_id' => $effectiveModelId,
                ]);

                $this->logUsage(
                    requestId: $requestId,
                    modelId: $effectiveModelId,
                    inputTokens: 0,
                    outputTokens: 0,
                    costEstimate: null,
                    responseTimeMs: $this->durationMs($startedAt),
                    success: false,
                    errorMessage: 'Konfigurasi model Bedrock tidak sah. Sila gunakan inference profile (contoh: us.* atau global.*).',
                    context: $context,
                );

                return [
                    'success' => false,
                    'content' => 'Konfigurasi model Bedrock tidak sah. Sila gunakan inference profile (contoh: us.* atau global.*).',
                    'usage' => [],
                    'error_code' => 'INFERENCE_PROFILE_REQUIRED',
                    'error' => 'Konfigurasi model Bedrock tidak sah. Sila gunakan inference profile (contoh: us.* atau global.*).',
                ];
            }

            if (str_contains($effectiveModelId, 'claude-opus-4-5') && str_starts_with($effectiveModelId, 'us.')) {
                Log::warning('Model Opus 4.5 memerlukan global inference profile.', [
                    'model_id' => $effectiveModelId,
                ]);

                $this->logUsage(
                    requestId: $requestId,
                    modelId: $effectiveModelId,
                    inputTokens: 0,
                    outputTokens: 0,
                    costEstimate: null,
                    responseTimeMs: $this->durationMs($startedAt),
                    success: false,
                    errorMessage: 'Konfigurasi model Opus 4.5 tidak sah. Sila gunakan global inference profile untuk Opus 4.5.',
                    context: $context,
                );

                return [
                    'success' => false,
                    'content' => 'Konfigurasi model Opus 4.5 tidak sah. Sila gunakan global inference profile untuk Opus 4.5.',
                    'usage' => [],
                    'error_code' => 'OPUS_REQUIRES_GLOBAL_PROFILE',
                    'error' => 'Konfigurasi model Opus 4.5 tidak sah. Sila gunakan global inference profile untuk Opus 4.5.',
                ];
            }

            $response = $this->client->invokeModel([
                'modelId' => $effectiveModelId,
                'contentType' => 'application/json',
                'accept' => 'application/json',
                'body' => json_encode([
                    'anthropic_version' => 'bedrock-2023-05-31',
                    'max_tokens' => $maxTokens,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                ]),
            ]);

            $result = json_decode($response['body']->getContents(), true);

            $usage = is_array($result['usage'] ?? null) ? $result['usage'] : [];
            $inputTokens = (int) ($usage['input_tokens'] ?? 0);
            $outputTokens = (int) ($usage['output_tokens'] ?? 0);
            $responseTimeMs = $this->durationMs($startedAt);

            $costEstimate = $this->estimateCost(
                modelId: $effectiveModelId,
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
            );

            $this->logUsage(
                requestId: $requestId,
                modelId: $effectiveModelId,
                inputTokens: $inputTokens,
                outputTokens: $outputTokens,
                costEstimate: $costEstimate,
                responseTimeMs: $responseTimeMs,
                success: true,
                errorMessage: null,
                context: $context,
            );

            try {
                Pulse::record(type: 'ai_bedrock_latency_ms', key: $effectiveModelId, value: $responseTimeMs)->avg();
                Pulse::record(type: 'ai_bedrock_output_tokens', key: $effectiveModelId, value: $outputTokens)->sum();
                Pulse::record(type: 'ai_bedrock_input_tokens', key: $effectiveModelId, value: $inputTokens)->sum();

                if ($costEstimate !== null) {
                    Pulse::record(type: 'ai_bedrock_cost_usd', key: $effectiveModelId, value: $costEstimate)->sum();
                }

                // Dispatch event for AIServiceMetrics Pulse recorder
                // trace: D03-SRS-AI-019, D18-§6.1
                event(new \App\Events\AIRequestCompleted(
                    service: 'bedrock',
                    responseTimeMs: $responseTimeMs,
                    modelId: $effectiveModelId,
                    inputTokens: $inputTokens,
                    outputTokens: $outputTokens,
                    queryType: $context['query_type'] ?? null,
                ));
            } catch (\Throwable $e) {
                // Jangan gagalkan permintaan jika Pulse bermasalah.
            }

            return [
                'success' => true,
                'content' => $result['content'][0]['text'] ?? '',
                'usage' => $usage,
            ];
        } catch (\Throwable $e) {
            $effectiveModelId = (string) ($modelId ?? config('bedrock.model_id'));
            $rawMessage = $e->getMessage();

            $errorCode = 'BEDROCK_REQUEST_FAILED';
            $userMessage = 'Permintaan ke Bedrock gagal. Sila cuba lagi.';

            if (stripos($rawMessage, "don't have access to the model") !== false) {
                $errorCode = 'MODEL_ACCESS_DENIED';
                $userMessage = 'Akses model AWS Bedrock belum diaktifkan. Sila aktifkan akses model di AWS Bedrock Console (us-east-1) dan cuba semula.';
            } elseif (stripos($rawMessage, 'provided model identifier is invalid') !== false || stripos($rawMessage, 'model identifier is invalid') !== false) {
                if (str_contains($effectiveModelId, 'claude-opus-4-5') && str_starts_with($effectiveModelId, 'us.')) {
                    $errorCode = 'OPUS_REQUIRES_GLOBAL_PROFILE';
                    $userMessage = 'Konfigurasi model Opus 4.5 tidak sah. Sila gunakan global inference profile untuk Opus 4.5.';
                } else {
                    $errorCode = 'INFERENCE_PROFILE_REQUIRED';
                    $userMessage = 'Konfigurasi model Bedrock tidak sah. Sila gunakan inference profile (contoh: us.* atau global.*).';
                }
            } elseif (stripos($rawMessage, 'Error retrieving credentials') !== false || stripos($rawMessage, 'credentials') !== false) {
                $errorCode = 'AWS_CREDENTIALS_NOT_FOUND';
                $userMessage = 'Kelayakan AWS tidak ditemui. Sila semak tetapan AWS_ACCESS_KEY_ID dan AWS_SECRET_ACCESS_KEY.';
            }

            Log::error('Bedrock API error', [
                'error' => $rawMessage,
                'model_id' => $effectiveModelId,
                'error_code' => $errorCode,
            ]);

            $responseTimeMs = $this->durationMs($startedAt);

            $this->logUsage(
                requestId: $requestId,
                modelId: $effectiveModelId,
                inputTokens: 0,
                outputTokens: 0,
                costEstimate: null,
                responseTimeMs: $responseTimeMs,
                success: false,
                errorMessage: $rawMessage,
                context: $context,
            );

            // Dispatch failure event for AIServiceMetrics Pulse recorder
            // trace: D03-SRS-AI-019, D18-§6.1
            try {
                event(new \App\Events\AIRequestFailed(
                    service: 'bedrock',
                    responseTimeMs: $responseTimeMs,
                    errorMessage: $rawMessage,
                    modelId: $effectiveModelId,
                    queryType: $context['query_type'] ?? null,
                ));
            } catch (\Throwable $dispatchError) {
                // Jangan gagalkan permintaan jika event dispatch bermasalah.
            }

            return [
                'success' => false,
                'content' => $userMessage,
                'usage' => [],
                'error_code' => $errorCode,
                'error' => $userMessage,
            ];
        }
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function logUsage(
        string $requestId,
        string $modelId,
        int $inputTokens,
        int $outputTokens,
        ?float $costEstimate,
        int $responseTimeMs,
        bool $success,
        ?string $errorMessage,
        array $context,
    ): void {
        try {
            $userId = $context['user_id'] ?? null;
            if (! is_int($userId)) {
                $userId = Auth::id();
            }

            if ($costEstimate === null) {
                $costEstimate = $this->estimateCost(
                    modelId: $modelId,
                    inputTokens: $inputTokens,
                    outputTokens: $outputTokens,
                );
            }

            BedrockUsageLog::query()->create([
                'request_id' => $requestId,
                'model_id' => $modelId,
                'input_tokens' => max(0, $inputTokens),
                'output_tokens' => max(0, $outputTokens),
                'cost_estimate' => $costEstimate,
                'response_time_ms' => max(0, $responseTimeMs),
                'success' => $success,
                'error_message' => $success ? null : $errorMessage,
                'user_id' => is_int($userId) ? $userId : null,
            ]);
        } catch (\Throwable $e) {
            // Jangan gagalkan pemanggilan jika logging gagal.
        }
    }

    private function estimateCost(string $modelId, int $inputTokens, int $outputTokens): ?float
    {
        if ($inputTokens <= 0 && $outputTokens <= 0) {
            return null;
        }

        try {
            $modelConfig = BedrockModelConfig::query()
                ->where('model_id', $modelId)
                ->where('enabled', true)
                ->first();

            if ($modelConfig && $modelConfig->cost_per_token !== null) {
                $costPerToken = (float) $modelConfig->cost_per_token;

                return ($inputTokens + $outputTokens) * $costPerToken;
            }

            return null;
        } catch (\Throwable $e) {
            // Jika jadual belum wujud atau DB bermasalah, abaikan kos.
            return null;
        }
    }

    private function durationMs(float $startedAt): int
    {
        return (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function safeStrlen(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return (int) mb_strlen($value);
        }

        return strlen($value);
    }
}
