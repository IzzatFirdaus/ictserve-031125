<?php

namespace Tests\Feature\Livewire;

use App\Livewire\AuthenticatedDashboard;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticatedDashboardTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(AuthenticatedDashboard::class)
            ->assertStatus(200);
    }
}
