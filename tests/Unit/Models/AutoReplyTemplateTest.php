<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\AutoReplyDraft;
use App\Models\AutoReplyTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for AutoReplyTemplate Model
 *
 * @requirements 3.1, 3.2, 3.3, 3.4
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class AutoReplyTemplateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $template = new AutoReplyTemplate;

        $expected = [
            'name',
            'template_content',
            'variables',
            'status',
            'created_by',
        ];

        $this->assertEquals($expected, $template->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $template = new AutoReplyTemplate;

        $casts = $template->getCasts();

        $this->assertEquals('array', $casts['variables']);
    }

    #[Test]
    public function it_has_correct_status_constants(): void
    {
        $this->assertEquals('draft', AutoReplyTemplate::STATUS_DRAFT);
        $this->assertEquals('active', AutoReplyTemplate::STATUS_ACTIVE);
        $this->assertEquals('archived', AutoReplyTemplate::STATUS_ARCHIVED);
    }

    #[Test]
    public function it_belongs_to_a_creator(): void
    {
        $user = User::factory()->create();
        $template = AutoReplyTemplate::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $template->creator);
        $this->assertEquals($user->id, $template->creator->id);
    }

    #[Test]
    public function it_has_many_drafts(): void
    {
        $template = AutoReplyTemplate::factory()->create();
        $drafts = AutoReplyDraft::factory()->count(3)->create(['template_id' => $template->id]);

        $this->assertCount(3, $template->drafts);
        $this->assertInstanceOf(AutoReplyDraft::class, $template->drafts->first());
    }

    #[Test]
    public function it_can_scope_active_templates(): void
    {
        AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_ACTIVE]);
        AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_DRAFT]);

        $active = AutoReplyTemplate::active()->get();

        $this->assertCount(1, $active);
        $this->assertEquals(AutoReplyTemplate::STATUS_ACTIVE, $active->first()->status);
    }

    #[Test]
    public function it_can_scope_draft_templates(): void
    {
        AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_ACTIVE]);
        AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_DRAFT]);

        $drafts = AutoReplyTemplate::draft()->get();

        $this->assertCount(1, $drafts);
        $this->assertEquals(AutoReplyTemplate::STATUS_DRAFT, $drafts->first()->status);
    }

    #[Test]
    public function it_can_check_if_active(): void
    {
        $template = AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_ACTIVE]);

        $this->assertTrue($template->isActive());
        $this->assertFalse($template->isDraft());
        $this->assertFalse($template->isArchived());
    }

    #[Test]
    public function it_can_check_if_draft(): void
    {
        $template = AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_DRAFT]);

        $this->assertTrue($template->isDraft());
        $this->assertFalse($template->isActive());
        $this->assertFalse($template->isArchived());
    }

    #[Test]
    public function it_can_check_if_archived(): void
    {
        $template = AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_ARCHIVED]);

        $this->assertTrue($template->isArchived());
        $this->assertFalse($template->isActive());
        $this->assertFalse($template->isDraft());
    }

    #[Test]
    public function it_processes_template_with_variables(): void
    {
        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Hello {{name}}, your ticket {{ticket_id}} has been processed.',
        ]);

        $variables = ['name' => 'Ahmad', 'ticket_id' => '12345'];
        $processed = $template->processTemplate($variables);

        $this->assertEquals('Hello Ahmad, your ticket 12345 has been processed.', $processed);
    }

    #[Test]
    public function it_processes_template_without_variables(): void
    {
        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Thank you for your submission.',
        ]);

        $processed = $template->processTemplate();

        $this->assertEquals('Thank you for your submission.', $processed);
    }

    #[Test]
    public function it_gets_required_variables_from_template(): void
    {
        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Hello {{name}}, your {{type}} {{id}} is {{status}}.',
        ]);

        $required = $template->getRequiredVariables();

        $this->assertCount(4, $required);
        $this->assertContains('name', $required);
        $this->assertContains('type', $required);
        $this->assertContains('id', $required);
        $this->assertContains('status', $required);
    }

    #[Test]
    public function it_validates_required_variables(): void
    {
        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Hello {{name}}, your ticket {{id}} is ready.',
        ]);

        $validVariables = ['name' => 'Ahmad', 'id' => '123'];
        $invalidVariables = ['name' => 'Ahmad']; // Missing 'id'

        $this->assertTrue($template->validateVariables($validVariables));
        $this->assertFalse($template->validateVariables($invalidVariables));
    }

    #[Test]
    public function it_can_be_activated(): void
    {
        $template = AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_DRAFT]);

        $result = $template->activate();

        $this->assertTrue($result);
        $this->assertEquals(AutoReplyTemplate::STATUS_ACTIVE, $template->fresh()->status);
    }

    #[Test]
    public function it_can_be_archived(): void
    {
        $template = AutoReplyTemplate::factory()->create(['status' => AutoReplyTemplate::STATUS_ACTIVE]);

        $result = $template->archive();

        $this->assertTrue($result);
        $this->assertEquals(AutoReplyTemplate::STATUS_ARCHIVED, $template->fresh()->status);
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $template = AutoReplyTemplate::factory()->create();

        $template->delete();

        $this->assertSoftDeleted($template);
        $this->assertNotNull($template->fresh()->deleted_at);
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $template = new AutoReplyTemplate;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $template);
    }

    #[Test]
    public function it_stores_variables_as_array(): void
    {
        $variables = ['name', 'email', 'ticket_id'];
        $template = AutoReplyTemplate::factory()->create(['variables' => $variables]);

        $this->assertEquals($variables, $template->fresh()->variables);
        $this->assertIsArray($template->fresh()->variables);
    }

    #[Test]
    public function it_handles_duplicate_variables_in_template(): void
    {
        $template = AutoReplyTemplate::factory()->create([
            'template_content' => 'Hello {{name}}, {{name}} your ticket is ready.',
        ]);

        $required = $template->getRequiredVariables();

        $this->assertCount(1, $required);
        $this->assertContains('name', $required);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $template = AutoReplyTemplate::factory()->create();

        $this->assertNotNull($template->created_at);
        $this->assertNotNull($template->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $template->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $template->updated_at);
    }
}
