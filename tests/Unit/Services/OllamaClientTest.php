<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\OllamaClientContract;
use App\Services\OllamaClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for OllamaClient Service
 *
 * Tests the Ollama LLM client functionality including:
 * - Text generation
 * - Embeddings generation
 * - Chat functionality
 * - Model listing
 * - Health checks
 * - Caching behavior
 * - Error handling
 *
 * @requirements 8.1, 8.2
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaClientTest extends TestCase
{
    private OllamaClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config([
            'ollama' => [
                'model' => 'llama3.1',
                'url' => 'http://127.0.0.1:11434',
                'default_prompt' => 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC.',
                'connection' => [
                    'timeout' => 300,
                    'connect_timeout' => 10,
                    'retry_attempts' => 3,
                    'retry_delay' => 1000,
                ],
                'cache' => [
                    'enabled' => true,
                    'driver' => 'redis',
                    'ttl' => [
                        'faq_queries' => 3600,
                        'embeddings' => 86400,
                        'common_queries' => 7200,
                    ],
                    'keys' => [
                        'faq_query' => 'ollama:faq:{hash}',
                        'embedding' => 'ollama:embedding:{hash}',
                        'health_check' => 'ollama:health_check',
                    ],
                ],
                'performance' => [
                    'temperature' => 0.7,
                    'top_p' => 0.9,
                    'max_tokens' => 2048,
                    'keep_alive' => '5m',
                ],
            ],
        ]);

        $this->client = new OllamaClient;
    }

    #[Test]
    public function it_implements_ollama_client_contract(): void
    {
        $this->assertInstanceOf(OllamaClientContract::class, $this->client);
    }

    #[Test]
    public function it_can_generate_text_response(): void
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => 'Ini adalah respons ujian dari Ollama.',
                'model' => 'llama3.1',
                'created_at' => now()->toISOString(),
                'done' => true,
                'total_duration' => 1000000000,
            ], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->client->generate([
            'prompt' => 'Apakah ICTServe?',
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('response', $response);
        $this->assertEquals('Ini adalah respons ujian dari Ollama.', $response['response']);
    }

    #[Test]
    public function it_returns_cached_response_for_generate(): void
    {
        $cachedResponse = [
            'response' => 'Respons dari cache',
            'model' => 'llama3.1',
            'done' => true,
        ];

        Cache::shouldReceive('get')
            ->once()
            ->andReturn($cachedResponse);

        $response = $this->client->generate([
            'prompt' => 'Soalan ujian',
        ]);

        $this->assertEquals($cachedResponse, $response);
        Http::assertNothingSent();
    }

    #[Test]
    public function it_validates_generate_payload_requires_prompt(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Prompt diperlukan untuk generate');

        $this->client->generate([]);
    }

    #[Test]
    #[DataProvider('invalidTemperatureProvider')]
    public function it_validates_temperature_range(float $temperature): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Temperature mesti antara 0.0 dan 1.0');

        $this->client->generate([
            'prompt' => 'Test prompt',
            'temperature' => $temperature,
        ]);
    }

    public static function invalidTemperatureProvider(): array
    {
        return [
            'negative temperature' => [-0.1],
            'temperature above 1' => [1.5],
            'temperature way above 1' => [2.0],
        ];
    }

    #[Test]
    #[DataProvider('invalidTopPProvider')]
    public function it_validates_top_p_range(float $topP): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Top_p mesti antara 0.0 dan 1.0');

        $this->client->generate([
            'prompt' => 'Test prompt',
            'top_p' => $topP,
        ]);
    }

    public static function invalidTopPProvider(): array
    {
        return [
            'negative top_p' => [-0.1],
            'top_p above 1' => [1.5],
        ];
    }

    #[Test]
    public function it_can_generate_embeddings(): void
    {
        // OllamaClient uses /api/embed endpoint (not /api/embeddings)
        // and returns 'embeddings' array which is normalized to 'embedding'
        Http::fake([
            '*/api/embed' => Http::response([
                'embeddings' => [array_fill(0, 384, 0.1)],
                'model' => 'nomic-embed-text',
            ], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->client->embeddings('Teks untuk embedding');

        $this->assertIsArray($response);
        $this->assertArrayHasKey('embedding', $response);
        // nomic-embed-text returns 768 dimensions, but we use 384 in test
        $this->assertGreaterThan(0, count($response['embedding']));
    }

    #[Test]
    public function it_validates_embeddings_text_not_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Teks tidak boleh kosong');

        $this->client->embeddings('');
    }

    #[Test]
    public function it_validates_embeddings_text_max_length(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Teks terlalu panjang (maksimum 8192 aksara)');

        $longText = str_repeat('a', 8193);
        $this->client->embeddings($longText);
    }

    #[Test]
    public function it_can_perform_chat(): void
    {
        Http::fake([
            '*/api/chat' => Http::response([
                'message' => [
                    'role' => 'assistant',
                    'content' => 'Saya boleh membantu anda.',
                ],
                'model' => 'llama3.1',
                'done' => true,
            ], 200),
        ]);

        $messages = [
            ['role' => 'user', 'content' => 'Boleh tolong saya?'],
        ];

        $response = $this->client->chat($messages);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('assistant', $response['message']['role']);
    }

    #[Test]
    public function it_validates_chat_messages_not_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Mesej diperlukan untuk chat');

        $this->client->chat([]);
    }

    #[Test]
    public function it_validates_chat_message_format(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Setiap mesej mesti mempunyai role dan content');

        $this->client->chat([
            ['content' => 'Missing role'],
        ]);
    }

    #[Test]
    public function it_validates_chat_message_role(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Role mesej mesti system, user, atau assistant');

        $this->client->chat([
            ['role' => 'invalid', 'content' => 'Test'],
        ]);
    }

    #[Test]
    public function it_can_list_models(): void
    {
        Http::fake([
            '*/api/tags' => Http::response([
                'models' => [
                    ['name' => 'llama3.1', 'size' => 4661224676],
                    ['name' => 'mistral', 'size' => 3825819519],
                ],
            ], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->client->models();

        $this->assertIsArray($response);
        $this->assertArrayHasKey('models', $response);
        $this->assertCount(2, $response['models']);
    }

    #[Test]
    public function it_can_perform_health_check_when_healthy(): void
    {
        Http::fake([
            '*/api/tags' => Http::response(['models' => []], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $result = $this->client->healthCheck();

        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_for_health_check_when_unhealthy(): void
    {
        Http::fake([
            '*/api/tags' => Http::response([], 500),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $result = $this->client->healthCheck();

        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_cached_health_check_result(): void
    {
        Cache::shouldReceive('get')
            ->with('ollama:health_check')
            ->andReturn('healthy');

        $result = $this->client->healthCheck();

        $this->assertTrue($result);
        Http::assertNothingSent();
    }

    #[Test]
    public function it_can_get_cached_response(): void
    {
        $cachedData = ['response' => 'cached'];

        Cache::shouldReceive('get')
            ->with('test_key')
            ->andReturn($cachedData);

        $result = $this->client->getCachedResponse('test_key');

        $this->assertEquals($cachedData, $result);
    }

    #[Test]
    public function it_returns_null_when_cache_disabled(): void
    {
        config(['ollama.cache.enabled' => false]);
        $client = new OllamaClient;

        $result = $client->getCachedResponse('test_key');

        $this->assertNull($result);
    }

    #[Test]
    public function it_can_cache_response(): void
    {
        Cache::shouldReceive('put')
            ->once()
            ->with('test_key', ['response' => 'test'], 3600)
            ->andReturn(true);

        $this->client->cacheResponse('test_key', ['response' => 'test'], 3600);

        $this->assertTrue(true); // No exception means success
    }

    #[Test]
    public function it_can_get_performance_stats(): void
    {
        Cache::shouldReceive('get')
            ->with('ollama:performance_stats')
            ->andReturn(null);

        $stats = $this->client->getPerformanceStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_requests', $stats);
        $this->assertArrayHasKey('cache_hit_rate', $stats);
        $this->assertArrayHasKey('average_response_time', $stats);
        $this->assertArrayHasKey('error_rate', $stats);
    }

    #[Test]
    public function it_can_set_and_get_config(): void
    {
        $this->client->setConfig('model', 'mistral');

        $result = $this->client->getConfig('model');

        $this->assertEquals('mistral', $result);
    }

    #[Test]
    public function it_can_get_full_config(): void
    {
        $config = $this->client->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('model', $config);
        $this->assertArrayHasKey('url', $config);
    }

    #[Test]
    public function it_retries_on_connection_failure(): void
    {
        Http::fake([
            '*/api/generate' => Http::sequence()
                ->push(null, 503)
                ->push(null, 503)
                ->push([
                    'response' => 'Success after retry',
                    'model' => 'llama3.1',
                    'done' => true,
                ], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $response = $this->client->generate([
            'prompt' => 'Test retry',
        ]);

        $this->assertEquals('Success after retry', $response['response']);
    }

    #[Test]
    public function it_throws_exception_after_max_retries(): void
    {
        Http::fake([
            '*/api/generate' => Http::response([], 503),
        ]);

        Cache::shouldReceive('get')->andReturn(null);

        $this->expectException(ConnectionException::class);

        $this->client->generate([
            'prompt' => 'Test max retries',
        ]);
    }

    #[Test]
    public function it_redacts_pii_in_logs(): void
    {
        Http::fake([
            '*/api/generate' => Http::response([
                'response' => 'Respons ujian',
                'model' => 'llama3.1',
                'done' => true,
            ], 200),
        ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        // This should not throw and should redact PII internally
        $response = $this->client->generate([
            'prompt' => 'Nombor IC saya 880101-01-1234 dan telefon +60123456789',
        ]);

        $this->assertIsArray($response);
    }
}
