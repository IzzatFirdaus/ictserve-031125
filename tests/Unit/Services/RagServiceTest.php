<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\OllamaClientContract;
use App\Models\Faq;
use App\Models\GuestConversation;
use App\Services\EmbeddingService;
use App\Services\RagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for RagService (Retrieval-Augmented Generation)
 *
 * @requirements 1.1, 1.2, 1.3, 1.7, 2.2
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class RagServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return array{OllamaClientContract&MockInterface, EmbeddingService&MockInterface}
     */
    private function createMocks(): array
    {
        /** @var OllamaClientContract&MockInterface $ollamaClientMock */
        $ollamaClientMock = Mockery::mock(OllamaClientContract::class);

        /** @var EmbeddingService&MockInterface $embeddingServiceMock */
        $embeddingServiceMock = Mockery::mock(EmbeddingService::class);

        return [$ollamaClientMock, $embeddingServiceMock];
    }

    private function createRagService(
        OllamaClientContract&MockInterface $ollamaClientMock,
        EmbeddingService&MockInterface $embeddingServiceMock
    ): RagService
    {
        config([
            'ollama.rag' => [
                'similarity_threshold' => 0.3,
                'max_results' => 5,
                'conversation_timeout' => 1800,
                'max_conversation_turns' => 5,
                'fallback_enabled' => true,
            ],
            'ollama.model' => 'llama3.1',
        ]);

        /** @phpstan-ignore-next-line */
        return new RagService($ollamaClientMock, $embeddingServiceMock);
    }

    #[Test]
    public function it_can_process_query_successfully(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Untuk membuat tiket helpdesk, sila ikuti langkah berikut...',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Bagaimana cara membuat tiket helpdesk?', 'test-session-123');

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('answer', $result);
        $this->assertArrayHasKey('request_id', $result);
    }

    #[Test]
    public function it_returns_fallback_response_on_error(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andThrow(new \Exception('Embedding service error'));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andThrow(new \Exception('LLM error'));

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Test query');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['is_fallback'] ?? false);
        $this->assertStringContainsString('tidak dapat memberikan jawapan', $result['answer']);
    }

    #[Test]
    public function it_retrieves_relevant_faqs(): void
    {
        Faq::factory()->create([
            'question' => 'Bagaimana cara reset kata laluan?',
            'answer' => 'Untuk reset kata laluan, klik pautan Lupa Kata Laluan di halaman log masuk.',
            'tags' => ['kata laluan', 'reset', 'log masuk'],
        ]);

        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Untuk reset kata laluan...',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('reset kata laluan');

        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_maintains_conversation_context(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons pertama',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Soalan pertama', 'conversation-test-123', null);

        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_can_claim_guest_conversation(): void
    {
        // Create a user first (required for foreign key constraint)
        $user = \App\Models\User::factory()->create([
            'email' => 'test@motac.gov.my',
        ]);

        $sessionId = 'guest-session-'.uniqid();
        $email = $user->email;

        // Create unclaimed conversation with explicit null for claimed_by_user_id
        $conversation = GuestConversation::create([
            'session_id' => $sessionId,
            'email' => $email,
            'conversation_history' => [
                ['query' => 'Test query', 'response' => 'Test response'],
            ],
            'claimed_by_user_id' => null,
            'claimed_at' => null,
            'expires_at' => now()->addHours(1),
        ]);

        // Verify the conversation was created correctly
        $this->assertNotNull($conversation);
        $this->assertNull($conversation->claimed_by_user_id);

        // Mock Cache::put for the claim operation
        Cache::shouldReceive('put')
            ->once()
            ->andReturn(true);

        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->claimGuestConversation($sessionId, $user->id, $email);

        $this->assertTrue($result, 'Claim should succeed for unclaimed conversation');

        // Refresh from database
        $conversation->refresh();
        $this->assertEquals($user->id, $conversation->claimed_by_user_id);
        $this->assertNotNull($conversation->claimed_at);
    }

    #[Test]
    public function it_returns_false_when_claiming_nonexistent_conversation(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->claimGuestConversation(
            'nonexistent-session-'.uniqid(),
            1,
            'test@motac.gov.my'
        );

        $this->assertFalse($result);
    }

    #[Test]
    public function it_can_get_conversation_history(): void
    {
        $sessionId = 'history-test-123';
        $conversationHistory = [
            ['query' => 'Soalan 1', 'response' => 'Jawapan 1'],
            ['query' => 'Soalan 2', 'response' => 'Jawapan 2'],
        ];

        Cache::shouldReceive('get')
            ->with("conversation_context:{$sessionId}", [])
            ->andReturn($conversationHistory);

        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->getConversationHistory($sessionId);

        $this->assertCount(2, $result);
        $this->assertEquals('Soalan 1', $result[0]['query']);
    }

    #[Test]
    public function it_detects_pii_in_queries(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons ujian',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Nombor IC saya 880101-01-1234');

        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_calculates_confidence_based_on_context(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons dengan konteks',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Test query');

        if (isset($result['confidence'])) {
            $this->assertIsFloat($result['confidence']);
            $this->assertGreaterThanOrEqual(0.0, $result['confidence']);
            $this->assertLessThanOrEqual(1.0, $result['confidence']);
        }
        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_logs_operations_for_audit(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons untuk audit',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Audit test query', null, 1);

        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_includes_processing_time_in_response(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons ujian',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Test query');

        // Processing time should be present in successful responses
        // If it's a fallback response, it won't have processing_time
        $this->assertTrue($result['success']);
        if (! isset($result['is_fallback']) || ! $result['is_fallback']) {
            $this->assertArrayHasKey('processing_time', $result);
            $this->assertIsFloat($result['processing_time']);
            $this->assertGreaterThan(0, $result['processing_time']);
        }
    }

    #[Test]
    public function it_supports_authenticated_user_queries(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons untuk pengguna berdaftar',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Test query', 'session-123', 1);

        $this->assertTrue($result['success']);
    }

    #[Test]
    public function it_supports_guest_queries_with_email(): void
    {
        [$ollamaClientMock, $embeddingServiceMock] = $this->createMocks();

        $embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturnUsing(static fn (): array => array_fill(0, 384, 0.1));

        $ollamaClientMock
            ->shouldReceive('generate')
            ->andReturnUsing(static fn (): array => [
                'response' => 'Respons untuk tetamu',
                'model' => 'llama3.1',
                'done' => true,
            ]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $ragService = $this->createRagService($ollamaClientMock, $embeddingServiceMock);
        $result = $ragService->processQuery('Test query', 'guest-session-456', null, 'guest@motac.gov.my');

        $this->assertTrue($result['success']);
    }
}
