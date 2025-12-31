<?php

declare(strict_types=1);

namespace Tests\Feature\VisualRegression;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Widget Card Styling Visual Regression Tests
 *
 * Tests for Task 5.1.4 - Visual regression tests for widget card styling
 * Validates Requirements R22.2.1 (Widget cards have consistent shadow elevation)
 *
 * @trace Task 5.1.4; R22.2.1; D14 §5.3
 *
 * @version 3.6.1
 *
 * @since 2025-01-01
 */
class WidgetCardStylingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Property: Widget cards have consistent shadow elevation
     * Validates: Requirements R22.2.1
     */
    public function test_widget_cards_have_consistent_shadow_elevation(): void
    {
        // Arrange: Login as admin
        $this->actingAs($this->admin);

        // Act: Visit admin dashboard
        $response = $this->get('/admin');

        // Assert: Page loads successfully
        $response->assertOk();

        // Assert: Widget card component is available
        $response->assertViewHas('widgets');

        // Assert: CSS shadow tokens are defined
        $response->assertSee('--shadow-card');
        $response->assertSee('shadow-card');

        // Assert: Widget card styling classes are present
        $response->assertSee('widget-card');
        $response->assertSee('rounded-lg');
        $response->assertSee('p-6');
    }

    /**
     * Property: Widget card component renders with proper MyDS styling
     * Validates: Requirements R22.2.2, R22.2.3, R22.2.5
     */
    public function test_widget_card_component_renders_with_proper_myds_styling(): void
    {
        // Arrange: Create a test widget card view
        $cardHtml = view('filament.components.widget-card', [
            'title' => 'Test Widget',
            'description' => 'Test Description',
            'icon' => 'heroicon-o-chart-bar',
            'color' => 'primary',
            'interactive' => false,
        ])->render();

        // Assert: Contains proper MyDS classes
        $this->assertStringContains('widget-card', $cardHtml);
        $this->assertStringContains('bg-white dark:bg-gray-800', $cardHtml);
        $this->assertStringContains('border border-gray-200 dark:border-gray-700', $cardHtml);
        $this->assertStringContains('rounded-lg', $cardHtml); // 12px border-radius
        $this->assertStringContains('p-6', $cardHtml); // 24px internal padding
        $this->assertStringContains('shadow-card', $cardHtml); // MyDS shadow elevation

        // Assert: Contains proper ARIA attributes
        $this->assertStringContains('role="region"', $cardHtml);
        $this->assertStringContains('aria-labelledby', $cardHtml);
        $this->assertStringContains('aria-describedby', $cardHtml);

        // Assert: Contains proper typography classes
        $this->assertStringContains('text-lg font-semibold font-poppins', $cardHtml);
        $this->assertStringContains('text-sm text-gray-600 dark:text-gray-400 mb-4 font-inter', $cardHtml);
    }

    /**
     * Property: Widget card component supports all color variations
     * Validates: Requirements R22.2.4
     */
    public function test_widget_card_component_supports_all_color_variations(): void
    {
        $colors = ['primary', 'success', 'warning', 'danger', 'info'];

        foreach ($colors as $color) {
            // Arrange & Act: Render widget card with color
            $cardHtml = view('filament.components.widget-card', [
                'title' => "Test {$color} Widget",
                'color' => $color,
            ])->render();

            // Assert: Contains color-specific border accent
            if ($color !== 'primary') {
                $this->assertStringContains("border-l-4 border-l-{$color}-500", $cardHtml);
            }

            // Assert: Contains color-specific icon background
            $this->assertStringContains("bg-{$color}-50 dark:bg-{$color}-900/20", $cardHtml);
            $this->assertStringContains("text-{$color}-600 dark:text-{$color}-400", $cardHtml);
        }
    }

    /**
     * Property: Widget card component supports size variations
     * Validates: Requirements R22.2.5
     */
    public function test_widget_card_component_supports_size_variations(): void
    {
        $sizes = [
            'small' => 'p-4',
            'default' => 'p-6',
            'large' => 'p-8',
        ];

        foreach ($sizes as $size => $expectedClass) {
            // Arrange & Act: Render widget card with size
            $cardHtml = view('filament.components.widget-card', [
                'title' => "Test {$size} Widget",
                'size' => $size,
            ])->render();

            // Assert: Contains size-specific padding class
            $this->assertStringContains($expectedClass, $cardHtml);
        }
    }

    /**
     * Property: Widget card component supports interactive states
     * Validates: Requirements R22.2.6
     */
    public function test_widget_card_component_supports_interactive_states(): void
    {
        // Arrange & Act: Render interactive widget card
        $cardHtml = view('filament.components.widget-card', [
            'title' => 'Interactive Widget',
            'interactive' => true,
        ])->render();

        // Assert: Contains interactive hover classes
        $this->assertStringContains('hover:shadow-lg hover:-translate-y-0.5 cursor-pointer', $cardHtml);

        // Arrange & Act: Render non-interactive widget card
        $cardHtml = view('filament.components.widget-card', [
            'title' => 'Static Widget',
            'interactive' => false,
        ])->render();

        // Assert: Does not contain interactive classes
        $this->assertStringNotContains('cursor-pointer', $cardHtml);
    }

    /**
     * Property: Widget card component supports loading states
     * Validates: Requirements R22.2.7
     */
    public function test_widget_card_component_supports_loading_states(): void
    {
        // Arrange & Act: Render loading widget card
        $cardHtml = view('filament.components.widget-card', [
            'title' => 'Loading Widget',
            'loading' => true,
        ])->render();

        // Assert: Contains loading skeleton
        $this->assertStringContains('animate-pulse', $cardHtml);
        $this->assertStringContains('bg-gray-200 dark:bg-gray-700', $cardHtml);

        // Arrange & Act: Render normal widget card
        $cardHtml = view('filament.components.widget-card', [
            'title' => 'Normal Widget',
            'loading' => false,
        ])->render();

        // Assert: Does not contain loading skeleton
        $this->assertStringNotContains('animate-pulse', $cardHtml);
    }

    /**
     * Property: Widget views use consistent card styling
     * Validates: Requirements R22.2.1-R22.2.5
     */
    public function test_widget_views_use_consistent_card_styling(): void
    {
        $widgetViews = [
            'filament.widgets.critical-alerts',
            'filament.widgets.health-check-table',
            'filament.widgets.quick-actions',
            'filament.widgets.slow-queries-table',
            'filament.widgets.asset-availability-calendar',
            'filament.widgets.horizon-health-widget',
            'filament.widgets.theme-toggle-unified',
        ];

        foreach ($widgetViews as $viewName) {
            // Act: Check if view exists and uses widget-card component
            $this->assertTrue(view()->exists($viewName), "View {$viewName} should exist");

            // Get view content
            $viewContent = file_get_contents(resource_path("views/{$viewName}.blade.php"));

            // Assert: Uses widget-card component
            $this->assertStringContains(
                'x-filament.components.widget-card',
                $viewContent,
                "View {$viewName} should use widget-card component"
            );

            // Assert: Has proper title and description
            $this->assertStringContains(
                'title=',
                $viewContent,
                "View {$viewName} should have title attribute"
            );
        }
    }

    /**
     * Property: CSS shadow system is properly defined
     * Validates: Requirements R22.2.1
     */
    public function test_css_shadow_system_is_properly_defined(): void
    {
        // Act: Read the theme CSS file
        $cssContent = file_get_contents(resource_path('css/filament/admin/theme.css'));

        // Assert: MyDS shadow tokens are defined
        $this->assertStringContains('--shadow-card: 0px 2px 6px 0px rgba(0, 0, 0, 0.05), 0px 6px 24px 0px rgba(0, 0, 0, 0.05)', $cssContent);
        $this->assertStringContains('--shadow-button: 0px 1px 3px 0px rgba(0, 0, 0, 0.07)', $cssContent);
        $this->assertStringContains('--shadow-dropdown: 0px 4px 12px 0px rgba(0, 0, 0, 0.1), 0px 2px 6px 0px rgba(0, 0, 0, 0.05)', $cssContent);

        // Assert: Shadow utility classes are defined
        $this->assertStringContains('.shadow-card {', $cssContent);
        $this->assertStringContains('box-shadow: var(--shadow-card);', $cssContent);

        // Assert: Dark mode shadow adjustments are defined
        $this->assertStringContains('.dark .shadow-card', $cssContent);
        $this->assertStringContains('[data-theme="dark"] .shadow-card', $cssContent);
    }

    /**
     * Property: Widget card styling is consistent across themes
     * Validates: Requirements R22.2.8
     */
    public function test_widget_card_styling_is_consistent_across_themes(): void
    {
        // Test light theme
        $lightCardHtml = view('filament.components.widget-card', [
            'title' => 'Light Theme Widget',
        ])->render();

        // Assert: Contains light theme classes
        $this->assertStringContains('bg-white dark:bg-gray-800', $lightCardHtml);
        $this->assertStringContains('border-gray-200 dark:border-gray-700', $lightCardHtml);
        $this->assertStringContains('text-gray-900 dark:text-gray-100', $lightCardHtml);

        // The dark theme styling is handled by CSS classes, so we verify the classes are present
        // The actual theme switching is tested in the theme toggle widget tests
    }

    /**
     * Helper method to assert string contains substring
     */
    private function assertStringContains(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertTrue(
            str_contains($haystack, $needle),
            $message ?: "Failed asserting that '{$haystack}' contains '{$needle}'"
        );
    }

    /**
     * Helper method to assert string does not contain substring
     */
    private function assertStringNotContains(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertFalse(
            str_contains($haystack, $needle),
            $message ?: "Failed asserting that '{$haystack}' does not contain '{$needle}'"
        );
    }
}
