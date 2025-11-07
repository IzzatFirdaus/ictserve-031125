<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedSearch>
 */
class SavedSearchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $searchType = $this->faker->randomElement(['tickets', 'loans']);

        $filters = $searchType === 'tickets'
            ? [
                'status' => $this->faker->randomElements(['submitted', 'assigned', 'in_progress', 'resolved'], 2),
                'priority' => $this->faker->randomElements(['low', 'medium', 'high', 'critical'], 2),
                'category' => $this->faker->randomElements(['hardware', 'software', 'network'], 1),
            ]
            : [
                'status' => $this->faker->randomElements(['pending', 'approved', 'rejected'], 2),
                'asset_type' => $this->faker->randomElements(['laptop', 'projector', 'camera'], 1),
            ];

        return [
            'user_id' => \App\Models\User::factory(),
            'name' => $this->faker->words(3, true),
            'search_type' => $searchType,
            'filters' => $filters,
        ];
    }

    /**
     * Indicate that the saved search is for tickets
     */
    public function forTickets(): static
    {
        return $this->state(fn (array $attributes) => [
            'search_type' => 'tickets',
            'filters' => [
                'status' => ['submitted', 'assigned'],
                'priority' => ['high', 'critical'],
            ],
        ]);
    }

    /**
     * Indicate that the saved search is for loans
     */
    public function forLoans(): static
    {
        return $this->state(fn (array $attributes) => [
            'search_type' => 'loans',
            'filters' => [
                'status' => ['pending', 'approved'],
            ],
        ]);
    }
}
