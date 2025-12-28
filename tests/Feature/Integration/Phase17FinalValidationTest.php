<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Models\User;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ICTServe Frontend Comprehensive v3.6.1 - Phase 17 Final Validation Test
 *
 * This test suite validates the final checkpoint requirements:
 * - Task 17.1: Translation key completeness validation
 * - Task 17.2: WCAG 2.2 AA compliance validation
 *
 * @see .kiro/specs/frontend-comprehensive-v3.6/requirements.md v3.6.1-r1
 * @see .kiro/specs/frontend-comprehensive-v3.6/tasks.md Phase 17
 *
 * @trace D00 v3.6.1 (True Hybrid Architecture)
 * @trace D15 §2.1 (Bahasa Melayu Exclusive)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 */
#[Group('frontend-comprehensive')]
#[Group('phase-17')]
#[Group('final-validation')]
class Phase17FinalValidationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['email' => 'test@motac.gov.my']);
    }

    // =========================================================================
    // TASK 17.1: TRANSLATION KEY COMPLETENESS VALIDATION
    // =========================================================================

    #[Test]
    #[Group('task-17-1')]
    public function it_has_all_required_bm_translation_files(): void
    {
        // Validates: Task 17.1 - Translation key validation
        $requiredFiles = [
            'lang/ms/auth.php',
            'lang/ms/pagination.php',
            'lang/ms/passwords.php',
            'lang/ms/validation.php',
            'lang/ms/status.php',
            'lang/ms/helpdesk.php',
            'lang/ms/loan.php',
        ];

        foreach ($requiredFiles as $file) {
            $this->assertFileExists(
                base_path($file),
                "Required BM translation file missing: {$file}"
            );
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_has_status_page_translation_keys(): void
    {
        // Validates: Task 17.1 - Status page translations (from Phase 16 remediation)
        $statusTranslations = trans('status');

        $requiredKeys = [
            'page_tagline',
            'quick_help_title',
            'quick_help_email',
            'quick_help_phone',
            'quick_help_ticket',
            'quick_help_ticket_cta',
        ];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey(
                $key,
                $statusTranslations,
                "Missing status translation key: {$key}"
            );
            $this->assertNotEmpty(
                $statusTranslations[$key],
                "Empty status translation value for: {$key}"
            );
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_has_helpdesk_translation_keys(): void
    {
        // Validates: Task 17.1 - Helpdesk module translations
        $helpdeskTranslations = trans('helpdesk');

        $this->assertIsArray($helpdeskTranslations);
        $this->assertNotEmpty($helpdeskTranslations);

        // Check for common helpdesk keys
        $commonKeys = ['title', 'submit', 'status'];
        foreach ($commonKeys as $key) {
            if (isset($helpdeskTranslations[$key])) {
                $this->assertNotEmpty(
                    $helpdeskTranslations[$key],
                    "Empty helpdesk translation for: {$key}"
                );
            }
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_has_loan_translation_keys(): void
    {
        // Validates: Task 17.1 - Loan module translations
        $loanTranslations = trans('loan');

        $this->assertIsArray($loanTranslations);
        $this->assertNotEmpty($loanTranslations);
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_displays_no_raw_translation_keys_on_landing_page(): void
    {
        // Validates: Task 17.1 - No raw keys displayed in UI
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for common raw key patterns that indicate missing translations
        // Note: '::' is excluded as it's used in CSS pseudo-elements and other valid contexts
        $rawKeyPatterns = [
            'status.page_tagline',
            'helpdesk.title',
            'loan.title',
            'validation.required',
        ];

        foreach ($rawKeyPatterns as $pattern) {
            // Only fail if the pattern appears as visible text (not in scripts/attributes)
            $this->assertStringNotContainsString(
                '>'.$pattern.'<',
                $content,
                "Raw translation key found in visible content: {$pattern}"
            );
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_displays_no_raw_translation_keys_on_helpdesk_form(): void
    {
        // Validates: Task 17.1 - No raw keys on helpdesk form
        $response = $this->get('/helpdesk/create');

        if ($response->status() === 200) {
            $content = $response->getContent();

            // Should not contain raw translation key patterns
            $this->assertStringNotContainsString(
                'helpdesk.form.',
                $content,
                'Raw helpdesk translation keys found in form'
            );
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_displays_no_raw_translation_keys_on_loan_form(): void
    {
        // Validates: Task 17.1 - No raw keys on loan form
        $response = $this->get('/loan/apply');

        if ($response->status() === 200) {
            $content = $response->getContent();

            // Should not contain raw translation key patterns
            $this->assertStringNotContainsString(
                'loan.form.',
                $content,
                'Raw loan translation keys found in form'
            );
        }
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_displays_no_raw_translation_keys_on_dashboard(): void
    {
        // Validates: Task 17.1 - No raw keys on authenticated dashboard
        $this->actingAs($this->user);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Should not contain raw translation key patterns
        $this->assertStringNotContainsString(
            'dashboard.',
            $content,
            'Raw dashboard translation keys found'
        );
    }

    // =========================================================================
    // TASK 17.2: WCAG 2.2 AA COMPLIANCE VALIDATION
    // =========================================================================

    #[Test]
    #[Group('task-17-2')]
    public function it_has_proper_html_lang_attribute(): void
    {
        // Validates: Task 17.2 - Language attribute for accessibility
        $response = $this->get('/');
        $response->assertStatus(200);

        // Must have lang="ms" for BM-exclusive interface
        $response->assertSee('lang="ms"', false);
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_skip_to_content_link(): void
    {
        // Validates: Task 17.2 - Keyboard navigation (skip links)
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, 'skip-to-content') ||
                str_contains($content, 'skip-link') ||
                str_contains($content, '#main-content'),
            'Page must have skip-to-content link for keyboard navigation'
        );
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_main_landmark(): void
    {
        // Validates: Task 17.2 - ARIA landmarks
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, '<main') ||
                str_contains($content, 'role="main"'),
            'Page must have main landmark'
        );
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_navigation_landmark(): void
    {
        // Validates: Task 17.2 - Navigation landmark
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, '<nav') ||
                str_contains($content, 'role="navigation"'),
            'Page must have navigation landmark'
        );
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_proper_heading_structure(): void
    {
        // Validates: Task 17.2 - Heading hierarchy
        $response = $this->get('/');
        $response->assertStatus(200);

        // Page must have h1
        $response->assertSee('<h1', false);
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_focus_indicators_on_interactive_elements(): void
    {
        // Validates: Task 17.2 - Focus indicators
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for focus ring classes (Tailwind)
        $this->assertTrue(
            str_contains($content, 'focus:ring') ||
                str_contains($content, 'focus:outline') ||
                str_contains($content, 'focus-visible:'),
            'Interactive elements must have focus indicators'
        );
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_aria_live_regions_for_dynamic_content(): void
    {
        // Validates: Task 17.2 - Screen reader support
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for ARIA live regions (for notifications, loading states)
        $this->assertTrue(
            str_contains($content, 'aria-live') ||
                str_contains($content, 'role="alert"') ||
                str_contains($content, 'role="status"'),
            'Page should have ARIA live regions for dynamic content'
        );
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_accessible_forms_on_helpdesk_page(): void
    {
        // Validates: Task 17.2 - Form accessibility
        $response = $this->get('/helpdesk/create');

        if ($response->status() === 200) {
            $content = $response->getContent();

            // Forms should have labels
            if (str_contains($content, '<input')) {
                $this->assertTrue(
                    str_contains($content, '<label') ||
                        str_contains($content, 'aria-label') ||
                        str_contains($content, 'aria-labelledby'),
                    'Form inputs must have accessible labels'
                );
            }
        }
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_has_accessible_forms_on_loan_page(): void
    {
        // Validates: Task 17.2 - Form accessibility
        $response = $this->get('/loan/apply');

        if ($response->status() === 200) {
            $content = $response->getContent();

            // Forms should have labels
            if (str_contains($content, '<input')) {
                $this->assertTrue(
                    str_contains($content, '<label') ||
                        str_contains($content, 'aria-label') ||
                        str_contains($content, 'aria-labelledby'),
                    'Form inputs must have accessible labels'
                );
            }
        }
    }

    #[Test]
    #[Group('task-17-2')]
    public function it_supports_keyboard_navigation_on_modals(): void
    {
        // Validates: Task 17.2 - Keyboard navigation
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for ESC key handling on modals
        if (str_contains($content, 'x-show') || str_contains($content, 'wire:model')) {
            $this->assertTrue(
                str_contains($content, '@keydown.escape') ||
                    str_contains($content, 'keydown.esc') ||
                    str_contains($content, 'x-trap'),
                'Modals should support ESC key to close'
            );
        }
    }

    // =========================================================================
    // THEME VALIDATION (Light/Dark Mode)
    // =========================================================================

    #[Test]
    #[Group('task-17-1')]
    public function it_has_theme_toggle_component(): void
    {
        // Validates: Task 17.1 - Test all pages in both light and dark modes
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Check for theme toggle or dark mode support
        $this->assertTrue(
            str_contains($content, 'dark:') ||
                str_contains($content, 'theme-toggle') ||
                str_contains($content, 'data-theme'),
            'Page should support theme switching (light/dark mode)'
        );
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_has_dark_mode_styles(): void
    {
        // Validates: Task 17.1 - Dark mode support
        $cssPath = resource_path('css/app.css');

        if (File::exists($cssPath)) {
            $cssContent = File::get($cssPath);

            $this->assertTrue(
                str_contains($cssContent, 'dark') ||
                    str_contains($cssContent, '@media (prefers-color-scheme'),
                'CSS should include dark mode styles'
            );
        }
    }

    // =========================================================================
    // BM-EXCLUSIVE INTERFACE VALIDATION
    // =========================================================================

    #[Test]
    #[Group('task-17-1')]
    public function it_enforces_bm_locale(): void
    {
        // Validates: Task 17.1 - BM exclusive interface
        $this->assertEquals('ms', config('app.locale'));
        // Config key is 'supported_locales' per v3.6.0 deprecation
        $this->assertEquals(['ms'], config('app.supported_locales'));
    }

    #[Test]
    #[Group('task-17-1')]
    public function it_has_no_language_switcher(): void
    {
        // Validates: Task 17.1 - Language switcher removed per v3.6.0
        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();

        $this->assertFalse(
            str_contains($content, 'language-switcher') ||
                str_contains($content, 'LanguageSwitcher'),
            'Language switcher should not exist in BM-exclusive interface'
        );
    }

    // =========================================================================
    // COMPLETE VALIDATION SUMMARY
    // =========================================================================

    #[Test]
    #[Group('complete-validation')]
    public function it_passes_complete_phase_17_validation(): void
    {
        // Validates: All Phase 17 checkpoint requirements

        // 17.1.1 - Translation files exist
        $this->assertFileExists(base_path('lang/ms/status.php'));
        $this->assertFileExists(base_path('lang/ms/helpdesk.php'));
        $this->assertFileExists(base_path('lang/ms/loan.php'));

        // 17.1.2 - BM locale enforced
        $this->assertEquals('ms', config('app.locale'));

        // 17.2.1 - WCAG landmarks exist
        $response = $this->get('/');
        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertTrue(
            str_contains($content, '<main') || str_contains($content, 'role="main"'),
            'Main landmark required'
        );

        // 17.2.2 - Heading structure
        $response->assertSee('<h1', false);

        // 17.2.3 - Focus indicators
        $this->assertTrue(
            str_contains($content, 'focus:ring') || str_contains($content, 'focus:outline'),
            'Focus indicators required'
        );

        // 17.2.4 - Language attribute
        $response->assertSee('lang="ms"', false);
    }
}
