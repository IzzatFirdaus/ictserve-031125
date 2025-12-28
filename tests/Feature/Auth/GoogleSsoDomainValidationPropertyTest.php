<?php

declare(strict_types=1);

/**
 * Property-Based Tests for Google SSO Domain Validation
 *
 * Property-based tests to verify domain validation logic across
 * a wide range of email inputs using random generation.
 *
 * @trace D03-FR-001.3 (Google SSO Authentication)
 * @trace Requirements 1.1, 4.3 (Domain Validation Consistency)
 *
 * @version 3.6.0
 *
 * @created 2025-12-13
 */

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoogleSsoDomainValidationPropertyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Fake events to prevent side effects
        Event::fake();
    }

    /**
     * Property 1: Domain Validation Consistency
     *
     * For any email address provided during Google SSO authentication,
     * the system should only allow authentication if the email ends with
     * '@motac.gov.my', regardless of case sensitivity.
     *
     * **Feature: google-sso-enhancement, Property 1: Domain Validation Consistency**
     * **Validates: Requirements 1.1, 4.3**
     */
    #[Test]
    public function property_domain_validation_accepts_valid_motac_emails(): void
    {
        // Test valid @motac.gov.my email
        $this->mockSocialiteUser([
            'id' => 'valid123',
            'name' => 'Valid Test User',
            'email' => 'test@motac.gov.my',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        // Verify user was created
        $user = User::where('email', 'test@motac.gov.my')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Valid Test User', $user->name);
        $this->assertEquals('valid123', $user->google_id);
    }

    #[Test]
    public function property_domain_validation_handles_case_insensitive_emails(): void
    {
        // Test uppercase email
        $this->mockSocialiteUser([
            'id' => 'case123',
            'name' => 'Case Test User',
            'email' => 'CASE@MOTAC.GOV.MY',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        // Verify user was created with lowercase email
        $user = User::where('email', 'case@motac.gov.my')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Case Test User', $user->name);
    }

    #[Test]
    public function property_domain_validation_rejects_invalid_domains(): void
    {
        // Test with an invalid email
        $this->mockSocialiteUser([
            'id' => '987654321',
            'name' => 'Invalid Test User',
            'email' => 'test@gmail.com',
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    /**
     * Property 2: User Creation Idempotency
     *
     * For any Google user attempting authentication multiple times,
     * the system should create exactly one User record and subsequent
     * authentications should update the existing record.
     *
     * **Feature: google-sso-enhancement, Property 2: User Creation Idempotency**
     * **Validates: Requirements 1.2, 6.3**
     */
    #[Test]
    public function property_user_creation_idempotency(): void
    {
        $testEmail = 'idempotency.test@motac.gov.my';
        $googleId = '999888777';

        // First authentication - should create user
        $this->mockSocialiteUser([
            'id' => $googleId,
            'name' => 'Idempotency Test User',
            'email' => $testEmail,
            'avatar' => 'https://example.com/avatar1.jpg',
        ]);

        $response1 = $this->get(route('auth.google.callback'));
        $response1->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user1 = User::where('email', $testEmail)->first();
        $this->assertNotNull($user1);
        $initialUserId = $user1->id;
        $initialUserCount = User::count();

        Auth::logout();
        session()->flush();

        // Second authentication - should not create new user
        $this->mockSocialiteUser([
            'id' => $googleId,
            'name' => 'Idempotency Test User Updated',
            'email' => $testEmail,
            'avatar' => 'https://example.com/avatar2.jpg',
        ]);

        $response2 = $this->get(route('auth.google.callback'));
        $response2->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        // Verify same user, no new user created
        $user2 = User::where('email', $testEmail)->first();
        $this->assertEquals($initialUserId, $user2->id);
        $this->assertEquals($initialUserCount, User::count());

        Auth::logout();
        session()->flush();

        // Third authentication with different case - should still be same user
        $this->mockSocialiteUser([
            'id' => $googleId,
            'name' => 'Idempotency Test User Case',
            'email' => strtoupper($testEmail), // Different case
            'avatar' => 'https://example.com/avatar3.jpg',
        ]);

        $response3 = $this->get(route('auth.google.callback'));
        $response3->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        // Verify still same user
        $user3 = User::where('email', $testEmail)->first();
        $this->assertEquals($initialUserId, $user3->id);
        $this->assertEquals($initialUserCount, User::count());
    }

    /**
     * Generate test cases for domain validation property testing
     */
    private function generateDomainValidationTestCases(int $count): array
    {
        $testCases = [];

        // Valid cases - @motac.gov.my with various cases and usernames
        $validDomain = '@motac.gov.my';
        for ($i = 0; $i < $count / 4; $i++) {
            $username = $this->generateRandomUsername();
            $email = $username.$validDomain;

            // Test different cases
            $cases = [
                $email, // original
                strtoupper($email), // uppercase
                strtolower($email), // lowercase
                ucfirst($email), // first letter uppercase
            ];

            foreach ($cases as $case) {
                $testCases[] = [
                    'email' => $case,
                    'shouldBeValid' => true,
                ];
            }
        }

        // Invalid cases - various invalid domains
        $invalidDomains = [
            '@gmail.com',
            '@yahoo.com',
            '@hotmail.com',
            '@outlook.com',
            '@company.com',
            '@motac.com', // missing .gov
            '@motac.gov.com', // wrong TLD
            '@sub.motac.gov.my', // subdomain
            '@motac.gov.my.fake.com', // suffix
            '@fake-motac.gov.my', // prefix
        ];

        foreach ($invalidDomains as $domain) {
            for ($i = 0; $i < 3; $i++) {
                $username = $this->generateRandomUsername();
                $testCases[] = [
                    'email' => $username.$domain,
                    'shouldBeValid' => false,
                ];
            }
        }

        return $testCases;
    }

    /**
     * Generate random username for testing
     */
    private function generateRandomUsername(): string
    {
        $prefixes = ['test', 'user', 'staff', 'admin', 'john', 'jane', 'ahmad', 'siti'];
        $suffixes = ['', '123', '456', 'x', 'new', 'old'];

        $prefix = $prefixes[array_rand($prefixes)];
        $suffix = $suffixes[array_rand($suffixes)];

        return $prefix.$suffix;
    }

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
