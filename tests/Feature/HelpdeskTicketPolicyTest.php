<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Helpdesk Ticket Policy Tests
 *
 * Tests the HelpdeskTicketPolicy authorization logic for hybrid architecture:
 * - Guest submissions (no user_id)
 * - Authenticated submissions (with user_id)
 * - Ticket claiming by email matching
 * - Internal comments access control
 *
 * @see D03-FR-001.4 (Ticket claiming)
 * @see D03-FR-010.1 (Role-based access control)
 * @see D04 §6.2 (Authentication Architecture)
 */
class HelpdeskTicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    #[Test]
    public function staff_can_view_their_own_authenticated_ticket(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('view', $ticket));
    }

    #[Test]
    public function staff_can_view_guest_ticket_with_matching_email(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'staff@motac.gov.my',
        ]);

        $this->assertTrue($user->can('view', $ticket));
    }

    #[Test]
    public function staff_cannot_view_other_users_ticket(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $otherUser = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $otherUser->id]);

        $this->assertFalse($user->can('view', $ticket));
    }

    #[Test]
    public function admin_can_view_any_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('view', $ticket));
    }

    #[Test]
    public function superuser_can_view_any_ticket(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);
        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($superuser->can('view', $ticket));
    }

    #[Test]
    public function all_authenticated_users_can_create_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $approver = User::factory()->create(['role' => 'approver']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->assertTrue($staff->can('create', HelpdeskTicket::class));
        $this->assertTrue($approver->can('create', HelpdeskTicket::class));
        $this->assertTrue($admin->can('create', HelpdeskTicket::class));
        $this->assertTrue($superuser->can('create', HelpdeskTicket::class));
    }

    #[Test]
    public function only_admin_and_superuser_can_update_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->assertFalse($staff->can('update', $ticket));
        $this->assertTrue($admin->can('update', $ticket));
        $this->assertTrue($superuser->can('update', $ticket));
    }

    #[Test]
    public function only_superuser_can_delete_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->assertFalse($staff->can('delete', $ticket));
        $this->assertFalse($admin->can('delete', $ticket));
        $this->assertTrue($superuser->can('delete', $ticket));
    }

    #[Test]
    public function user_can_claim_guest_ticket_with_matching_email(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'staff@motac.gov.my',
        ]);

        $this->assertTrue($user->can('claim', $guestTicket));
        $this->assertTrue($user->can('canClaim', $guestTicket));
    }

    #[Test]
    public function user_cannot_claim_guest_ticket_with_different_email(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'other@motac.gov.my',
        ]);

        $this->assertFalse($user->can('claim', $guestTicket));
        $this->assertFalse($user->can('canClaim', $guestTicket));
    }

    #[Test]
    public function user_cannot_claim_authenticated_ticket(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        $authenticatedTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertFalse($user->can('claim', $authenticatedTicket));
        $this->assertFalse($user->can('canClaim', $authenticatedTicket));
    }

    #[Test]
    public function only_admin_and_superuser_can_view_internal_comments(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $approver = User::factory()->create(['role' => 'approver']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->assertFalse($staff->can('canViewInternal', $ticket));
        $this->assertFalse($approver->can('canViewInternal', $ticket));
        $this->assertTrue($admin->can('canViewInternal', $ticket));
        $this->assertTrue($superuser->can('canViewInternal', $ticket));
    }

    #[Test]
    public function user_can_add_comment_to_their_own_ticket(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('addComment', $ticket));
    }

    #[Test]
    public function user_can_add_comment_to_guest_ticket_with_matching_email(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'staff@motac.gov.my',
        ]);

        $this->assertTrue($user->can('addComment', $guestTicket));
    }

    #[Test]
    public function user_cannot_add_comment_to_other_users_ticket(): void
    {
        $user = User::factory()->create(['role' => 'staff']);
        $otherUser = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $otherUser->id]);

        $this->assertFalse($user->can('addComment', $ticket));
    }

    #[Test]
    public function admin_can_add_comment_to_any_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('addComment', $ticket));
    }

    #[Test]
    public function only_superuser_can_restore_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->assertFalse($staff->can('restore', $ticket));
        $this->assertFalse($admin->can('restore', $ticket));
        $this->assertTrue($superuser->can('restore', $ticket));
    }

    #[Test]
    public function only_superuser_can_force_delete_tickets(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->assertFalse($staff->can('forceDelete', $ticket));
        $this->assertFalse($admin->can('forceDelete', $ticket));
        $this->assertTrue($superuser->can('forceDelete', $ticket));
    }

    #[Test]
    public function hybrid_access_logic_for_guest_and_authenticated_submissions(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'email' => 'staff@motac.gov.my',
        ]);

        // Create guest ticket with matching email
        $guestTicket = HelpdeskTicket::factory()->create([
            'user_id' => null,
            'guest_email' => 'staff@motac.gov.my',
        ]);

        // Create authenticated ticket
        $authenticatedTicket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        // User can view both tickets
        $this->assertTrue($user->can('view', $guestTicket));
        $this->assertTrue($user->can('view', $authenticatedTicket));

        // User can claim guest ticket but not authenticated ticket
        $this->assertTrue($user->can('claim', $guestTicket));
        $this->assertFalse($user->can('claim', $authenticatedTicket));

        // User can add comments to both tickets
        $this->assertTrue($user->can('addComment', $guestTicket));
        $this->assertTrue($user->can('addComment', $authenticatedTicket));
    }
}
