<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\OllamaClientContract;
use App\Models\BedrockUsageLog;
use App\Services\ModelRouter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ModelRouterTest extends TestCase
{
    use RefreshDatabase;

    private ModelRouter $router;

    private $mockOllamaClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockOllamaClient = Mockery::mock(OllamaClientContract::class);
        $this->router = new ModelRouter($this->mockOllamaClient);

        // Clear caches before each test
        Cache::flush();
        RateLimiter::clear('bedrock_rate_limit:opus');
        RateLimiter::clear('bedrock_rate_limit:sonnet');
        RateLimiter::clear('bedrock_rate_limit:haiku');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_selects_haiku_for_simple_faq_queries(): void
    {
        config(['bedrock.enabled' => false]); // Force Ollama fallback

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, [
            'query' => 'Bagaimana cara reset password?',
        ]);

        $this->assertEquals(ModelRouter::MODEL_HAIKU, $decision['model_tier']);
        $this->assertArrayHasKey('reason', $decision);
        $this->assertArrayHasKey('cost_estimate', $decision);
    }

    #[Test]
    public function it_selects_sonnet_for_document_analysis(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_DOCUMENT_ANALYSIS, [
            'query' => 'Analisis dokumen ini untuk maklumat penting.',
        ]);

        $this->assertEquals(ModelRouter::MODEL_SONNET, $decision['model_tier']);
    }

    #[Test]
    public function it_selects_opus_for_auto_reply_generation(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_AUTO_REPLY, [
            'query' => 'Jana respons untuk tiket helpdesk ini.',
        ]);

        $this->assertEquals(ModelRouter::MODEL_OPUS, $decision['model_tier']);
    }

    #[Test]
    public function it_selects_opus_for_code_analysis(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_CODE_ANALYSIS, [
            'query' => 'Debug kod PHP ini.',
        ]);

        $this->assertEquals(ModelRouter::MODEL_OPUS, $decision['model_tier']);
    }

    #[Test]
    #[DataProvider('complexityDataProvider')]
    public function it_analyzes_complexity_correctly(string $query, string $expectedComplexity): void
    {
        $complexity = $this->router->analyzeComplexity(['query' => $query]);

        $this->assertEquals($expectedComplexity, $complexity);
    }

    public static function complexityDataProvider(): array
    {
        return [
            'simple_query' => [
                'Bagaimana cara reset password?',
                'simple',
            ],
            'medium_query_with_technical_terms' => [
                // Query with 3+ technical terms (server, database, authentication, error) triggers medium complexity
                'Saya menghadapi masalah dengan server database dan authentication tidak berfungsi. Error berlaku semasa log masuk.',
                'medium',
            ],
            'complex_query_with_many_technical_terms' => [
                // Long query (>50 words) with 6+ technical terms and multiple sentences triggers complex
                'Saya perlu bantuan untuk troubleshoot masalah network firewall yang menyebabkan authentication gagal. Server database tidak dapat diakses dan log menunjukkan error SSL/TLS. Bagaimana cara debug masalah ini dan apakah perlu update configuration? Saya sudah cuba restart server tetapi masalah masih berlaku. Adakah ini berkaitan dengan security policy atau permission access yang salah?',
                'complex',
            ],
        ];
    }

    #[Test]
    public function it_falls_back_to_ollama_when_bedrock_disabled(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, [
            'query' => 'Test query',
        ]);

        $this->assertEquals(ModelRouter::PROVIDER_OLLAMA, $decision['provider']);
        $this->assertTrue(str_contains($decision['reason'], 'Ollama tempatan'));
    }

    #[Test]
    public function it_falls_back_to_static_when_all_providers_unavailable(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(false);

        $decision = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, [
            'query' => 'Test query',
        ]);

        $this->assertEquals(ModelRouter::PROVIDER_STATIC, $decision['provider']);
    }

    #[Test]
    public function it_returns_static_fallback_response_in_malay(): void
    {
        $response = $this->router->getStaticFallbackResponse(ModelRouter::TASK_FAQ_SIMPLE);

        $this->assertStringContainsString('Maaf', $response);
        $this->assertStringContainsString('BPM', $response);
    }

    #[Test]
    public function it_estimates_request_cost_correctly(): void
    {
        $cost = $this->router->estimateRequestCost(ModelRouter::MODEL_HAIKU, [
            'query' => str_repeat('a', 400), // ~100 tokens
            'max_tokens' => 500,
        ]);

        $this->assertNotNull($cost);
        $this->assertGreaterThan(0, $cost);
        $this->assertLessThan(1, $cost); // Should be less than $1 for small request
    }

    #[Test]
    public function it_caches_routing_decisions(): void
    {
        config(['bedrock.enabled' => false]);
        config(['bedrock.routing.cache_ttl_seconds' => 3600]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $context = ['query' => 'Test caching query'];

        // First call
        $decision1 = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, $context);

        // Second call should use cache
        $decision2 = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, $context);

        $this->assertEquals($decision1['model_tier'], $decision2['model_tier']);
        $this->assertEquals($decision1['provider'], $decision2['provider']);
    }

    #[Test]
    public function it_respects_force_model_tier_override(): void
    {
        config(['bedrock.enabled' => false]);

        $this->mockOllamaClient->shouldReceive('healthCheck')->andReturn(true);

        $decision = $this->router->selectModel(ModelRouter::TASK_FAQ_SIMPLE, [
            'query' => 'Simple query',
            'force_model_tier' => ModelRouter::MODEL_OPUS,
        ]);

        $this->assertEquals(ModelRouter::MODEL_OPUS, $decision['model_tier']);
    }

    #[Test]
    public function it_returns_routing_statistics(): void
    {
        // Create some test usage logs
        BedrockUsageLog::factory()->count(5)->create([
            'success' => true,
            'model_id' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ]);

        BedrockUsageLog::factory()->count(2)->create([
            'success' => false,
            'model_id' => 'us.anthropic.claude-haiku-4-5-20251001-v1:0',
        ]);

        $stats = $this->router->getRoutingStatistics();

        $this->assertArrayHasKey('total_requests', $stats);
        $this->assertArrayHasKey('successful_requests', $stats);
        $this->assertArrayHasKey('success_rate', $stats);
        $this->assertArrayHasKey('by_model', $stats);
        $this->assertEquals(7, $stats['total_requests']);
        $this->assertEquals(5, $stats['successful_requests']);
    }

    #[Test]
    public function it_clears_routing_cache(): void
    {
        Cache::tags(['model_router', 'routing_decisions'])->put('test_key', 'test_value', 3600);

        $result = $this->router->clearRoutingCache();

        $this->assertTrue($result);
        $this->assertNull(Cache::tags(['model_router', 'routing_decisions'])->get('test_key'));
    }

    #[Test]
    public function it_returns_routing_config(): void
    {
        $config = $this->router->getRoutingConfig();

        $this->assertArrayHasKey('bedrock_enabled', $config);
        $this->assertArrayHasKey('rate_limits', $config);
        $this->assertArrayHasKey('models', $config);
        $this->assertArrayHasKey('model_costs', $config);
        $this->assertArrayHasKey('task_types', $config);
    }

    #[Test]
    public function it_detects_code_in_query(): void
    {
        $queryWithCode = 'Saya ada masalah dengan kod ini: ```php function test() { return true; } ```';

        $complexity = $this->router->analyzeComplexity(['query' => $queryWithCode]);

        // Code presence should increase complexity
        $this->assertContains($complexity, ['medium', 'complex']);
    }
}
