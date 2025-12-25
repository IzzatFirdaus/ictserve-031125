<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Channel Authorization Test - PKS 5.2.1 Compliant
 *
 * Tests the authorization logic for all WebSocket channels defined in routes/channels.php
 * for the ICTServe v4.0 architecture. All channels require authenticated users per PKS 5.2.1.
 *
 * @see routes/channels.php - Channel definitions
 * @see D16_BROADCASTING_SETUP.md - Channel authorization
 *
 * @requirements 6.1, 6.2, 6.4, 6.5, 8.1, 8.2, 24.5, 24.6, 25.1
 */
class ChannelAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_access_own_private_channel(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-user.{$user->id}",
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_cannot_access_other_users_private_channel(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user1);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-user.{$user2->id}",
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function admin_can_access_admin_notifications_channel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-admin.notifications',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function superuser_can_access_admin_notifications_channel(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->actingAs($superuser);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-admin.notifications',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_cannot_access_admin_notifications_channel(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-admin.notifications',
        ]);

        $response->assertForbidden();
    }

    /**
     * PKS 5.2.1: Guest channels are no longer supported
     * All channels require authenticated user_id
     */
    #[Test]
    public function authenticated_user_can_access_ticket_channel_if_authorized(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // PKS 5.2.1: Use new authenticated channel format
        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "ticket.{$user->id}.{$ticket->id}",
        ]);

        $response->assertOk();
    }

    /**
     * PKS 5.2.1: Guest channels are no longer supported
     * All channels require authenticated user_id
     */
    #[Test]
    public function authenticated_user_can_access_loan_channel_if_authorized(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $loan = LoanApplication::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // PKS 5.2.1: Use new authenticated channel format
        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "loan.{$user->id}.{$loan->id}",
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_can_access_submission_channel_for_viewable_ticket(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-submission.ticket.{$ticket->id}",
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_can_access_submission_channel_for_viewable_loan(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $loan = LoanApplication::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-submission.loan.{$loan->id}",
        ]);

        $response->assertOk();
    }

    #[Test]
    public function user_can_access_asset_channel_if_authorized(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $asset = Asset::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-asset.{$asset->id}",
        ]);

        $response->assertOk();
    }

    #[Test]
    public function admin_can_access_ai_status_channel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-status',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function superuser_can_access_ai_alerts_channel(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);

        $this->actingAs($superuser);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-alerts',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_cannot_access_ai_performance_channel(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-performance',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function approver_can_access_ai_approvals_channel(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);

        $this->actingAs($approver);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-approvals',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function staff_cannot_access_ai_approvals_channel(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-approvals',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function invalid_channel_name_returns_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-invalid-channel',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function nonexistent_resource_id_returns_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ticket.nonexistent-uuid',
        ]);

        $response->assertForbidden();
    }
}
