<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Staff\AuthenticatedDashboard;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Authenticated Dashboard Livewire Component Test
 *
 * Tests rendering and functionality of the authenticated user dashboard.
 */
class AuthenticatedDashboardTest extends TestCase
{
    #[Test]
    public function renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertStatus(200)
            ->assertSee(__('common.dashboard'))
            ->assertSee(__('common.welcome_back'))
            ->assertSee(__('common.my_open_tickets'))
            ->assertSee(__('common.my_pending_loans'));
    }
}
