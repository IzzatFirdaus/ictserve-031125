<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\SessionManager;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SessionManagerTest extends TestCase
{
    #[Test]
    public function other_browser_sessions_can_be_logged_out(): void
    {
        $user = User::factory()->create();

        // Create a dummy session for the user (this will be the "other" session)
        DB::table('sessions')->insert([
            'id' => 'other_session_id',
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Symfony',
            'payload' => 'payload',
            'last_activity' => now()->subHours(2)->timestamp,
        ]);

        // Verify the session exists before the test
        $this->assertDatabaseHas('sessions', [
            'id' => 'other_session_id',
        ]);

        // Call the logout method - since we're in a Livewire test context without
        // a real session, all sessions for the user will be deleted
        Livewire::actingAs($user)
            ->test(SessionManager::class)
            ->call('logoutOtherBrowserSessions')
            ->assertDispatched('logged-out-other-devices');

        // The other session should be deleted
        $this->assertDatabaseMissing('sessions', [
            'id' => 'other_session_id',
        ]);
    }
}
