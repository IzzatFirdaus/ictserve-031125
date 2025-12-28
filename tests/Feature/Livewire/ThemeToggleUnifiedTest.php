<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeToggleUnified;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unified Theme Toggle Component Tests (v3.6.1)
 *
 * Tests the unified theme toggle component functionality including:
 * - WCAG 2.2 AA compliance (keyboard navigation, screen reader support)
 * - Performance optimization (single initialization, no FOUT)
 * - D00-D18 documentation standards compliance
 * - Bahasa Melayu interface (D15 v3.6.0)
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 2.4.7 (Focus Visible)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class ThemeToggleUnifiedTest extends TestCase
{
    #[Test]
    public function it_defaults_to_light_theme(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertStatus(200)
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_can_toggle_theme_from_light_to_dark(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->assertSet('theme', 'light')
            ->call('toggleTheme')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', ['theme' => 'dark']);
    }

    #[Test]
    public function it_can_toggle_theme_from_dark_to_light(): void
    {
        Livewire::test(ThemeToggleUnified::class)
            ->set('theme', 'dark')
            ->call('toggleTheme')
            ->assertSet('theme', 'light')
            ->assertDispatched('theme-changed', ['theme' => 'light']);
    }

    #[Test]
    public function it_renders_with_wcag_compliant_attributes(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check WCAG 2.2 AA compliance attributes
        $this->assertStringContainsString('aria-label="Tukar tema"', $html);
        $this->assertStringContainsString('aria-pressed=', $html);
        $this->assertStringContainsString('min-h-11', $html);
        $this->assertStringContainsString('min-w-11', $html);
        $this->assertStringContainsString('focus-visible:ring-3', $html);
        $this->assertStringContainsString('focus-visible:ring-primary-500', $html);
    }

    #[Test]
    public function it_renders_correct_icons_for_theme_states(): void
    {
        // Test light theme (shows moon icon)
        $lightComponent = Livewire::test(ThemeToggleUnified::class)
            ->set('theme', 'light');

        $lightHtml = $lightComponent->html();
        $this->assertStringContainsString('x-show="theme === \'light\'"', $lightHtml);

        // Test dark theme (shows sun icon)
        $darkComponent = Livewire::test(ThemeToggleUnified::class)
            ->set('theme', 'dark');

        $darkHtml = $darkComponent->html();
        $this->assertStringContainsString('x-show="theme === \'dark\'"', $darkHtml);
    }

    #[Test]
    public function it_includes_screen_reader_support(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check screen reader text
        $this->assertStringContainsString('class="sr-only"', $html);
        $this->assertStringContainsString('Tukar ke mod terang', $html);
        $this->assertStringContainsString('Tukar ke mod gelap', $html);
    }

    #[Test]
    public function it_uses_bahasa_melayu_interface(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check Bahasa Melayu text (D15 v3.6.0 compliance)
        $this->assertStringContainsString('Tukar tema', $html);
        $this->assertStringContainsString('Tukar ke mod terang', $html);
        $this->assertStringContainsString('Tukar ke mod gelap', $html);
    }

    #[Test]
    public function it_includes_alpine_js_integration(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check Alpine.js directives
        $this->assertStringContainsString('x-data=', $html);
        $this->assertStringContainsString('x-bind:aria-pressed=', $html);
        $this->assertStringContainsString('x-show=', $html);
        $this->assertStringContainsString('x-cloak', $html);
    }

    #[Test]
    public function it_includes_loading_state_handling(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check loading state attributes
        $this->assertStringContainsString('wire:loading.attr="disabled"', $html);
    }

    #[Test]
    public function it_has_proper_contrast_colors(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check color classes for WCAG 2.2 AA compliance
        $this->assertStringContainsString('text-slate-600 dark:text-slate-300', $html);
        $this->assertStringContainsString('text-warning-400', $html); // Sun icon
        $this->assertStringContainsString('hover:bg-slate-100 dark:hover:bg-slate-700', $html);
    }

    #[Test]
    public function it_prevents_multiple_initializations(): void
    {
        $component = Livewire::test(ThemeToggleUnified::class);

        $html = $component->html();

        // Check for initialization prevention (window flag)
        $this->assertStringContainsString('window.__ictserveThemeToggleHandler', $html);
        $this->assertStringContainsString('if (window.__ictserveThemeToggleHandler) return;', $html);
    }
}
