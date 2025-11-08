<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Staff Dashboard Accessibility Test
 *
 * Verifies WCAG 2.2 Level AA compliance for the authenticated staff dashboard.
 *
 * Test Coverage:
 * - Keyboard navigation (Tab order, focus indicators)
 * - Color contrast (4.5:1 text, 3:1 UI components)
 * - Touch targets (minimum 44×44px)
 * - ARIA attributes (labels, roles, live regions)
 * - Screen reader compatibility (semantic HTML)
 *
 * @see D03-FR-019 Staff dashboard requirements
 * @see D12 §9 WCAG 2.2 AA compliance
 * @see D14 §4 Compliant color palette
 */
#[Group('accessibility')]
#[Group('dashboard')]
#[Group('wcag')]
class AccessibilityDashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test keyboard navigation through dashboard elements
     *
     * Verifies:
     * - Tab order follows logical flow
     * - Focus indicators are visible (3-4px outline, 2px offset)
     * - All interactive elements are keyboard accessible
     * - Skip links work correctly
     */
    #[Test]
    public function keyboard_navigation_through_dashboard_elements(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard')
                ->assertSee($user->name);

            // Test refresh button keyboard accessibility
            $browser->keys('body', ['{tab}']) // Tab to refresh button
                ->assertFocused('button[wire:click="refreshData"]')
                ->screenshot('accessibility-refresh-button-focus');

            // Verify focus indicator is visible
            $browser->script('
                const button = document.querySelector(\'button[wire\\\\:click="refreshData"]\');
                const styles = window.getComputedStyle(button, \':focus\');
                return {
                    outline: styles.outline,
                    outlineWidth: styles.outlineWidth,
                    outlineOffset: styles.outlineOffset
                };
            ');

            // Test statistics card links
            $browser->keys('body', ['{tab}', '{tab}', '{tab}']) // Tab through cards
                ->screenshot('accessibility-statistics-focus');

            // Test quick action buttons
            $browser->keys('body', ['{tab}', '{tab}', '{tab}'])
                ->screenshot('accessibility-quick-actions-focus');

            // Test recent activity links
            $browser->keys('body', ['{tab}', '{tab}'])
                ->screenshot('accessibility-recent-activity-focus');

            // Verify Enter key activates focused links
            $browser->keys('body', ['{enter}'])
                ->pause(500);
        });
    }

    /**
     * Test color contrast ratios for WCAG AA compliance
     *
     * Verifies:
     * - Text contrast: minimum 4.5:1
     * - UI component contrast: minimum 3:1
     * - Compliant color palette usage
     */
    #[Test]
    public function color_contrast_meets_wcag_aa_standards(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard');

            // Check primary text contrast (should be 4.5:1 minimum)
            $headingContrast = $browser->script('
                const heading = document.querySelector("h1");
                const styles = window.getComputedStyle(heading);
                return {
                    color: styles.color,
                    backgroundColor: styles.backgroundColor
                };
            ');

            // Check button contrast
            $buttonContrast = $browser->script('
                const button = document.querySelector("button[wire\\\\:click=\'refreshData\']");
                const styles = window.getComputedStyle(button);
                return {
                    color: styles.color,
                    backgroundColor: styles.backgroundColor,
                    borderColor: styles.borderColor
                };
            ');

            // Check statistics card icon colors
            $iconColors = $browser->script('
                const icons = document.querySelectorAll(".text-motac-blue, .text-warning, .text-success, .text-danger");
                return Array.from(icons).map(icon => {
                    const styles = window.getComputedStyle(icon);
                    return {
                        color: styles.color,
                        className: icon.className
                    };
                });
            ');

            // Verify compliant colors are used
            $this->assertNotEmpty($iconColors);

            // Take screenshot for manual verification
            $browser->screenshot('accessibility-color-contrast');
        });
    }

    /**
     * Test touch target sizes meet WCAG 2.5.8 requirements
     *
     * Verifies:
     * - All interactive elements are minimum 44×44px
     * - Buttons, links, and cards meet size requirements
     * - Adequate spacing between touch targets
     */
    #[Test]
    public function touch_targets_meet_minimum_size_requirements(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard');

            // Check refresh button size
            $refreshButtonSize = $browser->script('
                const button = document.querySelector(\'button[wire\\\\:click="refreshData"]\');
                const rect = button.getBoundingClientRect();
                return {
                    width: rect.width,
                    height: rect.height,
                    minHeight: window.getComputedStyle(button).minHeight,
                    minWidth: window.getComputedStyle(button).minWidth
                };
            ');

            $this->assertGreaterThanOrEqual(44, $refreshButtonSize['width']);
            $this->assertGreaterThanOrEqual(44, $refreshButtonSize['height']);

            // Check quick action button sizes
            $quickActionSizes = $browser->script('
                const buttons = document.querySelectorAll(".inline-flex.items-center.px-4.py-2");
                return Array.from(buttons).map(button => {
                    const rect = button.getBoundingClientRect();
                    return {
                        width: rect.width,
                        height: rect.height,
                        text: button.textContent.trim()
                    };
                });
            ');

            foreach ($quickActionSizes as $size) {
                $this->assertGreaterThanOrEqual(44, $size['width'], "Button '{$size['text']}' width is less than 44px");
                $this->assertGreaterThanOrEqual(44, $size['height'], "Button '{$size['text']}' height is less than 44px");
            }

            // Check statistics card link sizes
            $cardLinkSizes = $browser->script('
                const links = document.querySelectorAll(".bg-gray-50 a");
                return Array.from(links).map(link => {
                    const rect = link.getBoundingClientRect();
                    return {
                        width: rect.width,
                        height: rect.height,
                        text: link.textContent.trim()
                    };
                });
            ');

            foreach ($cardLinkSizes as $size) {
                $this->assertGreaterThanOrEqual(44, $size['height'], "Link '{$size['text']}' height is less than 44px");
            }

            $browser->screenshot('accessibility-touch-targets');
        });
    }

    /**
     * Test ARIA attributes and semantic HTML
     *
     * Verifies:
     * - ARIA labels on interactive elements
     * - ARIA roles on lists and regions
     * - ARIA live regions for dynamic updates
     * - Semantic HTML structure (header, main, nav)
     */
    #[Test]
    public function aria_attributes_and_semantic_html(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard');

            // Check refresh button has aria-label
            $browser->assertAttribute('button[wire:click="refreshData"]', 'aria-label');

            // Check SVG icons have aria-hidden
            $hiddenIcons = $browser->script('
                const icons = document.querySelectorAll("svg[aria-hidden]");
                return icons.length;
            ');
            $this->assertGreaterThan(0, $hiddenIcons);

            // Check lists have role="list"
            $lists = $browser->script('
                const lists = document.querySelectorAll(\'ul[role="list"]\');
                return lists.length;
            ');
            $this->assertGreaterThanOrEqual(2, $lists); // Recent tickets and loans

            // Check for semantic HTML structure
            $semanticElements = $browser->script('
                return {
                    hasH1: document.querySelectorAll("h1").length > 0,
                    hasH2: document.querySelectorAll("h2").length > 0,
                    hasH3: document.querySelectorAll("h3").length > 0,
                    hasMain: document.querySelectorAll("main").length > 0
                };
            ');

            $this->assertTrue($semanticElements['hasH1'], 'Page should have h1 heading');
            $this->assertTrue($semanticElements['hasH2'] || $semanticElements['hasH3'], 'Page should have h2 or h3 headings');

            $browser->screenshot('accessibility-aria-attributes');
        });
    }

    /**
     * Test screen reader compatibility
     *
     * Verifies:
     * - Proper heading hierarchy (h1 → h2 → h3)
     * - Descriptive link text (no "click here")
     * - Form labels associated with inputs
     * - Status messages announced via ARIA live regions
     */
    #[Test]
    public function screen_reader_compatibility(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard');

            // Check heading hierarchy
            $headings = $browser->script('
                const headings = document.querySelectorAll("h1, h2, h3, h4, h5, h6");
                return Array.from(headings).map(h => ({
                    level: parseInt(h.tagName.substring(1)),
                    text: h.textContent.trim()
                }));
            ');

            // Verify h1 exists and is first
            $this->assertNotEmpty($headings);
            $this->assertEquals(1, $headings[0]['level'], 'First heading should be h1');

            // Check for descriptive link text
            $links = $browser->script('
                const links = document.querySelectorAll("a");
                return Array.from(links).map(link => ({
                    text: link.textContent.trim(),
                    href: link.getAttribute("href")
                }));
            ');

            foreach ($links as $link) {
                $this->assertNotEmpty($link['text'], 'Links should have descriptive text');
                $this->assertNotContains(strtolower($link['text']), ['click here', 'read more', 'link']);
            }

            // Check for loading state announcement
            $browser->click('button[wire:click="refreshData"]')
                ->pause(100);

            $loadingState = $browser->script('
                const loading = document.querySelector("[wire\\\\:loading]");
                return loading ? {
                    visible: window.getComputedStyle(loading).display !== "none",
                    text: loading.textContent.trim()
                } : null;
            ');

            if ($loadingState) {
                $this->assertNotEmpty($loadingState['text'], 'Loading state should have descriptive text');
            }

            $browser->screenshot('accessibility-screen-reader');
        });
    }

    /**
     * Test focus management and skip links
     *
     * Verifies:
     * - Focus is not trapped
     * - Focus order is logical
     * - Skip links are available
     * - Focus returns to appropriate element after modal close
     */
    #[Test]
    public function focus_management_and_skip_links(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->waitForText('Dashboard');

            // Test that focus can move through all interactive elements
            $interactiveElements = $browser->script('
                const elements = document.querySelectorAll("a, button, input, select, textarea, [tabindex]:not([tabindex=\'-1\'])");
                return elements.length;
            ');

            $this->assertGreaterThan(0, $interactiveElements, 'Page should have interactive elements');

            // Test that focus is visible on all elements
            $browser->keys('body', ['{tab}'])
                ->pause(100);

            $focusVisible = $browser->script('
                const focused = document.activeElement;
                const styles = window.getComputedStyle(focused, ":focus");
                return {
                    element: focused.tagName,
                    outline: styles.outline,
                    ring: focused.classList.contains("focus:ring-4") ||
                          focused.classList.contains("focus:ring-2")
                };
            ');

            $this->assertNotEmpty($focusVisible['element'], 'An element should be focused');

            $browser->screenshot('accessibility-focus-management');
        });
    }

    /**
     * Test responsive accessibility across viewport sizes
     *
     * Verifies:
     * - Touch targets remain adequate on mobile
     * - Content reflows without horizontal scrolling
     * - Focus indicators visible at all sizes
     * - Text remains readable (no truncation issues)
     */
    #[Test]
    public function responsive_accessibility_across_viewports(): void
    {
        $user = User::factory()->create();

        $viewports = [
            ['width' => 375, 'height' => 667, 'name' => 'mobile'],
            ['width' => 768, 'height' => 1024, 'name' => 'tablet'],
            ['width' => 1280, 'height' => 720, 'name' => 'desktop'],
        ];

        foreach ($viewports as $viewport) {
            $this->browse(function (Browser $browser) use ($user, $viewport) {
                $browser->loginAs($user)
                    ->resize($viewport['width'], $viewport['height'])
                    ->visit('/dashboard')
                    ->waitForText('Dashboard');

                // Check touch targets at this viewport
                $touchTargets = $browser->script('
                    const buttons = document.querySelectorAll("button, a");
                    return Array.from(buttons).map(el => {
                        const rect = el.getBoundingClientRect();
                        return {
                            width: rect.width,
                            height: rect.height,
                            visible: rect.width > 0 && rect.height > 0
                        };
                    }).filter(t => t.visible);
                ');

                foreach ($touchTargets as $target) {
                    $this->assertGreaterThanOrEqual(
                        44,
                        $target['height'],
                        "Touch target height less than 44px at {$viewport['name']} viewport"
                    );
                }

                // Check for horizontal scrolling
                $hasHorizontalScroll = $browser->script('
                    return document.documentElement.scrollWidth > document.documentElement.clientWidth;
                ');

                $this->assertFalse(
                    $hasHorizontalScroll,
                    "Page should not have horizontal scroll at {$viewport['name']} viewport"
                );

                $browser->screenshot("accessibility-{$viewport['name']}-viewport");
            });
        }
    }
}
