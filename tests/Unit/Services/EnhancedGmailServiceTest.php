<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Services\GmailService;
use App\Services\GoogleOAuthVerificationService;
use Tests\TestCase;

/**
 * Enhanced Gmail Service Tests for ICTServe v3.6.1
 *
 * Tests the enhanced Gmail service with OAuth verification support:
 * - Service instantiation with dependency injection
 * - Authentication method selection
 * - Verification status integration
 * - Fallback mechanism handling
 *
 * @see Requirements 3.1, 3.2, 3.3, 3.4, 6.1
 */
class EnhancedGmailServiceTest extends TestCase
{
    private GoogleOAuthVerificationServiceInterface $mockVerificationService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mock verification service
        $this->mockVerificationService = $this->createMock(GoogleOAuthVerificationServiceInterface::class);
    }

    public function test_service_can_be_instantiated_with_verification_service(): void
    {
        // Configure mock to return testing status
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service to avoid initialization issues in tests
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);

        $this->assertInstanceOf(GmailService::class, $service);
    }

    public function test_service_defaults_to_smtp_fallback_when_disabled(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);

        $this->assertEquals(GmailService::AUTH_METHOD_SMTP_FALLBACK, $service->getAuthenticationMethod());
        $this->assertFalse($service->isAuthenticated());
    }

    public function test_service_provides_verification_status(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_VERIFIED);

        $this->mockVerificationService
            ->method('isInProductionMode')
            ->willReturn(true);

        $this->mockVerificationService
            ->method('isInTestingMode')
            ->willReturn(false);

        // Disable Gmail service to avoid initialization
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);
        $status = $service->getVerificationStatus();

        $this->assertIsArray($status);
        $this->assertEquals(GoogleOAuthVerificationService::STATUS_VERIFIED, $status['status']);
        $this->assertTrue($status['is_production_mode']);
        $this->assertFalse($status['is_testing_mode']);
        $this->assertArrayHasKey('authentication_method', $status);
        $this->assertArrayHasKey('is_authenticated', $status);
    }

    public function test_service_handles_test_user_limitation(): void
    {
        $testEmail = 'test@motac.gov.my';

        // Configure mock for test user limitation
        $this->mockVerificationService
            ->method('canUserAuthenticate')
            ->with($testEmail)
            ->willReturn(false);

        $this->mockVerificationService
            ->method('isTestUser')
            ->with($testEmail)
            ->willReturn(false);

        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);
        $limitation = $service->handleTestUserLimitation($testEmail);

        $this->assertIsArray($limitation);
        $this->assertFalse($limitation['allowed']);
        $this->assertFalse($limitation['is_test_user']);
        $this->assertEquals(GoogleOAuthVerificationService::STATUS_TESTING, $limitation['verification_status']);
    }

    public function test_service_allows_authenticated_test_user(): void
    {
        $testEmail = 'testuser@motac.gov.my';

        // Configure mock for allowed test user
        $this->mockVerificationService
            ->method('canUserAuthenticate')
            ->with($testEmail)
            ->willReturn(true);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);
        $limitation = $service->handleTestUserLimitation($testEmail);

        $this->assertIsArray($limitation);
        $this->assertTrue($limitation['allowed']);
        $this->assertEquals('', $limitation['message']);
    }

    public function test_service_can_fallback_to_smtp(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);

        // Should already be in SMTP fallback mode, but test explicit fallback
        $service->fallbackToSmtp();

        $this->assertEquals(GmailService::AUTH_METHOD_SMTP_FALLBACK, $service->getAuthenticationMethod());
        $this->assertFalse($service->isAuthenticated());
    }

    public function test_service_provides_quota_usage_information(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);
        $quotaInfo = $service->getQuotaUsage();

        $this->assertIsArray($quotaInfo);
        $this->assertArrayHasKey('daily_limit', $quotaInfo);
        $this->assertArrayHasKey('per_user_limit', $quotaInfo);
        $this->assertArrayHasKey('current_usage', $quotaInfo);
        $this->assertArrayHasKey('percentage_used', $quotaInfo);
        $this->assertArrayHasKey('reset_time', $quotaInfo);
    }

    public function test_service_provides_health_status(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);
        $healthStatus = $service->getHealthStatus();

        $this->assertIsArray($healthStatus);
        $this->assertArrayHasKey('service_enabled', $healthStatus);
        $this->assertArrayHasKey('authentication_method', $healthStatus);
        $this->assertArrayHasKey('is_authenticated', $healthStatus);
        $this->assertArrayHasKey('verification_status', $healthStatus);
        $this->assertArrayHasKey('connectivity', $healthStatus);
        $this->assertArrayHasKey('quota_usage', $healthStatus);
    }

    public function test_service_validates_email_addresses(): void
    {
        // Configure mock
        $this->mockVerificationService
            ->method('getVerificationStatus')
            ->willReturn(GoogleOAuthVerificationService::STATUS_TESTING);

        // Disable Gmail service
        config(['services.google.gmail_enabled' => false]);

        $service = new GmailService($this->mockVerificationService);

        $this->assertTrue($service->validateEmailAddress('valid@motac.gov.my'));
        $this->assertTrue($service->validateEmailAddress('test.user@example.com'));
        $this->assertFalse($service->validateEmailAddress('invalid-email'));
        $this->assertFalse($service->validateEmailAddress(''));
        $this->assertFalse($service->validateEmailAddress('missing@'));
        $this->assertFalse($service->validateEmailAddress('@missing.com'));
    }
}
