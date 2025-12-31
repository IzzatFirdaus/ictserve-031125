<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Mail\TicketCreatedConfirmation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\EmailLog>
 */
class EmailLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'recipient_email' => fake()->unique()->safeEmail(),
            'recipient_name' => fake()->name(),
            'subject' => fake()->sentence(),
            'mailable_class' => TicketCreatedConfirmation::class,
            'status' => 'queued',
            'message_id' => fake()->uuid(),
            'status_message' => null,
            'meta' => ['locale' => fake()->randomElement(['ms', 'en'])],
            'queued_at' => now(),
            'sent_at' => null,
            'failed_at' => null,
            'delivered_at' => null,
            'retry_attempts' => 0,
            'notification_type' => fake()->randomElement(['ticket_assigned', 'loan_approved', 'asset_overdue', null]),
            'priority' => fake()->randomElement(['critical', 'high', 'normal', 'low']),
            'final_status' => null,
            'next_retry_at' => null,
            'preference_bypassed' => false,
        ];
    }

    /**
     * Indicate that the email was delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'sent_at' => now()->subMinutes(5),
            'delivered_at' => now(),
            'final_status' => 'delivered',
        ]);
    }

    /**
     * Indicate that the email failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failed_at' => now(),
            'status_message' => 'Connection timeout',
            'retry_attempts' => fake()->numberBetween(1, 3),
        ]);
    }

    /**
     * Indicate that the email permanently failed.
     */
    public function permanentlyFailed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'permanently_failed',
            'final_status' => 'permanently_failed',
            'failed_at' => now(),
            'status_message' => 'Max retries exceeded',
            'retry_attempts' => 3,
        ]);
    }

    /**
     * Indicate that the email bounced.
     */
    public function bounced(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'bounced',
            'final_status' => 'bounced',
            'failed_at' => now(),
            'status_message' => 'Mailbox not found',
            'meta' => ['bounce_type' => 'hard'],
        ]);
    }

    /**
     * Indicate that the email was sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Set a specific notification type.
     */
    public function ofType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'notification_type' => $type,
        ]);
    }

    /**
     * Set a specific priority.
     */
    public function withPriority(string $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }
}
