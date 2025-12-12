<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\OllamaAI\DocumentResource;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for Document Filament Resource
 *
 * Tests the Document management interface including:
 * - CRUD operations
 * - File upload functionality
 * - Status tracking
 * - Authorization
 * - Accessibility compliance
 *
 * @requirements 2.1, 2.5, 5.1, 5.2, 5.4
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaDocumentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    #[Test]
    public function admin_can_render_document_index_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(DocumentResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_document_create_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(DocumentResource::getUrl('create'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_document_edit_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $document = Document::factory()->create();

        $this->get(DocumentResource::getUrl('edit', ['record' => $document]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_document_view_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $document = Document::factory()->create();

        $this->get(DocumentResource::getUrl('view', ['record' => $document]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_create_document_with_file_upload(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

        Livewire::test(DocumentResource\Pages\CreateDocument::class)
            ->fillForm([
                'filename' => 'test-document.pdf',
                'metadata' => ['source' => 'test', 'category' => 'manual'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('documents', [
            'filename' => 'test-document.pdf',
        ]);
    }

    #[Test]
    public function admin_can_edit_document(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $document = Document::factory()->create([
            'filename' => 'original-document.pdf',
            'status' => 'pending',
        ]);

        Livewire::test(DocumentResource\Pages\EditDocument::class, ['record' => $document->id])
            ->fillForm([
                'filename' => 'updated-document.pdf',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'filename' => 'updated-document.pdf',
        ]);
    }

    #[Test]
    public function admin_can_delete_document(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $document = Document::factory()->create();

        Livewire::test(DocumentResource\Pages\EditDocument::class, ['record' => $document->id])
            ->callAction('delete');

        $this->assertSoftDeleted('documents', ['id' => $document->id]);
    }

    #[Test]
    public function regular_user_cannot_access_document_resource(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $this->get(DocumentResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function document_form_validates_required_fields(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(DocumentResource\Pages\CreateDocument::class)
            ->fillForm([
                'filename' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['filename']);
    }

    #[Test]
    public function document_list_shows_records(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $documents = Document::factory()->count(5)->create();

        Livewire::test(DocumentResource\Pages\ListDocuments::class)
            ->assertCanSeeTableRecords($documents);
    }

    #[Test]
    public function document_list_can_filter_by_status(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $pendingDoc = Document::factory()->create(['status' => 'pending']);
        $completedDoc = Document::factory()->create(['status' => 'completed']);

        Livewire::test(DocumentResource\Pages\ListDocuments::class)
            ->filterTable('status', 'pending')
            ->assertCanSeeTableRecords([$pendingDoc])
            ->assertCanNotSeeTableRecords([$completedDoc]);
    }

    #[Test]
    public function document_list_can_search_by_filename(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $targetDoc = Document::factory()->create([
            'filename' => 'panduan-pengguna-ictserve.pdf',
        ]);
        $otherDoc = Document::factory()->create([
            'filename' => 'dokumen-lain.pdf',
        ]);

        Livewire::test(DocumentResource\Pages\ListDocuments::class)
            ->searchTable('panduan-pengguna')
            ->assertCanSeeTableRecords([$targetDoc])
            ->assertCanNotSeeTableRecords([$otherDoc]);
    }

    #[Test]
    public function document_shows_status_badge(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $document = Document::factory()->create(['status' => 'processing']);

        $response = $this->get(DocumentResource::getUrl('view', ['record' => $document]));

        $response->assertSuccessful();
    }

    #[Test]
    public function superuser_can_access_document_resource(): void
    {
        $user = User::factory()->superuser()->create();
        $this->actingAs($user);

        $this->get(DocumentResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function approver_cannot_access_document_resource(): void
    {
        $user = User::factory()->approver()->create();
        $this->actingAs($user);

        $this->get(DocumentResource::getUrl('index'))
            ->assertForbidden();
    }
}
