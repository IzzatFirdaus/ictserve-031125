<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Events\StatusUpdated;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Model Observer Integration Test
 *
 * Tests that model observers correctly dispatch StatusUpdated events
 * when model status changes occur.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 1.1, 2.2
 */
class ModelObserverIntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function helpdesk_ticket_status_change_dispatches_status_updated_event(): void
    {
        Event::fake([StatusUpdated::class]);

        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        // Update the status
        $ticket->update(['status' => 'in_progress']);

        // Assert the event was dispatched
        Event::assertDispatched(StatusUpdated::class, function ($event) use ($ticket) {
            return $event->model->is($ticket)
                && $event->oldStatus === 'open'
                && $event->newStatus === 'in_progress';
        });
    }

    #[Test]
    public function loan_application_status_change_dispatches_status_updated_event(): void
    {
        Event::fake([StatusUpdated::class]);

        $loanApplication = LoanApplication::factory()->withoutLoanItems()->create(['status' => 'under_review']);

        // Update the status
        $loanApplication->update(['status' => 'approved']);

        // Assert the event was dispatched
        Event::assertDispatched(StatusUpdated::class, function ($event) use ($loanApplication) {
            return $event->model->is($loanApplication)
                && $event->oldStatus === 'under_review'
                && $event->newStatus === 'approved';
        });
    }

    #[Test]
    public function status_updated_event_not_dispatched_on_initial_creation(): void
    {
        Event::fake([StatusUpdated::class]);

        // Create a new ticket (initial status setting) - use withoutLoanItems to avoid extra model creation
        HelpdeskTicket::factory()->create(['status' => 'open']);

        // Create a new loan application (initial status setting) - use withoutLoanItems to avoid extra model creation
        LoanApplication::factory()->withoutLoanItems()->create(['status' => 'submitted']);

        // Assert no events were dispatched for initial creation
        Event::assertNotDispatched(StatusUpdated::class);
    }

    #[Test]
    public function status_updated_event_not_dispatched_when_status_unchanged(): void
    {
        Event::fake([StatusUpdated::class]);

        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        // Update a different field (not status)
        $ticket->update(['subject' => 'Updated subject']);

        // Assert no events were dispatched
        Event::assertNotDispatched(StatusUpdated::class);
    }

    #[Test]
    public function status_updated_event_works_for_both_guest_and_authenticated_submissions(): void
    {
        Event::fake([StatusUpdated::class]);

        // Create pre-existing models to avoid factory-related events
        $user = User::factory()->create();
        $category = \App\Models\TicketCategory::factory()->create();
        $division = \App\Models\Division::factory()->create();

        // Clear any events that might have been dispatched during setup
        Event::fake([StatusUpdated::class]);

        // Test guest submission - create manually to avoid factory complexity
        $guestTicket = HelpdeskTicket::create([
            'ticket_number' => 'HD2025000001',
            'user_id' => null,
            'category_id' => $category->id,
            'subject' => 'Test Guest Ticket',
            'description' => 'Test Description',
            'priority' => 'normal',
            'status' => 'open',
            'damage_type' => 'hardware',
            'division_id' => $division->id,
            'job_grade' => '41',
            'declaration_accepted' => true,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '123456789',
            'guest_staff_id' => 'MOTAC1234',
            'guest_grade' => 'N41',
            'guest_division' => 'ICT',
        ]);
        $guestTicket->update(['status' => 'resolved']);

        // Test authenticated submission - create manually to avoid factory complexity
        $authTicket = HelpdeskTicket::create([
            'ticket_number' => 'HD2025000002',
            'user_id' => $user->id,
            'category_id' => $category->id,
            'subject' => 'Test Auth Ticket',
            'description' => 'Test Description',
            'priority' => 'normal',
            'status' => 'open',
            'damage_type' => 'hardware',
            'division_id' => $division->id,
            'job_grade' => '41',
            'declaration_accepted' => true,
        ]);

        $authTicket->update(['status' => 'resolved']);

        // Assert both dispatched events (should be exactly 2)
        Event::assertDispatchedTimes(StatusUpdated::class, 2);
    }
}
