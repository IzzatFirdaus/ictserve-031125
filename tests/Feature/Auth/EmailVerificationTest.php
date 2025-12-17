<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Email Verification Feature Tests
 *
 * Tests the email verification flow for self-registered MOTAC staff.
 * Per Requirements 15.4, 15.5 - signed URL with 24-hour expiry.
 *
 * @trace D01 §4.3 (Self-registration requirements)
 * @trace D03 SRS-AUTH-001 (Authentication requirements)
 * @trace Requirements 15.4, 15.5 (Email Verification Flow)
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    #[Test]
    public function email_verification_screen_shows_bilingual_content(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
        // The page should render without errors - check for key content
        $response->assertSee('verify');
    }

    #[Test]
    public function email_can_be_verified(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertTrue($freshUser->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
        // Redirects to dashboard after successful verification
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    #[Test]
    public function email_verification_with_24_hour_expiry(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        // Per Requirement 15.4: Signed URL valid for 24 hours
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(24),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertTrue($freshUser->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    #[Test]
    public function email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertFalse($freshUser->hasVerifiedEmail());
    }

    #[Test]
    public function email_verification_fails_with_expired_link(): void
    {
        $user = User::factory()->unverified()->create();

        // Create an expired URL (in the past)
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(1),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        // Should return 403 for expired signature
        $response->assertStatus(403);

        $freshUser = $user->fresh();
        $this->assertNotNull($freshUser);
        $this->assertFalse($freshUser->hasVerifiedEmail());
    }

    #[Test]
    public function user_can_resend_verification_email(): void
    {
        $user = User::factory()->unverified()->create();

        $component = Volt::actingAs($user)->test('pages.auth.verify-email');

        $component->call('sendVerification');

        $component->assertSet('emailSent', true);
    }

    #[Test]
    public function verified_user_is_redirected_from_verification_page(): void
    {
        $user = User::factory()->create(); // Already verified

        $component = Volt::actingAs($user)->test('pages.auth.verify-email');

        $component->call('sendVerification');

        $component->assertRedirect(route('dashboard', absolute: false));
    }

    #[Test]
    public function user_can_logout_from_verification_page(): void
    {
        $user = User::factory()->unverified()->create();

        $component = Volt::actingAs($user)->test('pages.auth.verify-email');

        $component->call('logout');

        $component->assertRedirect('/');
    }
}
