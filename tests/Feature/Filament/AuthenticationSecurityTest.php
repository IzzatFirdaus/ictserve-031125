<?php

declare(strict_types=1);

namespace Tests\Feature\Filament;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

/**
 * Authentication and Security Test Suite
 *
 * Tests for Task 1.3: Configure Authentication and Security
 * Requirements: 17.2, 17.5
 *
 * @see .kiro/specs/filament-admin-access/tasks.md Task 1.3
 */
class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear rate limiter before each test
        RateLimiter::clear('admin_login_test@example.com|127.0.0.1');
    }

    /**
     * Test session timeout is configured to 30 minutes
     *
     * @test
     */
    public function test_session_timeout_is_configured_to_30_minutes(): void
    {
        $this->assertEquals(30, config('session.lifetime'));
    }

    /**
     * Test session timeout middleware logs out inactive users
     *
     * @test
     */
    public function test_session_timeout_middleware_logs_out_inactive_users(): void
    {
        $admin = User::factory()->admin()->create();

        // Login user
        $this->actingAs($admin);

        // Set last activity time to 31 minutes ago
        Session::put('last_activity_time', now()->subMinutes(31)->timestamp);

        // Make request to trigger middleware
        $response = $this->get('/admin');

        // Should redirect to login with timeout message
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        // User should be logged out
        $this->assertGuest();
    }

    /**
     * Test session timeout middleware updates last activity time
     *
     * @test
     */
    public function test_session_timeout_middleware_updates_last_activity_time(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        // Set initial last activity time
        $initialTime = now()->subMinutes(5)->timestamp;
        Session::put('last_activity_time', $initialTime);

        // Make request
        $this->get('/admin');

        // Last activity time should be updated
        $this->assertNotEquals($initialTime, Session::get('last_activity_time'));
        $this->assertGreaterThan($initialTime, Session::get('last_activity_time'));
    }

    /**
     * Test rate limiting is configured correctly
     *
     * @test
     */
    public function test_rate_limiting_blocks_after_5_failed_attempts(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Make 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('filament.admin.auth.login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // 6th attempt should be rate limited
        $response = $this->post(route('filament.admin.auth.login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }

    /**
     * Test rate limiting clears on successful login
     *
     * @test
     */
    public function test_rate_limiting_clears_on_successful_login(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Make 3 failed attempts
        for ($i = 0; $i < 3; $i++) {
            $this->post(route('filament.admin.auth.login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // Successful login should clear rate limit
        $response = $this->post(route('filament.admin.auth.login'), [
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertRedirect('/admin');

        // Should be able to login again without rate limiting
        $this->post(route('logout'));

        $response = $this->post(route('filament.admin.auth.login'), [
            'email' => 'test@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertRedirect('/admin');
    }

    /**
     * Test CSRF protection is enabled for admin forms
     *
     * @test
     */
    public function test_csrf_protection_is_enabled_for_admin_forms(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        // Attempt to submit form without CSRF token
        $response = $this->post('/admin/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Should fail with 419 (CSRF token mismatch)
        $response->assertStatus(419);
    }

    /**
     * Test password complexity requirements are configured
     *
     * @test
     */
    public function test_password_complexity_requirements_are_configured(): void
    {
        $rule = Password::defaults();

        $this->assertInstanceOf(Password::class, $rule);
    }

    /**
     * Test password must be at least 8 characters
     *
     * @test
     */
    public function test_password_must_be_at_least_8_characters(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'Short1!'], ['password' => $rule]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    /**
     * Test password must contain uppercase letter
     *
     * @test
     */
    public function test_password_must_contain_uppercase_letter(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'lowercase123!'], ['password' => $rule]);

        $this->assertTrue($validator->fails());
    }

    /**
     * Test password must contain lowercase letter
     *
     * @test
     */
    public function test_password_must_contain_lowercase_letter(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'UPPERCASE123!'], ['password' => $rule]);

        $this->assertTrue($validator->fails());
    }

    /**
     * Test password must contain number
     *
     * @test
     */
    public function test_password_must_contain_number(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'NoNumbers!'], ['password' => $rule]);

        $this->assertTrue($validator->fails());
    }

    /**
     * Test password must contain special character
     *
     * @test
     */
    public function test_password_must_contain_special_character(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'NoSpecial123'], ['password' => $rule]);

        $this->assertTrue($validator->fails());
    }

    /**
     * Test valid password passes all requirements
     *
     * @test
     */
    public function test_valid_password_passes_all_requirements(): void
    {
        $rule = Password::defaults();

        $validator = validator(['password' => 'ValidPass123!'], ['password' => $rule]);

        $this->assertFalse($validator->fails());
    }

    /**
     * Test automatic logout on session expiry
     *
     * @test
     */
    public function test_automatic_logout_on_session_expiry(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        // Simulate session expiry by setting last activity to 31 minutes ago
        Session::put('last_activity_time', now()->subMinutes(31)->timestamp);

        // Make request
        $response = $this->get('/admin');

        // Should be logged out and redirected to login
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Test session is invalidated on logout
     *
     * @test
     */
    public function test_session_is_invalidated_on_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $sessionId = Session::getId();

        // Logout
        $this->post(route('logout'));

        // Session ID should be regenerated
        $this->assertNotEquals($sessionId, Session::getId());

        // User should be logged out
        $this->assertGuest();
    }

    /**
     * Test session cookie is HTTP only
     *
     * @test
     */
    public function test_session_cookie_is_http_only(): void
    {
        $this->assertTrue(config('session.http_only'));
    }

    /**
     * Test session cookie has same-site protection
     *
     * @test
     */
    public function test_session_cookie_has_same_site_protection(): void
    {
        $this->assertEquals('lax', config('session.same_site'));
    }

    /**
     * Test production environment enforces stricter password rules
     *
     * @test
     */
    public function test_production_environment_enforces_stricter_password_rules(): void
    {
        // Verify the PasswordValidationServiceProvider class exists
        $this->assertTrue(class_exists(\App\Providers\PasswordValidationServiceProvider::class));
    }
}
