<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

/**
 * Asset Category Seeder
 *
 * Seeds standard ICT equipment categories with realistic specifications.
 *
 * @see D03-FR-005.1 Model factories and seeders for testing
 * @see D03-FR-018.2 Asset categorization system
 */
class AssetCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Laptops',
                'code' => 'LAP',
                'description' => 'Portable computing devices for office and field work',
                'specification_template' => [
                    'processor' => 'text',
                    'ram' => 'text',
                    'storage' => 'text',
                    'display' => 'text',
                    'os' => 'text',
                    'graphics' => 'text',
                    'weight' => 'text',
                ],
                'default_loan_duration_days' => 14,
                'max_loan_duration_days' => 30,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 10,
            ],
            [
                'name' => 'Projectors',
                'code' => 'PRJ',
                'description' => 'Presentation equipment for meetings and events',
                'specification_template' => [
                    'brightness' => 'text',
                    'resolution' => 'text',
                    'contrast_ratio' => 'text',
                    'lamp_life' => 'text',
                    'connectivity' => 'text',
                    'throw_distance' => 'text',
                ],
                'default_loan_duration_days' => 7,
                'max_loan_duration_days' => 14,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 20,
            ],
            [
                'name' => 'Tablets',
                'code' => 'TAB',
                'description' => 'Mobile computing devices for presentations and field work',
                'specification_template' => [
                    'screen_size' => 'text',
                    'storage' => 'text',
                    'ram' => 'text',
                    'os' => 'text',
                    'connectivity' => 'text',
                    'battery_life' => 'text',
                ],
                'default_loan_duration_days' => 14,
                'max_loan_duration_days' => 30,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 30,
            ],
            [
                'name' => 'Cameras',
                'code' => 'CAM',
                'description' => 'Photography and videography equipment for events',
                'specification_template' => [
                    'sensor' => 'text',
                    'megapixels' => 'text',
                    'lens' => 'text',
                    'video_resolution' => 'text',
                    'storage_type' => 'text',
                    'battery_type' => 'text',
                ],
                'default_loan_duration_days' => 7,
                'max_loan_duration_days' => 14,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 40,
            ],
            [
                'name' => 'Networking Equipment',
                'code' => 'NET',
                'description' => 'Network infrastructure equipment for connectivity',
                'specification_template' => [
                    'ports' => 'text',
                    'speed' => 'text',
                    'protocol' => 'text',
                    'management' => 'text',
                    'power' => 'text',
                    'mounting' => 'text',
                ],
                'default_loan_duration_days' => 30,
                'max_loan_duration_days' => 90,
                'requires_approval' => true,
                'is_active' => true,
                'sort_order' => 50,
            ],
            [
                'name' => 'Monitors',
                'code' => 'MON',
                'description' => 'Display screens for workstations',
                'specification_template' => [
                    'screen_size' => 'text',
                    'resolution' => 'text',
                    'panel_type' => 'text',
                    'refresh_rate' => 'text',
                    'connectivity' => 'text',
                ],
                'default_loan_duration_days' => 30,
                'max_loan_duration_days' => 90,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 60,
            ],
            [
                'name' => 'Peripherals',
                'code' => 'PER',
                'description' => 'Computer accessories and peripherals',
                'specification_template' => [
                    'type' => 'text',
                    'connectivity' => 'text',
                    'compatibility' => 'text',
                ],
                'default_loan_duration_days' => 14,
                'max_loan_duration_days' => 30,
                'requires_approval' => false,
                'is_active' => true,
                'sort_order' => 70,
            ],
        ];

        foreach ($categories as $categoryData) {
            AssetCategory::firstOrCreate(
                ['code' => $categoryData['code']],
                $categoryData
            );
        }

        $this->command->info('Asset categories seeded successfully.');
    }
}
