<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ApprovalEmailToken;
use App\Models\AutoReplyDraft;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory untuk model ApprovalEmailToken
 *
 * Menjana token kelulusan e-mel untuk testing aliran kerja auto-reply
  *
 * @extends Factory<\App\Models\ApprovalEmailToken>
 */
class ApprovalEmailTokenFactory extends Factory
{
    /**
     * Model yang berkaitan dengan factory ini
     */
    protected $model = ApprovalEmailToken::class;

    /**
     * Takrifkan keadaan lalai model
     */
    public function definition(): array
    {
        $actions = ['approve', 'reject'];
        $isUsed = $this->faker->boolean(40); // 40% sudah digunakan

        return [
            'auto_reply_draft_id' => AutoReplyDraft::factory(),
            'token' => $this->generateSecureToken(),
            'action' => $this->faker->randomElement($actions),
            'expires_at' => $this->faker->dateTimeBetween('now', '+7 days'),
            'used' => $isUsed,
            'used_at' => $isUsed ? $this->faker->dateTimeBetween('-7 days', 'now') : null,
            'used_by_ip' => $isUsed ? $this->faker->ipv4 : null,
        ];
    }

    /**
     * State untuk token kelulusan (approve)
     */
    public function approve(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'approve',
        ]);
    }

    /**
     * State untuk token penolakan (reject)
     */
    public function reject(): static
    {
        return $this->state(fn (array $attributes) => [
            'action' => 'reject',
        ]);
    }

    /**
     * State untuk token yang belum digunakan
     */
    public function unused(): static
    {
        return $this->state(fn (array $attributes) => [
            'used' => false,
            'used_at' => null,
            'used_by_ip' => null,
        ]);
    }

    /**
     * State untuk token yang sudah digunakan
     */
    public function used(): static
    {
        return $this->state(fn (array $attributes) => [
            'used' => true,
            'used_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'used_by_ip' => $this->faker->ipv4,
        ]);
    }

    /**
     * State untuk token yang masih sah (belum tamat tempoh)
     */
    public function valid(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('+1 hour', '+7 days'),
            'used' => false,
            'used_at' => null,
            'used_by_ip' => null,
        ]);
    }

    /**
     * State untuk token yang sudah tamat tempoh
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('-7 days', '-1 hour'),
        ]);
    }

    /**
     * State untuk token yang akan tamat tempoh tidak lama lagi
     */
    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => $this->faker->dateTimeBetween('now', '+2 hours'),
            'used' => false,
        ]);
    }

    /**
     * State untuk token dengan IP address tertentu
     */
    public function withIpAddress(string $ipAddress): static
    {
        return $this->state(fn (array $attributes) => [
            'used' => true,
            'used_at' => $this->faker->dateTimeBetween('-1 day', 'now'),
            'used_by_ip' => $ipAddress,
        ]);
    }

    /**
     * Menjana token selamat yang unik
     */
    private function generateSecureToken(): string
    {
        return hash('sha256', uniqid('approval_', true) . microtime() . random_bytes(32));
    }
}
