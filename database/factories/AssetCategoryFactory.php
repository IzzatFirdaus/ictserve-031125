<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Asset Category Factory
 *
 * Factory for ICT equipment categories with specification templates.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D03-FR-018.2 Asset categorization system
 *
 * @extends Factory<AssetCategory>
 */
class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Laptops', 'duration' => 14, 'max' => 30],
            ['name' => 'Projectors', 'duration' => 7, 'max' => 14],
            ['name' => 'Tablets', 'duration' => 14, 'max' => 30],
            ['name' => 'Cameras', 'duration' => 7, 'max' => 14],
            ['name' => 'Networking Equipment', 'duration' => 30, 'max' => 90],
        ];

        $category = fake()->randomElement($categories);
        $code = fake()->unique()->bothify('???');
        $name = $category['name'].' '.fake()->unique()->numberBetween(1, 999);

        return [
            'name' => $name,
            'code' => $code,
            'description' => fake()->sentence(10),
            'specification_template' => $this->generateSpecificationTemplate($code),
            'default_loan_duration_days' => $category['duration'],
            'max_loan_duration_days' => $category['max'],
            'requires_approval' => fake()->boolean(80), // 80% require approval
            'is_active' => true,
            'sort_order' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * Generate specification template based on category
     */
    private function generateSpecificationTemplate(string $code): array
    {
        return match ($code) {
            'LAP' => [
                'processor' => 'text',
                'ram' => 'text',
                'storage' => 'text',
                'display' => 'text',
                'os' => 'text',
                'graphics' => 'text',
            ],
            'PRJ' => [
                'brightness' => 'text',
                'resolution' => 'text',
                'contrast_ratio' => 'text',
                'lamp_life' => 'text',
                'connectivity' => 'text',
            ],
            'TAB' => [
                'screen_size' => 'text',
                'storage' => 'text',
                'ram' => 'text',
                'os' => 'text',
                'connectivity' => 'text',
            ],
            'CAM' => [
                'sensor' => 'text',
                'megapixels' => 'text',
                'lens' => 'text',
                'video_resolution' => 'text',
                'storage_type' => 'text',
            ],
            'NET' => [
                'ports' => 'text',
                'speed' => 'text',
                'protocol' => 'text',
                'management' => 'text',
                'power' => 'text',
            ],
            default => [
                'specifications' => 'text',
            ],
        };
    }

    /**
     * State: Laptops category
     */
    public function laptops(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Laptops',
            'code' => 'LAP',
            'description' => 'Portable computing devices for office and field work',
            'specification_template' => $this->generateSpecificationTemplate('LAP'),
            'default_loan_duration_days' => 14,
            'max_loan_duration_days' => 30,
            'requires_approval' => true,
        ]);
    }

    /**
     * State: Projectors category
     */
    public function projectors(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Projectors',
            'code' => 'PRJ',
            'description' => 'Presentation equipment for meetings and events',
            'specification_template' => $this->generateSpecificationTemplate('PRJ'),
            'default_loan_duration_days' => 7,
            'max_loan_duration_days' => 14,
            'requires_approval' => true,
        ]);
    }

    /**
     * State: Tablets category
     */
    public function tablets(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Tablets',
            'code' => 'TAB',
            'description' => 'Mobile computing devices for presentations and field work',
            'specification_template' => $this->generateSpecificationTemplate('TAB'),
            'default_loan_duration_days' => 14,
            'max_loan_duration_days' => 30,
            'requires_approval' => true,
        ]);
    }

    /**
     * State: Cameras category
     */
    public function cameras(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Cameras',
            'code' => 'CAM',
            'description' => 'Photography and videography equipment for events',
            'specification_template' => $this->generateSpecificationTemplate('CAM'),
            'default_loan_duration_days' => 7,
            'max_loan_duration_days' => 14,
            'requires_approval' => true,
        ]);
    }

    /**
     * State: Networking Equipment category
     */
    public function networking(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Networking Equipment',
            'code' => 'NET',
            'description' => 'Network infrastructure equipment for connectivity',
            'specification_template' => $this->generateSpecificationTemplate('NET'),
            'default_loan_duration_days' => 30,
            'max_loan_duration_days' => 90,
            'requires_approval' => true,
        ]);
    }

    /**
     * State: No approval required
     */
    public function noApproval(): static
    {
        return $this->state(fn (array $attributes) => [
            'requires_approval' => false,
        ]);
    }

    /**
     * State: Inactive category
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
