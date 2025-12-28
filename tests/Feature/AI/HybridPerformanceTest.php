<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\BedrockUsageLog;
use App\Services\BedrockService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Performance tests for Cloud Hybrid AI system.
 *
 * Tests model routing performance, streaming response handling,
 * cost optimization, and failover scenarios.
 *
 * trace: D03-SRS-AI-017, D11-§8.1 (Performance Monitoring)
 * trace: Phase 15.2 (Performance and Load Tests)
 */
#[Group('ai')]
#[Group('performance')]
class HybridPerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['bedrock.enabled' => true]);
        config(['bedrock.routing.max_prompt_chars' => 10000]);
    }

    #[Test]
    public function it_tracks_response_time_accurately(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturnCallback(function () {
                usleep(50000); // 50ms delay

                return [
                    'body' => Utils::streamFor(json_encode([
                        'content' => [['text' => 'Response']],
                        'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                    ])),
                ];
            });

        $service = new BedrockService($mockClient);
        $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $log = BedrockUsageLog::first();
        $this->assertGreaterThanOrEqual(50, $log->response_time_ms);
    }

    #[Test]
    public function it_handles_multiple_sequential_requests(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->exactly(3))
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                ])),
            ]);

        $service = new BedrockService($mockClient);

        for ($i = 0; $i < 3; $i++) {
            $result = $service->invoke(
                "Test query {$i}",
                100,
                'us.anthropic.claude-haiku-4-5-20251001-v1:0'
            );
            $this->assertTrue($result['success']);
        }

        $this->assertDatabaseCount('bedrock_usage_logs', 3);
    }

    #[Test]
    public function it_logs_token_usage_for_cost_tracking(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => ['input_tokens' => 150, 'output_tokens' => 300],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $service->invoke(
            'Test',
            500,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $log = BedrockUsageLog::first();
        $this->assertSame(150, $log->input_tokens);
        $this->assertSame(300, $log->output_tokens);
    }

    #[Test]
    public function it_handles_empty_response_gracefully(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => '']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 0],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame('', $result['content']);
    }

    #[Test]
    public function it_handles_missing_usage_data_gracefully(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertTrue($result['success']);
        $this->assertSame([], $result['usage']);
    }

    #[Test]
    public function it_handles_api_timeout_gracefully(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException('Connection timed out'));

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
    public function it_handles_rate_limiting_error(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException('ThrottlingException: Rate exceeded'));

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertFalse($result['success']);
        $this->assertDatabaseCount('bedrock_usage_logs', 1);

        $log = BedrockUsageLog::first();
        $this->assertFalse($log->success);
    }

    #[Test]
    public function it_validates_max_tokens_parameter(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('invokeModel'),
                $this->callback(function (array $args): bool {
                    $body = json_decode($args[0]['body'], true);

                    return $body['max_tokens'] === 500;
                })
            )
            ->willReturn([
                'body' => Utils::streamFor(json_encode([
                    'content' => [['text' => 'Response']],
                    'usage' => ['input_tokens' => 10, 'output_tokens' => 20],
                ])),
            ]);

        $service = new BedrockService($mockClient);
        $result = $service->invoke(
            'Test',
            500,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertTrue($result['success']);
    }
}
