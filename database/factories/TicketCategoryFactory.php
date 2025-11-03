<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TicketCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Ticket Category Factory
 *
 * Provides bilingual default data for helpdesk ticket categories.
 *
 * @extends Factory<TicketCategory>
 */
class TicketCategoryFactory extends Factory
{
    protected $model = TicketCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('CAT-###'),
            'name_ms' => fake()->randomElement([
                'Kategori Perkakasan',
                'Kategori Perisian',
                'Kategori Aplikasi',
            ]),
            'name_en' => fake()->randomElement([
                'Hardware Category',
                'Software Category',
                'Application Category',
            ]),
            'description_ms' => fake()->sentence(),
            'description_en' => fake()->sentence(),
            'parent_id' => null,
            'sla_response_hours' => fake()->numberBetween(1, 8),
            'sla_resolution_hours' => fake()->numberBetween(8, 72),
            'is_active' => true,
        ];
    }

    /**
     * State: Hardware issues category.
     */
    public function hardware(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'HARDWARE',
            'name_ms' => 'Isu Perkakasan',
            'name_en' => 'Hardware Issues',
            'description_ms' => 'Permasalahan berkaitan perkakasan ICT.',
            'description_en' => 'Issues related to ICT hardware.',
        ]);
    }
}
