<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\SessionManager;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SessionManagerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function other_browser_sessions_can_be_logged_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Create a dummy session for the user
        DB::table('sessions')->insert([
            'id' => 'other_session_id',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'payload' => 'payload',
            'last_activity' => now()->subHours(2)->timestamp,
        ]);

        // Create current session
        $sessionId = session()->getId();
        DB::table('sessions')->insert([
            'id' => $sessionId,
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'payload' => 'payload',
            'last_activity' => now()->timestamp,
        ]);

        Livewire::test(SessionManager::class)
            ->call('logoutOtherBrowserSessions')
            ->assertDispatched('logged-out-other-devices');

        $this->assertDatabaseMissing('sessions', [
            'id' => 'other_session_id',
        ]);

        $this->assertDatabaseHas('sessions', [
            'id' => $sessionId,
        ]);
    }
}
