<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\OllamaClientContract;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for EmbeddingService
 *
 * Tests vector embedding functionality including:
 * - Embedding generation
 * - Batch embedding processing
 * - Cosine similarity calculation
 * - Similar embedding search
 * - Caching behavior
 *
 * @requirements 2.2, 8.1, 8.4
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class EmbeddingServiceTest extends TestCase
{
    private EmbeddingService $embeddingService;

    private MockInterface $ollamaClientMock;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ollama.embedding' => [
                'cache_ttl' => 86400,
                'batch_size' => 10,
                'max_text_length' => 8192,
                'performance_target' => 0.1,
                'cache_key_prefix' => 'embedding',
            ],
            'ollama.model' => 'nomic-embed-text',
        ]);

        $this->ollamaClientMock = Mockery::mock(OllamaClientContract::class);
        $this->embeddingService = new EmbeddingService($this->ollamaClientMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_generate_embedding(): void
    {
        $text = 'Ini adalah teks ujian untuk embedding.';
        $expectedEmbedding = array_fill(0, 384, 0.1);

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->with($text, null)
            ->andReturn(['embedding' => $expectedEmbedding]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $result = $this->embeddingService->generateEmbedding($text);

        $this->assertIsArray($result);
        $this->assertCount(384, $result);
    }

    #[Test]
    public function it_returns_cached_embedding(): void
    {
        $text = 'Cached text';
        $cachedEmbedding = array_fill(0, 384, 0.2);

        Cache::shouldReceive('get')
            ->once()
            ->andReturn($cachedEmbedding);

        $result = $this->embeddingService->generateEmbedding($text);

        $this->assertEquals($cachedEmbedding, $result);
        $this->ollamaClientMock->shouldNotHaveReceived('embeddings');
    }

    #[Test]
    public function it_validates_empty_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Teks tidak boleh kosong');

        $this->embeddingService->generateEmbedding('');
    }

    #[Test]
    public function it_validates_whitespace_only_text(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Teks tidak boleh kosong');

        $this->embeddingService->generateEmbedding('   ');
    }

    #[Test]
    public function it_validates_text_max_length(): void
    {
        $longText = str_repeat('a', 8193);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Teks terlalu panjang (maksimum 8192 aksara)');

        $this->embeddingService->generateEmbedding($longText);
    }

    #[Test]
    public function it_can_generate_batch_embeddings(): void
    {
        $texts = [
            'Teks pertama',
            'Teks kedua',
            'Teks ketiga',
        ];

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->times(3)
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $results = $this->embeddingService->generateBatchEmbeddings($texts);

        $this->assertCount(3, $results);
        foreach ($results as $embedding) {
            $this->assertCount(384, $embedding);
        }
    }

    #[Test]
    public function it_handles_batch_embedding_failures_gracefully(): void
    {
        $texts = ['Valid text', 'Another text'];

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andThrow(new \Exception('Embedding failed'));

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $results = $this->embeddingService->generateBatchEmbeddings($texts);

        $this->assertCount(2, $results);
        $this->assertCount(384, $results[0]);
        $this->assertEmpty($results[1]);
    }

    #[Test]
    #[DataProvider('cosineSimilarityProvider')]
    public function it_calculates_cosine_similarity(array $embedding1, array $embedding2, float $expected): void
    {
        $result = $this->embeddingService->cosineSimilarity($embedding1, $embedding2);

        $this->assertEqualsWithDelta($expected, $result, 0.001);
    }

    public static function cosineSimilarityProvider(): array
    {
        return [
            'identical vectors' => [
                [1.0, 0.0, 0.0],
                [1.0, 0.0, 0.0],
                1.0,
            ],
            'orthogonal vectors' => [
                [1.0, 0.0, 0.0],
                [0.0, 1.0, 0.0],
                0.0,
            ],
            'opposite vectors' => [
                [1.0, 0.0, 0.0],
                [-1.0, 0.0, 0.0],
                -1.0,
            ],
            'similar vectors' => [
                [0.8, 0.6, 0.0],
                [0.6, 0.8, 0.0],
                0.96,
            ],
        ];
    }

    #[Test]
    public function it_returns_zero_for_empty_embeddings(): void
    {
        $result = $this->embeddingService->cosineSimilarity([], [1.0, 0.0]);
        $this->assertEquals(0.0, $result);

        $result = $this->embeddingService->cosineSimilarity([1.0, 0.0], []);
        $this->assertEquals(0.0, $result);
    }

    #[Test]
    public function it_returns_zero_for_zero_norm_vectors(): void
    {
        $result = $this->embeddingService->cosineSimilarity([0.0, 0.0], [1.0, 0.0]);
        $this->assertEquals(0.0, $result);
    }

    #[Test]
    public function it_can_find_similar_embeddings(): void
    {
        $queryEmbedding = [1.0, 0.0, 0.0];
        $candidates = [
            [0.9, 0.1, 0.0],
            [0.0, 1.0, 0.0],
            [0.8, 0.2, 0.0],
            [0.5, 0.5, 0.0],
        ];

        $results = $this->embeddingService->findSimilarEmbeddings(
            $queryEmbedding,
            $candidates,
            0.3,
            3
        );

        $this->assertCount(3, $results);
        $this->assertEquals(0, $results[0]['index']);
        $this->assertGreaterThan($results[1]['similarity'], $results[0]['similarity']);
    }

    #[Test]
    public function it_respects_similarity_threshold(): void
    {
        $queryEmbedding = [1.0, 0.0, 0.0];
        $candidates = [
            [0.0, 1.0, 0.0],
            [0.0, 0.0, 1.0],
        ];

        $results = $this->embeddingService->findSimilarEmbeddings(
            $queryEmbedding,
            $candidates,
            0.5,
            10
        );

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_respects_result_limit(): void
    {
        $queryEmbedding = [1.0, 0.0, 0.0];
        $candidates = array_fill(0, 10, [0.9, 0.1, 0.0]);

        $results = $this->embeddingService->findSimilarEmbeddings(
            $queryEmbedding,
            $candidates,
            0.3,
            3
        );

        $this->assertCount(3, $results);
    }

    #[Test]
    public function it_sorts_results_by_similarity_descending(): void
    {
        $queryEmbedding = [1.0, 0.0, 0.0];
        $candidates = [
            [0.5, 0.5, 0.0],
            [0.9, 0.1, 0.0],
            [0.7, 0.3, 0.0],
        ];

        $results = $this->embeddingService->findSimilarEmbeddings(
            $queryEmbedding,
            $candidates,
            0.3,
            10
        );

        $this->assertEquals(1, $results[0]['index']);
        $this->assertGreaterThan($results[1]['similarity'], $results[0]['similarity']);
        $this->assertGreaterThan($results[2]['similarity'], $results[1]['similarity']);
    }

    #[Test]
    public function it_throws_exception_for_empty_ollama_response(): void
    {
        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andReturn(['embedding' => []]);

        Cache::shouldReceive('get')->andReturn(null);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Gagal menjana embedding: respons kosong');

        $this->embeddingService->generateEmbedding('Test text');
    }

    #[Test]
    public function it_uses_custom_model_for_embedding(): void
    {
        $text = 'Test with custom model';
        $customModel = 'custom-embed-model';

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->with($text, $customModel)
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $result = $this->embeddingService->generateEmbedding($text, $customModel);

        $this->assertCount(384, $result);
    }

    #[Test]
    public function it_can_precompute_embeddings(): void
    {
        $texts = ['Text 1', 'Text 2', 'Text 3'];

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->times(3)
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $successCount = $this->embeddingService->precomputeEmbeddings($texts);

        $this->assertEquals(3, $successCount);
    }

    #[Test]
    public function it_counts_successful_precomputed_embeddings(): void
    {
        $texts = ['Success 1', 'Fail', 'Success 2'];

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andThrow(new \Exception('Failed'));

        $this->ollamaClientMock
            ->shouldReceive('embeddings')
            ->once()
            ->andReturn(['embedding' => array_fill(0, 384, 0.1)]);

        Cache::shouldReceive('get')->andReturn(null);
        Cache::shouldReceive('put')->andReturn(true);

        $successCount = $this->embeddingService->precomputeEmbeddings($texts);

        $this->assertEquals(2, $successCount);
    }
}
