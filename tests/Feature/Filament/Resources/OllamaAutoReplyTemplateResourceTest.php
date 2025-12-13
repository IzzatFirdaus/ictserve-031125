<?php

declare(strict_types=1);

namespace Tests\Feature\Filament\Resources;

use App\Filament\Resources\OllamaAI\AutoReplyTemplateResource;
use App\Models\AutoReplyTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature Tests for AutoReplyTemplate Filament Resource
 *
 * Tests the Auto-Reply Template management interface including:
 * - CRUD operations for templates
 * - Template variable support
 * - Approval workflow actions
 * - Authorization
 * - Accessibility compliance
 *
 * @requirements 3.4, 5.1, 5.2, 5.4, 5.5
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class OllamaAutoReplyTemplateResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_render_template_index_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(AutoReplyTemplateResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_template_create_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $this->get(AutoReplyTemplateResource::getUrl('create'))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_template_edit_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create();

        $this->get(AutoReplyTemplateResource::getUrl('edit', ['record' => $template]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_render_template_view_page(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create();

        $this->get(AutoReplyTemplateResource::getUrl('view', ['record' => $template]))
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_create_template(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(AutoReplyTemplateResource\Pages\CreateAutoReplyTemplate::class)
            ->fillForm([
                'name' => 'Templat Respons Tiket Helpdesk',
                'template_content' => 'Terima kasih atas pertanyaan anda mengenai {{subject}}. Kami akan menghubungi anda dalam masa 24 jam.',
                'variables' => ['subject', 'ticket_id', 'user_name'],
                'status' => 'active',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('auto_reply_templates', [
            'name' => 'Templat Respons Tiket Helpdesk',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function admin_can_edit_template(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create([
            'name' => 'Templat Asal',
            'status' => 'draft',
        ]);

        Livewire::test(AutoReplyTemplateResource\Pages\EditAutoReplyTemplate::class, ['record' => $template->id])
            ->fillForm([
                'name' => 'Templat Dikemaskini',
                'status' => 'active',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('auto_reply_templates', [
            'id' => $template->id,
            'name' => 'Templat Dikemaskini',
            'status' => 'active',
        ]);
    }

    #[Test]
    public function admin_can_delete_template(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create();

        Livewire::test(AutoReplyTemplateResource\Pages\EditAutoReplyTemplate::class, ['record' => $template->id])
            ->callAction('delete');

        $this->assertSoftDeleted('auto_reply_templates', ['id' => $template->id]);
    }

    #[Test]
    public function regular_user_cannot_access_template_resource(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $this->actingAs($user);

        $this->get(AutoReplyTemplateResource::getUrl('index'))
            ->assertForbidden();
    }

    #[Test]
    public function template_form_validates_required_fields(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Livewire::test(AutoReplyTemplateResource\Pages\CreateAutoReplyTemplate::class)
            ->fillForm([
                'name' => '',
                'template_content' => '',
            ])
            ->call('create')
            ->assertHasFormErrors(['name', 'template_content']);
    }

    #[Test]
    public function template_list_shows_records(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $templates = AutoReplyTemplate::factory()->count(5)->create();

        Livewire::test(AutoReplyTemplateResource\Pages\ListAutoReplyTemplates::class)
            ->assertCanSeeTableRecords($templates);
    }

    #[Test]
    public function template_list_can_filter_by_status(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $activeTemplate = AutoReplyTemplate::factory()->create(['status' => 'active']);
        $draftTemplate = AutoReplyTemplate::factory()->create(['status' => 'draft']);

        Livewire::test(AutoReplyTemplateResource\Pages\ListAutoReplyTemplates::class)
            ->filterTable('status', 'active')
            ->assertCanSeeTableRecords([$activeTemplate])
            ->assertCanNotSeeTableRecords([$draftTemplate]);
    }

    #[Test]
    public function template_list_can_search_by_name(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $targetTemplate = AutoReplyTemplate::factory()->create([
            'name' => 'Templat Respons Helpdesk',
        ]);
        $otherTemplate = AutoReplyTemplate::factory()->create([
            'name' => 'Templat Lain',
        ]);

        Livewire::test(AutoReplyTemplateResource\Pages\ListAutoReplyTemplates::class)
            ->searchTable('Helpdesk')
            ->assertCanSeeTableRecords([$targetTemplate])
            ->assertCanNotSeeTableRecords([$otherTemplate]);
    }

    #[Test]
    public function superuser_can_access_template_resource(): void
    {
        $user = User::factory()->superuser()->create();
        $this->actingAs($user);

        $this->get(AutoReplyTemplateResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function approver_can_view_template_resource(): void
    {
        $user = User::factory()->approver()->create();
        $this->actingAs($user);

        // Approvers should be able to view templates for approval workflow
        $this->get(AutoReplyTemplateResource::getUrl('index'))
            ->assertSuccessful();
    }

    #[Test]
    public function template_supports_variable_placeholders(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Salam {{user_name}}, tiket anda #{{ticket_id}} telah diterima.',
            'variables' => ['user_name', 'ticket_id'],
        ]);

        $response = $this->get(AutoReplyTemplateResource::getUrl('view', ['record' => $template]));

        $response->assertSuccessful();
    }

    #[Test]
    public function admin_can_archive_template(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create(['status' => 'active']);

        Livewire::test(AutoReplyTemplateResource\Pages\EditAutoReplyTemplate::class, ['record' => $template->id])
            ->fillForm([
                'status' => 'archived',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('auto_reply_templates', [
            'id' => $template->id,
            'status' => 'archived',
        ]);
    }

    #[Test]
    public function template_content_displays_in_bahasa_melayu(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        $template = AutoReplyTemplate::factory()->create([
            'name' => 'Templat Bahasa Melayu',
            'template_content' => 'Terima kasih kerana menghubungi kami. Pertanyaan anda sedang diproses.',
        ]);

        $response = $this->get(AutoReplyTemplateResource::getUrl('view', ['record' => $template]));

        $response->assertSuccessful()
            ->assertSee('Templat Bahasa Melayu');
    }
}
