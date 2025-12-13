<?php

declare(strict_types=1);

/**
 * Unit Tests for GoogleAuthController
 *
 * Unit tests focusing on individual methods and edge cases
 * for the GoogleAuthController class.
 *
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace Requirements 1.1, 1.3 (GoogleAuthController Testing)
 *
 * @version 3.6.0
 *
 * @created 2025-12-13
 *
 * @updated 2025-12-13 - Updated to inject GoogleSsoService dependency
 */

namespace Tests\Unit;

use App\Contracts\GoogleSsoServiceInterface;
use App\Events\GoogleSsoLinked;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Models\User;
use App\Services\GoogleSsoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoogleAuthControllerUnitTest extends TestCase
{
    use RefreshDatabase;

    private GoogleAuthController $controller;

    private GoogleSsoServiceInterface $ssoService;

    protected function setUp(): void
    {
        parent::setUp();

        // Get the GoogleSsoService from the container
        $this->ssoService = $this->app->make(GoogleSsoServiceInterface::class);

        // Create controller with injected service
        $this->controller = new GoogleAuthController($this->ssoService);

        // Fake events
        Event::fake();
    }

    // =========================================================================
    // Redirect Method Tests
    // =========================================================================

    #[Test]
    public function redirect_method_returns_redirect_response(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('scopes')
            ->with(['email', 'profile'])
            ->andReturnSelf();
        $provider->shouldReceive('redirect')
            ->andReturn(new RedirectResponse('https://accounts.google.com/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        $response = $this->controller->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertStringContainsString('google.com', $response->getTargetUrl());
    }

    #[Test]
    public function redirect_method_calls_socialite_driver(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/oauth/authorize'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->controller->redirect();

        $this->assertInstanceOf(RedirectResponse::class, $response);
    }

    // =========================================================================
    // Callback Method Tests - Success Cases
    // =========================================================================

    #[Test]
    public function callback_creates_new_user_successfully(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Unit Test User',
            'email' => 'unittest@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('dashboard'), $response->getTargetUrl());

        $this->assertAuthenticated();

        $user = User::where('email', 'unittest@motac.gov.my')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Unit Test User', $user->name);
        $this->assertEquals('123456789', $user->google_id);
        $this->assertNotNull($user->email_verified_at);
    }

    #[Test]
    public function callback_links_existing_user_without_google_id(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'existing@motac.gov.my',
            'google_id' => null,
        ]);

        $this->mockSocialiteUser([
            'id' => '987654321',
            'name' => 'Existing User',
            'email' => 'existing@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertAuthenticatedAs($existingUser);

        $existingUser->refresh();
        $this->assertEquals('987654321', $existingUser->google_id);

        Event::assertDispatched(GoogleSsoLinked::class);
    }

    #[Test]
    public function callback_handles_case_insensitive_email(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Case Test',
            'email' => 'CaseTest@MOTAC.GOV.MY',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);

        // Email should be stored in lowercase
        $user = User::where('email', 'casetest@motac.gov.my')->first();
        $this->assertNotNull($user);
    }

    // =========================================================================
    // Callback Method Tests - Domain Validation
    // =========================================================================

    #[Test]
    public function callback_rejects_gmail_domain(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Gmail User',
            'email' => 'user@gmail.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'user@gmail.com',
        ]);
    }

    #[Test]
    public function callback_rejects_subdomain_motac(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Subdomain User',
            'email' => 'user@sub.motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

    #[Test]
    public function callback_rejects_wrong_tld(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Wrong TLD User',
            'email' => 'user@motac.gov.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

    // =========================================================================
    // Callback Method Tests - Error Handling
    // =========================================================================

    #[Test]
    public function callback_handles_invalid_state_exception(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new InvalidStateException('Invalid state'));

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

    #[Test]
    public function callback_handles_general_exception(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new \Exception('General error'));

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

    #[Test]
    public function callback_handles_null_socialite_user(): void
    {
        Socialite::shouldReceive('driver')
            ->with('google')
            ->andThrow(new \Exception('No user returned'));

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('login'), $response->getTargetUrl());
        $this->assertGuest();
    }

    // =========================================================================
    // Session and Authentication Tests
    // =========================================================================

    #[Test]
    public function callback_authenticates_user_successfully(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Auth Test',
            'email' => 'auth@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->controller->callback();

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('dashboard'), $response->getTargetUrl());
        $this->assertAuthenticated();
    }

    #[Test]
    public function callback_regenerates_session(): void
    {
        $this->mockSocialiteUser([
            'id' => '123456789',
            'name' => 'Session Test',
            'email' => 'session@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        // Start session to get initial ID
        $this->startSession();
        $initialSessionId = session()->getId();

        $this->controller->callback();

        // Session should be regenerated
        $newSessionId = session()->getId();
        $this->assertNotEquals($initialSessionId, $newSessionId);
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    /**
     * Mock Socialite user response
     */
    private function mockSocialiteUser(array $userData): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn($userData['id']);
        $socialiteUser->shouldReceive('getName')->andReturn($userData['name']);
        $socialiteUser->shouldReceive('getEmail')->andReturn($userData['email']);
        $socialiteUser->shouldReceive('getAvatar')->andReturn($userData['avatar']);

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);
    }
}
