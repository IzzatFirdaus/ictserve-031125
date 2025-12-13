<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SsoAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for SsoAuditLog model.
 *
 * @extends Factory<SsoAuditLog>
 */
class SsoAuditLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<SsoAuditLog>
     */
    protected $model = SsoAuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'email' => fake()->unique()->safeEmail(),
            'google_id' => fake()->numerify('##########'),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'success' => true,
            'error_type' => null,
            'error_message' => null,
            'attempted_at' => now(),
        ];
    }

    /**
     * Indicate that the audit log is for a successful authentication.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'success' => true,
            'error_type' => null,
            'error_message' => null,
        ]);
    }

    /**
     * Indicate that the audit log is for a failed authentication.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'success' => false,
            'error_type' => fake()->randomElement(['domain_error', 'oauth_error', 'network_error', 'general_error']),
            'error_message' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the audit log is for a domain validation error.
     */
    public function domainError(): static
    {
        return $this->state(fn (array $attributes) => [
            'success' => false,
            'error_type' => 'domain_error',
            'error_message' => 'Hanya akaun @motac.gov.my sahaja dibenarkan untuk log masuk.',
        ]);
    }

    /**
     * Indicate that the audit log is for an OAuth error.
     */
    public function oauthError(): static
    {
        return $this->state(fn (array $attributes) => [
            'success' => false,
            'error_type' => 'oauth_error',
            'error_message' => 'Pengesahan Google tidak berjaya.',
        ]);
    }

    /**
     * Indicate that the audit log is for a network error.
     */
    public function networkError(): static
    {
        return $this->state(fn (array $attributes) => [
            'success' => false,
            'error_type' => 'network_error',
            'error_message' => 'Masalah sambungan ke Google.',
        ]);
    }

    /**
     * Indicate that the audit log is for a @motac.gov.my email.
     */
    public function motacEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'email' => fake()->userName().'@motac.gov.my',
        ]);
    }

    /**
     * Associate the audit log with a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Create an audit log with a new user.
     */
    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }

    /**
     * Indicate that the audit log is from a specific IP address.
     */
    public function fromIp(string $ipAddress): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => $ipAddress,
        ]);
    }

    /**
     * Indicate that the audit log is from a recent time.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'attempted_at' => now()->subMinutes(fake()->numberBetween(1, 60)),
        ]);
    }

    /**
     * Indicate that the audit log is from an older time.
     */
    public function old(): static
    {
        return $this->state(fn (array $attributes) => [
            'attempted_at' => now()->subDays(fake()->numberBetween(7, 30)),
        ]);
    }
}
