<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for Document Model
 *
 * @requirements 2.1, 2.2, 4.1
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class DocumentTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $document = new Document;

        $expected = [
            'filename',
            'metadata',
            'uploaded_by',
            'status',
        ];

        $this->assertEquals($expected, $document->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $document = new Document;

        $casts = $document->getCasts();

        $this->assertEquals('array', $casts['metadata']);
    }

    #[Test]
    public function it_has_correct_status_constants(): void
    {
        $this->assertEquals('pending', Document::STATUS_PENDING);
        $this->assertEquals('processing', Document::STATUS_PROCESSING);
        $this->assertEquals('completed', Document::STATUS_COMPLETED);
        $this->assertEquals('failed', Document::STATUS_FAILED);
    }

    #[Test]
    public function it_belongs_to_an_uploader(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['uploaded_by' => $user->id]);

        $this->assertInstanceOf(User::class, $document->uploader);
        $this->assertEquals($user->id, $document->uploader->id);
    }

    #[Test]
    public function it_can_have_null_uploader_for_true_hybrid_architecture(): void
    {
        $document = Document::factory()->create(['uploaded_by' => null]);

        $this->assertNull($document->uploaded_by);
        $this->assertNull($document->uploader->id ?? null);
    }

    #[Test]
    public function it_has_many_chunks(): void
    {
        $document = Document::factory()->create();
        $chunks = DocumentChunk::factory()->count(3)->create(['document_id' => $document->id]);

        $this->assertCount(3, $document->chunks);
        $this->assertInstanceOf(DocumentChunk::class, $document->chunks->first());
    }

    #[Test]
    public function it_can_filter_by_status(): void
    {
        Document::factory()->create(['status' => Document::STATUS_COMPLETED]);
        Document::factory()->create(['status' => Document::STATUS_FAILED]);
        Document::factory()->create(['status' => Document::STATUS_PENDING]);

        $completed = Document::withStatus(Document::STATUS_COMPLETED)->get();
        $this->assertCount(1, $completed);
        $this->assertEquals(Document::STATUS_COMPLETED, $completed->first()->status);
    }

    #[Test]
    public function it_can_scope_completed_documents(): void
    {
        Document::factory()->create(['status' => Document::STATUS_COMPLETED]);
        Document::factory()->create(['status' => Document::STATUS_FAILED]);

        $completed = Document::completed()->get();

        $this->assertCount(1, $completed);
        $this->assertEquals(Document::STATUS_COMPLETED, $completed->first()->status);
    }

    #[Test]
    public function it_can_scope_failed_documents(): void
    {
        Document::factory()->create(['status' => Document::STATUS_COMPLETED]);
        Document::factory()->create(['status' => Document::STATUS_FAILED]);

        $failed = Document::failed()->get();

        $this->assertCount(1, $failed);
        $this->assertEquals(Document::STATUS_FAILED, $failed->first()->status);
    }

    #[Test]
    public function it_can_check_if_processing(): void
    {
        $document = Document::factory()->create(['status' => Document::STATUS_PROCESSING]);

        $this->assertTrue($document->isProcessing());
        $this->assertFalse($document->isCompleted());
        $this->assertFalse($document->isFailed());
    }

    #[Test]
    public function it_can_check_if_completed(): void
    {
        $document = Document::factory()->create(['status' => Document::STATUS_COMPLETED]);

        $this->assertTrue($document->isCompleted());
        $this->assertFalse($document->isProcessing());
        $this->assertFalse($document->isFailed());
    }

    #[Test]
    public function it_can_check_if_failed(): void
    {
        $document = Document::factory()->create(['status' => Document::STATUS_FAILED]);

        $this->assertTrue($document->isFailed());
        $this->assertFalse($document->isProcessing());
        $this->assertFalse($document->isCompleted());
    }

    #[Test]
    public function it_formats_file_size_correctly(): void
    {
        $document = Document::factory()->create([
            'metadata' => ['size' => 1024],
        ]);

        $this->assertEquals('1.00 KB', $document->file_size);
    }

    #[Test]
    public function it_returns_null_for_missing_file_size(): void
    {
        $document = Document::factory()->create([
            'metadata' => [],
        ]);

        $this->assertNull($document->file_size);
    }

    #[Test]
    public function it_gets_file_type_from_metadata(): void
    {
        $document = Document::factory()->create([
            'metadata' => ['mime_type' => 'application/pdf'],
        ]);

        $this->assertEquals('application/pdf', $document->file_type);
    }

    #[Test]
    public function it_gets_file_type_from_filename_extension(): void
    {
        $document = Document::factory()->create([
            'filename' => 'document.pdf',
            'metadata' => [],
        ]);

        $this->assertEquals('pdf', $document->file_type);
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $document = Document::factory()->create();

        $document->delete();

        $this->assertSoftDeleted($document);
        $this->assertNotNull($document->fresh()->deleted_at);
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $document = new Document;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $document);
    }

    #[Test]
    public function it_stores_metadata_as_array(): void
    {
        $metadata = ['size' => 1024, 'mime_type' => 'application/pdf'];
        $document = Document::factory()->create(['metadata' => $metadata]);

        $this->assertEquals($metadata, $document->fresh()->metadata);
        $this->assertIsArray($document->fresh()->metadata);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $document = Document::factory()->create();

        $this->assertNotNull($document->created_at);
        $this->assertNotNull($document->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $document->updated_at);
    }
}
