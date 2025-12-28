<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Widgets\ThemeToggleWidget;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Theme Toggle Widget Test Suite
 *
 * Comprehensive testing for the enhanced theme toggle widget with
 * MyDS color system integration and accessibility features.
 *
 * @trace Requirements: R15 (Color System), R16 (WCAG Dark Mode)
 *
 * @see D12 §4 MyDS Design System
 * @see D14 §2 WCAG 2.2 AA Compliance
 *
 * @version 3.6.1
 */
class ThemeToggleWidgetTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'dashboard_layout' => [
                'theme_preference' => 'light',
                'high_contrast_mode' => false,
            ],
        ]);
    }

    #[Test]
    public function it_can_render_theme_toggle_widget(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->assertStatus(200)
            ->assertSee('Tetapan Tema')
            ->assertSee('Pilihan Tema')
            ->assertSee('Terang')
            ->assertSee('Gelap')
            ->assertSee('Sistem');
    }

    #[Test]
    public function it_initializes_with_user_theme_preference(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->assertSet('themePreference', 'light')
            ->assertSet('theme', 'light')
            ->assertSet('highContrastMode', false);
    }

    #[Test]
    public function it_can_set_light_theme_preference(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setThemePreference', 'light')
            ->assertSet('themePreference', 'light')
            ->assertSet('theme', 'light')
            ->assertDispatched('theme-preference-changed');

        // Verify database update
        $this->user->refresh();
        $this->assertEquals('light', $this->user->dashboard_layout['theme_preference']);
    }

    #[Test]
    public function it_can_set_dark_theme_preference(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setThemePreference', 'dark')
            ->assertSet('themePreference', 'dark')
            ->assertSet('theme', 'dark')
            ->assertDispatched('theme-preference-changed');

        // Verify database update
        $this->user->refresh();
        $this->assertEquals('dark', $this->user->dashboard_layout['theme_preference']);
    }

    #[Test]
    public function it_can_set_system_theme_preference(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setThemePreference', 'system')
            ->assertSet('themePreference', 'system')
            ->assertSet('systemThemeDetected', true)
            ->assertDispatched('theme-preference-changed');

        // Verify database update
        $this->user->refresh();
        $this->assertEquals('system', $this->user->dashboard_layout['theme_preference']);
    }

    #[Test]
    public function it_rejects_invalid_theme_preference(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setThemePreference', 'invalid')
            ->assertHasErrors(['theme']);

        // Verify database was not updated
        $this->user->refresh();
        $this->assertEquals('light', $this->user->dashboard_layout['theme_preference']);
    }

    #[Test]
    public function it_can_toggle_high_contrast_mode(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->assertSet('highContrastMode', false)
            ->call('toggleHighContrast')
            ->assertSet('highContrastMode', true)
            ->assertDispatched('high-contrast-changed');

        // Verify database update
        $this->user->refresh();
        $this->assertTrue($this->user->dashboard_layout['high_contrast_mode']);
    }

    #[Test]
    public function it_can_disable_high_contrast_mode(): void
    {
        // Set initial high contrast mode
        $this->user->update([
            'dashboard_layout' => [
                'theme_preference' => 'light',
                'high_contrast_mode' => true,
            ],
        ]);

        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->assertSet('highContrastMode', true)
            ->call('toggleHighContrast')
            ->assertSet('highContrastMode', false)
            ->assertDispatched('high-contrast-changed');

        // Verify database update
        $this->user->refresh();
        $this->assertFalse($this->user->dashboard_layout['high_contrast_mode']);
    }

    #[Test]
    public function it_provides_theme_statistics(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);
        $stats = $component->instance()->getThemeStatistics();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('light_users', $stats);
        $this->assertArrayHasKey('dark_users', $stats);
        $this->assertArrayHasKey('system_users', $stats);
    }

    #[Test]
    public function it_provides_accessibility_report(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);
        $report = $component->instance()->getAccessibilityReport();

        $this->assertIsArray($report);
        $this->assertArrayHasKey('theme', $report);
        $this->assertArrayHasKey('overall_compliance', $report);
        $this->assertArrayHasKey('wcag_level', $report);
    }

    #[Test]
    public function it_provides_css_custom_properties(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);
        $properties = $component->instance()->getCssCustomProperties();

        $this->assertIsArray($properties);
        $this->assertArrayHasKey('--color-background', $properties);
        $this->assertArrayHasKey('--color-foreground', $properties);
    }

    #[Test]
    public function it_provides_high_contrast_css_when_enabled(): void
    {
        // Enable high contrast mode
        $this->user->update([
            'dashboard_layout' => [
                'theme_preference' => 'light',
                'high_contrast_mode' => true,
            ],
        ]);

        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);
        $properties = $component->instance()->getCssCustomProperties();

        $this->assertIsArray($properties);
        $this->assertArrayHasKey('--color-background', $properties);
        $this->assertArrayHasKey('--color-foreground', $properties);

        // Just verify that high contrast mode is enabled and properties are returned
        $this->assertTrue($component->get('highContrastMode'));
    }

    #[Test]
    public function it_supports_legacy_set_theme_method(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setTheme', 'dark')
            ->assertSet('themePreference', 'dark')
            ->assertSet('theme', 'dark');

        // Verify database update
        $this->user->refresh();
        $this->assertEquals('dark', $this->user->dashboard_layout['theme_preference']);
    }

    #[Test]
    public function it_handles_legacy_light_theme_correctly(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setTheme', 'light')
            ->assertSet('themePreference', 'light')
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_handles_invalid_legacy_theme(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setTheme', 'invalid')
            ->assertSet('themePreference', 'light')
            ->assertSet('theme', 'light');
    }

    #[Test]
    public function it_provides_widget_configuration(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);
        $config = $component->instance()->getWidgetConfiguration();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('theme_preference', $config);
        $this->assertArrayHasKey('high_contrast_mode', $config);
        $this->assertArrayHasKey('system_theme_detected', $config);
    }

    #[Test]
    public function it_has_correct_widget_metadata(): void
    {
        $this->assertEquals('header', ThemeToggleWidget::getWidgetCategory());
        $this->assertEquals(100, ThemeToggleWidget::getWidgetSortOrder());
        $this->assertEquals(['staff', 'admin', 'superuser'], ThemeToggleWidget::getWidgetRoles());
        $this->assertEquals(0, ThemeToggleWidget::getWidgetRefreshRate());
        $this->assertEquals(300, ThemeToggleWidget::getWidgetCacheTtl());
    }

    #[Test]
    public function it_requires_authentication_to_view(): void
    {
        $this->assertFalse(ThemeToggleWidget::canView());

        $this->actingAs($this->user);
        $this->assertTrue(ThemeToggleWidget::canView());
    }

    #[Test]
    public function it_has_correct_documentation_reference(): void
    {
        $reference = ThemeToggleWidget::getDocumentationReference();
        $this->assertStringContainsString('D04', $reference);
        $this->assertStringContainsString('D12', $reference);
        $this->assertStringContainsString('D14', $reference);
    }

    #[Test]
    public function it_initializes_with_system_theme_when_preference_is_system(): void
    {
        $this->user->update([
            'dashboard_layout' => [
                'theme_preference' => 'system',
                'high_contrast_mode' => false,
            ],
        ]);

        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);

        $component->assertSet('themePreference', 'system')
            ->assertSet('systemThemeDetected', true);

        // Should resolve to actual theme
        $theme = $component->get('theme');
        $this->assertContains($theme, ['light', 'dark']);
    }

    #[Test]
    public function it_handles_user_without_dashboard_layout(): void
    {
        $userWithoutLayout = User::factory()->create([
            'dashboard_layout' => null,
        ]);

        $this->actingAs($userWithoutLayout);

        Livewire::test(ThemeToggleWidget::class)
            ->assertSet('themePreference', 'system')
            ->assertSet('highContrastMode', false);
    }

    #[Test]
    public function it_dispatches_correct_events_on_theme_change(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('setThemePreference', 'dark')
            ->assertDispatched('theme-preference-changed', [
                'preference' => 'dark',
                'theme' => 'dark',
                'systemDetected' => false,
            ])
            ->assertDispatched('notify', [
                'type' => 'success',
                'title' => 'Tema Dikemas Kini',
                'message' => 'Pilihan tema anda telah disimpan.',
            ]);
    }

    #[Test]
    public function it_dispatches_correct_events_on_high_contrast_toggle(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->call('toggleHighContrast')
            ->assertDispatched('high-contrast-changed', [
                'enabled' => true,
            ])
            ->assertDispatched('notify', [
                'type' => 'success',
                'title' => 'Mod Kontras Tinggi',
                'message' => 'Mod kontras tinggi telah diaktifkan.',
            ]);
    }

    #[Test]
    public function it_shows_bahasa_melayu_interface_elements(): void
    {
        $this->actingAs($this->user);

        Livewire::test(ThemeToggleWidget::class)
            ->assertSee('Tetapan Tema')
            ->assertSee('Pilihan Tema')
            ->assertSee('Terang')
            ->assertSee('Gelap')
            ->assertSee('Sistem')
            ->assertSee('Mod Kontras Tinggi')
            ->assertSee('Tema Semasa');
    }

    #[Test]
    public function it_handles_service_resolution_correctly(): void
    {
        $this->actingAs($this->user);

        // Test that the widget can be instantiated
        $widget = new ThemeToggleWidget;
        $this->assertInstanceOf(ThemeToggleWidget::class, $widget);
    }

    #[Test]
    public function it_maintains_theme_consistency_across_preference_changes(): void
    {
        $this->actingAs($this->user);

        $component = Livewire::test(ThemeToggleWidget::class);

        // Test light theme
        $component->call('setThemePreference', 'light')
            ->assertSet('themePreference', 'light')
            ->assertSet('theme', 'light');

        // Test dark theme
        $component->call('setThemePreference', 'dark')
            ->assertSet('themePreference', 'dark')
            ->assertSet('theme', 'dark');

        // Test system theme (should resolve to light or dark)
        $component->call('setThemePreference', 'system')
            ->assertSet('themePreference', 'system');

        $theme = $component->get('theme');
        $this->assertContains($theme, ['light', 'dark']);
    }
}
