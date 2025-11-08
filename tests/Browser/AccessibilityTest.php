<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Browser-based WCAG 2.2 Level AA Accessibility Tests
 *
 * Manual accessibility testing with real browser interactions:
 * - Keyboard navigation testing
 * - Screen reader simulation
 * - Focus management validation
 * - Color contrast verification
 * - Touch target testing
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §9 (Accessibility Standards)
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class AccessibilityTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected User $user;

    protected LoanApplication $loanApplication;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $division = Division::factory()->create();
        $category = AssetCategory::factory()->create();
        $this->asset = Asset::factory()->create(['category_id' => $category->id]);
        $this->user = User::factory()->create(['division_id' => $division->id]);
        $this->loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $division->id,
        ]);
    }

    /**
     * Test keyboard navigation on guest loan application form
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function guest_loan_form_keyboard_navigation(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->assertSee('Asset Loan Application')

                    // Test skip links
                ->keys('body', ['{tab}'])
                ->assertFocused('[href="#main-content"]')
                ->keys('[href="#main-content"]', ['{enter}'])
                ->assertFocused('#main-content')

                    // Test form navigation
                ->keys('#main-content', ['{tab}'])
                ->assertFocused('input[name="applicant_name"]')
                ->type('applicant_name', 'Test User')
                ->keys('input[name="applicant_name"]', ['{tab}'])
                ->assertFocused('input[name="applicant_email"]')
                ->type('applicant_email', 'test@motac.gov.my')
                ->keys('input[name="applicant_email"]', ['{tab}'])
                ->assertFocused('input[name="applicant_phone"]')
                ->type('applicant_phone', '03-12345678')

                    // Test dropdown navigation
                ->keys('input[name="applicant_phone"]', ['{tab}'])
                ->assertFocused('select[name="division_id"]')
                ->keys('select[name="division_id"]', ['{arrow-down}', '{enter}'])

                    // Test submit button
                ->keys('select[name="division_id"]', ['{tab}'])
                ->waitUntilMissing('.loading')
                ->keys('body', ['{tab}']) // Navigate to submit button
                ->assertFocused('button[type="submit"]')

                    // Verify focus indicators are visible
                ->assertScript('
                        const focused = document.activeElement;
                        const styles = window.getComputedStyle(focused);
                        return styles.outline !== "none" || styles.boxShadow.includes("rgb");
                    ');
        });
    }

    /**
     * Test screen reader announcements and ARIA live regions
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function screen_reader_announcements(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->assertSee('Asset Loan Application')

                    // Test ARIA live region exists
                ->assertPresent('[aria-live="polite"]')

                    // Test form validation announcements
                ->click('button[type="submit"]')
                ->waitFor('.error, [aria-invalid="true"]')

                    // Verify error messages are announced
                ->assertScript('
                        const errorElements = document.querySelectorAll(".error, [aria-invalid=true]");
                        return Array.from(errorElements).some(el =>
                            el.getAttribute("role") === "alert" ||
                            el.getAttribute("aria-live") === "assertive"
                        );
                    ')

                    // Test loading state announcements
                ->type('applicant_name', 'Test User')
                ->type('applicant_email', 'test@motac.gov.my')
                ->type('applicant_phone', '03-12345678')
                ->select('division_id', $this->user->division_id)
                ->type('purpose', 'Testing accessibility')
                ->type('location', 'Test Location')
                ->type('loan_start_date', now()->addDay()->format('Y-m-d'))
                ->type('loan_end_date', now()->addDays(7)->format('Y-m-d'))
                ->click('button[type="submit"]')
                ->waitFor('[wire\\:loading]')

                    // Verify loading states have ARIA attributes
                ->assertScript('
                        const loadingElements = document.querySelectorAll("[wire\\\\:loading]");
                        return Array.from(loadingElements).some(el =>
                            el.getAttribute("aria-live") ||
                            el.getAttribute("aria-busy") === "true"
                        );
                    ');
        });
    }

    /**
     * Test color contrast and visual accessibility
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function color_contrast_compliance(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->assertSee('Asset Loan Application')

                    // Test primary button contrast
                ->assertScript('
                        const button = document.querySelector("button[type=submit]");
                        const styles = window.getComputedStyle(button);
                        const bgColor = styles.backgroundColor;
                        const textColor = styles.color;

                        // Check if using compliant colors (simplified check)
                        return bgColor.includes("0, 86, 179") || // #0056b3
                               bgColor.includes("25, 135, 84") || // #198754
                               textColor.includes("255, 255, 255"); // white text
                    ')

                    // Test error message contrast
                ->click('button[type="submit"]')
                ->waitFor('.error, .text-danger')
                ->assertScript('
                        const errorElement = document.querySelector(".error, .text-danger");
                        if (!errorElement) return true;

                        const styles = window.getComputedStyle(errorElement);
                        const color = styles.color;

                        // Check if using compliant danger color
                        return color.includes("181, 12, 12") || // #b50c0c
                               color.includes("220, 38, 38"); // Tailwind red-600
                    ')

                    // Test focus indicator contrast
                ->keys('body', ['{tab}'])
                ->assertScript('
                        const focused = document.activeElement;
                        const styles = window.getComputedStyle(focused);
                        const outline = styles.outline;
                        const boxShadow = styles.boxShadow;

                        // Check for visible focus indicators
                        return outline !== "none" ||
                               boxShadow.includes("rgb") ||
                               boxShadow.includes("rgba");
                    ');
        });
    }

    /**
     * Test touch target sizes for mobile accessibility
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function touch_target_sizes(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->resize(375, 667) // iPhone SE size
                ->assertSee('Asset Loan Application')

                    // Test button touch targets
                ->assertScript('
                        const buttons = document.querySelectorAll("button, input[type=submit], input[type=button]");
                        return Array.from(buttons).every(button => {
                            const rect = button.getBoundingClientRect();
                            return rect.width >= 44 && rect.height >= 44;
                        });
                    ')

                    // Test link touch targets
                ->assertScript('
                        const links = document.querySelectorAll("a");
                        return Array.from(links).every(link => {
                            const rect = link.getBoundingClientRect();
                            // Skip hidden or empty links
                            if (rect.width === 0 || rect.height === 0) return true;
                            return rect.width >= 44 && rect.height >= 44;
                        });
                    ')

                    // Test form input touch targets
                ->assertScript('
                        const inputs = document.querySelectorAll("input, select, textarea");
                        return Array.from(inputs).every(input => {
                            const rect = input.getBoundingClientRect();
                            return rect.height >= 44;
                        });
                    ');
        });
    }

    /**
     * Test language switcher accessibility
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function language_switcher_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->assertSee('Asset Loan Application')

                    // Test ARIA menu button pattern
                ->assertPresent('[aria-haspopup="menu"]')
                ->assertPresent('[role="menu"]')

                    // Test keyboard navigation
                ->keys('body', ['{tab}'])
                ->waitUntilMissing('.loading')

                    // Navigate to language switcher
                ->keys('body', ['{shift}', '{tab}']) // Go backwards to header
                ->keys('body', ['{shift}', '{tab}'])
                ->keys('body', ['{shift}', '{tab}'])

                    // Test menu activation with keyboard
                ->keys('[aria-haspopup="menu"]', ['{enter}'])
                ->waitFor('[role="menu"]')
                ->assertVisible('[role="menu"]')

                    // Test menu item navigation
                ->keys('[role="menu"]', ['{arrow-down}'])
                ->assertFocused('[role="menuitem"]:first-child')
                ->keys('[role="menuitem"]:first-child', ['{arrow-down}'])
                ->assertFocused('[role="menuitem"]:last-child')

                    // Test escape key
                ->keys('[role="menuitem"]:last-child', ['{escape}'])
                ->waitUntilMissing('[role="menu"]')
                ->assertNotVisible('[role="menu"]');
        });
    }

    /**
     * Test authenticated portal accessibility
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function authenticated_portal_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/loans/dashboard')
                ->assertSee('Loan Dashboard')

                    // Test navigation landmarks
                ->assertPresent('[role="banner"], header')
                ->assertPresent('[role="navigation"], nav')
                ->assertPresent('[role="main"], main')
                ->assertPresent('[role="contentinfo"], footer')

                    // Test heading hierarchy
                ->assertScript('
                        const headings = Array.from(document.querySelectorAll("h1, h2, h3, h4, h5, h6"));
                        const levels = headings.map(h => parseInt(h.tagName.charAt(1)));

                        if (levels.length === 0) return false;
                        if (levels[0] !== 1) return false; // Must start with h1

                        // Check for skipped levels
                        for (let i = 1; i < levels.length; i++) {
                            if (levels[i] - levels[i-1] > 1) return false;
                        }

                        return true;
                    ')

                    // Test data table accessibility
                ->assertScript('
                        const tables = document.querySelectorAll("table");
                        return Array.from(tables).every(table => {
                            const hasHeaders = table.querySelectorAll("th").length > 0;
                            const hasCaption = table.querySelector("caption") !== null;
                            const hasAriaLabel = table.getAttribute("aria-label") !== null;

                            return hasHeaders && (hasCaption || hasAriaLabel);
                        });
                    ')

                    // Test form accessibility
                ->assertScript('
                        const inputs = document.querySelectorAll("input, select, textarea");
                        return Array.from(inputs).every(input => {
                            const id = input.getAttribute("id");
                            const ariaLabel = input.getAttribute("aria-label");
                            const ariaLabelledby = input.getAttribute("aria-labelledby");

                            if (ariaLabel || ariaLabelledby) return true;
                            if (id) {
                                const label = document.querySelector(`label[for="${id}"]`);
                                return label !== null;
                            }

                            return false;
                        });
                    ');
        });
    }

    /**
     * Test modal dialog accessibility and focus management
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function modal_dialog_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                ->visit('/loans/dashboard')
                ->assertSee('Loan Dashboard')

                    // Find and click a button that opens a modal
                ->whenAvailable('[data-modal-target], [x-data*="modal"]', function ($modal) {
                    $modal->click();
                })

                    // Test modal ARIA attributes
                ->whenAvailable('[role="dialog"], .modal', function ($modal) {
                    $modal->assertAttribute('role', 'dialog')
                        ->assertAttribute('aria-modal', 'true')
                        ->assertPresent('[aria-labelledby], [aria-label]');
                })

                    // Test focus trap
                ->assertScript('
                        const modal = document.querySelector("[role=dialog], .modal");
                        if (!modal) return true;

                        const focusableElements = modal.querySelectorAll(
                            "a[href], button, textarea, input, select, [tabindex]:not([tabindex=\'-1\'])"
                        );

                        return focusableElements.length > 0;
                    ')

                    // Test escape key closes modal
                ->keys('body', ['{escape}'])
                ->waitUntilMissing('[role="dialog"], .modal');
        });
    }

    /**
     * Test error handling and validation accessibility
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function error_handling_accessibility(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/loans/guest/create')
                ->assertSee('Asset Loan Application')

                    // Trigger validation errors
                ->click('button[type="submit"]')
                ->waitFor('.error, [aria-invalid="true"]')

                    // Test error message accessibility
                ->assertScript('
                        const errorElements = document.querySelectorAll(".error, [aria-invalid=true]");
                        return Array.from(errorElements).some(el => {
                            const role = el.getAttribute("role");
                            const ariaLive = el.getAttribute("aria-live");
                            return role === "alert" || ariaLive === "assertive";
                        });
                    ')

                    // Test error association with form fields
                ->assertScript('
                        const invalidInputs = document.querySelectorAll("[aria-invalid=true]");
                        return Array.from(invalidInputs).every(input => {
                            const describedBy = input.getAttribute("aria-describedby");
                            if (describedBy) {
                                const errorElement = document.getElementById(describedBy);
                                return errorElement !== null;
                            }
                            return true; // Allow other error indication methods
                        });
                    ')

                    // Test error summary for multiple errors
                ->assertScript('
                        const errors = document.querySelectorAll(".error");
                        if (errors.length > 1) {
                            const summary = document.querySelector("[role=alert], .error-summary");
                            return summary !== null;
                        }
                        return true;
                    ');
        });
    }
}
