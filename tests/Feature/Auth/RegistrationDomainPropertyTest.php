<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Email Domain Restriction
 *
 * Property-based tests to verify email domain validation logic across
 * a wide range of email inputs during self-registration.
 *
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D03 SRS-AUTH-001 (Self-Registration)
 * @trace Requirements 5.1, 5.5 (Email Domain Restriction)
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

class RegistrationDomainPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 5: Email Domain Restriction Validation
     *
     * For any self-registration attempt, the system should only allow
     * registration if the email ends with '@motac.gov.my', regardless
     * of case sensitivity. All other domains must be rejected.
     *
     * **Feature: test-suite-comprehensive-v3.6, Property 5: Email Domain Restriction Validation**
     * **Validates: Requirements 5.1, 5.5**
     */
    #[Test]
    #[DataProvider('validMotacEmailProvider')]
    public function property_email_domain_accepts_valid_motac_emails(string $email): void
    {
        $password = 'TestP@ssw0rd'.time();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        $component
            ->assertHasNoErrors(['email'])
            ->assertRedirect(route('verification.notice', absolute: false));

        // Verify user was created with normalized email
        $normalizedEmail = strtolower($email);
        $this->assertDatabaseHas('users', [
            'email' => $normalizedEmail,
        ]);
    }

    #[Test]
    #[DataProvider('invalidEmailDomainProvider')]
    public function property_email_domain_rejects_invalid_domains(string $email, string $description): void
    {
        $password = 'TestP@ssw0rd'.time();

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', $email)
            ->set('password', $password)
            ->set('password_confirmation', $password);

        $component->call('register');

        // Should have email error due to domain validation
        $component->assertHasErrors(['email']);

        // User should NOT be created
        $this->assertDatabaseMissing('users', [
            'email' => strtolower($email),
        ]);
    }

    #[Test]
    public function property_email_domain_is_case_insensitive(): void
    {
        $caseVariations = [
            'user1@motac.gov.my',
            'USER2@MOTAC.GOV.MY',
            'User3@Motac.Gov.My',
            'uSeR4@mOtAc.GoV.mY',
        ];

        foreach ($caseVariations as $index => $email) {
            $password = 'TestP@ssw0rd'.time().$index;

            $component = Volt::test('pages.auth.register')
                ->set('name', 'Test User '.$index)
                ->set('email', $email)
                ->set('password', $password)
                ->set('password_confirmation', $password);

            $component->call('register');

            $component
                ->assertHasNoErrors(['email'])
                ->assertRedirect(route('verification.notice', absolute: false));

            // Verify user was created with lowercase email
            $this->assertDatabaseHas('users', [
                'email' => strtolower($email),
            ]);
        }
    }

    #[Test]
    public function property_email_domain_rejects_similar_domains(): void
    {
        $similarDomains = [
            'user@motac.com',           // Missing .gov
            'user@motac.gov.com',       // Wrong TLD
            'user@sub.motac.gov.my',    // Subdomain
            'user@motac.gov.my.fake.com', // Suffix attack
            'user@fake-motac.gov.my',   // Prefix attack
            'user@motac-gov.my',        // Hyphen instead of dot
            'user@motac.gov',           // Missing .my
            'user@motacgov.my',         // Missing dot
        ];

        foreach ($similarDomains as $email) {
            $password = 'TestP@ssw0rd'.time();

            $component = Volt::test('pages.auth.register')
                ->set('name', 'Test User')
                ->set('email', $email)
                ->set('password', $password)
                ->set('password_confirmation', $password);

            $component->call('register');

            $component->assertHasErrors(['email']);

            $this->assertDatabaseMissing('users', [
                'email' => strtolower($email),
            ]);
        }
    }

    #[Test]
    public function property_email_domain_accepts_various_username_formats(): void
    {
        $validUsernames = [
            'simple',
            'user.name',
            'user_name',
            'user-name',
            'user123',
            'user.name.123',
            'a',
            'very.long.username.with.many.parts',
        ];

        foreach ($validUsernames as $index => $username) {
            $email = $username.'@motac.gov.my';
            $password = 'TestP@ssw0rd'.time().$index;

            $component = Volt::test('pages.auth.register')
                ->set('name', 'Test User '.$index)
                ->set('email', $email)
                ->set('password', $password)
                ->set('password_confirmation', $password);

            $component->call('register');

            $component
                ->assertHasNoErrors(['email'])
                ->assertRedirect(route('verification.notice', absolute: false));

            $this->assertDatabaseHas('users', [
                'email' => $email,
            ]);
        }
    }

    /**
     * Data provider for valid @motac.gov.my emails
     */
    public static function validMotacEmailProvider(): array
    {
        $timestamp = time();

        return [
            'simple username' => ["testuser{$timestamp}a@motac.gov.my"],
            'username with numbers' => ["user123{$timestamp}@motac.gov.my"],
            'username with dots' => ["user.name{$timestamp}@motac.gov.my"],
            'username with underscore' => ["user_name{$timestamp}@motac.gov.my"],
            'username with hyphen' => ["user-name{$timestamp}@motac.gov.my"],
            'uppercase email' => ["UPPERCASE{$timestamp}@MOTAC.GOV.MY"],
            'mixed case email' => ["MixedCase{$timestamp}@Motac.Gov.My"],
        ];
    }

    /**
     * Data provider for invalid email domains
     */
    public static function invalidEmailDomainProvider(): array
    {
        return [
            'gmail domain' => ['user@gmail.com', 'Common personal email'],
            'yahoo domain' => ['user@yahoo.com', 'Common personal email'],
            'hotmail domain' => ['user@hotmail.com', 'Common personal email'],
            'outlook domain' => ['user@outlook.com', 'Microsoft personal email'],
            'company domain' => ['user@company.com', 'Generic company email'],
            'gov.my without motac' => ['user@other.gov.my', 'Other government agency'],
            'motac without gov' => ['user@motac.com', 'Missing .gov segment'],
            'motac.gov without my' => ['user@motac.gov', 'Missing .my TLD'],
            'subdomain of motac' => ['user@sub.motac.gov.my', 'Subdomain not allowed'],
            'motac as suffix' => ['user@fake.motac.gov.my', 'Fake domain with motac suffix'],
            'similar domain' => ['user@m0tac.gov.my', 'Typosquatting attempt'],
            'empty domain' => ['user@', 'Empty domain'],
            'no at symbol' => ['usermotac.gov.my', 'Missing @ symbol'],
        ];
    }
}
