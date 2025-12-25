<?php

declare(strict_types=1);

/**
 * Google SSO Authentication Tests
 *
 * Comprehensive test suite for GoogleAuthController including:
 * - OAuth flow simulation with Socialite fake
 * - Domain validation testing
 * - User creation and linking scenarios
 * - Error handling and edge cases
 * - Property-based tests for domain validation
 *
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace D04 §6.1 (Security)
 * @trace Requirements 1.1, 1.2, 1.3 (Google SSO Enhancement)
 *
 * @version 3.6.0
 *
 * @created 2025-12-13
 */

namespace Tests\Feature\Auth;

use App\Contracts\SsoHealthCheckInterface;
use App\Events\GoogleSsoLinked;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoogleAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Fake events to test event dispatching
        Event::fake();

        // Mock the SsoHealthCheckInterface to return healthy status for tests
        $this->mock(SsoHealthCheckInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getServiceStatus')->andReturn([
                'status' => 'healthy',
                'configured' => true,
                'available' => true,
                'message' => 'Google SSO service is fully operational',
                'details' => [
                    'configuration_errors' => [],
                    'configuration_warnings' => [],
                    'connectivity_tested' => true,
                    'connectivity_passed' => true,
                    'allowed_domains' => ['motac.gov.my'],
                    'redirect_uri_configured' => true,
                ],
                'checked_at' => now()->toIso8601String(),
            ]);
        });
    }

    // =========================================================================
    // OAuth Redirect Tests
    // =========================================================================

    #[Test]
    public function redirect_returns_socialite_redirect_response(): void
    {
        // Mock Socialite redirect to return a redirect response to Google
        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturnSelf();

        Socialite::shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/oauth/authorize?client_id=test'));

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect();
        // Should redirect to Google OAuth URL
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    // =========================================================================
    // OAuth Callback Success Tests
    // =========================================================================

    #[Test]
    public function callback_creates_new_user_with_valid_motac_email(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'John Doe',
            'email' => 'john.doe@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = User::where('email', 'john.doe@motac.gov.my')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('123456789', $user->google_id);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar);

        $this->assertNotNull($user->email_verified_at);
    }

    #[Test]
    public function callback_links_existing_user_without_google_id(): void
    {
        // Create existing user without Google ID
        $existingUser = User::factory()->create([
            'email' => 'jane.smith@motac.gov.my',
            'google_id' => null,
            'avatar' => null,
        ]);

        $this->mockSocialiteUser([
            'id' => '987654321',
            'name' => 'Jane Smith',
            'email' => 'jane.smith@motac.gov.my',
            'avatar' => 'https://example.com/jane-avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($existingUser);

        $existingUser->refresh();
        $this->assertEquals('987654321', $existingUser->google_id);
        $this->assertEquals('https://example.com/jane-avatar.jpg', $existingUser->avatar);
        $this->assertNotNull($existingUser->email_verified_at);

        // Verify GoogleSsoLinked event was dispatched
        Event::assertDispatched(GoogleSsoLinked::class, fn ($event) => $event->user->id === $existingUser->id);
    }

    #[Test]
    public function callback_authenticates_existing_user_with_google_id(): void
    {
        // Create existing user with Google ID already set
        $existingUser = User::factory()->create([
            'email' => 'existing@motac.gov.my',
            'google_id' => '555666777',
            'avatar' => 'https://example.com/existing-avatar.jpg',
        ]);

        $this->mockSocialiteUser([
            'id' => '555666777',
            'name' => 'Existing User',
            'email' => 'existing@motac.gov.my',
            'avatar' => 'https://example.com/new-avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->assertAuthenticatedAs($existingUser);

        // Verify no GoogleSsoLinked event was dispatched (already linked)
        Event::assertNotDispatched(GoogleSsoLinked::class);

        // Avatar should not be updated for already linked users
        $existingUser->refresh();
        $this->assertEquals('https://example.com/existing-avatar.jpg', $existingUser->avatar);
    }

    // =========================================================================
    // Domain Validation Tests
    // =========================================================================

    #[Test]
    public function callback_rejects_non_motac_email_domains(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'External User',
            'email' => 'external@gmail.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));

        // The key assertions: user should not be authenticated and no user created
        $this->assertGuest();

        // Verify no user was created
        $this->assertDatabaseMissing('users', [
            'email' => 'external@gmail.com',
        ]);
    }

    #[Test]
    #[DataProvider('invalidEmailDomainsProvider')]
    public function callback_rejects_various_invalid_domains(string $email): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Test User',
            'email' => $email,
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();

        // Verify no user was created
        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);
    }

    public static function invalidEmailDomainsProvider(): array
    {
        return [
            'gmail' => ['test@gmail.com'],
            'yahoo' => ['test@yahoo.com'],
            'hotmail' => ['test@hotmail.com'],
            'outlook' => ['test@outlook.com'],
            'company' => ['test@company.com'],
            'subdomain_motac' => ['test@sub.motac.gov.my'],
            'wrong_tld' => ['test@motac.gov.com'],
            'partial_match' => ['test@motac.gov.my.fake.com'],
        ];
    }

    #[Test]
    public function callback_handles_case_insensitive_email_domains(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Case Test User',
            'email' => 'CaseTest@MOTAC.GOV.MY',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        // Verify user was created with lowercase email
        $user = User::where('email', 'casetest@motac.gov.my')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Case Test User', $user->name);
    }

    // =========================================================================
    // Error Handling Tests
    // =========================================================================

    #[Test]
    public function callback_handles_socialite_exceptions(): void
    {
        // Mock Socialite to throw an exception
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new \Exception('OAuth provider error'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    #[Test]
    public function callback_handles_invalid_state_exception(): void
    {
        // Mock Socialite to throw InvalidStateException
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new \Laravel\Socialite\Two\InvalidStateException('Invalid state'));

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    // =========================================================================
    // Session and Authentication Tests
    // =========================================================================

    #[Test]
    public function callback_regenerates_session_on_successful_login(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Session Test User',
            'email' => 'session@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        // Start session to get initial session ID
        $this->startSession();
        $initialSessionId = session()->getId();

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        // Verify session was regenerated
        $newSessionId = session()->getId();
        $this->assertNotEquals($initialSessionId, $newSessionId);
    }

    #[Test]
    public function callback_sets_remember_token(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Remember Test User',
            'email' => 'remember@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        // Verify remember token was set
        $user = User::where('email', 'remember@motac.gov.my')->first();
        $this->assertNotNull($user->remember_token);
    }

    #[Test]
    public function callback_redirects_to_intended_url(): void
    {
        // Set intended URL in session
        session(['url.intended' => route('profile.edit')]);

        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Intended Test User',
            'email' => 'intended@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('profile.edit'));

        $this->assertAuthenticated();
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Mock Socialite user response using Mockery (proper Laravel 12 approach)
     */
    private function mockSocialiteUser(array $userData): void
    {
        $abstractUser = \Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn($userData['id']);
        $abstractUser->shouldReceive('getName')->andReturn($userData['name']);
        $abstractUser->shouldReceive('getEmail')->andReturn($userData['email']);
        $abstractUser->shouldReceive('getAvatar')->andReturn($userData['avatar']);

        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);
    }
}
