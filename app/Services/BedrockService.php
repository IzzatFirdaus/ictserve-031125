<?php

declare(strict_types=1);

namespace App\Services;

use Aws\BedrockRuntime\BedrockRuntimeClient;
use Illuminate\Support\Facades\Log;

class BedrockService
{
    public function __construct(
        private BedrockRuntimeClient $client
    ) {}

    public function invoke(string $prompt, int $maxTokens = 1000, ?string $modelId = null): array
    {
        try {
            $response = $this->client->invokeModel([
                'modelId' => $modelId ?? config('bedrock.model_id'),
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

            return [
                'success' => true,
                'content' => $result['content'][0]['text'] ?? '',
                'usage' => $result['usage'] ?? [],
            ];
        } catch (\Exception $e) {
            Log::error('Bedrock API error', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
