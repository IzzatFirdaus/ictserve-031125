<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Components\ThemeDropdownUnified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unified Theme Dropdown Component Tests (v3.6.1)
 *
 * Tests the unified theme dropdown component functionality including:
 * - WCAG 2.2 AA compliance (keyboard navigation, dropdown accessibility)
 * - Performance optimization (single initialization, no FOUT)
 * - D00-D18 documentation standards compliance
 * - Bahasa Melayu interface (D15 v3.6.0)
 *
 * @trace D12 §4 (Color System), D13 §2.2 (Livewire), D14 §6.1.2 (Theme Switcher)
 *
 * @wcag SC 1.4.3 (Contrast), SC 2.1.1 (Keyboard), SC 4.1.2 (Name, Role, Value)
 *
 * @requirements SRS-UX-007 (Dark Mode Support)
 */
class ThemeDropdownUnifiedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_defaults_to_light_theme(): void
    {
        Livewire::test(ThemeDropdownUnified::class)
            ->assertStatus(200)
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_can_set_theme_to_dark(): void
    {
        Livewire::test(ThemeDropdownUnified::class)
            ->assertSet('theme', 'light')
            ->call('setTheme', 'dark')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-changed', ['theme' => 'dark']);
    }

    #[Test]
    public function it_can_set_theme_to_light(): void
    {
        Livewire::test(ThemeDropdownUnified::class)
            ->set('theme', 'dark')
            ->call('setTheme', 'light')
            ->assertSet('theme', 'light')
            ->assertDispatched('theme-changed', ['theme' => 'light']);
    }

    #[Test]
    public function it_normalizes_invalid_theme_values(): void
    {
        Livewire::test(ThemeDropdownUnified::class)
            ->call('setTheme', 'invalid')
            ->assertSet('theme', 'light')
            ->assertDispatched('theme-changed', ['theme' => 'light']);
    }

    #[Test]
    public function it_renders_with_wcag_compliant_dropdown_attributes(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check WCAG 2.2 AA compliance attributes for dropdown
        $this->assertStringContainsString('aria-expanded=', $html);
        $this->assertStringContainsString('aria-haspopup="listbox"', $html);
        $this->assertStringContainsString('role="listbox"', $html);
        $this->assertStringContainsString('role="option"', $html);
        $this->assertStringContainsString('aria-selected=', $html);
        $this->assertStringContainsString('w-11 h-11', $html); // 44x44px touch target
    }

    #[Test]
    public function it_renders_correct_icons_for_theme_states(): void
    {
        // Test light theme (shows sun icon)
        $lightComponent = Livewire::test(ThemeDropdownUnified::class)
            ->set('theme', 'light');

        $lightHtml = $lightComponent->html();
        $this->assertStringContainsString('x-show="theme === \'light\'"', $lightHtml);

        // Test dark theme (shows moon icon)
        $darkComponent = Livewire::test(ThemeDropdownUnified::class)
            ->set('theme', 'dark');

        $darkHtml = $darkComponent->html();
        $this->assertStringContainsString('x-show="theme === \'dark\'"', $darkHtml);
    }

    #[Test]
    public function it_includes_keyboard_navigation_support(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check keyboard navigation attributes
        $this->assertStringContainsString('@keydown="handleKeydown($event)"', $html);
        $this->assertStringContainsString('@keydown.escape.window=', $html);
        $this->assertStringContainsString('@click.outside=', $html);
    }

    #[Test]
    public function it_uses_bahasa_melayu_interface(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check Bahasa Melayu text (D15 v3.6.0 compliance)
        $this->assertStringContainsString('Pilihan tema', $html);
        $this->assertStringContainsString('Pilih tema', $html);
        $this->assertStringContainsString('Terang', $html);
        $this->assertStringContainsString('Gelap', $html);
    }

    #[Test]
    public function it_includes_alpine_js_dropdown_functionality(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check Alpine.js directives for dropdown
        $this->assertStringContainsString('x-data=', $html);
        $this->assertStringContainsString('x-show="open"', $html);
        $this->assertStringContainsString('x-transition:', $html);
        $this->assertStringContainsString('@click="open = !open"', $html);
        $this->assertStringContainsString('@click="setTheme(', $html);
    }

    #[Test]
    public function it_has_proper_contrast_colors(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check color classes for WCAG 2.2 AA compliance
        $this->assertStringContainsString('text-gray-600 dark:text-gray-300', $html);
        $this->assertStringContainsString('bg-white dark:bg-gray-800', $html);
        $this->assertStringContainsString('hover:bg-gray-100 dark:hover:bg-gray-700', $html);
        $this->assertStringContainsString('focus:ring-2 focus:ring-primary-500', $html);
    }

    #[Test]
    public function it_includes_selection_indicators(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check selection indicators (checkmarks)
        $this->assertStringContainsString('x-show="theme === \'light\'"', $html);
        $this->assertStringContainsString('x-show="theme === \'dark\'"', $html);
        $this->assertStringContainsString('text-primary-600 dark:text-primary-400', $html);
    }

    #[Test]
    public function it_prevents_multiple_initializations(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check for initialization prevention (window flag)
        $this->assertStringContainsString('window.__ictserveThemeDropdownHandler', $html);
        $this->assertStringContainsString('if (window.__ictserveThemeDropdownHandler) return;', $html);
    }

    #[Test]
    public function it_includes_focus_management(): void
    {
        $component = Livewire::test(ThemeDropdownUnified::class);

        $html = $component->html();

        // Check focus management attributes
        $this->assertStringContainsString('focus:outline-none', $html);
        $this->assertStringContainsString('focus:ring-2', $html);
        $this->assertStringContainsString('focus:bg-gray-100 dark:focus:bg-gray-700', $html);
    }
}
