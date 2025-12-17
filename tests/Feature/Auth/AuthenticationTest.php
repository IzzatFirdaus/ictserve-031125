<?php

declare(strict_types=1);

/**
 * Authentication Tests
 *
 * Tests for user authentication including:
 * - Task 14.1: Flexible Login (email OR username)
 * - Role-based redirects
 * - Generic error messages (no user enumeration)
 *
 * @trace D03 SRS-AUTH-001 (Flexible Login)
 * @trace Requirements 16.2, 16.3, 16.5
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.login');
    }

    #[Test]
    public function users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    #[Test]
    public function admin_users_are_redirected_to_filament_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $admin->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('filament.admin.pages.admin-dashboard', absolute: false));
    }

    #[Test]
    public function users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors()
            ->assertNoRedirect();

        $this->assertGuest();
    }

    #[Test]
    public function navigation_menu_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response
            ->assertOk()
            ->assertSee('ICTServe'); // Check for system name
    }

    #[Test]
    public function users_can_logout(): void
    {
        $user = User::factory()->create();

        // Logout via the form post (the actual logout mechanism)
        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');

        $this->assertGuest();
    }

    #[Test]
    public function users_can_logout_via_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/');

        $this->assertGuest();
    }

    // =========================================================================
    // Task 14.1: Flexible Login Tests (Requirements 16.2, 16.3, 16.5)
    // =========================================================================

    /**
     * Test that users can authenticate using full email address.
     * Requirement 16.2: Accept full email (user@motac.gov.my)
     */
    #[Test]
    public function users_can_authenticate_with_full_email(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'testuser@motac.gov.my')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test that users can authenticate using short username only.
     * Requirement 16.3: Accept short username (user) and append @motac.gov.my
     */
    #[Test]
    public function users_can_authenticate_with_short_username(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'testuser')  // Short username without domain
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test that username login is case-insensitive.
     * Requirement 16.3: Flexible login should handle case variations
     */
    #[Test]
    public function username_login_is_case_insensitive(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'TESTUSER')  // Uppercase username
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test that email login is case-insensitive.
     * Requirement 16.2: Email login should handle case variations
     */
    #[Test]
    public function email_login_is_case_insensitive(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'TESTUSER@MOTAC.GOV.MY')  // Uppercase email
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test that generic error message is shown for non-existent user.
     * Requirement 16.5: Generic error messages - no user enumeration
     */
    #[Test]
    public function generic_error_shown_for_nonexistent_user(): void
    {
        // Don't create any user - test with non-existent email
        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'nonexistent@motac.gov.my')
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();

        // Verify the error message is generic (same as wrong password)
        $errors = $component->errors();
        $this->assertStringContainsString(
            trans('auth.failed'),
            $errors->first('form.email')
        );
    }

    /**
     * Test that generic error message is shown for wrong password.
     * Requirement 16.5: Same error message for wrong password as non-existent user
     */
    #[Test]
    public function generic_error_shown_for_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', 'testuser@motac.gov.my')
            ->set('form.password', 'wrong-password');

        $component->call('login');

        $component
            ->assertHasErrors(['form.email'])
            ->assertNoRedirect();

        $this->assertGuest();

        // Verify the error message is generic (same as non-existent user)
        $errors = $component->errors();
        $this->assertStringContainsString(
            trans('auth.failed'),
            $errors->first('form.email')
        );
    }

    /**
     * Test that username with whitespace is trimmed.
     * Requirement 16.3: Handle edge cases in username input
     */
    #[Test]
    public function username_with_whitespace_is_trimmed(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', '  testuser  ')  // Username with whitespace
            ->set('form.password', 'password');

        $component->call('login');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_page_displays_bahasa_melayu_content(): void
    {
        // Ensure locale is set to Bahasa Melayu
        app()->setLocale('ms');

        $response = $this->get('/login');

        $response->assertStatus(200);

        // Check for Bahasa Melayu content (actual text displayed)
        $response->assertSee('Log Masuk'); // Login title
        $response->assertSee('E-mel atau Nama Pengguna'); // Email field label
        $response->assertSee('Log Masuk dengan Google'); // Google login button
        $response->assertSee('Ingat saya'); // Remember me checkbox
        $response->assertSee('Lupa kata laluan?'); // Forgot password link

        // Verify we're using Bahasa Melayu locale
        $this->assertEquals('ms', app()->getLocale());
    }

    #[Test]
    public function dashboard_displays_bahasa_melayu_content(): void
    {
        $user = User::factory()->create();

        // Ensure locale is set to Bahasa Melayu
        app()->setLocale('ms');

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);

        // Check for Bahasa Melayu content on dashboard
        $response->assertSee('Tiket Terbuka Saya'); // My Open Tickets
        $response->assertSee('Pinjaman Menunggu Saya'); // My Pending Loans
        $response->assertSee('Tindakan Pantas'); // Quick Actions
        $response->assertSee('Aktiviti Terkini'); // Recent Activity

        // Verify we're using Bahasa Melayu locale
        $this->assertEquals('ms', app()->getLocale());
    }
}
