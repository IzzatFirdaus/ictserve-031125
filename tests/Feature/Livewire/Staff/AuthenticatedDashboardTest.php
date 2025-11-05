<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\AuthenticatedDashboard;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedDashboardTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_counts_open_tickets_including_assigned_and_pending_user_statuses(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'assigned',
        ]);

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'pending_user',
        ]);

        // Tickets that should not be counted
        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'resolved',
        ]);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);

        $stats = $component->statistics();

        $this->assertSame(3, $stats['open_tickets']);
    }

    #[Test]
    public function it_returns_recent_tickets_assigned_to_the_authenticated_user(): void
    {
        $user = User::factory()->create();

        $recentTicket = HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'open',
            'subject' => 'Dashboard Assigned Ticket',
        ]);

        // An older ticket assigned to the same user should still appear if within limit
        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'assigned',
            'created_at' => now()->subDay(),
        ]);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);

        $tickets = $component->recentTickets();

        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->is($recentTicket)));
    }
}
