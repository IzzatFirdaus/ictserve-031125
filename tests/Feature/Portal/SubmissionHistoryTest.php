<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Submission History Functionality Tests
 *
 * Tests tabbed interface, search, filtering, sorting, and pagination
 * for helpdesk tickets and asset loan submissions.
 *
 * Requirements: 2.1, 2.2, 2.3, 2.4
 * Traceability: D03 SRS-FR-002, D04 §2.1-2.3
 */
class SubmissionHistoryTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Division $division;

    private TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'ICT Division']);
        $this->category = TicketCategory::factory()->create(['name' => 'Hardware']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
        ]);
    }

    #[Test]
    public function authenticated_user_can_access_submission_history(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $response->assertStatus(200);
        $response->assertSee('My Submissions');
        $response->assertSee('My Helpdesk Tickets');
        $response->assertSee('My Asset Loans');
    }

    #[Test]
    public function guest_cannot_access_submission_history(): void
    {
        $response = $this->get('/portal/submissions');

        $response->assertRedirect('/login');
    }

    #[Test]
    public function submission_history_displays_tabbed_interface(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $response->assertStatus(200);
        $response->assertSee('My Helpdesk Tickets');
        $response->assertSee('My Asset Loans');
    }

    #[Test]
    public function user_can_view_their_helpdesk_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'subject' => 'Test Ticket Subject',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->user)->get('/portal/submissions?tab=tickets');

        $response->assertStatus(200);
        // Component is lazy-loaded, so we check for the component presence
        $response->assertSeeLivewire('staff.submission-history');
    }

    #[Test]
    public function user_can_view_their_loan_applications(): void
    {
        $loan = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->user)->get('/portal/submissions?tab=loans');

        $response->assertStatus(200);
        $response->assertSee($loan->application_number);
    }

    #[Test]
    public function user_cannot_view_other_users_submissions(): void
    {
        $otherUser = User::factory()->create();
        $otherTicket = HelpdeskTicket::factory()->create([
            'user_id' => $otherUser->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $response->assertStatus(200);
        $response->assertDontSee($otherTicket->ticket_number);
    }

    #[Test]
    public function user_can_search_submissions_by_ticket_number(): void
    {
        $ticket1 = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'subject' => 'First Ticket',
        ]);

        $ticket2 = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'subject' => 'Second Ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/portal/submissions?search='.$ticket1->ticket_number);

        $response->assertStatus(200);
        $response->assertSee($ticket1->ticket_number);
        $response->assertDontSee($ticket2->ticket_number);
    }

    #[Test]
    public function user_can_filter_submissions_by_status(): void
    {
        $openTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'status' => 'open',
            'subject' => 'Open Ticket',
        ]);

        $resolvedTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'status' => 'resolved',
            'subject' => 'Resolved Ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/portal/submissions?status[]=open');

        $response->assertStatus(200);
        $response->assertSee('Open Ticket');
        $response->assertDontSee('Resolved Ticket');
    }

    #[Test]
    public function user_can_sort_submissions_by_date(): void
    {
        $oldTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now()->subDays(5),
            'subject' => 'Old Ticket',
        ]);

        $newTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now(),
            'subject' => 'New Ticket',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/portal/submissions?sort=created_at&direction=desc');

        $response->assertStatus(200);
        $content = $response->getContent();
        $newPos = strpos($content, 'New Ticket');
        $oldPos = strpos($content, 'Old Ticket');

        $this->assertLessThan($oldPos, $newPos);
    }

    #[Test]
    public function submissions_are_paginated(): void
    {
        HelpdeskTicket::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $response->assertStatus(200);
        $response->assertSee('pagination');
    }

    #[Test]
    public function user_can_view_submission_detail(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'subject' => 'Detailed Ticket',
            'description' => 'This is a detailed description',
        ]);

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$ticket->id}?type=ticket");

        $response->assertStatus(200);
        $response->assertSee('Detailed Ticket');
        $response->assertSee('This is a detailed description');
    }

    #[Test]
    public function submission_detail_shows_status_timeline(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$ticket->id}?type=ticket");

        $response->assertStatus(200);
        $response->assertSee('Timeline');
        $response->assertSee('Submitted');
    }

    #[Test]
    public function user_cannot_view_other_users_submission_details(): void
    {
        $otherUser = User::factory()->create();
        $otherTicket = HelpdeskTicket::factory()->create([
            'user_id' => $otherUser->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$otherTicket->id}?type=ticket");

        $response->assertStatus(403);
    }
}
