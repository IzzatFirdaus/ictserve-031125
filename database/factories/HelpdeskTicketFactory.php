<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HelpdeskTicket>
 */
class HelpdeskTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a category if none exists
        $category = TicketCategory::first() ?? TicketCategory::factory()->create();

        return [
            'ticket_number' => 'HD'.date('Y').str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'category_id' => $category->id,
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'damage_type' => fake()->randomElement(['hardware', 'software', 'network', 'other']),
        ];
    }

    /**
     * Indicate that the ticket is a guest submission.
     */
    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'guest_staff_id' => 'MOTAC'.fake()->numberBetween(1000, 9999),
            'guest_grade' => fake()->randomElement(['N32', 'N36', 'N41', 'N44']),
            'guest_division' => fake()->randomElement(['ICT', 'HR', 'Finance', 'Admin']),
        ]);
    }

    /**
     * Indicate that the ticket is an authenticated submission.
     */
    public function authenticated(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
            'guest_name' => null,
            'guest_email' => null,
            'guest_phone' => null,
            'guest_staff_id' => null,
            'guest_grade' => null,
            'guest_division' => null,
        ]);
    }
}
