<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BedrockConversation;
use App\Models\BedrockModelConfig;
use App\Models\BedrockUsageLog;
use App\Models\User;
use App\Services\BedrockService;
use App\Services\DataClassificationService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Comprehensive integration tests for AWS Bedrock Cloud Hybrid AI system.
 *
 * trace: D03-SRS-AI-007, D03-SRS-AI-011, D03-SRS-AI-017
 * trace: Phase 15.1 (Bedrock Integration Tests)
 */
class BedrockIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    #[Test]
    public function it_routes_simple_faq_to_haiku_model(): void
    {
        config(['bedrock.enabled' => true]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Jawapan FAQ']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Apakah waktu operasi pejabat?',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('Jawapan FAQ', $result['content']);
    }

    #[Test]
    public function it_logs_usage_to_database(): void
    {
        config(['bedrock.enabled' => true]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Test response']],
                    'usage' => ['input_tokens' => 15, 'output_tokens' => 25],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $service->invoke('Test prompt', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertDatabaseCount('bedrock_usage_logs', 1);

        $log = BedrockUsageLog::first();
        $this->assertTrue($log->success);
        $this->assertSame(15, $log->input_tokens);
        $this->assertSame(25, $log->output_tokens);
    }

    #[Test]
    public function it_logs_failed_requests(): void
    {
        config(['bedrock.enabled' => true]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException('API Error'));

        $service = new BedrockService($mockClient);
        $result = $service->invoke('Test', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertFalse($result['success']);
        $this->assertDatabaseCount('bedrock_usage_logs', 1);

        $log = BedrockUsageLog::first();
        $this->assertFalse($log->success);
        $this->assertNotNull($log->error_message);
    }

    #[Test]
    public function it_creates_conversation_context(): void
    {
        $user = User::factory()->create();

        $conversation = BedrockConversation::create([
            'user_id' => $user->id,
            'title' => 'Perbualan Ujian',
            'messages' => [
                ['role' => 'user', 'content' => 'Soalan pertama'],
                ['role' => 'assistant', 'content' => 'Jawapan pertama'],
            ],
            'model' => 'haiku',
            'total_tokens' => 50,
        ]);

        $this->assertDatabaseHas('bedrock_conversations', [
            'user_id' => $user->id,
            'title' => 'Perbualan Ujian',
        ]);

        $this->assertCount(2, $conversation->messages);
    }

    #[Test]
    public function it_supports_guest_conversations_without_user(): void
    {
        $conversation = BedrockConversation::create([
            'user_id' => null,
            'title' => 'Perbualan Tetamu',
            'messages' => [
                ['role' => 'user', 'content' => 'Soalan tetamu'],
            ],
            'model' => 'haiku',
            'total_tokens' => 10,
        ]);

        $this->assertDatabaseHas('bedrock_conversations', [
            'user_id' => null,
            'title' => 'Perbualan Tetamu',
        ]);
    }

    #[Test]
    #[DataProvider('modelConfigDataProvider')]
    public function it_uses_model_config_for_cost_estimation(
        string $modelId,
        float $costPerToken,
        int $inputTokens,
        int $outputTokens
    ): void {
        BedrockModelConfig::factory()->create([
            'model_id' => $modelId,
            'cost_per_token' => $costPerToken,
            'enabled' => true,
        ]);

        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => [
                        'input_tokens' => $inputTokens,
                        'output_tokens' => $outputTokens,
                    ],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $service->invoke('Test', 100, $modelId);

        $log = BedrockUsageLog::first();
        $expectedCost = ($inputTokens + $outputTokens) * $costPerToken;
        $this->assertEquals($expectedCost, $log->cost_estimate);
    }

    /**
     * @return array<string, array{0: string, 1: float, 2: int, 3: int}>
     */
    public static function modelConfigDataProvider(): array
    {
        return [
            'haiku_model' => [
                'us.anthropic.claude-haiku-4-5-20251001-v1:0',
                0.00025,
                100,
                200,
            ],
            'sonnet_model' => [
                'us.anthropic.claude-sonnet-4-5-20250929-v1:0',
                0.003,
                500,
                300,
            ],
        ];
    }

    #[Test]
    public function it_enforces_data_classification_for_cloud_routing(): void
    {
        $classifier = new DataClassificationService;

        $publicResult = $classifier->classify('Apakah waktu operasi pejabat?');
        $this->assertTrue($publicResult['allow_cloud']);

        $restrictedResult = $classifier->classify('Dokumen SULIT kerajaan.');
        $this->assertFalse($restrictedResult['allow_cloud']);
        $this->assertTrue($restrictedResult['should_block']);
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
        $result = $service->invoke('Test', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

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
        $result = $service->invoke('Test', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $this->assertFalse($result['success']);
        $this->assertSame('AWS_CREDENTIALS_NOT_FOUND', $result['error_code']);
    }

    #[Test]
    public function it_tracks_response_time_in_usage_log(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $service->invoke('Test', 100, 'us.anthropic.claude-haiku-4-5-20251001-v1:0');

        $log = BedrockUsageLog::first();
        $this->assertGreaterThan(0, $log->response_time_ms);
    }
}
