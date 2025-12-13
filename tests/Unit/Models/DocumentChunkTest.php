<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Document;
use App\Models\DocumentChunk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for DocumentChunk Model
 *
 * @requirements 2.1, 2.2
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class DocumentChunkTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $chunk = new DocumentChunk;

        $expected = [
            'document_id',
            'chunk_text',
            'embedding',
            'source',
            'chunk_index',
        ];

        $this->assertEquals($expected, $chunk->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $chunk = new DocumentChunk;

        $casts = $chunk->getCasts();

        $this->assertEquals('array', $casts['embedding']);
    }

    #[Test]
    public function it_belongs_to_a_document(): void
    {
        $document = Document::factory()->create();
        $chunk = DocumentChunk::factory()->create(['document_id' => $document->id]);

        $this->assertInstanceOf(Document::class, $chunk->document);
        $this->assertEquals($document->id, $chunk->document->id);
    }

    #[Test]
    public function it_can_filter_by_chunk_index(): void
    {
        $document = Document::factory()->create();
        DocumentChunk::factory()->create(['document_id' => $document->id, 'chunk_index' => 0]);
        DocumentChunk::factory()->create(['document_id' => $document->id, 'chunk_index' => 1]);
        DocumentChunk::factory()->create(['document_id' => $document->id, 'chunk_index' => 2]);

        $chunks = DocumentChunk::withIndex(1)->get();

        $this->assertCount(1, $chunks);
        $this->assertEquals(1, $chunks->first()->chunk_index);
    }

    #[Test]
    public function it_can_filter_chunks_with_embedding(): void
    {
        $document = Document::factory()->create();
        DocumentChunk::factory()->create([
            'document_id' => $document->id,
            'embedding' => [0.1, 0.2, 0.3],
        ]);
        DocumentChunk::factory()->create([
            'document_id' => $document->id,
            'embedding' => null,
        ]);

        $chunksWithEmbedding = DocumentChunk::withEmbedding()->get();

        $this->assertCount(1, $chunksWithEmbedding);
        $this->assertNotNull($chunksWithEmbedding->first()->embedding);
    }

    #[Test]
    public function it_calculates_cosine_similarity_correctly(): void
    {
        $chunk = DocumentChunk::factory()->create([
            'embedding' => [1.0, 0.0, 0.0],
        ]);

        $otherEmbedding = [1.0, 0.0, 0.0]; // Identical vectors
        $similarity = $chunk->cosineSimilarity($otherEmbedding);

        $this->assertEqualsWithDelta(1.0, $similarity, 0.001);
    }

    #[Test]
    public function it_returns_zero_similarity_for_empty_embeddings(): void
    {
        $chunk = DocumentChunk::factory()->create(['embedding' => []]);

        $similarity = $chunk->cosineSimilarity([1.0, 0.0, 0.0]);

        $this->assertEquals(0.0, $similarity);
    }

    #[Test]
    public function it_returns_zero_similarity_for_null_embeddings(): void
    {
        $chunk = DocumentChunk::factory()->create(['embedding' => null]);

        $similarity = $chunk->cosineSimilarity([1.0, 0.0, 0.0]);

        $this->assertEquals(0.0, $similarity);
    }

    #[Test]
    public function it_calculates_orthogonal_vectors_similarity(): void
    {
        $chunk = DocumentChunk::factory()->create([
            'embedding' => [1.0, 0.0, 0.0],
        ]);

        $orthogonalEmbedding = [0.0, 1.0, 0.0]; // Orthogonal vectors
        $similarity = $chunk->cosineSimilarity($orthogonalEmbedding);

        $this->assertEqualsWithDelta(0.0, $similarity, 0.001);
    }

    #[Test]
    public function it_generates_preview_text(): void
    {
        $longText = str_repeat('A', 150);
        $chunk = DocumentChunk::factory()->create(['chunk_text' => $longText]);

        $preview = $chunk->preview;

        $this->assertEquals(103, strlen($preview)); // 100 chars + '...'
        $this->assertStringEndsWith('...', $preview);
    }

    #[Test]
    public function it_returns_full_text_for_short_content(): void
    {
        $shortText = 'Short text';
        $chunk = DocumentChunk::factory()->create(['chunk_text' => $shortText]);

        $preview = $chunk->preview;

        $this->assertEquals($shortText, $preview);
        $this->assertStringEndsNotWith('...', $preview);
    }

    #[Test]
    public function it_calculates_text_length(): void
    {
        $text = 'This is a test text';
        $chunk = DocumentChunk::factory()->create(['chunk_text' => $text]);

        $this->assertEquals(strlen($text), $chunk->text_length);
    }

    #[Test]
    public function it_checks_if_has_embedding(): void
    {
        $chunkWithEmbedding = DocumentChunk::factory()->create([
            'embedding' => [0.1, 0.2, 0.3],
        ]);

        $chunkWithoutEmbedding = DocumentChunk::factory()->create([
            'embedding' => null,
        ]);

        $this->assertTrue($chunkWithEmbedding->hasEmbedding());
        $this->assertFalse($chunkWithoutEmbedding->hasEmbedding());
    }

    #[Test]
    public function it_checks_empty_array_as_no_embedding(): void
    {
        $chunk = DocumentChunk::factory()->create(['embedding' => []]);

        $this->assertFalse($chunk->hasEmbedding());
    }

    #[Test]
    public function it_stores_embedding_as_array(): void
    {
        $embedding = [0.1, 0.2, 0.3, 0.4, 0.5];
        $chunk = DocumentChunk::factory()->create(['embedding' => $embedding]);

        $this->assertEquals($embedding, $chunk->fresh()->embedding);
        $this->assertIsArray($chunk->fresh()->embedding);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $chunk = DocumentChunk::factory()->create();

        $this->assertNotNull($chunk->created_at);
        $this->assertNotNull($chunk->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $chunk->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $chunk->updated_at);
    }
}
