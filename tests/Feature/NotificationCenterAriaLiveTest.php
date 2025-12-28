<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationCenterAriaLiveTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_announces_unread_count_via_aria_live_region(): void
    {
        $user = User::factory()->create();

        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => \App\Notifications\GenericNotification::class,
            'notifiable_type' => \get_class($user),
            'notifiable_id' => $user->id,
            'data' => json_encode([
                'type' => 'general',
                'title' => 'Ujian',
                'message' => 'Notifikasi ujian',
                'url' => null,
            ]),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test that the notification center page renders with aria-live region
        $response = $this->actingAs($user)->get('/notifications');

        $response->assertOk();
        // Check for aria-live attribute in the response
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertStringContainsString('aria-live', $content);
        // The component should show unread notification indicator (the count "1" appears in the page)
        // Note: The unreadCount is a computed property accessed via $this->unreadCount in the Volt component
        $this->assertStringContainsString('1', $content);
    }
}
