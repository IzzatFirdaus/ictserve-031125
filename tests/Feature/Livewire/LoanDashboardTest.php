<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Loans\LoanDashboard;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanDashboardTest extends TestCase
{
    #[Test]
    public function renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::withoutLazyLoading()
            ->actingAs($user)
            ->test(LoanDashboard::class)
            ->assertStatus(200)
            ->assertDontSee('*** End Patch')
            ->assertSee(__('loan.dashboard.title'))
            ->assertSee(__('loan.dashboard.quick_actions'));
    }
}
