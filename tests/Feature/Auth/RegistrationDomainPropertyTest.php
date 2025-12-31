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

use App\Rules\MotacEmailDomain;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationDomainPropertyTest extends TestCase
{
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
        $this->assertEmailDomainValidity($email, true);
    }

    #[Test]
    #[DataProvider('invalidEmailDomainProvider')]
    public function property_email_domain_rejects_invalid_domains(string $email, string $description): void
    {
        $this->assertEmailDomainValidity($email, false, $description);
    }

    #[Test]
    public function property_email_domain_is_case_insensitive(): void
    {
        $caseVariations = [
            'testuser@motac.gov.my',
            'TESTUSER@MOTAC.GOV.MY',
            'TestUser@Motac.Gov.My',
        ];

        foreach ($caseVariations as $email) {
            $this->assertEmailDomainValidity($email, true);
        }
    }

    #[Test]
    public function property_email_domain_rejects_similar_domains(): void
    {
        $similarDomains = [
            'user@m0tac.gov.my',
            'user@motac.gov.my.evil',
            'user@motac-gov.my',
            'user@motacgov.my',
        ];

        foreach ($similarDomains as $email) {
            $this->assertEmailDomainValidity($email, false);
        }
    }

    #[Test]
    public function property_email_domain_accepts_various_username_formats(): void
    {
        $validUsernames = [
            'user.name@motac.gov.my',
            'user_name@motac.gov.my',
            'user-name@motac.gov.my',
            'user123@motac.gov.my',
        ];

        foreach ($validUsernames as $email) {
            $this->assertEmailDomainValidity($email, true);
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

    private function assertEmailDomainValidity(string $email, bool $expected, ?string $description = null): void
    {
        $isValid = $this->isEmailDomainValid($email);
        $message = $description ? "Failed: {$description}" : "Failed for {$email}";

        $this->assertSame($expected, $isValid, $message);
    }

    private function isEmailDomainValid(string $email): bool
    {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'string', 'email', 'max:255', new MotacEmailDomain]]
        );

        return ! $validator->fails();
    }
}
