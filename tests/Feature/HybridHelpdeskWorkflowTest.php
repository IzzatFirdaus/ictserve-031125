<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Models\User;
use App\Services\HybridHelpdeskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PKS 5.2.1 Compliant Helpdesk Workflow Feature Tests
 *
 * Validates SSO-only authenticated ticket flows, access rules,
 * and cross-module integration behaviour for the PKS-compliant architecture.
 *
 * PKS 5.2.1 Compliance: All submissions require mandatory user_id (NOT NULL)
 * NO GUEST ACCESS - All users MUST authenticate via SSO
 *
 * @requirements 1.1, 1.2, 1.3, 2.2, 3.1, 25.1
 */
class HybridHelpdeskWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected HybridHelpdeskService $hybridHelpdeskService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hybridHelpdeskService = app(HybridHelpdeskService::class);
    }

    /**
     * PKS 5.2.1: Authenticated ticket creation with mandatory user_id linkage
     */
    #[Test]
    public function authenticated_ticket_creation_with_mandatory_user_id_linked(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create([
            'code' => 'NET',
            'name_en' => 'Network Issues',
            'name_ms' => 'Isu Rangkaian',
            'sla_response_hours' => 2,
            'sla_resolution_hours' => 12,
        ]);
        $user = User::factory()->create([
            'email' => 'siti.rahman@motac.gov.my',
            'name' => 'Siti Rahman',
            'division_id' => $division->id,
        ]);

        $ticket = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'job_grade' => 'Gred 44',
            'declaration_accepted' => true,
            'category_id' => $category->id,
            'priority' => 'urgent',
            'subject' => 'Kegagalan sambungan VPN',
            'description' => 'Tidak dapat mewujudkan sambungan VPN dari pejabat jauh.',
            'internal_notes' => 'Eskalasi kepada operasi rangkaian untuk semakan.',
        ], $user);

        // PKS 5.2.1: Verify mandatory user_id linkage (NOT NULL)
        $this->assertNotNull($ticket->user_id, 'PKS 5.2.1: All submissions must have mandatory user_id (NOT NULL)');
        $this->assertEquals($user->id, $ticket->user_id);

        // Verify database constraint enforces mandatory user_id
        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
            'division_id' => $division->id,
            'job_grade' => 'Gred 44',
            'declaration_accepted' => true,
            'internal_notes' => 'Eskalasi kepada operasi rangkaian untuk semakan.',
            'status' => 'open',
        ]);
    }

    /**
     * PKS 5.2.1: User can only access their own tickets
     */
    #[Test]
    public function user_can_only_access_own_tickets(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();

        $user1 = User::factory()->create(['division_id' => $division->id]);
        $user2 = User::factory()->create(['division_id' => $division->id]);

        // Create ticket for user1
        $ticket = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'User 1 Ticket',
            'description' => 'Test ticket for user 1',
        ], $user1);

        // User1 should have access
        $this->assertTrue(
            $this->hybridHelpdeskService->canUserAccessTicket($ticket, $user1),
            'PKS 5.2.1: Owner should have access to their ticket'
        );

        // User2 should NOT have access
        $this->assertFalse(
            $this->hybridHelpdeskService->canUserAccessTicket($ticket, $user2),
            'PKS 5.2.1: Non-owner should not have access to ticket'
        );
    }

    /**
     * PKS 5.2.1: getUserTickets returns only user's own tickets
     */
    #[Test]
    public function get_user_tickets_returns_only_own_tickets(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();

        $user1 = User::factory()->create(['division_id' => $division->id]);
        $user2 = User::factory()->create(['division_id' => $division->id]);

        // Create tickets for both users
        $ticket1 = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'User 1 Ticket A',
            'description' => 'First ticket for user 1',
        ], $user1);

        $ticket2 = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'User 1 Ticket B',
            'description' => 'Second ticket for user 1',
        ], $user1);

        $ticket3 = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'User 2 Ticket',
            'description' => 'Ticket for user 2',
        ], $user2);

        // User1 should only see their 2 tickets
        $user1Tickets = $this->hybridHelpdeskService->getUserTickets($user1)->get();
        $this->assertCount(2, $user1Tickets);
        $this->assertTrue($user1Tickets->contains('id', $ticket1->id));
        $this->assertTrue($user1Tickets->contains('id', $ticket2->id));
        $this->assertFalse($user1Tickets->contains('id', $ticket3->id));

        // User2 should only see their 1 ticket
        $user2Tickets = $this->hybridHelpdeskService->getUserTickets($user2)->get();
        $this->assertCount(1, $user2Tickets);
        $this->assertTrue($user2Tickets->contains('id', $ticket3->id));
    }

    /**
     * PKS 5.2.1: Ticket statistics are user-specific
     */
    #[Test]
    public function ticket_statistics_are_user_specific(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();

        $user = User::factory()->create(['division_id' => $division->id]);

        // Create tickets directly with factory to avoid broadcast events
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'Open Ticket',
            'description' => 'Test open ticket',
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'In Progress Ticket',
            'description' => 'Test in progress ticket',
            'status' => 'in_progress',
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'Resolved Ticket',
            'description' => 'Test resolved ticket',
            'status' => 'resolved',
        ]);

        $stats = $this->hybridHelpdeskService->getUserTicketStats($user);

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['open']);
        $this->assertEquals(1, $stats['in_progress']);
        $this->assertEquals(1, $stats['resolved']);
        $this->assertEquals(0, $stats['closed']);
    }

    /**
     * PKS 5.2.1: Ticket update requires ownership
     */
    #[Test]
    public function ticket_update_requires_ownership(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();

        $owner = User::factory()->create(['division_id' => $division->id]);
        $otherUser = User::factory()->create(['division_id' => $division->id]);

        $ticket = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'Original Subject',
            'description' => 'Original description',
        ], $owner);

        // Owner can update
        $updatedTicket = $this->hybridHelpdeskService->updateTicket(
            $ticket,
            ['subject' => 'Updated Subject'],
            $owner
        );
        $this->assertEquals('Updated Subject', $updatedTicket->subject);

        // Non-owner cannot update
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unauthorized access to ticket');

        $this->hybridHelpdeskService->updateTicket(
            $ticket,
            ['subject' => 'Hacked Subject'],
            $otherUser
        );
    }

    /**
     * PKS 5.2.1: Ticket creation generates proper ticket number
     */
    #[Test]
    public function ticket_creation_generates_proper_ticket_number(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();
        $user = User::factory()->create(['division_id' => $division->id]);

        $ticket = $this->hybridHelpdeskService->createTicket([
            'division_id' => $division->id,
            'category_id' => $category->id,
            'subject' => 'Test Ticket',
            'description' => 'Test description',
        ], $user);

        // Ticket number should be generated (not TEMP-)
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringNotContainsString('TEMP-', $ticket->ticket_number);
    }

    /**
     * PKS 5.2.1: All ticket priorities are supported
     */
    #[Test]
    public function all_ticket_priorities_are_supported(): void
    {
        $division = Division::factory()->create();
        $category = TicketCategory::factory()->create();
        $user = User::factory()->create(['division_id' => $division->id]);

        $priorities = ['low', 'normal', 'high', 'urgent'];

        foreach ($priorities as $priority) {
            $ticket = $this->hybridHelpdeskService->createTicket([
                'division_id' => $division->id,
                'category_id' => $category->id,
                'priority' => $priority,
                'subject' => "Test {$priority} priority ticket",
                'description' => 'Test description',
            ], $user);

            $this->assertEquals($priority, $ticket->priority);
            $this->assertNotNull($ticket->user_id, "PKS 5.2.1: {$priority} priority ticket must have user_id");
        }
    }

    /**
     * PKS 5.2.1: Authenticated user dashboard access
     */
    #[Test]
    public function authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * PKS 5.2.1: Unauthenticated users cannot access dashboard
     */
    #[Test]
    public function unauthenticated_users_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
