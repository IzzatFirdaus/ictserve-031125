<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Helpdesk Ticket Factory
 *
 * PKS 5.2.1 Compliant - All tickets require authenticated user_id.
 * Guest submission functionality has been removed per PKS Accountability requirements.
 *
 * @see D03-FR-001.1 (Authenticated helpdesk ticket submission)
 * @see D04 §6.2 (Authentication Architecture)
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HelpdeskTicket>
 */
class HelpdeskTicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * PKS 5.2.1: All tickets must have authenticated user_id (NOT NULL).
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a category if none exists
        $category = TicketCategory::first() ?? TicketCategory::factory()->create();

        return [
            'ticket_number' => 'HD'.date('Y').str_pad((string) fake()->unique()->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT),
            'user_id' => User::factory(), // PKS 5.2.1: Mandatory authenticated user
            'category_id' => $category->id,
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(['low', 'normal', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'damage_type' => fake()->randomElement(['hardware', 'software', 'network', 'other']),
            'division_id' => \App\Models\Division::factory(),
            'job_grade' => fake()->randomElement(['41', '44', '48', '52', '54', '56', 'JUSA C', 'JUSA B', 'JUSA A']),
            'declaration_accepted' => true,
        ];
    }

    /**
     * State: Open ticket
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * State: In progress ticket
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * State: Resolved ticket
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
        ]);
    }

    /**
     * State: Closed ticket
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    /**
     * State: High priority ticket
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * State: Urgent priority ticket
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'urgent',
        ]);
    }

    /**
     * State: With specific user
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
