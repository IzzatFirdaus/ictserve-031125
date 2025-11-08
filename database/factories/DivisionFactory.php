<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Division;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Division Factory
 *
 * Factory for organizational divisions with bilingual support.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D03-FR-016.2 Shared organizational data
 *
 * @extends Factory<Division>
 */
class DivisionFactory extends Factory
{
    protected $model = Division::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $divisions = [
            [
                'code' => 'ICT',
                'name_ms' => 'Bahagian Teknologi Maklumat dan Komunikasi',
                'name_en' => 'Information and Communication Technology Division',
                'desc_ms' => 'Menguruskan infrastruktur dan perkhidmatan ICT',
                'desc_en' => 'Manages ICT infrastructure and services',
            ],
            [
                'code' => 'HR',
                'name_ms' => 'Bahagian Sumber Manusia',
                'name_en' => 'Human Resources Division',
                'desc_ms' => 'Menguruskan hal ehwal kakitangan',
                'desc_en' => 'Manages staff affairs',
            ],
            [
                'code' => 'FIN',
                'name_ms' => 'Bahagian Kewangan',
                'name_en' => 'Finance Division',
                'desc_ms' => 'Menguruskan kewangan dan perakaunan',
                'desc_en' => 'Manages finance and accounting',
            ],
            [
                'code' => 'ADMIN',
                'name_ms' => 'Bahagian Pentadbiran',
                'name_en' => 'Administration Division',
                'desc_ms' => 'Menguruskan pentadbiran am',
                'desc_en' => 'Manages general administration',
            ],
            [
                'code' => 'TOURISM',
                'name_ms' => 'Bahagian Pelancongan',
                'name_en' => 'Tourism Division',
                'desc_ms' => 'Menguruskan pembangunan pelancongan',
                'desc_en' => 'Manages tourism development',
            ],
        ];

        $division = fake()->randomElement($divisions);

        return [
            'code' => fake()->unique()->bothify('??-###'),
            'name_ms' => $division['name_ms'],
            'name_en' => $division['name_en'],
            'description_ms' => $division['desc_ms'],
            'description_en' => $division['desc_en'],
            'parent_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * State: ICT Division
     */
    public function ict(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'ICT',
            'name_ms' => 'Bahagian Teknologi Maklumat dan Komunikasi',
            'name_en' => 'Information and Communication Technology Division',
            'description_ms' => 'Menguruskan infrastruktur dan perkhidmatan ICT',
            'description_en' => 'Manages ICT infrastructure and services',
        ]);
    }

    /**
     * State: HR Division
     */
    public function hr(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'HR',
            'name_ms' => 'Bahagian Sumber Manusia',
            'name_en' => 'Human Resources Division',
            'description_ms' => 'Menguruskan hal ehwal kakitangan',
            'description_en' => 'Manages staff affairs',
        ]);
    }

    /**
     * State: Finance Division
     */
    public function finance(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => 'FIN',
            'name_ms' => 'Bahagian Kewangan',
            'name_en' => 'Finance Division',
            'description_ms' => 'Menguruskan kewangan dan perakaunan',
            'description_en' => 'Manages finance and accounting',
        ]);
    }

    /**
     * State: With parent division
     */
    public function withParent(): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => Division::factory(),
        ]);
    }

    /**
     * State: Inactive division
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
