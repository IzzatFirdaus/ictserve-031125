<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserNotificationPreference>
 */
class UserNotificationPreferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'preference_key' => $this->faker->randomElement([
                'ticket_status_updates',
                'loan_approval_notifications',
                'overdue_reminders',
                'system_announcements',
                'ticket_assignments',
                'comment_replies',
            ]),
            'preference_value' => $this->faker->boolean(80), // 80% chance of being enabled
        ];
    }

    /**
     * Indicate that the preference is enabled
     */
    public function enabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_value' => true,
        ]);
    }

    /**
     * Indicate that the preference is disabled
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_value' => false,
        ]);
    }

    /**
     * Set a specific preference key
     */
    public function forKey(string $key): static
    {
        return $this->state(fn (array $attributes) => [
            'preference_key' => $key,
        ]);
    }
}
