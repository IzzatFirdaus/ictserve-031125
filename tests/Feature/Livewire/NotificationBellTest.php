<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\NotificationBell;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotificationBellTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function component_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->assertStatus(200)
            ->assertSet('unreadCount', 0)
            ->assertSet('recentNotifications', []);
    }

    #[Test]
    public function mark_as_read_updates_notification_count(): void
    {
        $user = User::factory()->create();
        $notification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => \stdClass::class,
            'data' => ['title' => 'Test', 'message' => 'Message'],
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->call('markAsRead', $notification->id)
            ->assertHasNoErrors();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    #[Test]
    public function mark_all_as_read_marks_all_notifications(): void
    {
        $user = User::factory()->create();

        $firstNotification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => \stdClass::class,
            'data' => ['title' => 'Test 1'],
        ]);

        $secondNotification = $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => \stdClass::class,
            'data' => ['title' => 'Test 2'],
        ]);

        $this->actingAs($user);

        Livewire::test(NotificationBell::class)
            ->call('markAllAsRead')
            ->assertHasNoErrors();

        $this->assertNotNull($firstNotification->fresh()->read_at);
        $this->assertNotNull($secondNotification->fresh()->read_at);
    }
}
