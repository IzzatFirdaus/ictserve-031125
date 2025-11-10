<?php

namespace Tests\Feature\Livewire;

use App\Livewire\SubmissionHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubmissionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertStatus(200);
    }
}
