<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Filament\Widgets\ThemeToggleWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Theme Toggle Widget Test
 *
 * Tests the Filament theme toggle widget functionality including:
 * - Widget visibility for authenticated users
 * - Proper rendering of theme toggle UI
 * - Accessibility attributes (aria-label, min touch target)
 *
 * @trace D03-FR-001 (UI/UX Requirements)
 * @trace D12 UI/UX Design Guide - Theme Management
 * @trace WCAG 2.2 AA - Interactive Controls
 */
class ThemeToggleWidgetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that theme toggle widget can be rendered for authenticated users
     */
    public function test_theme_toggle_widget_renders_for_authenticated_users(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        Livewire::actingAs($user)
            ->test(ThemeToggleWidget::class)
            ->assertOk();
    }

    /**
     * Test that theme toggle widget cannot be viewed by guests
     */
    public function test_theme_toggle_widget_not_visible_to_guests(): void
    {
        $this->assertFalse(
            ThemeToggleWidget::canView(),
            'Theme toggle widget should not be visible to guests'
        );
    }

    /**
     * Test that theme toggle widget contains required accessibility attributes
     */
    public function test_theme_toggle_widget_has_accessibility_attributes(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $component = Livewire::actingAs($user)
            ->test(ThemeToggleWidget::class);

        $html = $component->html();

        // Check for aria-label attribute
        $this->assertStringContainsString(
            'aria-label',
            $html,
            'Theme toggle should have aria-label attribute for screen readers'
        );

        // Check for aria-live attribute for announcements
        $this->assertStringContainsString(
            'aria-live',
            $html,
            'Theme toggle should have aria-live attribute for dynamic updates'
        );

        // Check for minimum touch target size (44x44px for WCAG 2.2 AA)
        $this->assertStringContainsString(
            'min-w-[44px]',
            $html,
            'Theme toggle should meet minimum touch target size (44x44px)'
        );

        $this->assertStringContainsString(
            'min-h-[44px]',
            $html,
            'Theme toggle should meet minimum touch target size (44x44px)'
        );
    }

    /**
     * Test that theme toggle widget has icons for light and dark modes
     */
    public function test_theme_toggle_widget_has_theme_icons(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $component = Livewire::actingAs($user)
            ->test(ThemeToggleWidget::class);

        $html = $component->html();

        // Check for sun icon (light mode)
        $this->assertStringContainsString(
            'theme-icon-light',
            $html,
            'Theme toggle should have light mode icon'
        );

        // Check for moon icon (dark mode)
        $this->assertStringContainsString(
            'theme-icon-dark',
            $html,
            'Theme toggle should have dark mode icon'
        );
    }

    /**
     * Test that theme toggle widget has localStorage script for persistence
     */
    public function test_theme_toggle_widget_has_localstorage_script(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $component = Livewire::actingAs($user)
            ->test(ThemeToggleWidget::class);

        $html = $component->html();

        // Check for localStorage key
        $this->assertStringContainsString(
            'theme',
            $html,
            'Theme toggle should use localStorage key "theme"'
        );

        // Check for FOUT prevention
        $this->assertStringContainsString(
            'FOUT',
            $html,
            'Theme toggle should have FOUT prevention comment'
        );

        // Check for expiry/TTL logic
        $this->assertStringContainsString(
            'expiry',
            $html,
            'Theme toggle should implement TTL for localStorage'
        );
    }

    /**
     * Test that theme toggle widget has smooth transition styles
     */
    public function test_theme_toggle_widget_has_transition_styles(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $component = Livewire::actingAs($user)
            ->test(ThemeToggleWidget::class);

        $html = $component->html();

        // Check for transition styles
        $this->assertStringContainsString(
            'transition',
            $html,
            'Theme toggle should have smooth transitions'
        );
    }
}
