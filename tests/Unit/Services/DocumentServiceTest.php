<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Document;
use App\Models\DocumentChunk;
use App\Services\DocumentService;
use App\Services\EmbeddingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for DocumentService
 *
 * Tests document processing functionality including:
 * - File upload and validation
 * - Text extraction (PDF, DOCX, TXT)
 * - Document chunking
 * - PII detection and sanitization
 * - Embedding generation
 *
 * @requirements 2.1, 2.3, 6.2
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class DocumentServiceTest extends TestCase
{
    use RefreshDatabase;

    private DocumentService $documentService;

    private MockInterface $embeddingServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        config([
            'ollama.document' => [
                'max_file_size' => 10485760,
                'allowed_types' => ['pdf', 'docx', 'txt'],
                'chunk_size' => 750,
                'chunk_overlap' => 100,
                'storage_disk' => 'local',
                'storage_path' => 'documents',
            ],
        ]);

        $this->embeddingServiceMock = Mockery::mock(EmbeddingService::class);
        $this->documentService = new DocumentService($this->embeddingServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }


    #[Test]
    public function it_can_upload_txt_document(): void
    {
        $user = \App\Models\User::factory()->create();
        $file = UploadedFile::fake()->create('test.txt', 100, 'text/plain');

        $document = $this->documentService->uploadDocument($file, $user->id);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertEquals('test.txt', $document->filename);
        $this->assertEquals(Document::STATUS_PENDING, $document->status);
        $this->assertEquals($user->id, $document->uploaded_by);
        $this->assertArrayHasKey('original_name', $document->metadata);
        $this->assertArrayHasKey('stored_name', $document->metadata);
    }

    #[Test]
    public function it_can_upload_document_as_guest(): void
    {
        $file = UploadedFile::fake()->create('guest-doc.txt', 50, 'text/plain');

        $document = $this->documentService->uploadDocument($file, null);

        $this->assertInstanceOf(Document::class, $document);
        $this->assertNull($document->uploaded_by);
        $this->assertEquals(Document::STATUS_PENDING, $document->status);
    }

    #[Test]
    public function it_validates_file_size(): void
    {
        config(['ollama.document.max_file_size' => 1024]);
        $this->documentService = new DocumentService($this->embeddingServiceMock);

        $file = UploadedFile::fake()->create('large.txt', 2000, 'text/plain');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Saiz fail melebihi had maksimum');

        $this->documentService->uploadDocument($file);
    }

    #[Test]
    #[DataProvider('unsupportedFileTypesProvider')]
    public function it_validates_file_type(string $filename, string $mimeType): void
    {
        $file = UploadedFile::fake()->create($filename, 100, $mimeType);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Jenis fail tidak disokong');

        $this->documentService->uploadDocument($file);
    }

    public static function unsupportedFileTypesProvider(): array
    {
        return [
            'exe file' => ['malware.exe', 'application/x-msdownload'],
            'php file' => ['script.php', 'text/x-php'],
            'js file' => ['script.js', 'application/javascript'],
            'html file' => ['page.html', 'text/html'],
        ];
    }

    #[Test]
    public function it_stores_file_in_storage(): void
    {
        $file = UploadedFile::fake()->create('stored.txt', 100, 'text/plain');

        $document = $this->documentService->uploadDocument($file, null);

        $storedName = $document->metadata['stored_name'];
        Storage::disk('local')->assertExists('documents/' . $storedName);
    }

    #[Test]
    public function it_can_process_txt_document(): void
    {
        $content = 'Ini adalah kandungan ujian untuk dokumen TXT.';
        $file = UploadedFile::fake()->createWithContent('test.txt', $content);

        $document = $this->documentService->uploadDocument($file);

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturn(array_fill(0, 384, 0.1));

        $result = $this->documentService->processDocument($document);

        $this->assertTrue($result);
        $document->refresh();
        $this->assertEquals(Document::STATUS_COMPLETED, $document->status);
        $this->assertGreaterThan(0, $document->chunks()->count());
    }

    #[Test]
    public function it_creates_chunks_with_overlap(): void
    {
        $longContent = str_repeat('Ini adalah teks ujian yang panjang. ', 100);
        $file = UploadedFile::fake()->createWithContent('long.txt', $longContent);

        $document = $this->documentService->uploadDocument($file);

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturn(array_fill(0, 384, 0.1));

        $this->documentService->processDocument($document);

        $chunks = $document->chunks()->orderBy('chunk_index')->get();
        $this->assertGreaterThan(1, $chunks->count());

        foreach ($chunks as $chunk) {
            $this->assertNotEmpty($chunk->chunk_text);
            $this->assertIsInt($chunk->chunk_index);
        }
    }

    #[Test]
    public function it_detects_and_sanitizes_pii(): void
    {
        $contentWithPii = 'Nombor IC: 880101-01-1234, Telefon: +60123456789, Email: test@example.com';
        $file = UploadedFile::fake()->createWithContent('pii.txt', $contentWithPii);

        $document = $this->documentService->uploadDocument($file);

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturn(array_fill(0, 384, 0.1));

        $this->documentService->processDocument($document);

        $chunk = $document->chunks()->first();
        $this->assertStringContainsString('[REDACTED_IC]', $chunk->chunk_text);
        $this->assertStringContainsString('[REDACTED_PHONE]', $chunk->chunk_text);
        $this->assertStringContainsString('[REDACTED_EMAIL]', $chunk->chunk_text);
        $this->assertStringNotContainsString('880101-01-1234', $chunk->chunk_text);
    }

    #[Test]
    public function it_handles_processing_failure(): void
    {
        $file = UploadedFile::fake()->createWithContent('fail.txt', '');

        $document = $this->documentService->uploadDocument($file);

        $result = $this->documentService->processDocument($document);

        $this->assertFalse($result);
        $document->refresh();
        $this->assertEquals(Document::STATUS_FAILED, $document->status);
        $this->assertArrayHasKey('error', $document->metadata);
    }

    #[Test]
    public function it_can_reprocess_failed_document(): void
    {
        $file = UploadedFile::fake()->createWithContent('reprocess.txt', 'Kandungan untuk diproses semula.');

        $document = $this->documentService->uploadDocument($file);
        $document->update(['status' => Document::STATUS_FAILED]);

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturn(array_fill(0, 384, 0.1));

        $result = $this->documentService->reprocessDocument($document);

        $this->assertTrue($result);
        $document->refresh();
        $this->assertEquals(Document::STATUS_COMPLETED, $document->status);
    }

    #[Test]
    public function it_throws_exception_when_reprocessing_non_failed_document(): void
    {
        $file = UploadedFile::fake()->createWithContent('pending.txt', 'Content');
        $document = $this->documentService->uploadDocument($file);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Hanya dokumen yang gagal boleh diproses semula');

        $this->documentService->reprocessDocument($document);
    }

    #[Test]
    public function it_can_delete_document(): void
    {
        $file = UploadedFile::fake()->createWithContent('delete.txt', 'To be deleted');
        $document = $this->documentService->uploadDocument($file);
        $storedName = $document->metadata['stored_name'];

        $result = $this->documentService->deleteDocument($document);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing('documents/' . $storedName);
    }

    #[Test]
    public function it_deletes_chunks_when_deleting_document(): void
    {
        $file = UploadedFile::fake()->createWithContent('with-chunks.txt', 'Content with chunks');
        $document = $this->documentService->uploadDocument($file);

        $this->embeddingServiceMock
            ->shouldReceive('generateEmbedding')
            ->andReturn(array_fill(0, 384, 0.1));

        $this->documentService->processDocument($document);
        $chunkCount = $document->chunks()->count();
        $this->assertGreaterThan(0, $chunkCount);

        $this->documentService->deleteDocument($document);

        $this->assertDatabaseCount('document_chunks', 0);
    }

    #[Test]
    public function it_can_get_document_stats(): void
    {
        Document::factory()->count(3)->create(['status' => Document::STATUS_COMPLETED]);
        Document::factory()->count(2)->create(['status' => Document::STATUS_PENDING]);
        Document::factory()->create(['status' => Document::STATUS_FAILED]);

        $stats = $this->documentService->getDocumentStats();

        $this->assertArrayHasKey('total_documents', $stats);
        $this->assertArrayHasKey('pending_documents', $stats);
        $this->assertArrayHasKey('completed_documents', $stats);
        $this->assertArrayHasKey('failed_documents', $stats);
        $this->assertEquals(6, $stats['total_documents']);
        $this->assertEquals(3, $stats['completed_documents']);
        $this->assertEquals(2, $stats['pending_documents']);
        $this->assertEquals(1, $stats['failed_documents']);
    }

    #[Test]
    public function it_records_metadata_on_upload(): void
    {
        $user = \App\Models\User::factory()->create();
        $file = UploadedFile::fake()->create('metadata.txt', 500, 'text/plain');

        $document = $this->documentService->uploadDocument($file, $user->id);

        $this->assertArrayHasKey('original_name', $document->metadata);
        $this->assertArrayHasKey('stored_name', $document->metadata);
        $this->assertArrayHasKey('size', $document->metadata);
        $this->assertArrayHasKey('mime_type', $document->metadata);
        $this->assertArrayHasKey('extension', $document->metadata);
        $this->assertArrayHasKey('uploaded_at', $document->metadata);
        $this->assertEquals('txt', $document->metadata['extension']);
    }
}
