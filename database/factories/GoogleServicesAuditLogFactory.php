<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GoogleServicesAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoogleServicesAuditLog>
 */
class GoogleServicesAuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<GoogleServicesAuditLog>
     */
    protected $model = GoogleServicesAuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'email' => fake()->unique()->safeEmail().'@motac.gov.my',
            'google_id' => fake()->numerify('####################'),
            'service_type' => fake()->randomElement([
                GoogleServicesAuditLog::SERVICE_SSO,
                GoogleServicesAuditLog::SERVICE_GMAIL,
            ]),
            'operation_type' => GoogleServicesAuditLog::OPERATION_AUTHENTICATE,
            'authentication_method' => GoogleServicesAuditLog::AUTH_OAUTH,
            'verification_status' => fake()->randomElement([
                GoogleServicesAuditLog::VERIFICATION_VERIFIED,
                GoogleServicesAuditLog::VERIFICATION_TESTING,
            ]),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'success' => true,
            'error_type' => null,
            'error_message' => null,
            'metadata' => null,
            'attempted_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }

    /**
     * Indicate that the audit log is for a successful operation.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes): array => [
            'success' => true,
            'error_type' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the audit log is for a failed operation.
     */
    public function failed(?string $errorType = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'success' => false,
            'error_type' => $errorType ?? fake()->randomElement([
                GoogleServicesAuditLog::ERROR_DOMAIN,
                GoogleServicesAuditLog::ERROR_OAUTH,
                GoogleServicesAuditLog::ERROR_NETWORK,
                GoogleServicesAuditLog::ERROR_GENERAL,
            ]),
            'error_message' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the audit log is for SSO service.
     */
    public function sso(): static
    {
        return $this->state(fn (array $attributes): array => [
            'service_type' => GoogleServicesAuditLog::SERVICE_SSO,
            'operation_type' => GoogleServicesAuditLog::OPERATION_AUTHENTICATE,
        ]);
    }

    /**
     * Indicate that the audit log is for Gmail service.
     */
    public function gmail(): static
    {
        return $this->state(fn (array $attributes): array => [
            'service_type' => GoogleServicesAuditLog::SERVICE_GMAIL,
            'operation_type' => fake()->randomElement([
                GoogleServicesAuditLog::OPERATION_SEND_EMAIL,
                GoogleServicesAuditLog::OPERATION_AUTHORIZE,
            ]),
        ]);
    }

    /**
     * Indicate that the audit log is for email sending.
     */
    public function sendEmail(): static
    {
        return $this->state(fn (array $attributes): array => [
            'service_type' => GoogleServicesAuditLog::SERVICE_GMAIL,
            'operation_type' => GoogleServicesAuditLog::OPERATION_SEND_EMAIL,
        ]);
    }

    /**
     * Indicate that the audit log uses OAuth authentication.
     */
    public function oauth(): static
    {
        return $this->state(fn (array $attributes): array => [
            'authentication_method' => GoogleServicesAuditLog::AUTH_OAUTH,
        ]);
    }

    /**
     * Indicate that the audit log uses service account authentication.
     */
    public function serviceAccount(): static
    {
        return $this->state(fn (array $attributes): array => [
            'authentication_method' => GoogleServicesAuditLog::AUTH_SERVICE_ACCOUNT,
        ]);
    }

    /**
     * Indicate that the audit log uses SMTP fallback.
     */
    public function smtpFallback(): static
    {
        return $this->state(fn (array $attributes): array => [
            'authentication_method' => GoogleServicesAuditLog::AUTH_SMTP_FALLBACK,
        ]);
    }

    /**
     * Indicate that the operation was in verified mode.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleServicesAuditLog::VERIFICATION_VERIFIED,
        ]);
    }

    /**
     * Indicate that the operation was in testing mode.
     */
    public function testing(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleServicesAuditLog::VERIFICATION_TESTING,
        ]);
    }

    /**
     * Indicate that the operation had a quota exceeded error.
     */
    public function quotaExceeded(): static
    {
        return $this->state(fn (array $attributes): array => [
            'success' => false,
            'error_type' => GoogleServicesAuditLog::ERROR_QUOTA_EXCEEDED,
            'error_message' => 'Gmail API quota exceeded',
        ]);
    }

    /**
     * Indicate that the operation was rate limited.
     */
    public function rateLimited(): static
    {
        return $this->state(fn (array $attributes): array => [
            'success' => false,
            'error_type' => GoogleServicesAuditLog::ERROR_RATE_LIMITED,
            'error_message' => 'Rate limit exceeded',
        ]);
    }

    /**
     * Indicate that the operation had a domain validation error.
     */
    public function domainError(): static
    {
        return $this->state(fn (array $attributes): array => [
            'success' => false,
            'error_type' => GoogleServicesAuditLog::ERROR_DOMAIN,
            'error_message' => 'Invalid email domain',
            'email' => fake()->safeEmail(), // Non-motac email
        ]);
    }

    /**
     * Set custom metadata for the audit log.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function withMetadata(array $metadata): static
    {
        return $this->state(fn (array $attributes): array => [
            'metadata' => $metadata,
        ]);
    }

    /**
     * Create an audit log without a user association.
     */
    public function withoutUser(): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => null,
        ]);
    }

    /**
     * Create an audit log for a recent attempt.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes): array => [
            'attempted_at' => fake()->dateTimeBetween('-1 hour', 'now'),
        ]);
    }
}
