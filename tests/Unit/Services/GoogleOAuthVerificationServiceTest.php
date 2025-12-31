<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Models\GoogleOAuthVerification;
use App\Services\GoogleOAuthVerificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for GoogleOAuthVerificationService
 *
 * Tests verification status detection, test user management,
 * and verification requirement validation.
 *
 * @see Requirements 1.1, 2.5, 4.1
 */
class GoogleOAuthVerificationServiceTest extends TestCase
{
    private GoogleOAuthVerificationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Clear cache before each test
        Cache::flush();

        // Set up default config
        Config::set('services.google.client_id', 'test-client-id.apps.googleusercontent.com');
        Config::set('services.google.oauth_verification_status', null);
        Config::set('services.google.oauth_test_users', null);

        $this->service = app(GoogleOAuthVerificationServiceInterface::class);
    }

    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(GoogleOAuthVerificationServiceInterface::class);

        $this->assertInstanceOf(GoogleOAuthVerificationService::class, $service);
    }

    #[Test]
    public function service_is_singleton(): void
    {
        $service1 = app(GoogleOAuthVerificationServiceInterface::class);
        $service2 = app(GoogleOAuthVerificationServiceInterface::class);

        $this->assertSame($service1, $service2);
    }

    // =========================================================================
    // Verification Status Tests
    // =========================================================================

    #[Test]
    public function get_verification_status_returns_testing_by_default(): void
    {
        $status = $this->service->getVerificationStatus();

        $this->assertEquals(GoogleOAuthVerificationService::STATUS_TESTING, $status);
    }

    #[Test]
    public function get_verification_status_uses_config_when_set(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');

        // Clear cache to force re-read
        Cache::flush();
        $this->service->clearCache();

        $status = $this->service->getVerificationStatus();

        $this->assertEquals(GoogleOAuthVerificationService::STATUS_VERIFIED, $status);
    }

    #[Test]
    public function get_verification_status_uses_database_when_config_not_set(): void
    {
        GoogleOAuthVerification::factory()->verified()->create([
            'client_id' => 'test-client-id.apps.googleusercontent.com',
        ]);

        // Clear cache to force re-read
        Cache::flush();
        $this->service->clearCache();

        $status = $this->service->getVerificationStatus();

        $this->assertEquals(GoogleOAuthVerificationService::STATUS_VERIFIED, $status);
    }

    #[Test]
    public function is_in_testing_mode_returns_true_when_testing(): void
    {
        $this->assertTrue($this->service->isInTestingMode());
    }

    #[Test]
    public function is_in_testing_mode_returns_false_when_verified(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');
        Cache::flush();
        $this->service->clearCache();

        $this->assertFalse($this->service->isInTestingMode());
    }

    #[Test]
    public function is_in_production_mode_returns_true_when_verified(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');
        Cache::flush();
        $this->service->clearCache();

        $this->assertTrue($this->service->isInProductionMode());
    }

    #[Test]
    public function is_in_production_mode_returns_false_when_testing(): void
    {
        $this->assertFalse($this->service->isInProductionMode());
    }

    // =========================================================================
    // Test User Management Tests
    // =========================================================================

    #[Test]
    public function add_test_user_adds_valid_motac_email(): void
    {
        $result = $this->service->addTestUser('user@motac.gov.my');

        $this->assertTrue($result);
        $this->assertTrue($this->service->isTestUser('user@motac.gov.my'));
    }

    #[Test]
    public function add_test_user_normalizes_email_to_lowercase(): void
    {
        $result = $this->service->addTestUser('USER@MOTAC.GOV.MY');

        $this->assertTrue($result);
        $this->assertTrue($this->service->isTestUser('user@motac.gov.my'));
    }

    #[Test]
    public function add_test_user_rejects_invalid_email_format(): void
    {
        $result = $this->service->addTestUser('invalid-email');

        $this->assertFalse($result);
    }

    #[Test]
    public function add_test_user_rejects_non_motac_domain(): void
    {
        $result = $this->service->addTestUser('user@gmail.com');

        $this->assertFalse($result);
    }

    #[Test]
    public function add_test_user_returns_true_for_existing_user(): void
    {
        $this->service->addTestUser('existing@motac.gov.my');

        $result = $this->service->addTestUser('existing@motac.gov.my');

        $this->assertTrue($result);
        $this->assertEquals(1, $this->service->getTestUserCount());
    }

    #[Test]
    public function remove_test_user_removes_existing_user(): void
    {
        $this->service->addTestUser('remove@motac.gov.my');

        $result = $this->service->removeTestUser('remove@motac.gov.my');

        $this->assertTrue($result);
        $this->assertFalse($this->service->isTestUser('remove@motac.gov.my'));
    }

    #[Test]
    public function remove_test_user_returns_false_for_non_existing_user(): void
    {
        $result = $this->service->removeTestUser('nonexistent@motac.gov.my');

        $this->assertFalse($result);
    }

    #[Test]
    public function get_test_users_returns_empty_array_by_default(): void
    {
        $users = $this->service->getTestUsers();

        $this->assertIsArray($users);
        $this->assertEmpty($users);
    }

    #[Test]
    public function get_test_users_returns_added_users(): void
    {
        $this->service->addTestUser('user1@motac.gov.my');
        $this->service->addTestUser('user2@motac.gov.my');

        $users = $this->service->getTestUsers();

        $this->assertCount(2, $users);
        $this->assertContains('user1@motac.gov.my', $users);
        $this->assertContains('user2@motac.gov.my', $users);
    }

    #[Test]
    public function get_test_users_uses_config_when_set(): void
    {
        Config::set('services.google.oauth_test_users', [
            'config1@motac.gov.my',
            'config2@motac.gov.my',
        ]);
        Cache::flush();
        $this->service->clearCache();

        $users = $this->service->getTestUsers();

        $this->assertCount(2, $users);
        $this->assertContains('config1@motac.gov.my', $users);
    }

    #[Test]
    public function get_test_user_count_returns_correct_count(): void
    {
        $this->service->addTestUser('user1@motac.gov.my');
        $this->service->addTestUser('user2@motac.gov.my');
        $this->service->addTestUser('user3@motac.gov.my');

        $this->assertEquals(3, $this->service->getTestUserCount());
    }

    #[Test]
    public function get_max_test_users_returns_100(): void
    {
        $this->assertEquals(100, $this->service->getMaxTestUsers());
    }

    #[Test]
    public function is_test_user_limit_reached_returns_false_when_under_limit(): void
    {
        $this->service->addTestUser('user@motac.gov.my');

        $this->assertFalse($this->service->isTestUserLimitReached());
    }

    // =========================================================================
    // User Authentication Tests
    // =========================================================================

    #[Test]
    public function can_user_authenticate_returns_true_in_production_mode(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');
        Cache::flush();
        $this->service->clearCache();

        $result = $this->service->canUserAuthenticate('any.user@motac.gov.my');

        $this->assertTrue($result);
    }

    #[Test]
    public function can_user_authenticate_returns_false_for_non_motac_email(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');
        Cache::flush();
        $this->service->clearCache();

        $result = $this->service->canUserAuthenticate('user@gmail.com');

        $this->assertFalse($result);
    }

    #[Test]
    public function can_user_authenticate_returns_true_for_test_user_in_testing_mode(): void
    {
        $this->service->addTestUser('test.user@motac.gov.my');

        $result = $this->service->canUserAuthenticate('test.user@motac.gov.my');

        $this->assertTrue($result);
    }

    #[Test]
    public function can_user_authenticate_returns_false_for_non_test_user_in_testing_mode(): void
    {
        $result = $this->service->canUserAuthenticate('not.test.user@motac.gov.my');

        $this->assertFalse($result);
    }

    #[Test]
    #[DataProvider('emailCaseVariationsProvider')]
    public function can_user_authenticate_is_case_insensitive(string $email): void
    {
        $this->service->addTestUser('test.user@motac.gov.my');

        $result = $this->service->canUserAuthenticate($email);

        $this->assertTrue($result);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function emailCaseVariationsProvider(): array
    {
        return [
            'lowercase' => ['test.user@motac.gov.my'],
            'uppercase' => ['TEST.USER@MOTAC.GOV.MY'],
            'mixed case' => ['Test.User@Motac.Gov.My'],
        ];
    }

    // =========================================================================
    // Verification Requirements Tests
    // =========================================================================

    #[Test]
    public function get_verification_requirements_returns_all_requirements(): void
    {
        $requirements = $this->service->getVerificationRequirements();

        $this->assertArrayHasKey('privacy_policy', $requirements);
        $this->assertArrayHasKey('terms_of_service', $requirements);
        $this->assertArrayHasKey('domain_verification', $requirements);
        $this->assertArrayHasKey('authorized_domains', $requirements);
        $this->assertArrayHasKey('app_homepage', $requirements);
        $this->assertArrayHasKey('scopes_justification', $requirements);
    }

    #[Test]
    public function get_verification_requirements_includes_required_flag(): void
    {
        $requirements = $this->service->getVerificationRequirements();

        foreach ($requirements as $requirement) {
            $this->assertArrayHasKey('required', $requirement);
            $this->assertArrayHasKey('description', $requirement);
            $this->assertArrayHasKey('status', $requirement);
        }
    }

    // =========================================================================
    // Verification Details Tests
    // =========================================================================

    #[Test]
    public function get_verification_details_returns_complete_details(): void
    {
        $details = $this->service->getVerificationDetails();

        $this->assertArrayHasKey('status', $details);
        $this->assertArrayHasKey('status_label', $details);
        $this->assertArrayHasKey('is_production_mode', $details);
        $this->assertArrayHasKey('is_testing_mode', $details);
        $this->assertArrayHasKey('test_users_count', $details);
        $this->assertArrayHasKey('max_test_users', $details);
        $this->assertArrayHasKey('can_add_users', $details);
        $this->assertArrayHasKey('requirements', $details);
        $this->assertArrayHasKey('last_checked', $details);
    }

    #[Test]
    public function get_verification_details_reflects_current_state(): void
    {
        $this->service->addTestUser('user@motac.gov.my');

        $details = $this->service->getVerificationDetails();

        $this->assertEquals(GoogleOAuthVerificationService::STATUS_TESTING, $details['status']);
        $this->assertTrue($details['is_testing_mode']);
        $this->assertFalse($details['is_production_mode']);
        $this->assertEquals(1, $details['test_users_count']);
        $this->assertTrue($details['can_add_users']);
    }

    // =========================================================================
    // Bulk Operations Tests
    // =========================================================================

    #[Test]
    public function bulk_add_test_users_adds_multiple_users(): void
    {
        $emails = [
            'user1@motac.gov.my',
            'user2@motac.gov.my',
            'user3@motac.gov.my',
        ];

        $result = $this->service->bulkAddTestUsers($emails);

        $this->assertEquals(3, $result['added']);
        $this->assertEquals(0, $result['failed']);
        $this->assertEmpty($result['errors']);
    }

    #[Test]
    public function bulk_add_test_users_reports_failures(): void
    {
        $emails = [
            'valid@motac.gov.my',
            'invalid@gmail.com',
            'another.valid@motac.gov.my',
        ];

        $result = $this->service->bulkAddTestUsers($emails);

        $this->assertEquals(2, $result['added']);
        $this->assertEquals(1, $result['failed']);
        $this->assertNotEmpty($result['errors']);
    }

    #[Test]
    public function export_test_users_returns_complete_export(): void
    {
        $this->service->addTestUser('export1@motac.gov.my');
        $this->service->addTestUser('export2@motac.gov.my');

        $export = $this->service->exportTestUsers();

        $this->assertArrayHasKey('exported_at', $export);
        $this->assertArrayHasKey('verification_status', $export);
        $this->assertArrayHasKey('test_users', $export);
        $this->assertArrayHasKey('count', $export);
        $this->assertEquals(2, $export['count']);
    }

    #[Test]
    public function import_test_users_imports_new_users(): void
    {
        $emails = [
            'import1@motac.gov.my',
            'import2@motac.gov.my',
        ];

        $result = $this->service->importTestUsers($emails);

        $this->assertEquals(2, $result['imported']);
        $this->assertEquals(0, $result['skipped']);
    }

    #[Test]
    public function import_test_users_skips_existing_users(): void
    {
        $this->service->addTestUser('existing@motac.gov.my');

        $emails = [
            'existing@motac.gov.my',
            'new@motac.gov.my',
        ];

        $result = $this->service->importTestUsers($emails);

        $this->assertEquals(1, $result['imported']);
        $this->assertEquals(1, $result['skipped']);
    }

    // =========================================================================
    // Status Management Tests
    // =========================================================================

    #[Test]
    public function set_verification_status_updates_status(): void
    {
        $result = $this->service->setVerificationStatus(GoogleOAuthVerificationService::STATUS_VERIFIED);

        $this->assertTrue($result);
        $this->assertEquals(GoogleOAuthVerificationService::STATUS_VERIFIED, $this->service->getVerificationStatus());
    }

    #[Test]
    public function set_verification_status_rejects_invalid_status(): void
    {
        $result = $this->service->setVerificationStatus('invalid_status');

        $this->assertFalse($result);
    }

    #[Test]
    public function set_verification_status_sets_approved_at_when_verified(): void
    {
        $this->service->setVerificationStatus(GoogleOAuthVerificationService::STATUS_VERIFIED);

        $verification = GoogleOAuthVerification::where('client_id', 'test-client-id.apps.googleusercontent.com')->first();

        $this->assertNotNull($verification->verification_approved_at);
    }

    // =========================================================================
    // Cache Tests
    // =========================================================================

    #[Test]
    public function clear_cache_clears_all_verification_caches(): void
    {
        // Populate cache
        $this->service->getVerificationStatus();
        $this->service->getTestUsers();

        $this->service->clearCache();

        // Cache should be cleared - next call should hit database
        $this->assertNull(Cache::get('google_oauth_verification_status'));
        $this->assertNull(Cache::get('google_oauth_test_users'));
    }

    // =========================================================================
    // Test User Limitation Message Tests
    // =========================================================================

    #[Test]
    public function get_test_user_limitation_message_returns_empty_in_production(): void
    {
        Config::set('services.google.oauth_verification_status', 'verified');
        Cache::flush();
        $this->service->clearCache();

        $message = $this->service->getTestUserLimitationMessage('any@motac.gov.my');

        $this->assertEmpty($message);
    }

    #[Test]
    public function get_test_user_limitation_message_returns_empty_for_test_user(): void
    {
        $this->service->addTestUser('test@motac.gov.my');

        $message = $this->service->getTestUserLimitationMessage('test@motac.gov.my');

        $this->assertEmpty($message);
    }

    #[Test]
    public function get_test_user_limitation_message_returns_message_for_non_test_user(): void
    {
        $message = $this->service->getTestUserLimitationMessage('not.test@motac.gov.my');

        $this->assertNotEmpty($message);
    }
}
