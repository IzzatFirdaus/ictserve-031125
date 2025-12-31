<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ScheduledNotification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ScheduledNotification>
 */
class ScheduledNotificationFactory extends Factory
{
    protected $model = ScheduledNotification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'schedule_id' => (string) Str::uuid(),
            'user_id' => User::factory(),
            'notification_class' => 'App\\Notifications\\TestNotification',
            'notification_data' => [
                'class' => 'App\\Notifications\\TestNotification',
                'data' => ['message' => $this->faker->sentence()],
            ],
            'notification_type' => $this->faker->randomElement(['ticket_updates', 'loan_updates', 'system_announcements']),
            'priority' => $this->faker->randomElement(['critical', 'high', 'normal', 'low']),
            'channels' => ['database', 'mail'],
            'scheduled_at' => $this->faker->dateTimeBetween('now', '+1 week'),
            'status' => ScheduledNotification::STATUS_PENDING,
            'is_recurring' => false,
            'metadata' => [],
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduledNotification::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduledNotification::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ScheduledNotification::STATUS_FAILED,
            'error_message' => $this->faker->sentence(),
            'retry_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    public function recurring(string $pattern = ScheduledNotification::RECURRENCE_DAILY): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurrence_pattern' => $pattern,
            'next_occurrence_at' => now()->addDay(),
        ]);
    }

    public function due(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => now()->subMinutes(5),
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);
    }
}
