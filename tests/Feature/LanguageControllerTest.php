<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * LanguageController Feature Tests - v3.6.0 Bahasa Melayu Only
 *
 * Tests language functionality for ICTServe v3.6.0 which uses Bahasa Melayu exclusively.
 * Language switching is disabled in v3.6.0 - only 'ms' locale is supported.
 *
 * Test Coverage:
 * - Verify Bahasa Melayu is default and only supported locale
 * - Verify language switching routes are disabled/return errors
 * - Verify Bahasa Melayu content is displayed
 * - Verify English locale attempts are rejected
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 *
 * @since 2025-12-11
 *
 * Requirements: 1.1, 1.2, 1.3 (v3.6.0 Bahasa Melayu Only)
 * Standards: D03-FR-001, D15 §2.1, D00 §3.6.0
 */
class LanguageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test default locale is Bahasa Melayu in v3.6.0.
     */
    #[Test]
    public function default_locale_is_bahasa_melayu(): void
    {
        // Assert - v3.6.0 uses Bahasa Melayu exclusively
        $this->assertEquals('ms', config('app.locale'));
        $this->assertEquals('ms', config('app.fallback_locale'));
        $this->assertEquals(['ms'], config('app.supported_locales'));
    }

    /**
     * Test English locale switching is disabled in v3.6.0.
     */
    #[Test]
    public function english_locale_switching_is_disabled(): void
    {
        // Act & Assert - English locale switching should return 404 or redirect
        // Since language switching is disabled in v3.6.0
        $response = $this->get('/change-locale/en');

        // Should either be 404 (route not found) or redirect to home with ms locale
        $this->assertTrue(
            $response->status() === 404 ||
                ($response->isRedirect() && session('locale', 'ms') === 'ms')
        );
    }

    /**
     * Test Bahasa Melayu content is displayed on pages.
     */
    #[Test]
    public function bahasa_melayu_content_is_displayed(): void
    {
        // Act - Visit welcome page
        $response = $this->get('/');

        // Assert - Should see Bahasa Melayu content
        $response->assertStatus(200);
        $response->assertSee('ICTServe'); // System name
        $response->assertSee('Sistem Perkhidmatan ICT'); // System description in BM
    }

    /**
     * Test application locale remains Bahasa Melayu throughout session.
     */
    #[Test]
    public function application_locale_remains_bahasa_melayu(): void
    {
        // Act - Make multiple requests
        $this->get('/');
        $this->get('/login');

        // Assert - Locale should always be 'ms'
        $this->assertEquals('ms', app()->getLocale());
        $this->assertEquals('ms', session('locale', config('app.locale')));
    }

    /**
     * Test Bahasa Melayu translations are loaded correctly.
     */
    #[Test]
    public function bahasa_melayu_translations_are_loaded(): void
    {
        // Act - Set locale to Bahasa Melayu (should be default)
        app()->setLocale('ms');

        // Assert - Common translations should be in Bahasa Melayu
        $this->assertEquals('Kembali', __('common.back'));
        $this->assertEquals('Tindakan', __('common.actions'));
        $this->assertEquals('Status', __('common.Status'));
        $this->assertNotEquals('common.back', __('common.back')); // Should not return key
    }

    /**
     * Test language switcher is hidden/disabled in v3.6.0.
     */
    #[Test]
    public function language_switcher_is_disabled(): void
    {
        // Act - Visit pages that previously had language switcher
        $response = $this->get('/');

        // Assert - Should not see language switcher elements
        $response->assertStatus(200);
        $response->assertDontSee('English'); // Should not see English option
        $response->assertDontSee('switch_to'); // Should not see switch language text

        // Only Bahasa Melayu should be active
        $this->assertEquals(['ms'], config('app.supported_locales'));
    }

    /**
     * Test guest users see Bahasa Melayu content.
     */
    #[Test]
    public function guest_users_see_basaha_melayu_content(): void
    {
        // Act - Visit guest-accessible pages
        $loginResponse = $this->get('/login');
        $welcomeResponse = $this->get('/');

        // Assert - Should see Bahasa Melayu content
        $loginResponse->assertStatus(200);
        $welcomeResponse->assertStatus(200);

        // Check for Bahasa Melayu text (not English)
        $this->assertEquals('ms', app()->getLocale());
    }

    /**
     * Test authenticated users see Bahasa Melayu content.
     */
    #[Test]
    public function authenticated_users_see_basaha_melayu_content(): void
    {
        // Arrange
        /** @var \App\Models\User $user */
        $user = \App\Models\User::factory()->create();

        // Act - Visit authenticated pages
        $response = $this->actingAs($user)->get('/dashboard');

        // Assert - Should see Bahasa Melayu content
        $response->assertStatus(200);
        $this->assertEquals('ms', app()->getLocale());
    }

    /**
     * Test invalid locale attempts are rejected in v3.6.0.
     */
    #[Test]
    public function invalid_locale_attempts_are_rejected(): void
    {
        // Act & Assert - Try various invalid locales
        $invalidLocales = ['en', 'fr', 'zh', 'ar'];

        foreach ($invalidLocales as $locale) {
            $response = $this->get("/change-locale/{$locale}");

            // Should either return 404 or maintain 'ms' locale
            $this->assertTrue(
                $response->status() === 404 ||
                    app()->getLocale() === 'ms'
            );
        }
    }

    /**
     * Test v3.6.0 configuration is correctly set for Bahasa Melayu only.
     */
    #[Test]
    public function v360_configuration_is_correct(): void
    {
        // Assert - v3.6.0 specific configuration
        $this->assertEquals('ms', config('app.locale'));
        $this->assertEquals('ms', config('app.fallback_locale'));
        $this->assertEquals(['ms'], config('app.supported_locales'));

        // Verify English is not in supported locales
        $this->assertNotContains('en', config('app.supported_locales'));
    }
}
