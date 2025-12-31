<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\GoogleOAuthVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for GoogleOAuthVerification model
 *
 * @extends Factory<GoogleOAuthVerification>
 */
class GoogleOAuthVerificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<GoogleOAuthVerification>
     */
    protected $model = GoogleOAuthVerification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => $this->faker->uuid().'.apps.googleusercontent.com',
            'verification_status' => GoogleOAuthVerification::STATUS_TESTING,
            'test_users' => [],
            'verification_submitted_at' => null,
            'verification_approved_at' => null,
            'verification_documents' => [],
            'quota_limits' => [
                'daily_requests' => 10000,
                'per_user_requests' => 100,
            ],
            'last_status_check' => now(),
        ];
    }

    /**
     * Indicate that the OAuth app is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleOAuthVerification::STATUS_VERIFIED,
            'verification_submitted_at' => now()->subDays(30),
            'verification_approved_at' => now()->subDays(7),
        ]);
    }

    /**
     * Indicate that the OAuth app is in testing mode.
     */
    public function testing(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleOAuthVerification::STATUS_TESTING,
            'verification_submitted_at' => null,
            'verification_approved_at' => null,
        ]);
    }

    /**
     * Indicate that the OAuth app verification is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleOAuthVerification::STATUS_PENDING,
            'verification_submitted_at' => now()->subDays(7),
            'verification_approved_at' => null,
        ]);
    }

    /**
     * Indicate that the OAuth app verification was rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_status' => GoogleOAuthVerification::STATUS_REJECTED,
            'verification_submitted_at' => now()->subDays(14),
            'verification_approved_at' => null,
        ]);
    }

    /**
     * Add test users to the OAuth app.
     *
     * @param  array<string>  $emails
     */
    public function withTestUsers(array $emails): static
    {
        return $this->state(fn (array $attributes): array => [
            'test_users' => $emails,
        ]);
    }

    /**
     * Add verification documents.
     *
     * @param  array<string, mixed>  $documents
     */
    public function withDocuments(array $documents): static
    {
        return $this->state(fn (array $attributes): array => [
            'verification_documents' => $documents,
        ]);
    }

    /**
     * Set custom quota limits.
     *
     * @param  array<string, int>  $limits
     */
    public function withQuotaLimits(array $limits): static
    {
        return $this->state(fn (array $attributes): array => [
            'quota_limits' => $limits,
        ]);
    }
}
