<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

/**
 * Asset Seeder
 *
 * Seeds sample assets for testing cross-module integration
 * between helpdesk and asset loan modules.
 */
class AssetSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing categories (created by AssetCategorySeeder)
        $laptopCategory = AssetCategory::where('code', 'LAP')->first();
        $projectorCategory = AssetCategory::where('code', 'PRJ')->first();

        // If categories don't exist, skip asset creation
        if (! $laptopCategory || ! $projectorCategory) {
            $this->command->warn('Asset categories not found. Run AssetCategorySeeder first.');

            return;
        }

        $assets = [
            [
                'asset_tag' => 'LAP-2025-0001',
                'category_id' => $laptopCategory->id,
                'name' => 'Dell Latitude 5420',
                'brand' => 'Dell',
                'model' => 'Latitude 5420',
                'serial_number' => 'DL5420-2025-001',
                'specifications' => [
                    'processor' => 'Intel Core i5-1135G7',
                    'ram' => '16GB DDR4',
                    'storage' => '512GB SSD',
                    'display' => '14" FHD',
                ],
                'purchase_value' => 4500.00,
                'current_value' => 4000.00,
                'purchase_date' => now()->subMonths(6),
                'warranty_expiry' => now()->addMonths(30),
                'status' => 'available',
                'condition' => 'good',
                'location' => 'ICT Store Room',
                'accessories' => ['Power Adapter', 'Carrying Case', 'Wireless Mouse'],
            ],
            [
                'asset_tag' => 'LAP-2025-0002',
                'category_id' => $laptopCategory->id,
                'name' => 'HP EliteBook 840 G8',
                'brand' => 'HP',
                'model' => 'EliteBook 840 G8',
                'serial_number' => 'HP840-2025-002',
                'specifications' => [
                    'processor' => 'Intel Core i7-1165G7',
                    'ram' => '16GB DDR4',
                    'storage' => '512GB SSD',
                    'display' => '14" FHD',
                ],
                'purchase_value' => 5200.00,
                'current_value' => 4800.00,
                'purchase_date' => now()->subMonths(3),
                'warranty_expiry' => now()->addMonths(33),
                'status' => 'available',
                'condition' => 'excellent',
                'location' => 'ICT Store Room',
                'accessories' => ['Power Adapter', 'Carrying Case', 'USB-C Hub'],
            ],
            [
                'asset_tag' => 'PRJ-2025-0001',
                'category_id' => $projectorCategory->id,
                'name' => 'Epson EB-X06',
                'brand' => 'Epson',
                'model' => 'EB-X06',
                'serial_number' => 'EP-X06-2025-003',
                'specifications' => [
                    'brightness' => '3600 lumens',
                    'resolution' => 'XGA (1024x768)',
                    'contrast' => '16000:1',
                ],
                'purchase_value' => 2800.00,
                'current_value' => 2400.00,
                'purchase_date' => now()->subMonths(12),
                'warranty_expiry' => now()->addMonths(12),
                'status' => 'available',
                'condition' => 'good',
                'location' => 'Meeting Room A',
                'accessories' => ['Power Cable', 'Remote Control', 'HDMI Cable', 'VGA Cable'],
            ],
        ];

        foreach ($assets as $assetData) {
            Asset::firstOrCreate(
                ['asset_tag' => $assetData['asset_tag']],
                $assetData
            );
        }

        $this->command->info('✓ Created sample assets for testing');
    }
}
