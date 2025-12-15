<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeToggle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LivewireThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_defaults_to_light_when_no_preference_is_set(): void
    {
        Livewire::test(ThemeToggle::class)
            ->assertStatus(200)
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_initializes_from_cookie_when_present(): void
    {
        Livewire::withCookie('theme_preference', 'dark')
            ->test(ThemeToggle::class)
            ->assertStatus(200)
            ->assertSet('theme', 'dark');
    }

    #[Test]
    public function toggle_theme_persists_to_session_and_dispatches_event(): void
    {
        Livewire::test(ThemeToggle::class)
            ->call('toggleTheme')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', theme: 'dark');

        $this->assertSame('dark', session('theme_preference'));
    }

    #[Test]
    public function set_theme_persists_to_authenticated_user(): void
    {
        $user = User::factory()->create([
            'theme_preference' => 'light',
        ]);

        $this->actingAs($user);

        Livewire::test(ThemeToggle::class)
            ->call('setTheme', 'dark')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', theme: 'dark');

        $this->assertSame('dark', $user->fresh()->theme_preference);
    }
}
