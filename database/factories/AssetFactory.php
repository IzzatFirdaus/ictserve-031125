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
        $purchaseDate = $this->faker->dateTimeBetween('-5 years', '-1 year');
        $purchaseValue = $this->faker->randomFloat(2, 1000, 20000);

        return [
            'asset_tag' => $this->generateAssetTag(),
            'name' => $this->faker->randomElement([
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
            'brand' => $this->faker->randomElement(['Dell', 'HP', 'Lenovo', 'Epson', 'BenQ', 'Apple', 'Samsung', 'Canon', 'Sony', 'Cisco', 'TP-Link']),
            'model' => $this->faker->bothify('??-####'),
            'serial_number' => $this->faker->unique()->bothify('SN-########'),
            'category_id' => AssetCategory::factory(),
            // Asset specifications
            'specifications' => $this->generateSpecifications(),
            'purchase_date' => $purchaseDate,
            'purchase_value' => $purchaseValue,
            'current_value' => $purchaseValue * $this->faker->randomFloat(2, 0.4, 0.8), // Depreciation
            'status' => AssetStatus::AVAILABLE,
            'location' => $this->faker->randomElement(['Putrajaya HQ', 'Kuala Lumpur Office', 'Cyberjaya Branch', 'Shah Alam Office']),
            'condition' => AssetCondition::GOOD,
            'accessories' => $this->generateAccessories(),
            'warranty_expiry' => $this->faker->dateTimeBetween($purchaseDate, '+3 years'),
            // Maintenance tracking
            'last_maintenance_date' => $this->faker->optional(0.6)->dateTimeBetween('-6 months', 'now'),
            'next_maintenance_date' => $this->faker->optional(0.5)->dateTimeBetween('now', '+6 months'),
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
        $prefix = $this->faker->randomElement(['LAP', 'PRJ', 'TAB', 'CAM', 'NET']);
        $year = $this->faker->numberBetween(2019, 2025);
        $sequence = $this->faker->unique()->numberBetween(1000, 9999);

        return sprintf('%s-%d-%04d', $prefix, $year, $sequence);
    }

    /**
     * Generate realistic specifications
     */
    private function generateSpecifications(): array
    {
        return [
            'processor' => $this->faker->randomElement(['Intel Core i5-11th Gen', 'Intel Core i7-11th Gen', 'AMD Ryzen 5', 'AMD Ryzen 7']),
            'ram' => $this->faker->randomElement(['8GB', '16GB', '32GB']),
            'storage' => $this->faker->randomElement(['256GB SSD', '512GB SSD', '1TB SSD']),
            'display' => $this->faker->randomElement(['14" FHD', '15.6" FHD', '13.3" QHD']),
            'os' => $this->faker->randomElement(['Windows 11 Pro', 'Windows 10 Pro', 'macOS']),
        ];
    }

    /**
     * Generate realistic accessories
     */
    private function generateAccessories(): array
    {
        return $this->faker->randomElements([
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
        ], $this->faker->numberBetween(2, 5));
    }

    /**
     * State: Available for loan
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::AVAILABLE,
            'condition' => $this->faker->randomElement([AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR]),
        ]);
    }

    /**
     * State: Currently loaned
     */
    public function loaned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::LOANED,
            'condition' => $this->faker->randomElement([AssetCondition::EXCELLENT, AssetCondition::GOOD]),
        ]);
    }

    /**
     * State: Under maintenance
     */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::MAINTENANCE,
            'condition' => $this->faker->randomElement([AssetCondition::FAIR, AssetCondition::POOR]),
            'maintenance_tickets_count' => $this->faker->numberBetween(1, 5),
            'last_maintenance_date' => now()->subDays($this->faker->numberBetween(1, 30)),
            'next_maintenance_date' => now()->addDays($this->faker->numberBetween(30, 90)),
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
            'maintenance_tickets_count' => $this->faker->numberBetween(1, 3),
        ]);
    }

    /**
     * State: Retired
     */
    public function retired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::RETIRED,
            'condition' => $this->faker->randomElement([AssetCondition::POOR, AssetCondition::DAMAGED]),
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
            'maintenance_tickets_count' => $this->faker->numberBetween(3, 10),
            'loan_history_summary' => [
                'total_loans' => $this->faker->numberBetween(10, 50),
                'total_days_loaned' => $this->faker->numberBetween(100, 500),
                'average_loan_duration' => $this->faker->numberBetween(7, 21),
            ],
            'utilization_metrics' => [
                'utilization_rate' => $this->faker->randomFloat(2, 0.5, 0.95),
                'last_loan_date' => now()->subDays($this->faker->numberBetween(1, 30))->toDateString(),
            ],
        ]);
    }

    /**
     * State: Warranty expired
     */
    public function warrantyExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->subYears($this->faker->numberBetween(1, 3)),
        ]);
    }

    /**
     * State: Under warranty
     */
    public function underWarranty(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->addYears($this->faker->numberBetween(1, 2)),
        ]);
    }
}
