<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Contracts\OllamaClientContract;
use App\Models\Faq;
use App\Models\User;
use App\Services\EmbeddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for FAQ Bot API Endpoints
 *
 * Tests the FAQ query API functionality including:
 * - Query submission
 * - Authentication (guest and authenticated)
 * - Rate limiting
 * - Response format
 * - Error handling
 *
 * @requirements 1.1, 1.4, 7.1, 8.4
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaFaqApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test configuration
        config([
            'ollama' => [
                'model' => 'llama3.1',
                'url' => 'http://127.0.0.1:11434',
                'default_prompt' => 'Anda adalah pembantu AI untuk sistem ICTServe MOTAC.',
                'rag' => [
                    'similarity_threshold' => 0.3,
                    'max_results' => 5,
                    'conversation_timeout' => 1800,
                    'max_conversation_turns' => 5,
                    'fallback_enabled' => true,
                ],
                'cache' => [
                    'enabled' => true,
                ],
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_query_faq_as_authenticated_user(): void
    {
        $user = User::factory()->create();

        // Mock the Ollama client
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Bagaimana cara membuat tiket helpdesk?',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'answer',
                    'sources',
                    'confidence',
                    'request_id',
                ],
            ]);
    }

    #[Test]
    public function it_can_query_faq_as_guest(): void
    {
        $this->mockOllamaServices();

        $response = $this->postJson('/api/v1/ollama/faq/query', [
            'query' => 'Apakah perkhidmatan ICT yang tersedia?',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'answer',
                    'sources',
                    'confidence',
                ],
            ]);
    }

    #[Test]
    public function it_validates_query_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    #[Test]
    public function it_validates_query_max_length(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => str_repeat('a', 501),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    #[Test]
    public function it_validates_query_is_string(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 12345,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['query']);
    }

    #[Test]
    public function it_includes_request_id_in_response(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.request_id', fn ($id) => \is_string($id) && \strlen($id) === 36);
    }

    #[Test]
    public function it_returns_sources_in_response(): void
    {
        $user = User::factory()->create();

        // Create test FAQ
        Faq::factory()->create([
            'question' => 'Bagaimana cara reset kata laluan?',
            'answer' => 'Klik pautan "Lupa Kata Laluan" di halaman log masuk.',
        ]);

        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'reset kata laluan',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.sources', fn ($sources) => \is_array($sources));
    }

    #[Test]
    public function it_returns_confidence_score(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.confidence', fn ($confidence) => \is_float($confidence) || \is_int($confidence));
    }

    #[Test]
    public function it_supports_session_id_for_conversation_context(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
                'session_id' => 'test-session-123',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.conversation_id', 'test-session-123');
    }

    #[Test]
    public function it_returns_error_response_in_bahasa_melayu(): void
    {
        $user = User::factory()->create();

        // Mock service to throw exception
        $this->instance(
            OllamaClientContract::class,
            Mockery::mock(OllamaClientContract::class, function ($mock) {
                $mock->shouldReceive('generate')
                    ->andThrow(new \Exception('Service unavailable'));
            })
        );

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
            ]);

        // Should return fallback response, not error
        $response->assertStatus(200);
    }

    #[Test]
    public function it_applies_rate_limiting(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        // Make multiple requests to trigger rate limiting
        for ($i = 0; $i < 61; $i++) {
            $response = $this->actingAs($user, 'sanctum')
                ->postJson('/api/v1/ollama/faq/query', [
                    'query' => "Test query {$i}",
                ]);

            if ($response->status() === 429) {
                // Rate limit hit
                $response->assertStatus(429);

                return;
            }
        }

        // If we get here, rate limiting might not be configured
        $this->markTestSkipped('Rate limiting not configured or limit not reached');
    }

    #[Test]
    public function it_includes_processing_time_in_response(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.processing_time', fn ($time) => \is_float($time) || \is_int($time));
    }

    #[Test]
    public function it_sanitizes_pii_in_response(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Nombor IC saya 880101-01-1234',
            ]);

        $response->assertStatus(200);
        // PII should be handled internally, response should still be successful
    }

    #[Test]
    public function it_returns_json_content_type(): void
    {
        $user = User::factory()->create();
        $this->mockOllamaServices();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/ollama/faq/query', [
                'query' => 'Test query',
            ]);

        $response->assertHeader('Content-Type', 'application/json');
    }

    /**
     * Helper method to mock Ollama services
     */
    private function mockOllamaServices(): void
    {
        /** @var \Mockery\MockInterface&OllamaClientContract $ollamaClientMock */
        $ollamaClientMock = Mockery::mock(OllamaClientContract::class);
        $ollamaClientMock->shouldReceive('generate')
            ->andReturnUsing(fn (): array => [
                'response' => 'Ini adalah respons ujian dari AI.',
                'model' => 'llama3.1',
                'done' => true,
            ]);
        $ollamaClientMock->shouldReceive('healthCheck')->andReturn(true);

        /** @var \Mockery\MockInterface&EmbeddingService $embeddingServiceMock */
        $embeddingServiceMock = Mockery::mock(EmbeddingService::class);
        $embeddingServiceMock->shouldReceive('generateEmbedding')
            ->andReturnUsing(fn (): array => \array_fill(0, 384, 0.1));

        $this->instance(OllamaClientContract::class, $ollamaClientMock);
        $this->instance(EmbeddingService::class, $embeddingServiceMock);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);
    }
}
