<?php

declare(strict_types=1);

namespace Tests\Feature\AI;

use App\Models\BedrockUsageLog;
use App\Models\User;
use App\Services\BedrockService;
use App\Services\DataClassificationService;
use Aws\BedrockRuntime\BedrockRuntimeClient;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for Data Residency and Compliance in Cloud Hybrid AI system.
 *
 * Validates data classification accuracy, Malaysia data residency enforcement,
 * PDPA 2010 compliance, and audit trail completeness.
 *
 * trace: D03-SRS-AI-007, D09-§9 (Audit Requirements)
 * trace: Phase 15.3 (Compliance and Security Tests)
 */
#[Group('ai')]
#[Group('compliance')]
#[Group('security')]
class DataResidencyComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['bedrock.enabled' => true]);
        config(['bedrock.routing.max_prompt_chars' => 10000]);
    }

    #[Test]
    public function it_blocks_restricted_data_from_cloud_processing(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Dokumen SULIT kerajaan Malaysia');

        $this->assertSame('restricted', $result['classification']);
        $this->assertFalse($result['allow_cloud']);
        $this->assertTrue($result['should_block']);
    }

    #[Test]
    public function it_allows_public_data_for_cloud_processing(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Apakah waktu operasi pejabat?');

        $this->assertSame('public', $result['classification']);
        $this->assertTrue($result['allow_cloud']);
        $this->assertFalse($result['should_block']);
    }

    #[Test]
    #[DataProvider('sensitiveKeywordProvider')]
    public function it_detects_sensitive_keywords_correctly(
        string $text,
        string $expectedClassification,
        bool $expectedAllowCloud
    ): void {
        $classifier = new DataClassificationService;

        $result = $classifier->classify($text);

        $this->assertSame($expectedClassification, $result['classification']);
        $this->assertSame($expectedAllowCloud, $result['allow_cloud']);
    }

    /**
     * @return array<string, array{0: string, 1: string, 2: bool}>
     */
    public static function sensitiveKeywordProvider(): array
    {
        return [
            'sulit_keyword' => [
                'Dokumen SULIT',
                'restricted',
                false,
            ],
            'rahsia_keyword' => [
                'Maklumat RAHSIA',
                'restricted',
                false,
            ],
            'confidential_keyword' => [
                'Confidential document',
                'restricted',
                false,
            ],
            'restricted_keyword' => [
                'Restricted access only',
                'restricted',
                false,
            ],
            'public_query' => [
                'Soalan umum tentang perkhidmatan',
                'public',
                true,
            ],
        ];
    }

    #[Test]
    public function it_logs_all_bedrock_requests_for_audit(): void
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
        $service->invoke(
            'Test prompt',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertDatabaseCount('bedrock_usage_logs', 1);

        $log = BedrockUsageLog::first();
        $this->assertTrue($log->success);
        $this->assertNotNull($log->request_id);
        $this->assertNotNull($log->response_time_ms);
    }

    #[Test]
    public function it_logs_failed_requests_for_audit(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willThrowException(new \RuntimeException('API Error'));

        $service = new BedrockService($mockClient);
        $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertDatabaseCount('bedrock_usage_logs', 1);

        $log = BedrockUsageLog::first();
        $this->assertFalse($log->success);
        $this->assertNotNull($log->error_message);
    }

    #[Test]
    public function it_tracks_user_id_in_audit_log(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

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
        $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $this->assertDatabaseHas('bedrock_usage_logs', [
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    public function it_allows_null_user_id_for_guest_requests(): void
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
        $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $log = BedrockUsageLog::first();
        $this->assertNull($log->user_id);
    }

    #[Test]
    public function it_tracks_cost_estimate_for_billing(): void
    {
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
        $service->invoke(
            'Test',
            100,
            'us.anthropic.claude-haiku-4-5-20251001-v1:0'
        );

        $log = BedrockUsageLog::first();
        $this->assertSame(100, $log->input_tokens);
        $this->assertSame(200, $log->output_tokens);
    }

    #[Test]
    public function it_tracks_response_time_for_performance_monitoring(): void
    {
        $mockClient = $this->createMock(BedrockRuntimeClient::class);
        $mockClient->expects($this->once())
            ->method('__call')
            ->willReturnCallback(function () {
                // Simulate a small delay to ensure response_time_ms > 0
                usleep(1000); // 1ms delay
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
        $this->assertGreaterThan(0, $log->response_time_ms);
    }

    #[Test]
    public function it_provides_reason_for_classification(): void
    {
        $classifier = new DataClassificationService;

        $result = $classifier->classify('Dokumen SULIT');

        $this->assertArrayHasKey('reason', $result);
        $this->assertNotEmpty($result['reason']);
    }
}
