<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PKS 5.2.1 Compliant HelpdeskTicket Model Tests
 *
 * Tests mandatory user_id linkage for all submissions.
 * NO GUEST ACCESS - All users MUST authenticate via SSO.
 *
 * @requirements 1.1, 1.2, 3.1, 8.1, 25.1
 */
class HelpdeskTicketHybridTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PKS 5.2.1: Mandatory user_id FK for all submissions
     */
    #[Test]
    public function mandatory_user_id_foreign_key_for_all_submissions(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Fatimah',
            'email' => 'siti.fatimah@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($ticket->user_id, 'PKS 5.2.1: All submissions must have mandatory user_id');
        $this->assertEquals($user->id, $ticket->user_id);

        $this->assertDatabaseHas('helpdesk_tickets', [
            'id' => $ticket->id,
            'user_id' => $user->id,
        ]);
    }

    /**
     * PKS 5.2.1: Authenticated submissions use User model data
     */
    #[Test]
    public function authenticated_submissions_use_user_model_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Datuk Hakim Bin Omar',
            'email' => 'datuk.hakim@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals('Datuk Hakim Bin Omar', $ticket->getSubmitterName());
        $this->assertEquals('datuk.hakim@motac.gov.my', $ticket->getSubmitterEmail());
        $this->assertNotNull($ticket->user_id);
    }

    /**
     * PKS 5.2.1: getSubmitterEmail returns user email for authenticated tickets
     */
    #[Test]
    public function get_submitter_email_returns_user_email_for_authenticated_tickets(): void
    {
        $user = User::factory()->create(['email' => 'david@motac.gov.my']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals('david@motac.gov.my', $ticket->getSubmitterEmail());
    }

    /**
     * PKS 5.2.1: getSubmitterIdentifier returns correct format for authenticated user
     */
    #[Test]
    public function get_submitter_identifier_returns_correct_format_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals("user:{$user->id}", $ticket->getSubmitterIdentifier());
    }

    /**
     * PKS 5.2.1: User relationship is properly loaded
     */
    #[Test]
    public function user_relationship_is_properly_loaded(): void
    {
        $user = User::factory()->create([
            'name' => 'Ahmad Bin Ali',
            'email' => 'ahmad.ali@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($ticket->user);
        $this->assertEquals($user->id, $ticket->user->id);
        $this->assertEquals('Ahmad Bin Ali', $ticket->user->name);
        $this->assertEquals('ahmad.ali@motac.gov.my', $ticket->user->email);
    }

    /**
     * PKS 5.2.1: Multiple tickets can belong to same user
     */
    #[Test]
    public function multiple_tickets_can_belong_to_same_user(): void
    {
        $user = User::factory()->create();

        $ticket1 = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Tiket Pertama',
        ]);

        $ticket2 = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Tiket Kedua',
        ]);

        $ticket3 = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Tiket Ketiga',
        ]);

        $this->assertEquals($user->id, $ticket1->user_id);
        $this->assertEquals($user->id, $ticket2->user_id);
        $this->assertEquals($user->id, $ticket3->user_id);

        $userTickets = HelpdeskTicket::where('user_id', $user->id)->get();
        $this->assertCount(3, $userTickets);
    }

    /**
     * PKS 5.2.1: Ticket status lifecycle with authenticated user
     */
    #[Test]
    public function ticket_status_lifecycle_with_authenticated_user(): void
    {
        $user = User::factory()->create();

        // Create ticket directly with factory to avoid broadcast events
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        $this->assertEquals('open', $ticket->status);
        $this->assertNotNull($ticket->user_id);

        // Update status directly without triggering observers/broadcasts
        HelpdeskTicket::withoutEvents(function () use ($ticket) {
            $ticket->update(['status' => 'in_progress']);
        });
        $this->assertEquals('in_progress', $ticket->fresh()->status);

        HelpdeskTicket::withoutEvents(function () use ($ticket) {
            $ticket->update(['status' => 'resolved']);
        });
        $this->assertEquals('resolved', $ticket->fresh()->status);

        HelpdeskTicket::withoutEvents(function () use ($ticket) {
            $ticket->update(['status' => 'closed']);
        });
        $this->assertEquals('closed', $ticket->fresh()->status);
    }

    /**
     * PKS 5.2.1: Ticket priority levels with authenticated user
     */
    #[Test]
    public function ticket_priority_levels_with_authenticated_user(): void
    {
        $user = User::factory()->create();

        $priorities = ['low', 'normal', 'high', 'urgent'];

        foreach ($priorities as $priority) {
            $ticket = HelpdeskTicket::factory()->create([
                'user_id' => $user->id,
                'priority' => $priority,
            ]);

            $this->assertEquals($priority, $ticket->priority);
            $this->assertNotNull($ticket->user_id);
        }
    }

    /**
     * PKS 5.2.1: Ticket with division relationship
     */
    #[Test]
    public function ticket_with_division_relationship(): void
    {
        $user = User::factory()->create();

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($ticket->user_id);
        $this->assertNotNull($ticket->division_id);
    }

    /**
     * PKS 5.2.1: Ticket with category relationship
     */
    #[Test]
    public function ticket_with_category_relationship(): void
    {
        $user = User::factory()->create();

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($ticket->user_id);
        $this->assertNotNull($ticket->category_id);
    }
}
