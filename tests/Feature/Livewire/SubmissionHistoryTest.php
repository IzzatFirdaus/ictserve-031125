<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Staff\SubmissionHistory;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SubmissionHistoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function renders_successfully(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->assertStatus(200);
    }

    #[Test]
    public function can_filter_tickets(): void
    {
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Unique Ticket Subject',
            'ticket_number' => 'T-12345',
        ]);

        Livewire::actingAs($user)
            ->test(SubmissionHistory::class)
            ->set('search', 'Unique Ticket Subject')
            ->assertSee('Unique Ticket Subject')
            ->assertSee('T-12345');
    }
}
