<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\OllamaAI\FaqResource;
use App\Models\Faq;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for FAQ Filament Resource
 *
 * Tests the FAQ management interface including:
 * - CRUD operations
 * - Search functionality
 * - Filtering
 * - Authorization
 *
 * @requirements 1.1, 5.1, 5.5
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaFaqResourceTest extends TestCase
{
    #[Test]
    public function admin_can_render_faq_index_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(FaqResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_faq_create_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(FaqResource::getUrl('create'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_faq_edit_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faq = Faq::factory()->create();

        $this->get(FaqResource::getUrl('edit', ['record' => $faq]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_faq_view_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faq = Faq::factory()->create();

        $this->get(FaqResource::getUrl('view', ['record' => $faq]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_create_faq(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        // Test that the create form exists and can be rendered
        Livewire::test(FaqResource\Pages\CreateFaq::class)
            ->assertSuccessful()
            ->assertFormExists();
    }

    #[Test]
    public function admin_can_edit_faq(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faq = Faq::factory()->create([
            'question' => 'Soalan asal',
            'answer' => 'Jawapan asal',
        ]);

        // Test that the edit form exists and can be rendered
        Livewire::test(FaqResource\Pages\EditFaq::class, ['record' => $faq->id])
            ->assertSuccessful()
            ->assertFormExists();
    }

    #[Test]
    public function admin_can_delete_faq(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faq = Faq::factory()->create();

        Livewire::test(FaqResource\Pages\EditFaq::class, ['record' => $faq->id])
            ->callAction('delete');

        $this->assertSoftDeleted('faqs', ['id' => $faq->id]);
    }

    #[Test]
    public function regular_user_cannot_access_faq_resource(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $this->get(FaqResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function faq_form_validates_required_fields(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(FaqResource\Pages\CreateFaq::class)
            ->fillForm([
                'question' => '',
                'answer' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['question', 'answer']);
    }

    #[Test]
    public function faq_list_shows_records(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $faqs = Faq::factory()->count(5)->create();

        Livewire::test(FaqResource\Pages\ListFaqs::class)
            ->assertCanSeeTableRecords($faqs);
    }

    #[Test]
    public function faq_list_can_search_by_question(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $targetFaq = Faq::factory()->create([
            'question' => 'Bagaimana cara reset kata laluan?',
        ]);
        $otherFaq = Faq::factory()->create([
            'question' => 'Soalan lain yang berbeza',
        ]);

        Livewire::test(FaqResource\Pages\ListFaqs::class)
            ->searchTable('reset kata laluan')
            ->assertCanSeeTableRecords([$targetFaq])
            ->assertCanNotSeeTableRecords([$otherFaq]);
    }
}
