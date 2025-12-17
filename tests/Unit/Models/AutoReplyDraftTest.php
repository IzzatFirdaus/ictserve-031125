<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\AutoReplyDraft;
use App\Models\AutoReplyTemplate;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit Tests for AutoReplyDraft Model
 *
 * @requirements 3.1, 3.2, 3.3, 3.4
 *
 * @compliance D10 v3.6.0 Source Code Documentation
 */
class AutoReplyDraftTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_fillable_attributes(): void
    {
        $draft = new AutoReplyDraft;

        $expected = [
            'replyable_type',
            'replyable_id',
            'draft_content',
            'model_used',
            'generation_cost',
            'template_id',
            'status',
            'generated_by',
            'approved_by',
            'approved_at',
            'rejection_reason',
        ];

        $this->assertEquals($expected, $draft->getFillable());
    }

    #[Test]
    public function it_casts_attributes_correctly(): void
    {
        $draft = new AutoReplyDraft;

        $casts = $draft->getCasts();

        $this->assertEquals('datetime', $casts['approved_at']);
    }

    #[Test]
    public function it_has_correct_status_constants(): void
    {
        $this->assertEquals('draft', AutoReplyDraft::STATUS_DRAFT);
        $this->assertEquals('pending_review', AutoReplyDraft::STATUS_PENDING_REVIEW);
        $this->assertEquals('approved', AutoReplyDraft::STATUS_APPROVED);
        $this->assertEquals('rejected', AutoReplyDraft::STATUS_REJECTED);
        $this->assertEquals('sent', AutoReplyDraft::STATUS_SENT);
    }

    #[Test]
    public function it_has_polymorphic_relationship_with_replyable(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $draft = AutoReplyDraft::factory()->create([
            'replyable_type' => HelpdeskTicket::class,
            'replyable_id' => $ticket->id,
        ]);

        $this->assertInstanceOf(HelpdeskTicket::class, $draft->replyable);
        $this->assertEquals($ticket->id, $draft->replyable->id);
    }

    #[Test]
    public function it_belongs_to_a_template(): void
    {
        $template = AutoReplyTemplate::factory()->create();
        $draft = AutoReplyDraft::factory()->create(['template_id' => $template->id]);

        $this->assertInstanceOf(AutoReplyTemplate::class, $draft->template);
        $this->assertEquals($template->id, $draft->template->id);
    }

    #[Test]
    public function it_can_have_null_template(): void
    {
        $draft = AutoReplyDraft::factory()->create(['template_id' => null]);

        $this->assertNull($draft->template_id);
        $this->assertNull($draft->template->id ?? null);
    }

    #[Test]
    public function it_belongs_to_a_generator(): void
    {
        $user = User::factory()->create();
        $draft = AutoReplyDraft::factory()->create(['generated_by' => $user->id]);

        $this->assertInstanceOf(User::class, $draft->generator);
        $this->assertEquals($user->id, $draft->generator->id);
    }

    #[Test]
    public function it_belongs_to_an_approver(): void
    {
        $user = User::factory()->create();
        $draft = AutoReplyDraft::factory()->create(['approved_by' => $user->id]);

        $this->assertInstanceOf(User::class, $draft->approver);
        $this->assertEquals($user->id, $draft->approver->id);
    }

    #[Test]
    public function it_can_have_null_approver(): void
    {
        $draft = AutoReplyDraft::factory()->create(['approved_by' => null]);

        $this->assertNull($draft->approved_by);
        $this->assertNull($draft->approver->id ?? null);
    }

    #[Test]
    public function it_can_filter_by_status(): void
    {
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_REJECTED]);

        $approved = AutoReplyDraft::withStatus(AutoReplyDraft::STATUS_APPROVED)->get();

        $this->assertCount(1, $approved);
        $this->assertEquals(AutoReplyDraft::STATUS_APPROVED, $approved->first()->status);
    }

    #[Test]
    public function it_can_scope_pending_review_drafts(): void
    {
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW]);
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);

        $pending = AutoReplyDraft::pendingReview()->get();

        $this->assertCount(1, $pending);
        $this->assertEquals(AutoReplyDraft::STATUS_PENDING_REVIEW, $pending->first()->status);
    }

    #[Test]
    public function it_can_scope_approved_drafts(): void
    {
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_REJECTED]);

        $approved = AutoReplyDraft::approved()->get();

        $this->assertCount(1, $approved);
        $this->assertEquals(AutoReplyDraft::STATUS_APPROVED, $approved->first()->status);
    }

    #[Test]
    public function it_can_scope_rejected_drafts(): void
    {
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);
        AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_REJECTED]);

        $rejected = AutoReplyDraft::rejected()->get();

        $this->assertCount(1, $rejected);
        $this->assertEquals(AutoReplyDraft::STATUS_REJECTED, $rejected->first()->status);
    }

    #[Test]
    public function it_can_check_if_pending_review(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW]);

        $this->assertTrue($draft->isPendingReview());
        $this->assertFalse($draft->isApproved());
        $this->assertFalse($draft->isRejected());
        $this->assertFalse($draft->isSent());
    }

    #[Test]
    public function it_can_check_if_approved(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);

        $this->assertTrue($draft->isApproved());
        $this->assertFalse($draft->isPendingReview());
        $this->assertFalse($draft->isRejected());
        $this->assertFalse($draft->isSent());
    }

    #[Test]
    public function it_can_check_if_rejected(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_REJECTED]);

        $this->assertTrue($draft->isRejected());
        $this->assertFalse($draft->isPendingReview());
        $this->assertFalse($draft->isApproved());
        $this->assertFalse($draft->isSent());
    }

    #[Test]
    public function it_can_check_if_sent(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_SENT]);

        $this->assertTrue($draft->isSent());
        $this->assertFalse($draft->isPendingReview());
        $this->assertFalse($draft->isApproved());
        $this->assertFalse($draft->isRejected());
    }

    #[Test]
    public function it_can_submit_for_review(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_DRAFT]);

        $result = $draft->submitForReview();

        $this->assertTrue($result);
        $this->assertEquals(AutoReplyDraft::STATUS_PENDING_REVIEW, $draft->fresh()->status);
    }

    #[Test]
    public function it_can_be_approved(): void
    {
        $approver = User::factory()->create();
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW]);

        $result = $draft->approve($approver);

        $this->assertTrue($result);
        $fresh = $draft->fresh();
        $this->assertEquals(AutoReplyDraft::STATUS_APPROVED, $fresh->status);
        $this->assertEquals($approver->id, $fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
        $this->assertNull($fresh->rejection_reason);
    }

    #[Test]
    public function it_can_be_rejected(): void
    {
        $approver = User::factory()->create();
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW]);
        $reason = 'Content not appropriate';

        $result = $draft->reject($approver, $reason);

        $this->assertTrue($result);
        $fresh = $draft->fresh();
        $this->assertEquals(AutoReplyDraft::STATUS_REJECTED, $fresh->status);
        $this->assertEquals($approver->id, $fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
        $this->assertEquals($reason, $fresh->rejection_reason);
    }

    #[Test]
    public function it_can_be_marked_as_sent(): void
    {
        $draft = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);

        $result = $draft->markAsSent();

        $this->assertTrue($result);
        $this->assertEquals(AutoReplyDraft::STATUS_SENT, $draft->fresh()->status);
    }

    #[Test]
    public function it_generates_preview_text(): void
    {
        $longContent = str_repeat('A', 250);
        $draft = AutoReplyDraft::factory()->create(['draft_content' => $longContent]);

        $preview = $draft->preview;

        $this->assertEquals(203, strlen($preview)); // 200 chars + '...'
        $this->assertStringEndsWith('...', $preview);
    }

    #[Test]
    public function it_returns_full_content_for_short_draft(): void
    {
        $shortContent = 'Short draft content';
        $draft = AutoReplyDraft::factory()->create(['draft_content' => $shortContent]);

        $preview = $draft->preview;

        $this->assertEquals($shortContent, $preview);
        $this->assertStringEndsNotWith('...', $preview);
    }

    #[Test]
    public function it_gets_status_color_for_ui(): void
    {
        $draftStatus = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_DRAFT]);
        $pendingStatus = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_PENDING_REVIEW]);
        $approvedStatus = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_APPROVED]);
        $rejectedStatus = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_REJECTED]);
        $sentStatus = AutoReplyDraft::factory()->create(['status' => AutoReplyDraft::STATUS_SENT]);

        $this->assertEquals('gray', $draftStatus->status_color);
        $this->assertEquals('yellow', $pendingStatus->status_color);
        $this->assertEquals('green', $approvedStatus->status_color);
        $this->assertEquals('red', $rejectedStatus->status_color);
        $this->assertEquals('blue', $sentStatus->status_color);
    }

    #[Test]
    public function it_uses_soft_deletes(): void
    {
        $draft = AutoReplyDraft::factory()->create();

        $draft->delete();

        $this->assertSoftDeleted($draft);
        $this->assertNotNull($draft->fresh()->deleted_at);
    }

    #[Test]
    public function it_implements_auditable_contract(): void
    {
        $draft = new AutoReplyDraft;

        $this->assertInstanceOf(\OwenIt\Auditing\Contracts\Auditable::class, $draft);
    }

    #[Test]
    public function it_has_timestamps(): void
    {
        $draft = AutoReplyDraft::factory()->create();

        $this->assertNotNull($draft->created_at);
        $this->assertNotNull($draft->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $draft->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $draft->updated_at);
    }
}
