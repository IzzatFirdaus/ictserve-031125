<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\BedrockService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BedrockServiceTest extends TestCase
{
    #[Test]
    public function it_rejects_prompts_that_exceed_configured_max_chars(): void
    {
        config()->set('bedrock.routing.max_prompt_chars', 1);

        $client = $this->createMock(BedrockRuntimeClient::class);
        $client->expects($this->never())->method('__call');

        $service = new BedrockService($client);

        $result = $service->invoke('ab', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertFalse($result['success']);
        $this->assertSame('PROMPT_TOO_LONG', $result['error_code']);
        $this->assertNotEmpty($result['content']);
        $this->assertSame([], $result['usage']);
    }

    #[Test]
    public function it_rejects_model_ids_without_inference_profile_prefix(): void
    {
        config()->set('bedrock.routing.max_prompt_chars', 10000);

        $client = $this->createMock(BedrockRuntimeClient::class);
        $client->expects($this->never())->method('__call');

        $service = new BedrockService($client);

        $result = $service->invoke('Ujian', 100, 'anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertFalse($result['success']);
        $this->assertSame('INFERENCE_PROFILE_REQUIRED', $result['error_code']);
        $this->assertNotEmpty($result['content']);
    }

    #[Test]
    public function it_rejects_opus_4_5_when_using_us_inference_profile(): void
    {
        config()->set('bedrock.routing.max_prompt_chars', 10000);

        $client = $this->createMock(BedrockRuntimeClient::class);
        $client->expects($this->never())->method('__call');

        $service = new BedrockService($client);

        $result = $service->invoke('Ujian', 100, 'us.anthropic.claude-opus-4-5-20251101-v1:0');

        $this->assertFalse($result['success']);
        $this->assertSame('OPUS_REQUIRES_GLOBAL_PROFILE', $result['error_code']);
        $this->assertNotEmpty($result['content']);
    }

    #[Test]
    public function it_returns_success_and_usage_when_bedrock_call_succeeds(): void
    {
        config()->set('bedrock.routing.max_prompt_chars', 10000);

        $client = $this->createMock(BedrockRuntimeClient::class);
        $client->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('invokeModel'),
                $this->callback(function (array $arguments): bool {
                    if (! isset($arguments[0]) || ! is_array($arguments[0])) {
                        return false;
                    }

                    return ($arguments[0]['contentType'] ?? null) === 'application/json'
                        && ($arguments[0]['accept'] ?? null) === 'application/json'
                        && isset($arguments[0]['modelId'], $arguments[0]['body']);
                })
            )
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [
                        ['text' => 'Jawapan ujian'],
                    ],
                    'usage' => [
                        'input_tokens' => 3,
                        'output_tokens' => 5,
                    ],
                ])),
            ]);

        $service = new BedrockService($client);

        $result = $service->invoke('Ujian', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertTrue($result['success']);
        $this->assertSame('Jawapan ujian', $result['content']);
        $this->assertSame(5, $result['usage']['output_tokens']);
    }

    #[Test]
    public function it_maps_model_access_denied_errors_to_a_helpful_message(): void
    {
        config()->set('bedrock.routing.max_prompt_chars', 10000);

        $client = $this->createMock(BedrockRuntimeClient::class);
        $client->expects($this->once())
	            ->method('__call')
	            ->with(
	                $this->equalTo('invokeModel'),
	                $this->isArray()
	            )
	            ->willThrowException(new \RuntimeException(
	                "ValidationException: You don't have access to the model with the specified model ID."
	            ));

        $service = new BedrockService($client);

        $result = $service->invoke('Ujian', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertFalse($result['success']);
        $this->assertSame('MODEL_ACCESS_DENIED', $result['error_code']);
        $this->assertStringContainsString('Akses model', $result['content']);
    }
}
