<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Contracts\RegistrationServiceInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Registration Feature Tests
 *
 * Tests the self-registration flow for MOTAC staff.
 * Per Requirements 15.1, 15.2 - only @motac.gov.my emails are allowed.
 *
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D01 §4.3 (Self-registration requirements)
 * @trace D03 SRS-AUTH-001 (Authentication requirements)
 * @trace Requirements 15.1, 15.2 (Self-Registration)
 */
class RegistrationTest extends TestCase
{
    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSee('Daftar')  // Check for registration form content in Bahasa Melayu
            ->assertSee('E-mel')
            ->assertSee('Kata Laluan');
    }

    #[Test]
    public function new_users_can_register_with_motac_email(): void
    {
        Notification::fake();

        $email = 'testuser'.time().'@motac.gov.my';
        $password = 'StrongP@ssw0rd';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('verification.notice', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => strtolower($email),
            'role' => 'staff',
        ]);
    }

    #[Test]
    public function registration_rejects_non_motac_email(): void
    {
        Notification::fake();

        $email = 'user@gmail.com';
        $password = 'StrongP@ssw0rd';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasErrors(['email'])
            ->assertNoRedirect();

        $this->assertDatabaseMissing('users', [
            'email' => strtolower($email),
        ]);
    }

    #[Test]
    public function registration_validates_password_confirmation(): void
    {
        Notification::fake();

        $email = 'confirm'.time().'@motac.gov.my';
        $password = 'StrongP@ssw0rd';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', 'MismatchP@ssw0rd');

        $component->call('register');

        $component
            ->assertHasErrors(['password'])
            ->assertNoRedirect();

        $this->assertDatabaseMissing('users', [
            'email' => strtolower($email),
        ]);
    }

    #[Test]
    public function registration_validates_required_fields(): void
    {
        $component = Volt::test('pages.auth.register');

        $component->call('register');

        $component->assertHasErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function registration_prevents_duplicate_email(): void
    {
        Notification::fake();

        $email = 'duplicate'.time().'@motac.gov.my';
        $password = 'StrongP@ssw0rd';

        User::factory()->create([
            'email' => $email,
        ]);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasErrors(['email'])
            ->assertNoRedirect();
    }

    #[Test]
    public function registration_page_displays_bahasa_melayu_content(): void
    {
        // Ensure locale is set to Bahasa Melayu
        app()->setLocale('ms');

        $response = $this->get('/register');

        $response->assertStatus(200);

        // Check for Bahasa Melayu content using translation keys
        $response->assertSee(__('auth.register_title')); // Registration title
        $response->assertSee(__('auth.name')); // Name field label
        $response->assertSee(__('auth.email')); // Email field label
        $response->assertSee(__('auth.password')); // Password field label
        $response->assertSee(__('auth.register_button')); // Register button text
        $response->assertSee(__('auth.already_registered')); // Already registered link

        // Verify we're using Bahasa Melayu locale
        $this->assertEquals('ms', app()->getLocale());
    }

    #[Test]
    public function email_verification_flow_uses_signed_url(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $registrationService = app(RegistrationServiceInterface::class);
        $verificationUrl = $registrationService->generateVerificationUrl($user);

        $request = Request::create($verificationUrl);

        $this->assertTrue(URL::hasValidSignature($request));
    }
}
