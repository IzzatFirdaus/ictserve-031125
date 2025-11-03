<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Asset Factory
 *
 * Comprehensive factory with realistic ICT equipment data and state variations.
 *
 * @see D03-FR-005.1 Model factories for testing
 * @see D03-FR-018.1 Asset lifecycle management
 *
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchaseDate = fake()->dateTimeBetween('-5 years', '-1 year');
        $purchaseValue = fake()->randomFloat(2, 1000, 20000);

        return [
            'asset_tag' => $this->generateAssetTag(),
            'name' => fake()->randomElement([
                'Dell Latitude 5420 Laptop',
                'HP EliteBook 840 G8',
                'Lenovo ThinkPad X1 Carbon',
                'Epson EB-X05 Projector',
                'BenQ MH535A Projector',
                'Apple iPad Pro 12.9"',
                'Samsung Galaxy Tab S8',
                'Canon EOS 90D Camera',
                'Sony Alpha a7 III',
                'Cisco Catalyst 2960 Switch',
                'TP-Link Archer AX6000 Router',
            ]),
            'brand' => fake()->randomElement(['Dell', 'HP', 'Lenovo', 'Epson', 'BenQ', 'Apple', 'Samsung', 'Canon', 'Sony', 'Cisco', 'TP-Link']),
            'model' => fake()->bothify('??-####'),
            'serial_number' => fake()->unique()->bothify('SN-########'),
            'category_id' => AssetCategory::factory(),
            // Asset specifications
            'specifications' => $this->generateSpecifications(),
            'purchase_date' => $purchaseDate,
            'purchase_value' => $purchaseValue,
            'current_value' => $purchaseValue * fake()->randomFloat(2, 0.4, 0.8), // Depreciation
            'status' => AssetStatus::AVAILABLE,
            'location' => fake()->randomElement(['Putrajaya HQ', 'Kuala Lumpur Office', 'Cyberjaya Branch', 'Shah Alam Office']),
            'condition' => AssetCondition::GOOD,
            'accessories' => $this->generateAccessories(),
            'warranty_expiry' => fake()->dateTimeBetween($purchaseDate, '+3 years'),
            // Maintenance tracking
            'last_maintenance_date' => fake()->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'next_maintenance_date' => fake()->optional(0.5)->dateTimeBetween('now', '+6 months'),
            // Cross-module integration metrics
            'maintenance_tickets_count' => 0,
            'loan_history_summary' => null,
            'availability_calendar' => null,
            'utilization_metrics' => null,
        ];
    }

    /**
     * Generate unique asset tag
     */
    private function generateAssetTag(): string
    {
        $prefix = fake()->randomElement(['LAP', 'PRJ', 'TAB', 'CAM', 'NET']);
        $year = fake()->numberBetween(2019, 2025);
        $sequence = fake()->unique()->numberBetween(1000, 9999);

        return sprintf('%s-%d-%04d', $prefix, $year, $sequence);
    }

    /**
     * Generate realistic specifications
     */
    private function generateSpecifications(): array
    {
        return [
            'processor' => fake()->randomElement(['Intel Core i5-11th Gen', 'Intel Core i7-11th Gen', 'AMD Ryzen 5', 'AMD Ryzen 7']),
            'ram' => fake()->randomElement(['8GB', '16GB', '32GB']),
            'storage' => fake()->randomElement(['256GB SSD', '512GB SSD', '1TB SSD']),
            'display' => fake()->randomElement(['14" FHD', '15.6" FHD', '13.3" QHD']),
            'os' => fake()->randomElement(['Windows 11 Pro', 'Windows 10 Pro', 'macOS']),
        ];
    }

    /**
     * Generate realistic accessories
     */
    private function generateAccessories(): array
    {
        return fake()->randomElements([
            'Power Adapter',
            'Carrying Case',
            'Wireless Mouse',
            'USB-C Hub',
            'HDMI Cable',
            'VGA Cable',
            'Remote Control',
            'Lens Cap',
            'Memory Card',
            'Battery Pack',
        ], fake()->numberBetween(2, 5));
    }

    /**
     * State: Available for loan
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::AVAILABLE,
            'condition' => fake()->randomElement([AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR]),
        ]);
    }

    /**
     * State: Currently loaned
     */
    public function loaned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::LOANED,
            'condition' => fake()->randomElement([AssetCondition::EXCELLENT, AssetCondition::GOOD]),
        ]);
    }

    /**
     * State: Under maintenance
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::MAINTENANCE,
            'condition' => fake()->randomElement([AssetCondition::FAIR, AssetCondition::POOR]),
            'maintenance_tickets_count' => fake()->numberBetween(1, 5),
            'last_maintenance_date' => now()->subDays(fake()->numberBetween(1, 30)),
            'next_maintenance_date' => now()->addDays(fake()->numberBetween(30, 90)),
        ]);
    }

    /**
     * State: Damaged
     */
    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::DAMAGED,
            'condition' => AssetCondition::DAMAGED,
            'maintenance_tickets_count' => fake()->numberBetween(1, 3),
        ]);
    }

    /**
     * State: Retired
     */
    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::RETIRED,
            'condition' => fake()->randomElement([AssetCondition::POOR, AssetCondition::DAMAGED]),
            'current_value' => 0,
        ]);
    }

    /**
     * State: Excellent condition
     */
    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::EXCELLENT,
        ]);
    }

    /**
     * State: Good condition
     */
    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::GOOD,
        ]);
    }

    /**
     * State: Fair condition
     */
    public function fair(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::FAIR,
        ]);
    }

    /**
     * State: Poor condition
     */
    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::POOR,
        ]);
    }

    /**
     * State: With helpdesk integration data
     */
    public function withHelpdeskHistory(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_tickets_count' => fake()->numberBetween(3, 10),
            'loan_history_summary' => [
                'total_loans' => fake()->numberBetween(10, 50),
                'total_days_loaned' => fake()->numberBetween(100, 500),
                'average_loan_duration' => fake()->numberBetween(7, 21),
            ],
            'utilization_metrics' => [
                'utilization_rate' => fake()->randomFloat(2, 0.5, 0.95),
                'last_loan_date' => now()->subDays(fake()->numberBetween(1, 30))->toDateString(),
            ],
        ]);
    }

    /**
     * State: Warranty expired
     */
    public function warrantyExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->subYears(fake()->numberBetween(1, 3)),
        ]);
    }

    /**
     * State: Under warranty
     */
    public function underWarranty(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->addYears(fake()->numberBetween(1, 2)),
        ]);
    }
}
