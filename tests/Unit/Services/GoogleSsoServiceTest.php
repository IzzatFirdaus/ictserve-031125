<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\GoogleSsoServiceInterface;
use App\Contracts\SsoHealthCheckInterface;
use App\Exceptions\InvalidEmailDomainException;
use App\Models\User;
use App\Services\GoogleSsoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for GoogleSsoService
 *
 * Tests domain validation, user creation, account linking,
 * and audit logging functionality.
 *
 * @see Requirements 1.1, 1.2, 2.1, 4.1
 */
class GoogleSsoServiceTest extends TestCase
{
    use RefreshDatabase;

    private GoogleSsoService $service;

    private SsoHealthCheckInterface|MockInterface $healthCheck;

    protected function setUp(): void
    {
        parent::setUp();

        $this->healthCheck = Mockery::mock(SsoHealthCheckInterface::class);
        app()->instance(SsoHealthCheckInterface::class, $this->healthCheck);

        $this->service = app(GoogleSsoServiceInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(GoogleSsoServiceInterface::class);

        $this->assertInstanceOf(GoogleSsoService::class, $service);
    }

    #[Test]
    public function service_is_singleton(): void
    {
        $service1 = app(GoogleSsoServiceInterface::class);
        $service2 = app(GoogleSsoServiceInterface::class);

        $this->assertSame($service1, $service2);
    }

    #[Test]
    public function validate_domain_accepts_motac_email(): void
    {
        $this->assertTrue($this->service->validateDomain('user@motac.gov.my'));
    }

    #[Test]
    public function validate_domain_is_case_insensitive(): void
    {
        $this->assertTrue($this->service->validateDomain('USER@MOTAC.GOV.MY'));
        $this->assertTrue($this->service->validateDomain('User@Motac.Gov.My'));
        $this->assertTrue($this->service->validateDomain('user@MOTAC.gov.my'));
    }

    #[Test]
    #[DataProvider('invalidEmailDomainsProvider')]
    public function validate_domain_rejects_invalid_domains(string $email): void
    {
        $this->assertFalse($this->service->validateDomain($email));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidEmailDomainsProvider(): array
    {
        return [
            'gmail' => ['user@gmail.com'],
            'yahoo' => ['user@yahoo.com'],
            'hotmail' => ['user@hotmail.com'],
            'outlook' => ['user@outlook.com'],
            'company' => ['user@company.com'],
            'subdomain motac' => ['user@sub.motac.gov.my'],
            'wrong tld' => ['user@motac.gov.com'],
            'partial match' => ['user@notmotac.gov.my'],
            'empty local part' => ['@motac.gov.my'],
            'no at symbol' => ['usermotac.gov.my'],
            'empty string' => [''],
        ];
    }

    #[Test]
    public function get_allowed_domains_returns_motac_domain(): void
    {
        $domains = $this->service->getAllowedDomains();

        $this->assertIsArray($domains);
        $this->assertContains('motac.gov.my', $domains);
    }

    #[Test]
    public function create_or_update_user_creates_new_user(): void
    {
        $googleUser = $this->createMockSocialiteUser(
            'new.user@motac.gov.my',
            'New User',
            '123456789',
            'https://example.com/avatar.jpg'
        );

        $user = $this->service->createOrUpdateUser($googleUser);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('new.user@motac.gov.my', $user->email);
        $this->assertEquals('New User', $user->name);
        $this->assertEquals('123456789', $user->google_id);
        $this->assertEquals('https://example.com/avatar.jpg', $user->avatar);
        $this->assertNotNull($user->email_verified_at);
    }

    #[Test]
    public function create_or_update_user_is_idempotent(): void
    {
        $googleUser = $this->createMockSocialiteUser(
            'idempotent@motac.gov.my',
            'Idempotent User',
            '111222333',
            'https://example.com/avatar.jpg'
        );

        // First call creates user
        $user1 = $this->service->createOrUpdateUser($googleUser);

        // Second call should return same user
        $user2 = $this->service->createOrUpdateUser($googleUser);

        $this->assertEquals($user1->id, $user2->id);
        $this->assertEquals(1, User::where('email', 'idempotent@motac.gov.my')->count());
    }

    #[Test]
    public function create_or_update_user_throws_exception_for_invalid_domain(): void
    {
        $googleUser = $this->createMockSocialiteUser(
            'user@gmail.com',
            'Invalid User',
            '999888777',
            'https://example.com/avatar.jpg'
        );

        $this->expectException(InvalidEmailDomainException::class);

        $this->service->createOrUpdateUser($googleUser);
    }

    #[Test]
    public function link_existing_account_updates_google_id(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@motac.gov.my',
            'google_id' => null,
            'avatar' => null,
        ]);

        $googleUser = $this->createMockSocialiteUser(
            'existing@motac.gov.my',
            'Existing User',
            '444555666',
            'https://example.com/new-avatar.jpg'
        );

        $this->service->linkExistingAccount($user, $googleUser);

        $user->refresh();
        $this->assertEquals('444555666', $user->google_id);
        $this->assertEquals('https://example.com/new-avatar.jpg', $user->avatar);
    }

