<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeToggleUnified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Livewire Theme Toggle Tests (v3.6.1)
 *
 * Tests the unified theme toggle component functionality.
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class LivewireThemeToggleTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_defaults_to_light_when_no_preference_is_set(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertStatus(200)
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_can_toggle_theme(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertSet('theme', 'light')
            ->call('toggleTheme')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', ['theme' => 'dark']);
    }

    #[Test]
    public function toggle_theme_dispatches_event(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->call('toggleTheme')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', ['theme' => 'dark']);
    }

    #[Test]
    public function it_can_toggle_back_to_light(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->set('theme', 'dark')
            ->call('toggleTheme')
            ->assertSet('theme', 'light')
            ->assertDispatched('theme-changed', ['theme' => 'light']);
    }
}
