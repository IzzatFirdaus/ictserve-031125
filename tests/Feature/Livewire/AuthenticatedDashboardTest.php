<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\AuthenticatedDashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    use RefreshDatabase;

    #[Test]
    public function renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertStatus(200);
    }
}
