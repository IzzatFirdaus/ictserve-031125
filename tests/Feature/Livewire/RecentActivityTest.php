<?php

namespace Tests\Feature\Livewire;

use App\Livewire\RecentActivity;
use Livewire\Livewire;
use Tests\TestCase;

class RecentActivityTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(RecentActivity::class)
            ->assertStatus(200);
    }
}
