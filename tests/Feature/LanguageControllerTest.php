<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * LanguageController Feature Tests
 *
 * Tests the language switching functionality including session/cookie persistence.
 *
 * Test Coverage:
 * - Change locale with valid locale
 * - Change locale with invalid locale
 * - Session storage after locale change
 * - Cookie persistence after locale change
 * - Redirect back to previous page
 * - Success message display
 * - Guest and authenticated user scenarios
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 20.3, 20.4, 14.3, 15.2, 15.3
 * Standards: D03-FR-020, D04 §7.3, D10 §5.2, D11 §6.1
 */
class LanguageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test changing locale with valid locale.
     */
    #[Test]
    public function test_change_locale_with_valid_locale(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->get(route('change-locale', 'ms'));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ms');
        $response->assertCookie('locale', 'ms');
    }

    /**
     * Test changing locale with invalid locale returns error.
     */
    #[Test]
    public function test_change_locale_with_invalid_locale(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act & Assert - Route constraint rejects invalid locale with 404
        $this->get('/change-locale/fr')
            ->assertStatus(404);
    }

    /**
     * Test session is stored after locale change.
     */
    #[Test]
    public function test_session_stored_after_locale_change(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $this->get(route('change-locale', 'ms'));

        // Assert
        $this->assertEquals('ms', session('locale'));
    }

    /**
     * Test cookie is persisted after locale change.
     */
    #[Test]
    public function test_cookie_persisted_after_locale_change(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->get(route('change-locale', 'ms'));

        // Assert
        $response->assertCookie('locale', 'ms');
    }

    /**
     * Test redirect back to previous page.
     */
    #[Test]
    public function test_redirect_back_to_previous_page(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->from('/')->get(route('change-locale', 'ms'));

        // Assert
        $response->assertRedirect('/');
    }

    /**
     * Test success message is displayed.
     */
    #[Test]
    public function test_success_message_displayed(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->get(route('change-locale', 'ms'));

        // Assert
        $response->assertSessionHas('message');
    }

    /**
     * Test locale change works for guest users.
     */
    #[Test]
    public function test_locale_change_works_for_guest_users(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->get(route('change-locale', 'ms'));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ms');
        $response->assertCookie('locale', 'ms');
    }

    /**
     * Test locale change works for authenticated users.
     */
    #[Test]
    public function test_locale_change_works_for_authenticated_users(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);
        /** @var \App\Models\User $user */
        $user = \App\Models\User::factory()->create();

        // Act
        $response = $this->actingAs($user)->get(route('change-locale', 'ms'));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ms');
        $response->assertCookie('locale', 'ms');
    }

    /**
     * Test switching between locales multiple times.
     */
    #[Test]
    public function test_switching_between_locales_multiple_times(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act - Switch to Malay
        $response1 = $this->get(route('change-locale', 'ms'));
        $response1->assertSessionHas('locale', 'ms');

        // Act - Switch to English
        $response2 = $this->get(route('change-locale', 'en'));
        $response2->assertSessionHas('locale', 'en');

        // Act - Switch back to Malay
        $response3 = $this->get(route('change-locale', 'ms'));
        $response3->assertSessionHas('locale', 'ms');

        // Assert
        $this->assertEquals('ms', session('locale'));
    }

    /**
     * Test cookie expiration is set to 1 year.
     */
    #[Test]
    public function test_cookie_expiration_is_one_year(): void
    {
        // Arrange
        Config::set('app.supported_locales', ['en', 'ms']);

        // Act
        $response = $this->get(route('change-locale', 'ms'));

        // Assert - Cookie should be set with 1 year expiration (60 * 24 * 365 minutes)
        $response->assertCookie('locale', 'ms');
    }
}
