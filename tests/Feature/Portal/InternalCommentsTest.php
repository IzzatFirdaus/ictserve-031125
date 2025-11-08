<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Livewire\Portal\InternalComments;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Internal Comments Feature Tests
 *
 * Tests comment creation, comment threading, @mentions,
 * and email notifications.
 *
 * Requirements: 7.1, 7.2, 7.3, 7.4, 7.5
 * Traceability: D03 SRS-FR-007, D04 §3.6
 */
class InternalCommentsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected User $otherUser;

    protected Division $division;

    protected TicketCategory $category;

    protected HelpdeskTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->category = TicketCategory::factory()->create(['name' => 'Hardware']);

        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);

        $this->otherUser = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);

        $this->ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);
    }

    #[Test]
    public function user_can_view_internal_comments(): void
    {
        $comment = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Test internal comment',
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSee('Test internal comment');
    }

    #[Test]
    public function user_can_add_internal_comment(): void
    {
        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', 'This is a new comment')
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('internal_comments', [
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'This is a new comment',
        ]);
    }

    #[Test]
    public function comment_text_is_required(): void
    {
        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', '')
            ->call('addComment')
            ->assertHasErrors(['newComment' => 'required']);
    }

    #[Test]
    public function comment_cannot_exceed_1000_characters(): void
    {
        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', str_repeat('a', 1001))
            ->call('addComment')
            ->assertHasErrors(['newComment' => 'max']);
    }

    #[Test]
    public function user_can_reply_to_comment(): void
    {
        $parentComment = InternalComment::factory()->create([
            'user_id' => $this->otherUser->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Parent comment',
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->call('replyToComment', $parentComment->id)
            ->set('newComment', 'Reply to parent')
            ->call('addComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('internal_comments', [
            'user_id' => $this->user->id,
            'parent_id' => $parentComment->id,
            'comment' => 'Reply to parent',
        ]);
    }

    #[Test]
    public function comment_threading_displays_correctly(): void
    {
        $parentComment = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Parent comment',
        ]);

        $childComment = InternalComment::factory()->create([
            'user_id' => $this->otherUser->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'parent_id' => $parentComment->id,
            'comment' => 'Child comment',
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSee('Parent comment')
            ->assertSee('Child comment');
    }

    #[Test]
    public function comment_threading_limited_to_3_levels(): void
    {
        $level1 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Level 1',
        ]);

        $level2 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'parent_id' => $level1->id,
            'comment' => 'Level 2',
        ]);

        $level3 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'parent_id' => $level2->id,
            'comment' => 'Level 3',
        ]);

        // Attempting to create level 4 should fail or be prevented
        $component = Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->call('replyToComment', $level3->id);

        // Should not allow reply at level 4
        $component->assertSet('replyingTo', null);
    }

    #[Test]
    public function user_can_mention_other_users(): void
    {
        Mail::fake();

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', "@{$this->otherUser->name} Please review this")
            ->call('addComment')
            ->assertHasNoErrors();

        $comment = InternalComment::where('comment', 'like', '%Please review this%')->first();
        $this->assertNotNull($comment);
        $this->assertContains($this->otherUser->id, $comment->mentions ?? []);
    }

    #[Test]
    public function mentioned_users_receive_notification(): void
    {
        Mail::fake();

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', "@{$this->otherUser->name} Check this out")
            ->call('addComment');

        // Verify notification was created for mentioned user
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $this->otherUser->id,
            'type' => 'App\Notifications\UserMentioned',
        ]);
    }

    #[Test]
    public function comment_author_name_is_displayed(): void
    {
        $comment = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Test comment',
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSee($this->user->name);
    }

    #[Test]
    public function comment_timestamp_is_displayed(): void
    {
        $comment = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Test comment',
            'created_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSee('ago'); // Relative time format
    }

    #[Test]
    public function comments_are_ordered_chronologically(): void
    {
        $comment1 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'First comment',
            'created_at' => now()->subHours(2),
        ]);

        $comment2 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Second comment',
            'created_at' => now()->subHour(),
        ]);

        $comment3 = InternalComment::factory()->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $this->ticket->id,
            'comment' => 'Third comment',
            'created_at' => now(),
        ]);

        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSeeInOrder(['First comment', 'Second comment', 'Third comment']);
    }

    #[Test]
    public function character_counter_displays_remaining_characters(): void
    {
        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->set('newComment', 'Test')
            ->assertSee('996'); // 1000 - 4 characters
    }

    #[Test]
    public function empty_state_displayed_when_no_comments(): void
    {
        Livewire::actingAs($this->user)
            ->test(InternalComments::class, [
                'submissionType' => 'ticket',
                'submissionId' => $this->ticket->id,
            ])
            ->assertSee('No comments yet');
    }

    #[Test]
    public function comment_posted_event_is_broadcast(): void
    {
        // This would test Laravel Echo broadcasting
        // In a real test, you'd use Event::fake() and assert the event was dispatched
        $this->markTestIncomplete('Broadcasting test requires Echo setup');
    }
}
