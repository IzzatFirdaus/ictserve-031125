<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Services\TicketStatusTransitionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for TicketStatusTransitionService.
 *
 * Tests ticket status state machine and transition validation.
 */
#[CoversClass(TicketStatusTransitionService::class)]
class TicketStatusTransitionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TicketStatusTransitionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TicketStatusTransitionService;
        Mail::fake();
    }

    /**
     * Test valid transition from open to assigned.
     */
    #[Test]
    public function can_transition_from_open_to_assigned(): void
    {
        $this->assertTrue($this->service->canTransition('open', 'assigned'));
    }

    /**
     * Test valid transition from open to in_progress.
     */
    #[Test]
    public function can_transition_from_open_to_in_progress(): void
    {
        $this->assertTrue($this->service->canTransition('open', 'in_progress'));
    }

    /**
     * Test valid transition from assigned to in_progress.
     */
    #[Test]
    public function can_transition_from_assigned_to_in_progress(): void
    {
        $this->assertTrue($this->service->canTransition('assigned', 'in_progress'));
    }

    /**
     * Test valid transition from in_progress to resolved.
     */
    #[Test]
    public function can_transition_from_in_progress_to_resolved(): void
    {
        $this->assertTrue($this->service->canTransition('in_progress', 'resolved'));
    }

    /**
     * Test valid transition from resolved to closed.
     */
    #[Test]
    public function can_transition_from_resolved_to_closed(): void
    {
        $this->assertTrue($this->service->canTransition('resolved', 'closed'));
    }

    /**
     * Test reopening resolved ticket.
     */
    #[Test]
    public function can_reopen_resolved_ticket(): void
    {
        $this->assertTrue($this->service->canTransition('resolved', 'in_progress'));
    }

    /**
     * Test invalid transition from closed.
     */
    #[Test]
    public function cannot_transition_from_closed(): void
    {
        $this->assertFalse($this->service->canTransition('closed', 'open'));
        $this->assertFalse($this->service->canTransition('closed', 'in_progress'));
        $this->assertFalse($this->service->canTransition('closed', 'resolved'));
    }

    /**
     * Test invalid transition from open to resolved.
     */
    #[Test]
    public function cannot_transition_from_open_to_resolved(): void
    {
        $this->assertFalse($this->service->canTransition('open', 'resolved'));
    }

    /**
     * Test same status transition is allowed.
     */
    #[Test]
    public function same_status_transition_is_allowed(): void
    {
        $this->assertTrue($this->service->canTransition('open', 'open'));
        $this->assertTrue($this->service->canTransition('in_progress', 'in_progress'));
    }

    /**
     * Test get allowed transitions for open status.
     */
    #[Test]
    public function get_allowed_transitions_for_open(): void
    {
        $transitions = $this->service->getAllowedTransitions('open');

        $this->assertContains('assigned', $transitions);
        $this->assertContains('in_progress', $transitions);
        $this->assertContains('closed', $transitions);
        $this->assertNotContains('resolved', $transitions);
    }

    /**
     * Test get allowed transitions for closed status.
     */
    #[Test]
    public function get_allowed_transitions_for_closed(): void
    {
        $transitions = $this->service->getAllowedTransitions('closed');

        $this->assertEmpty($transitions);
    }

    /**
     * Test transition updates ticket status.
     */
    #[Test]
    public function transition_updates_ticket_status(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        $this->service->transition($ticket, 'assigned');

        $ticket->refresh();
        $this->assertEquals('assigned', $ticket->status);
    }

    /**
     * Test transition to resolved sets resolved_at.
     */
    #[Test]
    public function transition_to_resolved_sets_resolved_at(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'in_progress']);

        $this->service->transition($ticket, 'resolved');

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
    }

    /**
     * Test transition to closed sets closed_at.
     */
    #[Test]
    public function transition_to_closed_sets_closed_at(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'resolved']);

        $this->service->transition($ticket, 'closed');

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertNotNull($ticket->closed_at);
    }

    /**
     * Test invalid transition throws exception.
     */
    #[Test]
    public function invalid_transition_throws_exception(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        $this->expectException(ValidationException::class);

        $this->service->transition($ticket, 'resolved');
    }

    /**
     * Test transition from closed throws exception.
     */
    #[Test]
    public function transition_from_closed_throws_exception(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'closed']);

        $this->expectException(ValidationException::class);

        $this->service->transition($ticket, 'open');
    }

    /**
     * Test transition sends email to authenticated user.
     */
    #[Test]
    public function transition_sends_email_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'user_id' => $user->id,
        ]);

        $this->service->transition($ticket, 'assigned');

        Mail::assertQueued(\App\Mail\Helpdesk\TicketStatusChangedMail::class);
    }

    /**
     * Test transition sends email to guest.
     */
    #[Test]
    public function transition_sends_email_to_guest(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'user_id' => null,
            'guest_email' => 'guest@example.com',
        ]);

        $this->service->transition($ticket, 'assigned');

        Mail::assertQueued(\App\Mail\Helpdesk\TicketStatusChangedMail::class);
    }

    /**
     * Test get transition description returns Malay text.
     */
    #[Test]
    public function get_transition_description_returns_malay_text(): void
    {
        $description = $this->service->getTransitionDescription('open', 'assigned');

        $this->assertStringContainsString('ditugaskan', $description);
    }

    /**
     * Test get transition description for resolved.
     */
    #[Test]
    public function get_transition_description_for_resolved(): void
    {
        $description = $this->service->getTransitionDescription('in_progress', 'resolved');

        $this->assertStringContainsString('diselesaikan', $description);
    }

    /**
     * Test get valid next statuses is alias for get allowed transitions.
     */
    #[Test]
    public function get_valid_next_statuses_is_alias(): void
    {
        $allowed = $this->service->getAllowedTransitions('open');
        $valid = $this->service->getValidNextStatuses('open');

        $this->assertEquals($allowed, $valid);
    }

    /**
     * Test transition status is alias for transition.
     */
    #[Test]
    public function transition_status_is_alias(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        $this->service->transitionStatus($ticket, 'assigned');

        $ticket->refresh();
        $this->assertEquals('assigned', $ticket->status);
    }
}
