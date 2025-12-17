<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Flexible Login
 *
 * Property-based tests to verify flexible login logic across
 * various input formats (full email vs short username).
 *
 * @trace D03 SRS-AUTH-001 (Flexible Login)
 * @trace Requirements 5.3 (Flexible Login)
 *
 * @version 3.6.0
 *
 * @created 2025-12-16
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FlexibleLoginPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 6: Flexible Login Validation
     *
     * For any login attempt, the system should accept both full email
     * format (user@motac.gov.my) and short username format (user),
     * authenticating the same user regardless of input format.
     *
     * **Feature: test-suite-comprehensive-v3.6, Property 6: Flexible Login Validation**
     * **Validates: Requirements 5.3**
     */
    #[Test]
    #[DataProvider('flexibleLoginProvider')]
    public function property_flexible_login_accepts_both_formats(
        string $storedEmail,
        string $loginInput,
        bool $shouldSucceed,
        string $description
    ): void {
        $user = User::factory()->create([
            'email' => $storedEmail,
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $loginInput)
            ->set('form.password', 'password');

        $component->call('login');

        if ($shouldSucceed) {
            $component
                ->assertHasNoErrors()
                ->assertRedirect(route('dashboard', absolute: false));

            $this->assertAuthenticated();
            $this->assertAuthenticatedAs($user);
        } else {
            $component->assertHasErrors(['form.email']);
            $this->assertGuest();
        }
    }

    #[Test]
    public function property_flexible_login_is_case_insensitive(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $caseVariations = [
            'testuser',                    // lowercase username
            'TESTUSER',                    // uppercase username
            'TestUser',                    // mixed case username
            'testuser@motac.gov.my',       // lowercase email
            'TESTUSER@MOTAC.GOV.MY',       // uppercase email
            'TestUser@Motac.Gov.My',       // mixed case email
        ];

        foreach ($caseVariations as $loginInput) {
            // Reset authentication state
            auth()->logout();
            session()->flush();

            $component = Volt::test('pages.auth.login')
                ->set('form.email', $loginInput)
                ->set('form.password', 'password');

            $component->call('login');

            $component
                ->assertHasNoErrors()
                ->assertRedirect(route('dashboard', absolute: false));

            $this->assertAuthenticated();
            $this->assertAuthenticatedAs($user);
        }
    }

    #[Test]
    public function property_flexible_login_trims_whitespace(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser@motac.gov.my',
        ]);

        $whitespaceVariations = [
            '  testuser  ',                      // username with spaces
            '  testuser@motac.gov.my  ',         // email with spaces
            "\ttestuser\t",                      // username with tabs
            "\n testuser \n",                    // username with newlines
        ];

        foreach ($whitespaceVariations as $loginInput) {
            auth()->logout();
            session()->flush();

            $component = Volt::test('pages.auth.login')
                ->set('form.email', $loginInput)
                ->set('form.password', 'password');

            $component->call('login');

            $component
                ->assertHasNoErrors()
                ->assertRedirect(route('dashboard', absolute: false));

            $this->assertAuthenticated();
            $this->assertAuthenticatedAs($user);
        }
    }

    #[Test]
    public function property_flexible_login_same_user_for_both_formats(): void
    {
        $user = User::factory()->create([
            'email' => 'sameuser@motac.gov.my',
        ]);

        // Login with full email
        $component1 = Volt::test('pages.auth.login')
            ->set('form.email', 'sameuser@motac.gov.my')
            ->set('form.password', 'password');

        $component1->call('login');
        $component1->assertHasNoErrors();

        $authenticatedUser1 = auth()->user();
        $this->assertEquals($user->id, $authenticatedUser1->id);

        auth()->logout();
        session()->flush();

        // Login with short username
        $component2 = Volt::test('pages.auth.login')
            ->set('form.email', 'sameuser')
            ->set('form.password', 'password');

        $component2->call('login');
        $component2->assertHasNoErrors();

        $authenticatedUser2 = auth()->user();
        $this->assertEquals($user->id, $authenticatedUser2->id);

        // Verify both logins authenticated the same user
        $this->assertEquals($authenticatedUser1->id, $authenticatedUser2->id);
    }

    #[Test]
    public function property_flexible_login_generic_error_for_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'validuser@motac.gov.my',
        ]);

        $invalidAttempts = [
            ['nonexistent', 'password'],           // Non-existent username
            ['nonexistent@motac.gov.my', 'password'], // Non-existent email
            ['validuser', 'wrongpassword'],        // Valid username, wrong password
            ['validuser@motac.gov.my', 'wrongpassword'], // Valid email, wrong password
        ];

        foreach ($invalidAttempts as [$loginInput, $password]) {
            auth()->logout();
            session()->flush();

            $component = Volt::test('pages.auth.login')
                ->set('form.email', $loginInput)
                ->set('form.password', $password);

            $component->call('login');

            $component->assertHasErrors(['form.email']);
            $this->assertGuest();

            // Verify generic error message (no user enumeration)
            $errors = $component->errors();
            $this->assertStringContainsString(
                trans('auth.failed'),
                $errors->first('form.email')
            );
        }
    }

    /**
     * Data provider for flexible login test cases
     */
    public static function flexibleLoginProvider(): array
    {
        return [
            // Full email format tests
            'full email lowercase' => [
                'testuser@motac.gov.my',
                'testuser@motac.gov.my',
                true,
                'Full email should authenticate',
            ],
            'full email uppercase' => [
                'testuser@motac.gov.my',
                'TESTUSER@MOTAC.GOV.MY',
                true,
                'Uppercase email should authenticate',
            ],
            'full email mixed case' => [
                'testuser@motac.gov.my',
                'TestUser@Motac.Gov.My',
                true,
                'Mixed case email should authenticate',
            ],

            // Short username format tests
            'short username lowercase' => [
                'testuser@motac.gov.my',
                'testuser',
                true,
                'Short username should authenticate',
            ],
            'short username uppercase' => [
                'testuser@motac.gov.my',
                'TESTUSER',
                true,
                'Uppercase username should authenticate',
            ],
            'short username mixed case' => [
                'testuser@motac.gov.my',
                'TestUser',
                true,
                'Mixed case username should authenticate',
            ],

            // Username with special characters
            'username with dots' => [
                'user.name@motac.gov.my',
                'user.name',
                true,
                'Username with dots should authenticate',
            ],
            'username with underscore' => [
                'user_name@motac.gov.my',
                'user_name',
                true,
                'Username with underscore should authenticate',
            ],
            'username with numbers' => [
                'user123@motac.gov.my',
                'user123',
                true,
                'Username with numbers should authenticate',
            ],
        ];
    }
}
