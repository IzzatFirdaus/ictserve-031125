<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AuthenticationLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * PKS 5.4.3 Password Policy Compliance - Authentication Logging Tests
 *
 * Tests authentication attempt logging for security monitoring.
 *
 * @see D03-FR-027 (Authentication Requirements)
 * @see PKS 5.4.3 (Password Policy Requirements)
 *
 * @trace Requirements 27.4, 27.5
 */
class AuthenticationLoggingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function can_log_successful_authentication(): void
    {
        $user = User::factory()->create();

        $log = AuthenticationLog::logSuccess(
            username: $user->email,
            authMethod: AuthenticationLog::METHOD_LDAP,
            userId: $user->id,
        );

        $this->assertDatabaseHas('authentication_logs', [
            'id' => $log->id,
            'user_id' => $user->id,
            'username' => $user->email,
            'auth_method' => AuthenticationLog::METHOD_LDAP,
            'status' => AuthenticationLog::STATUS_SUCCESS,
        ]);
    }

    #[Test]
    public function can_log_failed_authentication(): void
    {
        $log = AuthenticationLog::logFailure(
            username: 'test@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            reason: AuthenticationLog::REASON_INVALID_CREDENTIALS,
            failedAttempts: 1,
        );

        $this->assertDatabaseHas('authentication_logs', [
            'id' => $log->id,
            'username' => 'test@example.com',
            'status' => AuthenticationLog::STATUS_FAILED,
            'failure_reason' => AuthenticationLog::REASON_INVALID_CREDENTIALS,
            'failed_attempts' => 1,
        ]);
    }

    #[Test]
    public function can_log_lockout_event(): void
    {
        $lockoutUntil = now()->addMinutes(30);

        $log = AuthenticationLog::logLockout(
            username: 'test@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            failedAttempts: 3,
            lockoutUntil: $lockoutUntil,
        );

        $this->assertDatabaseHas('authentication_logs', [
            'id' => $log->id,
            'username' => 'test@example.com',
            'status' => AuthenticationLog::STATUS_LOCKED,
            'is_lockout_event' => true,
            'failed_attempts' => 3,
        ]);

        $this->assertTrue($log->is_lockout_event);
    }

    #[Test]
    public function successful_scope_filters_correctly(): void
    {
        $user = User::factory()->create();

        AuthenticationLog::logSuccess(
            username: $user->email,
            authMethod: AuthenticationLog::METHOD_LDAP,
            userId: $user->id,
        );

        AuthenticationLog::logFailure(
            username: 'failed@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            reason: AuthenticationLog::REASON_INVALID_CREDENTIALS,
        );

        $this->assertEquals(1, AuthenticationLog::successful()->count());
        $this->assertEquals(1, AuthenticationLog::failed()->count());
    }

    #[Test]
    public function lockouts_scope_filters_correctly(): void
    {
        AuthenticationLog::logFailure(
            username: 'test@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            reason: AuthenticationLog::REASON_INVALID_CREDENTIALS,
        );

        AuthenticationLog::logLockout(
            username: 'locked@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            failedAttempts: 3,
            lockoutUntil: now()->addMinutes(30),
        );

        $this->assertEquals(1, AuthenticationLog::lockouts()->count());
    }

    #[Test]
    public function security_stats_calculation(): void
    {
        $user = User::factory()->create();

        // Create successful logins
        for ($i = 0; $i < 8; $i++) {
            AuthenticationLog::logSuccess(
                username: $user->email,
                authMethod: AuthenticationLog::METHOD_LDAP,
                userId: $user->id,
            );
        }

        // Create failed logins
        for ($i = 0; $i < 2; $i++) {
            AuthenticationLog::logFailure(
                username: 'failed@example.com',
                authMethod: AuthenticationLog::METHOD_LDAP,
                reason: AuthenticationLog::REASON_INVALID_CREDENTIALS,
            );
        }

        $stats = AuthenticationLog::getSecurityStats();

        $this->assertEquals(10, $stats['total_attempts']);
        $this->assertEquals(10, $stats['today_attempts']);
        $this->assertEquals(8, $stats['today_successful']);
        $this->assertEquals(2, $stats['today_failed']);
        $this->assertEquals(80.0, $stats['success_rate']);
    }

    #[Test]
    public function password_policy_messages_are_in_bahasa_melayu(): void
    {
        $messages = AuthenticationLog::getPasswordPolicyMessages();

        $this->assertArrayHasKey('min_length', $messages);
        $this->assertArrayHasKey('max_age', $messages);
        $this->assertArrayHasKey('lockout_threshold', $messages);
        $this->assertArrayHasKey('lockout_duration', $messages);
        $this->assertArrayHasKey('account_locked', $messages);
        $this->assertArrayHasKey('invalid_credentials', $messages);

        // Verify messages are in Bahasa Melayu
        $this->assertStringContainsString('aksara', $messages['min_length']);
        $this->assertStringContainsString('hari', $messages['max_age']);
        $this->assertStringContainsString('dikunci', $messages['lockout_threshold']);
    }

    #[Test]
    public function localized_failure_message_for_lockout(): void
    {
        $lockoutUntil = now()->addMinutes(25);

        $log = AuthenticationLog::logLockout(
            username: 'test@example.com',
            authMethod: AuthenticationLog::METHOD_LDAP,
            failedAttempts: 3,
            lockoutUntil: $lockoutUntil,
        );

        $message = $log->getLocalizedFailureMessage();

        $this->assertStringContainsString('dikunci', $message);
        $this->assertStringContainsString('minit', $message);
    }

    #[Test]
    public function authentication_log_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $log = AuthenticationLog::logSuccess(
            username: $user->email,
            authMethod: AuthenticationLog::METHOD_LDAP,
            userId: $user->id,
        );

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }
}
