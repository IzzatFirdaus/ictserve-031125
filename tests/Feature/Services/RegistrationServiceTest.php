<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Contracts\RegistrationServiceInterface;
use App\Exceptions\InvalidEmailDomainException;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Registration Service Tests
 *
 * Tests self-registration functionality for MOTAC staff:
 * - Email domain validation (@motac.gov.my only)
 * - User account creation with correct role
 * - Email verification workflow
 * - Username extraction for flexible login
 *
 * @trace Requirements 15.2, 15.3, 15.4, 15.5
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D01 §4.3 (Self-registration requirements)
 * @trace D03 SRS-AUTH-001 (Authentication requirements)
 */
class RegistrationServiceTest extends TestCase
{
    private RegistrationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(RegistrationServiceInterface::class);
    }

    // =========================================================================
    // Email Domain Validation Tests
    // =========================================================================

    /**
     * Test: Valid @motac.gov.my email passes domain validation
     *
     * @trace Requirement 15.2
     * @trace D00 §4.1
     */
    #[Test]
    public function validates_motac_email_domain(): void
    {
        $this->assertTrue($this->service->validateEmailDomain('user@motac.gov.my'));
        $this->assertTrue($this->service->validateEmailDomain('john.doe@motac.gov.my'));
        $this->assertTrue($this->service->validateEmailDomain('test123@motac.gov.my'));
    }

    /**
     * Test: Invalid email domains are rejected
     *
     * @trace Requirement 15.2
     * @trace D00 §4.1
     */
    #[Test]
    public function rejects_non_motac_email_domains(): void
    {
        $this->assertFalse($this->service->validateEmailDomain('user@gmail.com'));
        $this->assertFalse($this->service->validateEmailDomain('user@yahoo.com'));
        $this->assertFalse($this->service->validateEmailDomain('user@gov.my'));
        $this->assertFalse($this->service->validateEmailDomain('user@motac.com'));
        $this->assertFalse($this->service->validateEmailDomain('user@motac.gov'));
    }

    /**
     * Test: Email validation is case-insensitive
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function email_validation_is_case_insensitive(): void
    {
        $this->assertTrue($this->service->validateEmailDomain('USER@MOTAC.GOV.MY'));
        $this->assertTrue($this->service->validateEmailDomain('User@Motac.Gov.My'));
        $this->assertTrue($this->service->validateEmailDomain('user@MOTAC.gov.my'));
    }

    /**
     * Test: Invalid email format is rejected
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function rejects_invalid_email_format(): void
    {
        $this->assertFalse($this->service->validateEmailDomain('invalid-email'));
        $this->assertFalse($this->service->validateEmailDomain('no-at-sign'));
        $this->assertFalse($this->service->validateEmailDomain('@motac.gov.my'));
        $this->assertFalse($this->service->validateEmailDomain('user@'));
    }

    /**
     * Test: Email with whitespace is trimmed and validated
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function trims_whitespace_from_email(): void
    {
        $this->assertTrue($this->service->validateEmailDomain(' user@motac.gov.my '));
        $this->assertTrue($this->service->validateEmailDomain('  user@motac.gov.my'));
    }

    // =========================================================================
    // User Registration Tests
    // =========================================================================

    /**
     * Test: Successfully register user with valid @motac.gov.my email
     *
     * **Feature: ictserve-update-v3, Property 36: Registration Account Creation**
     *
     * @trace Requirement 15.3
     * @trace D01 §4.3
     */
    #[Test]
    public function registers_user_with_valid_motac_email(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => 'SecurePassword123!',
        ];

        $user = $this->service->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('testuser@motac.gov.my', $user->email);
        $this->assertEquals('staff', $user->role);
        $this->assertTrue($user->is_active);
        $this->assertNull($user->email_verified_at);

        // Verify user exists in database
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@motac.gov.my',
            'role' => 'staff',
        ]);

        // Verify Registered event was fired
        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }

    /**
     * Test: Registration throws exception for non-MOTAC email
     *
     * @trace Requirement 15.2
     * @trace D00 §4.1
     */
    #[Test]
    public function registration_throws_exception_for_invalid_domain(): void
    {
        $this->expectException(InvalidEmailDomainException::class);

        $data = [
            'name' => 'Test User',
            'email' => 'user@gmail.com',
            'password' => 'SecurePassword123!',
        ];

        $this->service->register($data);
    }

    /**
     * Test: Registration normalizes email to lowercase
     *
     * @trace Requirement 15.3
     */
    #[Test]
    public function registration_normalizes_email_to_lowercase(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'TestUser@MOTAC.GOV.MY',
            'password' => 'SecurePassword123!',
        ];

        $user = $this->service->register($data);

        $this->assertEquals('testuser@motac.gov.my', $user->email);
    }

    /**
     * Test: Registration hashes password securely
     *
     * @trace Requirement 15.3
     */
    #[Test]
    public function registration_hashes_password(): void
    {
        Event::fake([Registered::class]);

        $password = 'SecurePassword123!';
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => $password,
        ];

        $user = $this->service->register($data);

        // Password should be hashed, not plain text
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password));
    }

    /**
     * Test: Registration sets default notification preferences
     *
     * @trace Requirement 15.3
     */
    #[Test]
    public function registration_sets_default_notification_preferences(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => 'SecurePassword123!',
        ];

        $user = $this->service->register($data);

        $preferences = $user->notification_preferences;
        $this->assertIsArray($preferences);
        $this->assertTrue($preferences['ticket_updates']);
        $this->assertTrue($preferences['loan_updates']);
        $this->assertTrue($preferences['realtime_notifications']);
    }

    /**
     * Test: Registration sets default locale to Bahasa Melayu
     *
     * @trace Requirement 15.3
     * @trace D15 (Bilingual support)
     */
    #[Test]
    public function registration_sets_default_locale_to_malay(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => 'SecurePassword123!',
        ];

        $user = $this->service->register($data);

        $this->assertEquals('ms', $user->locale);
    }

    /**
     * Test: Registration initializes guest_submissions_linked to zero
     *
     * @trace Requirement 15.3
     * @trace D02 FR-050 (Account linking)
     */
    #[Test]
    public function registration_initializes_guest_submissions_counter(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => 'SecurePassword123!',
        ];

        $user = $this->service->register($data);

        $this->assertEquals(0, $user->guest_submissions_linked);
    }

    /**
     * Test: Registration accepts optional fields
     *
     * @trace Requirement 15.3
     */
    #[Test]
    public function registration_accepts_optional_fields(): void
    {
        Event::fake([Registered::class]);

        $data = [
            'name' => 'Test User',
            'email' => 'testuser@motac.gov.my',
            'password' => 'SecurePassword123!',
            'phone' => '0123456789',
            'division_code' => 'BPM',
            'grade' => '41',
        ];

        $user = $this->service->register($data);

        $this->assertEquals('0123456789', $user->phone);
        $this->assertEquals('BPM', $user->division_code);
    }

    // =========================================================================
    // Username Extraction Tests
    // =========================================================================

    /**
     * Test: Extract username from email address
     *
     * @trace Requirement 16.3
     * @trace D03 SRS-AUTH-001
     */
    #[Test]
    public function extracts_username_from_email(): void
    {
        $this->assertEquals('user', $this->service->extractUsernameFromEmail('user@motac.gov.my'));
        $this->assertEquals('john.doe', $this->service->extractUsernameFromEmail('john.doe@motac.gov.my'));
        $this->assertEquals('test123', $this->service->extractUsernameFromEmail('test123@motac.gov.my'));
    }

    /**
     * Test: Username extraction is case-insensitive
     *
     * @trace Requirement 16.3
     */
    #[Test]
    public function username_extraction_normalizes_to_lowercase(): void
    {
        $this->assertEquals('user', $this->service->extractUsernameFromEmail('USER@MOTAC.GOV.MY'));
        $this->assertEquals('john.doe', $this->service->extractUsernameFromEmail('John.Doe@Motac.Gov.My'));
    }

    /**
     * Test: Username extraction handles edge cases
     *
     * @trace Requirement 16.3
     */
    #[Test]
    public function username_extraction_handles_edge_cases(): void
    {
        $this->assertEquals('', $this->service->extractUsernameFromEmail(''));
        $this->assertEquals('invalid', $this->service->extractUsernameFromEmail('invalid'));
        $this->assertEquals('', $this->service->extractUsernameFromEmail('@motac.gov.my'));
    }

    // =========================================================================
    // Email Registration Check Tests
    // =========================================================================

    /**
     * Test: Check if email is already registered
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function checks_if_email_is_registered(): void
    {
        // Create existing user
        User::factory()->create(['email' => 'existing@motac.gov.my']);

        $this->assertTrue($this->service->isEmailRegistered('existing@motac.gov.my'));
        $this->assertFalse($this->service->isEmailRegistered('new@motac.gov.my'));
    }

    /**
     * Test: Email registration check is case-insensitive
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function email_registration_check_is_case_insensitive(): void
    {
        User::factory()->create(['email' => 'existing@motac.gov.my']);

        $this->assertTrue($this->service->isEmailRegistered('EXISTING@MOTAC.GOV.MY'));
        $this->assertTrue($this->service->isEmailRegistered('Existing@Motac.Gov.My'));
    }

    // =========================================================================
    // Allowed Domains Tests
    // =========================================================================

    /**
     * Test: Get allowed domains returns correct list
     *
     * @trace Requirement 15.2
     * @trace D00 §4.1
     */
    #[Test]
    public function returns_allowed_domains(): void
    {
        $domains = $this->service->getAllowedDomains();

        $this->assertIsArray($domains);
        $this->assertContains('motac.gov.my', $domains);
        $this->assertCount(1, $domains);
    }

    // =========================================================================
    // Verification URL Generation Tests
    // =========================================================================

    /**
     * Test: Generate verification URL creates signed URL
     *
     * **Feature: ictserve-update-v3, Property 37: Email Verification Round-Trip**
     *
     * @trace Requirement 15.4
     * @trace D01 §4.3
     */
    #[Test]
    public function generates_verification_url(): void
    {
        $user = User::factory()->create(['email' => 'test@motac.gov.my']);

        $url = $this->service->generateVerificationUrl($user);

        $this->assertIsString($url);
        $this->assertStringContainsString('verify-email', $url);
        $this->assertStringContainsString((string) $user->id, $url);
        $this->assertStringContainsString('signature=', $url);
        $this->assertStringContainsString('expires=', $url);
    }

    // =========================================================================
    // InvalidEmailDomainException Tests
    // =========================================================================

    /**
     * Test: InvalidEmailDomainException contains correct information
     *
     * @trace Requirement 15.2
     */
    #[Test]
    public function invalid_email_domain_exception_contains_details(): void
    {
        $email = 'user@gmail.com';

        try {
            $this->service->register([
                'name' => 'Test',
                'email' => $email,
                'password' => 'password123',
            ]);
            $this->fail('Expected InvalidEmailDomainException was not thrown');
        } catch (InvalidEmailDomainException $e) {
            $this->assertEquals($email, $e->getEmail());
            $this->assertEquals('gmail.com', $e->getProvidedDomain());
            $this->assertContains('motac.gov.my', $e->getAllowedDomains());
        }
    }

    // =========================================================================
    // Service Container Binding Tests
    // =========================================================================

    /**
     * Test: Service is properly bound in container
     */
    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(RegistrationServiceInterface::class);

        $this->assertInstanceOf(RegistrationService::class, $service);
    }

    /**
     * Test: Service is singleton
     */
    #[Test]
    public function service_is_singleton(): void
    {
        $service1 = app(RegistrationServiceInterface::class);
        $service2 = app(RegistrationServiceInterface::class);

        $this->assertSame($service1, $service2);
    }
}
