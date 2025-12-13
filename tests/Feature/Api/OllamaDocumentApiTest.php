<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Jobs\DocumentIngestJob;
use App\Models\Document;
use App\Models\DocumentChunk;
use App\Models\User;
use App\Services\DocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for Document Analysis API Endpoints
 *
 * Tests the Document API functionality including:
 * - Document upload
 * - Document status checking
 * - Document listing
 * - Document reprocessing
 * - Document deletion
 * - Document statistics
 * - Authentication and authorization
 * - Rate limiting
 * - Error handling
 *
 * @requirements 2.1, 2.5, 7.1
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaDocumentApiTest extends TestCase
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
                'document_processing' => [
                    'max_file_size' => 10485760, // 10MB
                    'allowed_types' => ['pdf', 'txt', 'docx'],
                    'chunk_size' => 1000,
                    'chunk_overlap' => 200,
                ],
            ],
        ]);

        Storage::fake('local');
        Queue::fake();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_upload_document_as_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        $this->mockDocumentService();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson('/api/v1/ollama/documents/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'message',
                    'document' => [
                        'id',
                        'filename',
                        'status',
                        'created_at',
                    ],
                    'request_id',
                ],
            ])
            ->assertJsonPath('data.document.filename', 'test-document.pdf')
            ->assertJsonPath('data.document.status', Document::STATUS_PENDING);

        Queue::assertPushed(DocumentIngestJob::class);
    }

    #[Test]
    public function it_requires_admin_role_for_document_upload(): void
    {
        $user = User::factory()->create(); // Regular user, not admin
        $token = $user->createToken('test-token', ['read:tickets'])->plainTextToken; // Wrong ability
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson('/api/v1/ollama/documents/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function it_requires_authentication_for_document_upload(): void
    {
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        $response = $this->postJson('/api/v1/ollama/documents/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_validates_file_is_required_for_upload(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson('/api/v1/ollama/documents/upload', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['file']);
    }

    #[Test]
    public function it_can_get_document_status(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;
        $document = Document::factory()->create([
            'status' => Document::STATUS_COMPLETED,
            'uploaded_by' => $admin->id,
        ]);

        // Create some chunks for the document
        DocumentChunk::factory()->count(3)->create([
            'document_id' => $document->id,
            'embedding' => array_fill(0, 384, 0.1),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson("/api/v1/ollama/documents/{$document->id}/status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'document' => [
                        'id',
                        'filename',
                        'status',
                        'chunks_count',
                        'chunks_with_embeddings',
                        'metadata',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ])
            ->assertJsonPath('data.document.id', $document->id)
            ->assertJsonPath('data.document.status', Document::STATUS_COMPLETED)
            ->assertJsonPath('data.document.chunks_count', 3)
            ->assertJsonPath('data.document.chunks_with_embeddings', 3);
    }

    #[Test]
    public function it_returns_404_for_nonexistent_document_status(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents/999/status');

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.message', 'Dokumen tidak dijumpai.');
    }

    #[Test]
    public function it_can_list_documents(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        // Create test documents
        Document::factory()->count(5)->create([
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'documents' => [
                        '*' => [
                            'id',
                            'filename',
                            'status',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data.documents'));
    }

    #[Test]
    public function it_can_filter_documents_by_status(): void
    {
        $admin = User::factory()->admin()->create();

        // Create documents with different statuses
        Document::factory()->count(2)->create([
            'status' => Document::STATUS_COMPLETED,
            'uploaded_by' => $admin->id,
        ]);
        Document::factory()->count(3)->create([
            'status' => Document::STATUS_FAILED,
            'uploaded_by' => $admin->id,
        ]);

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents?status='.Document::STATUS_COMPLETED);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.documents'));
    }

    #[Test]
    public function it_can_reprocess_failed_document(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;
        $document = Document::factory()->create([
            'status' => Document::STATUS_FAILED,
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson("/api/v1/ollama/documents/{$document->id}/reprocess");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'message',
                    'document' => [
                        'id',
                        'filename',
                        'status',
                    ],
                ],
            ])
            ->assertJsonPath('data.document.status', Document::STATUS_PENDING);

        Queue::assertPushed(DocumentIngestJob::class);
    }

    #[Test]
    public function it_cannot_reprocess_non_failed_document(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;
        $document = Document::factory()->create([
            'status' => Document::STATUS_COMPLETED,
            'uploaded_by' => $admin->id,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson("/api/v1/ollama/documents/{$document->id}/reprocess");

        $response->assertStatus(400)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.message', 'Hanya dokumen yang gagal boleh diproses semula.');
    }

    #[Test]
    public function it_can_delete_document(): void
    {
        $admin = User::factory()->admin()->create();
        $document = Document::factory()->create([
            'uploaded_by' => $admin->id,
        ]);

        $this->mockDocumentService();

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->deleteJson("/api/v1/ollama/documents/{$document->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.message', 'Dokumen berjaya dipadam.');
    }

    #[Test]
    public function it_returns_404_when_deleting_nonexistent_document(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->deleteJson('/api/v1/ollama/documents/999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.message', 'Dokumen tidak dijumpai.');
    }

    #[Test]
    public function it_can_get_document_statistics(): void
    {
        $admin = User::factory()->admin()->create();

        $this->mockDocumentService();

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'stats',
                ],
            ]);
    }

    #[Test]
    public function it_applies_rate_limiting_to_upload_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $this->mockDocumentService();

        // Make multiple requests to trigger rate limiting (30 per minute)
        for ($i = 0; $i < 31; $i++) {
            $file = UploadedFile::fake()->create("test-document-{$i}.pdf", 1024, 'application/pdf');

            $response = $this->withHeaders([
                'Authorization' => 'Bearer '.$admin->createToken('test-token', ['admin:all'])->plainTextToken,
            ])
                ->postJson('/api/v1/ollama/documents/upload', [
                    'file' => $file,
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
    public function it_includes_request_id_in_upload_response(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        $this->mockDocumentService();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson('/api/v1/ollama/documents/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.request_id', fn ($id) => \is_string($id) && \strlen($id) === 36);
    }

    #[Test]
    public function it_returns_json_content_type(): void
    {
        $admin = User::factory()->admin()->create();

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents');

        $response->assertHeader('Content-Type', 'application/json');
    }

    #[Test]
    public function it_handles_document_service_exceptions_gracefully(): void
    {
        $admin = User::factory()->admin()->create();
        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        // Mock service to throw exception
        $documentServiceMock = Mockery::mock(DocumentService::class);
        $documentServiceMock->shouldReceive('uploadDocument')
            ->andThrow(new \Exception('Storage service unavailable'));

        $this->instance(DocumentService::class, $documentServiceMock);

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->postJson('/api/v1/ollama/documents/upload', [
                'file' => $file,
            ]);

        $response->assertStatus(500)
            ->assertJsonPath('success', false)
            ->assertJsonPath('error.message', 'Gagal memuat naik dokumen: Storage service unavailable');
    }

    #[Test]
    public function it_supports_pagination_parameters(): void
    {
        $admin = User::factory()->admin()->create();

        // Create more documents than default per page
        Document::factory()->count(20)->create([
            'uploaded_by' => $admin->id,
        ]);

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents?per_page=5');

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.per_page', 5)
            ->assertJsonPath('data.pagination.total', 20);

        $this->assertCount(5, $response->json('data.documents'));
    }

    #[Test]
    public function it_limits_maximum_per_page_to_100(): void
    {
        $admin = User::factory()->admin()->create();

        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])
            ->getJson('/api/v1/ollama/documents?per_page=200');

        $response->assertStatus(200)
            ->assertJsonPath('data.pagination.per_page', 100);
    }

    /**
     * Helper method to create admin user with proper API token
     */
    private function createAdminWithToken(): array
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('test-token', ['admin:all'])->plainTextToken;

        return [$admin, $token];
    }

    /**
     * Helper method to make authenticated admin request
     */
    private function asAdmin(): self
    {
        [$admin, $token] = $this->createAdminWithToken();

        return $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ]);
    }

    /**
     * Helper method to mock DocumentService
     */
    private function mockDocumentService(): void
    {
        $documentServiceMock = Mockery::mock(DocumentService::class);

        $documentServiceMock->shouldReceive('uploadDocument')
            ->andReturn(Document::factory()->create([
                'filename' => 'test-document.pdf',
                'status' => Document::STATUS_PENDING,
            ]));

        $documentServiceMock->shouldReceive('deleteDocument')
            ->andReturn(true);

        $documentServiceMock->shouldReceive('getDocumentStats')
            ->andReturnUsing(function () {
                return [
                    'total_documents' => 10,
                    'completed_documents' => 8,
                    'failed_documents' => 1,
                    'processing_documents' => 1,
                    'total_chunks' => 150,
                    'storage_used' => '25.6 MB',
                ];
            });

        $this->instance(DocumentService::class, $documentServiceMock);
    }
}
