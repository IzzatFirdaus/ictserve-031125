<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeToggleUnified;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Theme Toggle Component Unit Tests (v3.6.1)
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class ThemeToggleTest extends TestCase
{
    #[Test]
    public function theme_toggle_renders_correctly(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertStatus(200)
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function theme_toggle_can_toggle_theme(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertSet('theme', 'light')
            ->call('toggleTheme')
            ->assertSet('theme', 'dark');
    }
}
