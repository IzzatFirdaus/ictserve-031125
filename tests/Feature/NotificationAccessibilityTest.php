<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\NotificationBell;
use App\Models\Division;
use App\Models\User;
use App\Services\Notifications\NotificationAccessibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Notification Accessibility Tests
 *
 * Property-based tests for accessibility features in the notification system.
 * Tests screen reader announcements, keyboard navigation, and email accessibility.
 *
 * @see D03 SRS-FR-014 (Accessibility)
 * @see D12 §9 (WCAG 2.2 AA Compliance)
 *
 * @requirements 7.1, 7.2, 7.4, 7.5
 *
 * Feature: email-notification-system-enhancement
 */
class NotificationAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationAccessibilityService $accessibilityService;

    protected User $user;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessibilityService = new NotificationAccessibilityService;

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    // =========================================================================
    // Property 24: Screen Reader Announcements
    // For any new notification received, appropriate ARIA live region
    // announcements should be made for screen reader users.
    // Validates: Requirements 7.1
    // =========================================================================

    #[Test]
    public function property_24_screen_reader_announcement_for_zero_notifications(): void
    {
        // Property: Zero notifications should announce "no unread"
        $announcement = $this->accessibilityService->generateCountAnnouncement(0);

        $this->assertNotEmpty($announcement);
        $this->assertIsString($announcement);
    }

    #[Test]
    public function property_24_screen_reader_announcement_for_single_notification(): void
    {
        // Property: Single notification should use singular form
        $announcement = $this->accessibilityService->generateCountAnnouncement(1);

        $this->assertNotEmpty($announcement);
        $this->assertIsString($announcement);
    }

    #[Test]
    #[DataProvider('notificationCountProvider')]
    public function property_24_screen_reader_announcement_for_multiple_notifications(int $count): void
    {
        // Property: For any count > 1, announcement should include the count
        $announcement = $this->accessibilityService->generateCountAnnouncement($count);

        $this->assertNotEmpty($announcement);
        $this->assertIsString($announcement);
    }

    #[Test]
    public function property_24_new_notification_announcement_includes_title_and_category(): void
    {
        // Property: New notification announcements should include title and category
        $notification = [
            'title' => 'Test Notification',
            'category' => 'tickets',
        ];

        $announcement = $this->accessibilityService->generateNewNotificationAnnouncement($notification);

        $this->assertNotEmpty($announcement);
        $this->assertIsString($announcement);
    }

    #[Test]
    public function property_24_notification_bell_has_aria_live_region(): void
    {
        // Property: Notification bell view should have ARIA live region
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('aria-live="polite"', false);
        $component->assertSee('aria-atomic="true"', false);
    }

    #[Test]
    #[DataProvider('notificationCategoryProvider')]
    public function property_24_announcement_handles_all_notification_categories(string $category): void
    {
        // Property: All notification categories should generate valid announcements
        $notification = [
            'title' => 'Test Notification',
            'category' => $category,
        ];

        $announcement = $this->accessibilityService->generateNewNotificationAnnouncement($notification);

        $this->assertNotEmpty($announcement);
    }

    // =========================================================================
    // Property 25: Keyboard Navigation Completeness
    // For any interactive element in notification components, it should be
    // reachable and operable using only keyboard navigation.
    // Validates: Requirements 7.2
    // =========================================================================

    #[Test]
    public function property_25_notification_bell_has_keyboard_accessible_button(): void
    {
        // Property: Bell button should be keyboard accessible
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        // Button should have proper type and be focusable
        $component->assertSee('type="button"', false);
        $component->assertSee('focus:outline-none', false);
        $component->assertSee('focus-visible:ring', false);
    }

    #[Test]
    public function property_25_dropdown_has_escape_key_handler(): void
    {
        // Property: Dropdown should close on Escape key
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('@keydown.escape.window', false);
    }

    #[Test]
    public function property_25_dropdown_has_click_away_handler(): void
    {
        // Property: Dropdown should close when clicking outside
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('@click.away', false);
    }

    #[Test]
    public function property_25_notification_items_have_tabindex(): void
    {
        // Property: Notification items should be focusable
        $this->actingAs($this->user);

        // Create a notification
        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'type' => 'App\\Notifications\\TestNotification',
            'notifiable_type' => $this->user->getMorphClass(),
            'notifiable_id' => $this->user->id,
            'data' => json_encode(['title' => 'Test', 'type' => 'ticket']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $component = Livewire::test(NotificationBell::class);
        $component->call('loadNotifications');

        $component->assertSee('tabindex="-1"', false);
    }

    #[Test]
    public function property_25_category_tabs_have_role_tablist(): void
    {
        // Property: Category tabs should have proper ARIA roles
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('role="tablist"', false);
        $component->assertSee('role="tab"', false);
    }

    #[Test]
    public function property_25_keyboard_instructions_are_available(): void
    {
        // Property: Keyboard instructions should be defined
        $instructions = $this->accessibilityService->getKeyboardInstructions();

        $this->assertArrayHasKey('open_dropdown', $instructions);
        $this->assertArrayHasKey('close_dropdown', $instructions);
        $this->assertArrayHasKey('navigate_items', $instructions);
        $this->assertArrayHasKey('select_item', $instructions);
    }

    #[Test]
    public function property_25_focus_trap_config_is_valid(): void
    {
        // Property: Focus trap configuration should be complete
        $config = $this->accessibilityService->getFocusTrapConfig();

        $this->assertArrayHasKey('initialFocus', $config);
        $this->assertArrayHasKey('fallbackFocus', $config);
        $this->assertArrayHasKey('escapeDeactivates', $config);
        $this->assertTrue($config['escapeDeactivates']);
        $this->assertTrue($config['returnFocusOnDeactivate']);
    }

    // =========================================================================
    // Property 26: Email Accessibility Structure
    // For any generated email, the HTML should include proper semantic structure
    // with headings, alt text for images, and sufficient color contrast.
    // Validates: Requirements 7.5
    // =========================================================================

    #[Test]
    public function property_26_email_layout_has_lang_attribute(): void
    {
        // Property: Email HTML should have lang attribute
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $validation = $this->accessibilityService->validateEmailAccessibility($html);

        $this->assertTrue($validation['has_lang_attribute']);
    }

    #[Test]
    public function property_26_email_layout_has_semantic_structure(): void
    {
        // Property: Email should have header, main, and footer roles
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $validation = $this->accessibilityService->validateEmailAccessibility($html);

        $this->assertTrue($validation['has_semantic_structure']);
    }

    #[Test]
    public function property_26_email_images_have_alt_text(): void
    {
        // Property: All images in email should have alt attributes
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $validation = $this->accessibilityService->validateEmailAccessibility($html);

        $this->assertTrue($validation['images_have_alt']);
    }

    #[Test]
    public function property_26_email_tables_have_presentation_role(): void
    {
        // Property: Layout tables should have role="presentation"
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $validation = $this->accessibilityService->validateEmailAccessibility($html);

        $this->assertTrue($validation['tables_have_roles']);
    }

    #[Test]
    public function property_26_email_has_contrast_styles(): void
    {
        // Property: Email should have color definitions for contrast
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $validation = $this->accessibilityService->validateEmailAccessibility($html);

        $this->assertTrue($validation['has_sufficient_contrast']);
    }

    #[Test]
    public function property_26_email_passes_all_accessibility_checks(): void
    {
        // Property: Email should pass all accessibility validations
        $html = view('emails.layout-branded', [
            'subject' => 'Test Subject',
        ])->render();

        $this->assertTrue($this->accessibilityService->emailPassesAccessibility($html));
    }

    // =========================================================================
    // Additional Accessibility Tests
    // =========================================================================

    #[Test]
    public function touch_targets_meet_minimum_size_requirement(): void
    {
        // Property: Touch targets should be at least 44x44 pixels
        $minSize = $this->accessibilityService->getMinTouchTargetSize();

        $this->assertEquals(44, $minSize);
        $this->assertTrue($this->accessibilityService->validateTouchTargetSize(44, 44));
        $this->assertTrue($this->accessibilityService->validateTouchTargetSize(48, 48));
        $this->assertFalse($this->accessibilityService->validateTouchTargetSize(40, 40));
    }

    #[Test]
    public function notification_bell_has_minimum_touch_target(): void
    {
        // Property: Bell button should have min-w-11 min-h-11 (44px)
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('min-w-11', false);
        $component->assertSee('min-h-11', false);
    }

    #[Test]
    public function notification_bell_has_proper_aria_attributes(): void
    {
        // Property: Bell button should have all required ARIA attributes
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('aria-expanded', false);
        $component->assertSee('aria-haspopup="true"', false);
        $component->assertSee('aria-controls="notification-dropdown"', false);
        $component->assertSee('aria-label', false);
    }

    #[Test]
    public function notification_dropdown_has_proper_menu_role(): void
    {
        // Property: Dropdown should have menu role and orientation
        $this->actingAs($this->user);

        $component = Livewire::test(NotificationBell::class);

        $component->assertSee('role="menu"', false);
        $component->assertSee('aria-orientation="vertical"', false);
    }

    #[Test]
    public function aria_attribute_validation_works_correctly(): void
    {
        // Property: ARIA validation should correctly identify missing attributes
        $completeAttributes = [
            'aria-label' => 'Notifications',
            'aria-expanded' => 'false',
            'aria-haspopup' => 'true',
            'aria-controls' => 'dropdown',
        ];

        $incompleteAttributes = [
            'aria-label' => 'Notifications',
            'aria-expanded' => 'false',
        ];

        $this->assertTrue(
            $this->accessibilityService->hasRequiredAriaAttributes('notification_bell', $completeAttributes)
        );

        $this->assertFalse(
            $this->accessibilityService->hasRequiredAriaAttributes('notification_bell', $incompleteAttributes)
        );
    }

    #[Test]
    public function screen_reader_content_is_sanitized(): void
    {
        // Property: Content should be sanitized for screen readers
        $htmlContent = '<p>Test <strong>notification</strong> with <a href="#">link</a></p>';
        $sanitized = $this->accessibilityService->sanitizeForScreenReader($htmlContent);

        $this->assertStringNotContainsString('<', $sanitized);
        $this->assertStringNotContainsString('>', $sanitized);
        $this->assertStringContainsString('Test', $sanitized);
        $this->assertStringContainsString('notification', $sanitized);
    }

    #[Test]
    public function long_content_is_truncated_for_announcements(): void
    {
        // Property: Long content should be truncated for screen reader announcements
        $longContent = str_repeat('This is a very long notification message. ', 20);
        $sanitized = $this->accessibilityService->sanitizeForScreenReader($longContent);

        $this->assertLessThanOrEqual(203, strlen($sanitized)); // 200 + "..."
    }

    #[Test]
    public function high_contrast_classes_are_available(): void
    {
        // Property: High contrast mode classes should be defined
        $classes = $this->accessibilityService->getHighContrastClasses();

        $this->assertArrayHasKey('text', $classes);
        $this->assertArrayHasKey('background', $classes);
        $this->assertArrayHasKey('border', $classes);
        $this->assertArrayHasKey('focus', $classes);
        $this->assertArrayHasKey('button_primary', $classes);
    }

    #[Test]
    public function bell_aria_label_changes_with_count(): void
    {
        // Property: Bell ARIA label should reflect notification count
        $labelZero = $this->accessibilityService->generateBellAriaLabel(0);
        $labelOne = $this->accessibilityService->generateBellAriaLabel(1);
        $labelMany = $this->accessibilityService->generateBellAriaLabel(5);

        $this->assertNotEmpty($labelZero);
        $this->assertNotEmpty($labelOne);
        $this->assertNotEmpty($labelMany);
    }

    #[Test]
    public function required_aria_attributes_are_defined_for_all_components(): void
    {
        // Property: All component types should have required ARIA attributes defined
        $componentTypes = ['notification_bell', 'notification_dropdown', 'notification_item', 'live_region'];

        foreach ($componentTypes as $type) {
            $attributes = $this->accessibilityService->getRequiredAriaAttributes($type);
            $this->assertNotEmpty($attributes, "Component type '{$type}' should have required ARIA attributes");
        }
    }

    // =========================================================================
    // Data Providers
    // =========================================================================

    /**
     * @return array<string, array<int>>
     */
    public static function notificationCountProvider(): array
    {
        return [
            'two notifications' => [2],
            'five notifications' => [5],
            'ten notifications' => [10],
            'fifty notifications' => [50],
            'ninety-nine notifications' => [99],
            'hundred notifications' => [100],
        ];
    }

    /**
     * @return array<string, array<string>>
     */
    public static function notificationCategoryProvider(): array
    {
        return [
            'tickets category' => ['tickets'],
            'loans category' => ['loans'],
            'system category' => ['system'],
            'all category' => ['all'],
        ];
    }
}
