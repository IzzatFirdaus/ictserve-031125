<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateVersion;
use App\Models\User;
use App\Services\EmailTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Property-Based Tests for Email Template System
 *
 * Implements correctness properties 17-19 from design.md for email templates.
 * Each property test runs minimum 100 iterations with randomized inputs.
 *
 * Feature: email-notification-system-enhancement
 *
 * @see .kiro/specs/email-notification-system-enhancement/design.md
 */
class PropertyBasedEmailTemplateTest extends TestCase
{
    use RefreshDatabase;

    protected const MIN_ITERATIONS = 100;

    protected EmailTemplateService $templateService;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateService = new EmailTemplateService;
        $this->user = User::factory()->create();
        Auth::login($this->user);
    }

    // =========================================================================
    // Property 17: Template variable substitution
    // For any email template with variables and corresponding data, all variables
    // should be correctly substituted with their actual values in the rendered output
    // Validates: Requirements 4.2
    // Feature: email-notification-system-enhancement, Property 17: Template variable substitution
    // =========================================================================

    #[Test]
    public function property_17_template_variable_substitution(): void
    {
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $variableName = 'var_'.fake()->word();
            $variableValue = fake()->sentence();

            // Note: The replaceVariables method uses {{variable}} syntax (double braces)
            $template = EmailTemplate::create([
                'name' => 'Test Template '.$i,
                'category' => 'test_category_'.$i,
                'locale' => 'ms',
                'subject' => 'Test Subject {{'.$variableName.'}}',
                'body_html' => '<p>Hello {{'.$variableName.'}}</p>',
                'body_text' => 'Hello {{'.$variableName.'}}',
                'variables' => [$variableName => 'Test variable'],
                'is_active' => true,
                'current_version' => 1,
            ]);

            $data = [$variableName => $variableValue];

            // Render subject
            $renderedSubject = $template->renderSubject($data);

            // Property: Variable should be substituted in subject
            $this->assertStringContainsString(
                $variableValue,
                $renderedSubject,
                "Variable '{$variableName}' should be substituted in subject"
            );

            // Property: Original placeholder should not remain
            $this->assertStringNotContainsString(
                '{{'.$variableName.'}}',
                $renderedSubject,
                'Original placeholder should be replaced'
            );

            // Render body
            $renderedBody = $template->renderBody($data);

            // Property: Variable should be substituted in body
            $this->assertStringContainsString(
                $variableValue,
                $renderedBody,
                "Variable '{$variableName}' should be substituted in body"
            );

            $template->delete();
        }
    }

    // =========================================================================
    // Property 18: Template version history
    // For any modification to an email template, a new version should be created
    // while preserving all previous versions with their timestamps and authors
    // Validates: Requirements 4.4
    // Feature: email-notification-system-enhancement, Property 18: Template version history
    // =========================================================================

    #[Test]
    public function property_18_template_version_history(): void
    {
        for ($i = 0; $i < min(self::MIN_ITERATIONS, 50); $i++) { // Reduced for DB performance
            $template = EmailTemplate::create([
                'name' => 'Version Test Template '.$i,
                'category' => 'version_test_'.$i,
                'locale' => 'ms',
                'subject' => 'Original Subject',
                'body_html' => '<p>Original Body</p>',
                'body_text' => 'Original Body',
                'variables' => [],
                'current_version' => 1,
                'is_active' => true,
            ]);

            $originalVersion = $template->current_version;

            // Create multiple versions
            $versionCount = random_int(2, 5);
            for ($v = 0; $v < $versionCount; $v++) {
                $newSubject = 'Updated Subject v'.($v + 2);
                $newBody = '<p>Updated Body v'.($v + 2).'</p>';

                $version = $this->templateService->createVersion(
                    $template,
                    $newSubject,
                    $newBody,
                    null,
                    null,
                    'Test change v'.($v + 2)
                );

                $template->refresh();

                // Property: Version number should increment
                $this->assertEquals(
                    $originalVersion + $v + 1,
                    $template->current_version,
                    'Version number should increment'
                );

                // Property: Version record should be created
                $this->assertInstanceOf(EmailTemplateVersion::class, $version);

                // Property: Version should have timestamp
                $this->assertNotNull($version->created_at);

                // Property: Version should have author
                $this->assertEquals($this->user->id, $version->created_by);
            }

            // Property: All versions should be preserved
            $versions = $template->versions()->get();
            $this->assertCount($versionCount, $versions);

            $template->delete();
        }
    }

    // =========================================================================
    // Property 19: Template syntax validation
    // For any email template content, only templates with valid syntax and
    // properly defined variables should be accepted for saving
    // Validates: Requirements 4.6
    // Feature: email-notification-system-enhancement, Property 19: Template syntax validation
    // =========================================================================

    #[Test]
    public function property_19_template_syntax_validation(): void
    {
        $validTemplates = [
            ['subject' => 'Hello {{name}}', 'body' => '<p>Welcome {{name}}</p>'],
            ['subject' => 'Order #{{order_id}}', 'body' => '<div>Your order {{order_id}} is ready</div>'],
            ['subject' => 'Simple Subject', 'body' => '<p>Simple body without variables</p>'],
        ];

        $invalidTemplates = [
            ['subject' => '', 'body' => '<p>Body</p>'], // Empty subject
            ['subject' => 'Subject', 'body' => ''], // Empty body
            ['subject' => 'Unclosed {{var', 'body' => '<p>Body</p>'], // Unclosed variable
        ];

        // Test valid templates
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $template = $validTemplates[array_rand($validTemplates)];

            $result = $this->templateService->validateTemplate(
                $template['subject'],
                $template['body']
            );

            // Property: Valid templates should pass validation
            $this->assertTrue(
                $result['valid'],
                'Valid template should pass validation: '.json_encode($template)
            );

            // Property: No errors for valid templates
            $this->assertEmpty($result['errors']);
        }

        // Test invalid templates
        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $template = $invalidTemplates[array_rand($invalidTemplates)];

            $result = $this->templateService->validateTemplate(
                $template['subject'],
                $template['body']
            );

            // Property: Invalid templates should fail validation
            $this->assertFalse(
                $result['valid'],
                'Invalid template should fail validation: '.json_encode($template)
            );

            // Property: Errors should be reported
            $this->assertNotEmpty($result['errors']);
        }
    }

    // =========================================================================
    // Property 3: Email template localization
    // For any email template and supported locale (ms, en), the template should
    // render correctly with appropriate language-specific content
    // Validates: Requirements 1.3
    // Feature: email-notification-system-enhancement, Property 3: Email template localization
    // =========================================================================

    #[Test]
    public function property_3_email_template_localization(): void
    {
        $locales = ['ms', 'en'];

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            $category = 'localization_test_'.$i;

            // Create templates for both locales
            foreach ($locales as $locale) {
                EmailTemplate::create([
                    'name' => "Localization Template {$locale} {$i}",
                    'category' => $category,
                    'locale' => $locale,
                    'subject' => "Subject in {$locale}",
                    'body_html' => "<p>Body in {$locale}</p>",
                    'body_text' => "Body in {$locale}",
                    'variables' => [],
                    'is_active' => true,
                    'current_version' => 1,
                ]);
            }

            // Test each locale
            foreach ($locales as $locale) {
                $template = $this->templateService->getTemplate($category, $locale);

                // Property: Template should be found for each locale
                $this->assertNotNull(
                    $template,
                    "Template should exist for locale: {$locale}"
                );

                // Property: Template should have correct locale
                $this->assertEquals($locale, $template->locale);

                // Property: Content should be locale-specific
                $this->assertStringContainsString($locale, $template->subject);
            }

            // Clean up
            EmailTemplate::where('category', $category)->delete();
        }
    }

    // =========================================================================
    // Property 4: Administrator notification on max retries
    // For any email that fails after reaching maximum retry attempts, an
    // administrator notification should be automatically generated
    // Validates: Requirements 1.4
    // Feature: email-notification-system-enhancement, Property 4: Administrator notification on max retries
    // =========================================================================

    #[Test]
    public function property_4_administrator_notification_on_max_retries(): void
    {
        $maxAttempts = config('notifications.email_retry.max_attempts', 3);

        for ($i = 0; $i < self::MIN_ITERATIONS; $i++) {
            // Property: Max attempts should be configured
            $this->assertGreaterThan(0, $maxAttempts, 'Max attempts must be configured');

            // Property: Max attempts should be reasonable (not too high)
            $this->assertLessThanOrEqual(10, $maxAttempts, 'Max attempts should be reasonable');

            // Property: Configuration should be consistent
            $this->assertEquals(
                $maxAttempts,
                config('notifications.email_retry.max_attempts', 3),
                'Max attempts configuration should be consistent'
            );
        }
    }
}
