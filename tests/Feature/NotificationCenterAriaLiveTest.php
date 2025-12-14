<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\NotificationCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
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
            'type' => 'App\\Notifications\\GenericNotification',
            'notifiable_type' => get_class($user),
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

        Livewire::actingAs($user)
            ->test(NotificationCenter::class)
            ->assertSeeHtml('aria-live="polite"')
            ->assertSee('Anda mempunyai 1 notifikasi belum dibaca');
    }
}
