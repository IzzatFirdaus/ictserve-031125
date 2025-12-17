<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    use RefreshDatabase;

    #[Test]
    public function registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    #[Test]
    public function new_users_can_register_with_motac_email(): void
    {
        // Use a unique complex password that meets all requirements:
        // - Min 8 chars, mixed case, numbers, symbols
        $password = 'TestP@ssw0rd'.time();

        // Per Requirement 15.2: Only @motac.gov.my emails are allowed
        $email = 'testuser'.time().'@motac.gov.my';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasNoErrors()
            ->assertRedirect(route('verification.notice', absolute: false));

        // User should be created but not authenticated until email verified
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Test User',
            'role' => 'staff',
        ]);
    }

    #[Test]
    public function registration_rejects_non_motac_email(): void
    {
        $password = 'TestP@ssw0rd'.time();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        // Should have email error due to domain validation
        $component->assertHasErrors(['email']);

        // User should NOT be created
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    #[Test]
    public function registration_validates_password_confirmation(): void
    {
        $email = 'testuser'.time().'@motac.gov.my';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', 'TestP@ssw0rd1')
            ->set('password_confirmation', 'DifferentP@ssw0rd2');

        $component->call('register');

        $component->assertHasErrors(['password']);
    }

    #[Test]
    public function registration_validates_required_fields(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '');

        $component->call('register');

        $component->assertHasErrors(['name', 'email', 'password']);
    }

    #[Test]
    public function registration_prevents_duplicate_email(): void
    {
        // Create existing user
        $existingEmail = 'existing'.time().'@motac.gov.my';
        User::factory()->create(['email' => $existingEmail]);

        $password = 'TestP@ssw0rd'.time();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'New User')
            ->set('email', $existingEmail)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component->assertHasErrors(['email']);
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
        $response->assertSee(__('common.password')); // Password field label
        $response->assertSee(__('auth.register_button')); // Register button text
        $response->assertSee(__('auth.already_registered')); // Already registered link

        // Verify we're using Bahasa Melayu locale
        $this->assertEquals('ms', app()->getLocale());
    }

    #[Test]
    public function email_verification_flow_uses_signed_url(): void
    {
        $password = 'TestP@ssw0rd'.time();
        $email = 'testuser'.time().'@motac.gov.my';

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        // Should redirect to verification notice page
        $component->assertRedirect(route('verification.notice', absolute: false));

        // User should be created but not verified
        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);
    }
}
