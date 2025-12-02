<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Staff\SubmissionHistory;
use App\Models\User;
use App\Models\HelpdeskTicket;
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

    public function test_can_filter_tickets()
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Unique Ticket Subject',
            'ticket_number' => 'T-12345'
        ]);

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('search', 'Unique Ticket Subject')
            ->assertSee('Unique Ticket Subject')
            ->assertSee('T-12345');
    }
}
