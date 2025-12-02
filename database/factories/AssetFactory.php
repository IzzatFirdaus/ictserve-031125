<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AssetCondition;
use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $purchaseDate = now()->subYears(\random_int(1, 5))->subMonths(\random_int(0, 11));
        $purchaseValue = \random_int(1000, 20000) + \random_int(0, 99) / 100;
        
        $names = [
            'Dell Latitude 5420 Laptop', 'HP EliteBook 840 G8', 'Lenovo ThinkPad X1 Carbon',
            'Epson EB-X05 Projector', 'BenQ MH535A Projector', 'Apple iPad Pro 12.9"',
            'Samsung Galaxy Tab S8', 'Canon EOS 90D Camera', 'Sony Alpha a7 III',
            'Cisco Catalyst 2960 Switch', 'TP-Link Archer AX6000 Router',
        ];
        
        $brands = ['Dell', 'HP', 'Lenovo', 'Epson', 'BenQ', 'Apple', 'Samsung', 'Canon', 'Sony', 'Cisco', 'TP-Link'];
        $locations = ['Putrajaya HQ', 'Kuala Lumpur Office', 'Cyberjaya Branch', 'Shah Alam Office'];
        $accessories = ['Power Adapter', 'Carrying Case', 'Wireless Mouse', 'USB-C Hub', 'HDMI Cable', 'VGA Cable', 'Remote Control', 'Lens Cap', 'Memory Card', 'Battery Pack'];

        $all_acc = [];
        $count = \random_int(2, \min(5, \count($accessories)));
        $keys = \array_rand($accessories, $count);
        if (!\is_array($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $k) {
            $all_acc[] = $accessories[$k];
        }

        return [
            'asset_tag' => $this->generateAssetTag(),
            'name' => $names[\array_rand($names)],
            'brand' => $brands[\array_rand($brands)],
            'model' => \sprintf('%s-%04d', \chr(65 + \random_int(0, 25)), \random_int(1000, 9999)),
            'serial_number' => \sprintf('SN-%08d', \random_int(10000000, 99999999)),
            'category_id' => AssetCategory::query()->inRandomOrder()->value('id') ?? AssetCategory::factory()->create()->id,
            'specifications' => [
                'processor' => ['Intel Core i5-11th Gen', 'Intel Core i7-11th Gen', 'AMD Ryzen 5', 'AMD Ryzen 7'][\array_rand(['Intel Core i5-11th Gen', 'Intel Core i7-11th Gen', 'AMD Ryzen 5', 'AMD Ryzen 7'])],
                'ram' => ['8GB', '16GB', '32GB'][\array_rand(['8GB', '16GB', '32GB'])],
                'storage' => ['256GB SSD', '512GB SSD', '1TB SSD'][\array_rand(['256GB SSD', '512GB SSD', '1TB SSD'])],
                'display' => ['14" FHD', '15.6" FHD', '13.3" QHD'][\array_rand(['14" FHD', '15.6" FHD', '13.3" QHD'])],
                'os' => ['Windows 11 Pro', 'Windows 10 Pro', 'macOS'][\array_rand(['Windows 11 Pro', 'Windows 10 Pro', 'macOS'])],
            ],
            'purchase_date' => $purchaseDate,
            'purchase_value' => $purchaseValue,
            'current_value' => $purchaseValue * (0.4 + \random_int(0, 40) / 100),
            'status' => AssetStatus::AVAILABLE,
            'location' => $locations[\array_rand($locations)],
            'condition' => AssetCondition::GOOD,
            'accessories' => $all_acc,
            'warranty_expiry' => $purchaseDate->copy()->addYears(\random_int(1, 3)),
            'last_maintenance_date' => \random_int(0, 1) ? now()->subMonths(\random_int(1, 6)) : null,
            'next_maintenance_date' => \random_int(0, 1) ? now()->addMonths(\random_int(1, 6)) : null,
            'maintenance_tickets_count' => 0,
            'loan_history_summary' => null,
            'availability_calendar' => null,
            'utilization_metrics' => null,
        ];
    }

    private function generateAssetTag(): string
    {
        $prefixes = ['LAP', 'PRJ', 'TAB', 'CAM', 'NET'];
        $prefix = $prefixes[\array_rand($prefixes)];
        $year = \random_int(2019, 2025);
        $number = \random_int(1000, 9999);
        return \sprintf('%s-%d-%04d', $prefix, $year, $number);
    }

    public function available(): static
    {
        $conditions = [AssetCondition::EXCELLENT, AssetCondition::GOOD, AssetCondition::FAIR];
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::AVAILABLE,
            'condition' => $conditions[\array_rand($conditions)],
        ]);
    }

    public function loaned(): static
    {
        $conditions = [AssetCondition::EXCELLENT, AssetCondition::GOOD];
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::LOANED,
            'condition' => $conditions[\array_rand($conditions)],
        ]);
    }

    public function maintenance(): static
    {
        $conditions = [AssetCondition::FAIR, AssetCondition::POOR];
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::MAINTENANCE,
            'condition' => $conditions[\array_rand($conditions)],
            'maintenance_tickets_count' => \random_int(1, 5),
            'last_maintenance_date' => now()->subDays(\random_int(1, 30)),
            'next_maintenance_date' => now()->addDays(\random_int(30, 90)),
        ]);
    }

    public function damaged(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::DAMAGED,
            'condition' => AssetCondition::DAMAGED,
            'maintenance_tickets_count' => \random_int(1, 3),
        ]);
    }

    public function retired(): static
    {
        $conditions = [AssetCondition::POOR, AssetCondition::DAMAGED];
        return $this->state(fn (array $attributes) => [
            'status' => AssetStatus::RETIRED,
            'condition' => $conditions[\array_rand($conditions)],
            'current_value' => 0,
        ]);
    }

    public function excellent(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::EXCELLENT,
        ]);
    }

    public function good(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::GOOD,
        ]);
    }

    public function fair(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::FAIR,
        ]);
    }

    public function poor(): static
    {
        return $this->state(fn (array $attributes) => [
            'condition' => AssetCondition::POOR,
        ]);
    }

    public function withHelpdeskHistory(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_tickets_count' => \random_int(3, 10),
            'loan_history_summary' => [
                'total_loans' => \random_int(10, 50),
                'total_days_loaned' => \random_int(100, 500),
                'average_loan_duration' => \random_int(7, 21),
            ],
            'utilization_metrics' => [
                'utilization_rate' => 0.5 + \random_int(0, 45) / 100,
                'last_loan_date' => now()->subDays(\random_int(1, 30))->toDateString(),
            ],
        ]);
    }

    public function warrantyExpired(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->subYears(\random_int(1, 3)),
        ]);
    }

    public function underWarranty(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_expiry' => now()->addYears(\random_int(1, 2)),
        ]);
    }
}
