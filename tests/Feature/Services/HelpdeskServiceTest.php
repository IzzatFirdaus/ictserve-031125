<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Contracts\HelpdeskServiceInterface;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Helpdesk Service Tests
 *
 * Tests helpdesk ticket management with hybrid architecture support.
 * Covers both guest submissions (user_id = NULL) and authenticated submissions.
 *
 * @trace Requirements 1.5, 2.1, 5.3, 5.4
 * @trace D03 SRS-HELP-001, SRS-HELP-002, SRS-HELP-003
 */
class HelpdeskServiceTest extends TestCase
{
    use RefreshDatabase;

    private HelpdeskServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(HelpdeskServiceInterface::class);
    }

    /**
     * Test: Create guest ticket (user_id = NULL)
     *
     * @trace Requirement 1.5
     */
    public function test_creates_guest_ticket_with_null_user_id(): void
    {
        $category = TicketCategory::factory()->create();

        $data = [
            'category_id' => $category->id,
            'subject' => 'Test Guest Ticket',
            'description' => 'This is a test guest ticket',
            'priority' => 'normal',
            'guest_name' => 'John Doe',
            'guest_email' => 'john.doe@motac.gov.my',
            'guest_phone' => '0123456789',
            'declaration_accepted' => true,
        ];

        $ticket = $this->service->createTicket($data);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertNull($ticket->user_id);
        $this->assertEquals('John Doe', $ticket->guest_name);
        $this->assertEquals('john.doe@motac.gov.my', $ticket->guest_email);
        $this->assertNotNull($ticket->status_token_hash);
        $this->assertStringStartsWith('HD-', $ticket->ticket_number);
    }

    /**
     * Test: Create authenticated ticket (user_id set)
     *
     * @trace Requirement 1.5
     */
    public function test_creates_authenticated_ticket_with_user_id(): void
    {
        $user = User::factory()->create([
            'email' => 'staff@motac.gov.my',
            'role' => 'staff',
        ]);
        $category = TicketCategory::factory()->create();

        Auth::login($user);

        $data = [
            'category_id' => $category->id,
            'subject' => 'Test Authenticated Ticket',
            'description' => 'This is a test authenticated ticket',
            'priority' => 'high',
            'declaration_accepted' => true,
        ];

        $ticket = $this->service->createTicket($data);

        $this->assertInstanceOf(HelpdeskTicket::class, $ticket);
        $this->assertEquals($user->id, $ticket->user_id);
        $this->assertNull($ticket->guest_name);
        $this->assertNull($ticket->guest_email);
        $this->assertNotNull($ticket->status_token_hash);
    }

    /**
     * Test: Update ticket status with required comment
     *
     * @trace Requirement 5.3
     */
    public function test_updates_ticket_status_with_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        Auth::login($admin);

        $this->service->updateStatus($ticket, 'in_progress', 'Starting work on this ticket');

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertCount(1, $ticket->comments);
        $this->assertEquals('Starting work on this ticket', $ticket->comments->first()->comment);
    }

    /**
     * Test: Update status requires comment
     *
     * @trace Requirement 5.3
     */
    public function test_update_status_requires_comment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        Auth::login($admin);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Comment is required for status updates');

        $this->service->updateStatus($ticket, 'in_progress', '');
    }

    /**
     * Test: Assign ticket to admin user
     *
     * @trace Requirement 5.4
     */
    public function test_assigns_ticket_to_admin_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $assignee = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create();

        Auth::login($admin);

        $this->service->assignTicket($ticket, $assignee);

        $ticket->refresh();
        $this->assertEquals($assignee->id, $ticket->assigned_to_user);
        $this->assertNotNull($ticket->assigned_at);
    }

    /**
     * Test: Cannot assign ticket to non-admin user
     *
     * @trace Requirement 5.4
     */
    public function test_cannot_assign_ticket_to_non_admin_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create();

        Auth::login($admin);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('User must have admin or superuser role');

        $this->service->assignTicket($ticket, $staff);
    }

    /**
     * Test: Retrieve ticket by status token
     *
     * @trace Requirement 2.1
     */
    public function test_retrieves_ticket_by_status_token(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        // Generate status token
        $tokenService = app(\App\Contracts\TokenServiceInterface::class);
        $token = $tokenService->generateStatusToken($ticket);

        // Retrieve ticket using token
        $retrievedTicket = $this->service->getByStatusToken($token);

        $this->assertInstanceOf(HelpdeskTicket::class, $retrievedTicket);
        $this->assertEquals($ticket->id, $retrievedTicket->id);
    }

    /**
     * Test: Returns null for invalid status token
     *
     * @trace Requirement 2.1
     */
    public function test_returns_null_for_invalid_status_token(): void
    {
        $retrievedTicket = $this->service->getByStatusToken('invalid-token-12345');

        $this->assertNull($retrievedTicket);
    }

    /**
     * Test: Calculate SLA due date based on category
     *
     * @trace Requirement 5.3
     */
    public function test_calculates_sla_due_date_based_on_category(): void
    {
        $category = TicketCategory::factory()->create([
            'name' => 'Hardware',
            'sla_resolution_hours' => 48,
        ]);

        $dueDate = $this->service->calculateSLADueDate((string) $category->id);

        $this->assertInstanceOf(\Carbon\Carbon::class, $dueDate);
        $this->assertTrue($dueDate->isFuture());
        $this->assertEqualsWithDelta(48, now()->diffInHours($dueDate), 1);
    }

    /**
     * Test: Check SLA breach for overdue ticket
     *
     * @trace Requirement 5.5
     */
    public function test_checks_sla_breach_for_overdue_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'sla_resolution_due_at' => now()->subHours(2),
            'resolved_at' => null,
        ]);

        $isBreached = $this->service->checkSLABreach($ticket);

        $this->assertTrue($isBreached);
    }

    /**
     * Test: No SLA breach for ticket within SLA
     *
     * @trace Requirement 5.5
     */
    public function test_no_sla_breach_for_ticket_within_sla(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'sla_resolution_due_at' => now()->addHours(24),
            'resolved_at' => null,
        ]);

        $isBreached = $this->service->checkSLABreach($ticket);

        $this->assertFalse($isBreached);
    }

    /**
     * Test: Ticket number format is HD-YYYYMM-XXXX
     *
     * @trace Requirement 1.5
     */
    public function test_ticket_number_format_is_correct(): void
    {
        $category = TicketCategory::factory()->create();

        $data = [
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test description',
            'guest_name' => 'Test User',
            'guest_email' => 'test@motac.gov.my',
            'declaration_accepted' => true,
        ];

        $ticket = $this->service->createTicket($data);

        $this->assertMatchesRegularExpression('/^HD-\d{6}-\d{4}$/', $ticket->ticket_number);
    }

    /**
     * Test: Form reference code is set correctly
     *
     * @trace Requirement 24.1
     */
    public function test_form_reference_code_is_set_correctly(): void
    {
        $category = TicketCategory::factory()->create();

        $data = [
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test description',
            'guest_name' => 'Test User',
            'guest_email' => 'test@motac.gov.my',
            'declaration_accepted' => true,
        ];

        $ticket = $this->service->createTicket($data);

        $this->assertEquals('PK.(S).MOTAC.07.(L1)', $ticket->form_reference_code);
    }
}