    #[Test]
    public function has_google_sso_linked_returns_true_when_linked(): void
    {
        $user = User::factory()->withGoogleSso()->create();

        $this->assertTrue($this->service->hasGoogleSsoLinked($user));
    }

    #[Test]
    public function has_google_sso_linked_returns_false_when_not_linked(): void
    {
        $user = User::factory()->create(['google_id' => null]);

        $this->assertFalse($this->service->hasGoogleSsoLinked($user));
    }

    #[Test]
    public function unlink_google_sso_removes_google_credentials(): void
    {
        $user = User::factory()->withGoogleSso()->create();

        $result = $this->service->unlinkGoogleSso($user);

        $this->assertTrue($result);
        $user->refresh();
        $this->assertNull($user->google_id);
    }

    #[Test]
    public function unlink_google_sso_returns_false_when_not_linked(): void
    {
        $user = User::factory()->create(['google_id' => null]);

        $result = $this->service->unlinkGoogleSso($user);

        $this->assertFalse($result);
    }

    #[Test]
    public function get_health_status_returns_configured_when_credentials_set(): void
    {
        $this->setHealthStatus($this->healthyStatus());

        $status = $this->service->getHealthStatus();

        $this->assertTrue($status['available']);
        $this->assertTrue($status['configured']);
        $this->assertEquals('Google SSO service is fully operational', $status['message']);
        $this->assertArrayHasKey('details', $status);
    }

    #[Test]
    public function get_health_status_returns_not_configured_when_credentials_missing(): void
    {
        $this->setHealthStatus($this->unconfiguredStatus());

        $status = $this->service->getHealthStatus();

        $this->assertFalse($status['available']);
        $this->assertFalse($status['configured']);
        $this->assertEquals('Google SSO is not properly configured', $status['message']);
    }

    /**
     * Create a mock Socialite user for testing
     */
    private function createMockSocialiteUser(
        string $email,
        string $name,
        string $id,
        string $avatar
    ): SocialiteUser {
        $mockUser = Mockery::mock(SocialiteUser::class);
        $mockUser->shouldReceive('getEmail')->andReturn($email);
        $mockUser->shouldReceive('getName')->andReturn($name);
        $mockUser->shouldReceive('getId')->andReturn($id);
        $mockUser->shouldReceive('getAvatar')->andReturn($avatar);

        return $mockUser;
    }

    /**
     * @return array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}
     */
    private function healthyStatus(): array
    {
        return [
            'status' => 'healthy',
            'configured' => true,
            'available' => true,
            'message' => 'Google SSO service is fully operational',
            'details' => [
                'configuration_errors' => [],
                'configuration_warnings' => [],
                'connectivity_tested' => true,
                'connectivity_passed' => true,
                'allowed_domains' => ['motac.gov.my'],
                'redirect_uri_configured' => true,
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return array{status: string, configured: bool, available: bool, message: string, details: array<string, mixed>, checked_at: string}
     */
    private function unconfiguredStatus(): array
    {
        return [
            'status' => 'unhealthy',
            'configured' => false,
            'available' => false,
            'message' => 'Google SSO is not properly configured',
            'details' => [
                'configuration_errors' => ['Google OAuth Client ID is not configured (GOOGLE_CLIENT_ID)'],
                'configuration_warnings' => [],
                'connectivity_tested' => false,
                'connectivity_passed' => false,
                'allowed_domains' => ['motac.gov.my'],
                'redirect_uri_configured' => false,
            ],
            'checked_at' => now()->toIso8601String(),
        ];
    }

    private function setHealthStatus(array $status): void
    {
        $this->healthCheck->allows('getServiceStatus')->andReturn($status);
    }
}
