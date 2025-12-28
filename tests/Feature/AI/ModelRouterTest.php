<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\BedrockModelConfig;
use App\Services\BedrockService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for Model Router functionality in Cloud Hybrid AI system.
 *
 * Validates model selection based on query complexity, cost optimization,
 * and fallback scenarios for the multi-model Bedrock integration.
 *
 * trace: D03-SRS-AI-012, D03-SRS-AI-017
 * trace: Phase 15.1 (Model Router Tests)
 */
#[Group('ai')]
#[Group('bedrock')]
class ModelRouterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['bedrock.enabled' => true]);
        config(['bedrock.routing.max_prompt_chars' => 10000]);
    }

    #[Test]
    public function it_accepts_haiku_model_with_us_inference_profile(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Jawapan Haiku']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Soalan ringkas',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Jawapan Haiku', $result['content']);
    }

    #[Test]
    public function it_accepts_sonnet_model_with_us_inference_profile(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Jawapan Sonnet']],
                    'usage' => ['input_tokens' => 50, 'output_tokens' => 100],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Soalan kompleks untuk analisis',
            500,
            'us.anthropic.claude-sonnet-4-5-20250929-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Jawapan Sonnet', $result['content']);
    }

    #[Test]
    public function it_accepts_opus_model_with_global_inference_profile(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Jawapan Opus']],
                    'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Soalan sangat kompleks memerlukan penaakulan mendalam',
            1000,
            'global.anthropic.claude-opus-4-5-20251101-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Jawapan Opus', $result['content']);
    }

    #[Test]
    public function it_rejects_opus_model_with_us_inference_profile(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->never())->method('__call');

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Soalan',
            100,
            'us.anthropic.claude-opus-4-5-20251101-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('OPUS_REQUIRES_GLOBAL_PROFILE', $result['error_code']);
    }

    #[Test]
    public function it_rejects_model_without_inference_profile_prefix(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->never())->method('__call');

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Soalan',
            100,
            'anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('INFERENCE_PROFILE_REQUIRED', $result['error_code']);
    }

    #[Test]
    #[DataProvider('modelIdValidationProvider')]
    public function it_validates_model_id_format(
        string $modelId,
        bool $expectedSuccess,
        ?string $expectedErrorCode
    ): void {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);

        if ($expectedSuccess) {
            $mockClient->expects($this->once())
                ->method('__call')
                ->willReturn([
                    'body' => Utils::streamFor(json_encode([
                        'content' => [['text' => 'Response']],
                        'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                    ])),
                ]);
        } else {
            $mockClient->expects($this->never())->method('__call');
        }

        $service = new BedrockService($mockClient);
        $result = $service->invoke('Test', 100, $modelId);

        $this->assertSame($expectedSuccess, $result['success']);
        if ($expectedErrorCode !== null) {
            $this->assertSame($expectedErrorCode, $result['error_code']);
        }
    }

    /**
     * @return array<string, array{0: string, 1: bool, 2: ?string}>
     */
    public static function modelIdValidationProvider(): array
    {
        return [
            'valid_haiku_us' => [
                'us.anthropic.claude-haiku-4-5-20251001-v1:0',
                true,
                null,
            ],
            'valid_sonnet_us' => [
                'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
                true,
                null,
            ],
            'valid_opus_global' => [
                'global.anthropic.claude-opus-4-5-20251101-v1:0',
                true,
                null,
            ],
            'invalid_opus_us' => [
                'us.anthropic.claude-opus-4-5-20251101-v1:0',
                false,
                'OPUS_REQUIRES_GLOBAL_PROFILE',
            ],
            'invalid_no_prefix' => [
                'anthropic.claude-haiku-4-5-20251001-v1:0',
                false,
                'INFERENCE_PROFILE_REQUIRED',
            ],
            'invalid_direct_model' => [
                'claude-haiku-4-5',
                false,
                'INFERENCE_PROFILE_REQUIRED',
            ],
        ];
    }

    #[Test]
    public function it_uses_model_config_for_cost_calculation(): void
    {
        BedrockModelConfig::factory()->create([
            'model_id' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
            'cost_per_token' => 0.00025,
            'enabled' => true,
        ]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => ['input_tokens' => 100, 'output_tokens' => 200],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $service->invoke('Test', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertDatabaseHas('bedrock_usage_logs', [
            'model_id' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
            'input_tokens' => 100,
            'output_tokens' => 200,
        ]);
    }

    #[Test]
    public function it_handles_api_errors_gracefully(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException('API Error'));

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error_code', $result);
    }

    #[Test]
    public function it_handles_model_access_denied_error(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException(
                "ValidationException: You don't have access to the model."
            ));

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('MODEL_ACCESS_DENIED', $result['error_code']);
        $this->assertStringContainsString('Akses model', $result['content']);
    }

    #[Test]
    public function it_handles_credentials_error(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException(
                'Error retrieving credentials from the instance profile.'
            ));

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('AWS_CREDENTIALS_NOT_FOUND', $result['error_code']);
    }

    #[Test]
    public function it_rejects_prompts_exceeding_max_length(): void
    {
        config(['bedrock.routing.max_prompt_chars' => 100]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->never())->method('__call');

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            str_repeat('a', 150),
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertSame('PROMPT_TOO_LONG', $result['error_code']);
    }
}
