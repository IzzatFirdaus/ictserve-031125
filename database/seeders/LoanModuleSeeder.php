<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AssetStatus;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\LoanItem;
use App\Models\LoanTransaction;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Loan Module Seeder
 *
 * Comprehensive seeder for loan module with realistic test data including
 * cross-module integration scenarios.
 *
 * @see D03-FR-005.1 Model factories and seeders for testing
 * @see D03-FR-008.1 Cross-module integration test data
 * @see D03-FR-016.2 Cross-module integration
 */
class LoanModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding loan module data...');

        // Ensure categories and divisions exist
        if (AssetCategory::count() === 0) {
            $this->call(AssetCategorySeeder::class);
        }

        if (Division::count() === 0) {
            $this->call(DivisionSeeder::class);
        }

        // Get categories for asset creation
        $laptopCategory = AssetCategory::where('code', 'LAP')->first();
        $projectorCategory = AssetCategory::where('code', 'PRJ')->first();
        $tabletCategory = AssetCategory::where('code', 'TAB')->first();
        $cameraCategory = AssetCategory::where('code', 'CAM')->first();
        $networkCategory = AssetCategory::where('code', 'NET')->first();

        if (! $laptopCategory || ! $projectorCategory || ! $tabletCategory || ! $cameraCategory || ! $networkCategory) {
            $this->command->warn('⚠️ Skipping loan module seeding - required asset categories not found');

            return;
        }

        // Create sample assets
        $this->command->info('Creating sample assets...');

        // Laptops (10 available, 3 loaned, 2 maintenance)
        for ($i = 0; $i < 10; $i++) {
            $attrs = Asset::factory()->available()->make(['category_id' => $laptopCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 3; $i++) {
            $attrs = Asset::factory()->loaned()->make(['category_id' => $laptopCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 2; $i++) {
            $attrs = Asset::factory()->maintenance()->make(['category_id' => $laptopCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }

        // Projectors (5 available, 2 loaned)
        for ($i = 0; $i < 5; $i++) {
            $attrs = Asset::factory()->available()->make(['category_id' => $projectorCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 2; $i++) {
            $attrs = Asset::factory()->loaned()->make(['category_id' => $projectorCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }

        // Tablets (8 available, 2 loaned, 1 damaged)
        for ($i = 0; $i < 8; $i++) {
            $attrs = Asset::factory()->available()->make(['category_id' => $tabletCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 2; $i++) {
            $attrs = Asset::factory()->loaned()->make(['category_id' => $tabletCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 1; $i++) {
            $attrs = Asset::factory()->damaged()->make(['category_id' => $tabletCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }

        // Cameras (4 available, 1 loaned)
        for ($i = 0; $i < 4; $i++) {
            $attrs = Asset::factory()->available()->make(['category_id' => $cameraCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 1; $i++) {
            $attrs = Asset::factory()->loaned()->make(['category_id' => $cameraCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }

        // Networking Equipment (6 available, 2 loaned)
        for ($i = 0; $i < 6; $i++) {
            $attrs = Asset::factory()->available()->make(['category_id' => $networkCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }
        for ($i = 0; $i < 2; $i++) {
            $attrs = Asset::factory()->loaned()->make(['category_id' => $networkCategory->id])->toArray();
            Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);
        }

        $this->command->info('Created '.Asset::count().' assets.');

        // Create sample users if needed
        if (User::count() < 5) {
            User::factory()->count(5)->create();
        }

        $users = User::take(5)->get();
        $divisions = Division::take(5)->get();
        $availableAssets = Asset::where('status', AssetStatus::AVAILABLE)->take(10)->get();

        // Create loan applications in various states
        $this->command->info('Creating sample loan applications...');

        // Guest submissions (5)
        foreach (range(1, 5) as $i) {
            LoanApplication::factory()
                ->guest()
                ->submitted()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);
        }

        // Authenticated submissions (5)
        foreach (range(1, 5) as $i) {
            LoanApplication::factory()
                ->authenticated()
                ->submitted()
                ->create([
                    'user_id' => $users->random()->id,
                    'division_id' => $divisions->random()->id,
                ]);
        }

        // Under review (3)
        foreach (range(1, 3) as $i) {
            LoanApplication::factory()
                ->underReview()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);
        }

        // Approved applications (5)
        foreach (range(1, 5) as $i) {
            $application = LoanApplication::factory()
                ->approved()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            // Add loan items
            $asset = $availableAssets->random();
            LoanItem::factory()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);
        }

        // In-use applications with issued assets (5)
        foreach (range(1, 5) as $i) {
            $application = LoanApplication::factory()
                ->inUse()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            $asset = $availableAssets->random();

            // Create loan item
            $loanItem = LoanItem::factory()->issued()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);

            // Create issue transaction
            LoanTransaction::factory()->issue()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(1, 10)),
                'condition_before' => $loanItem->condition_before,
            ]);

            // Update asset status
            $asset->update(['status' => AssetStatus::LOANED]);
        }

        // Overdue applications (2)
        foreach (range(1, 2) as $i) {
            $application = LoanApplication::factory()
                ->overdue()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            $asset = $availableAssets->random();

            LoanItem::factory()->issued()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);

            $asset->update(['status' => AssetStatus::LOANED]);
        }

        // Returned applications (3)
        foreach (range(1, 3) as $i) {
            $application = LoanApplication::factory()
                ->returned()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            $attrs = Asset::factory()->available()->make([
                'category_id' => $laptopCategory->id,
            ])->toArray();
            $asset = Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);

            // Create loan item with return data
            $loanItem = LoanItem::factory()->returned()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);

            // Create issue transaction
            LoanTransaction::factory()->issue()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(20, 40)),
                'condition_before' => $loanItem->condition_before,
            ]);

            // Create return transaction
            LoanTransaction::factory()->return()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(1, 5)),
                'condition_before' => $loanItem->condition_before,
                'condition_after' => $loanItem->condition_after,
            ]);
        }

        // Completed applications (5)
        foreach (range(1, 5) as $i) {
            $application = LoanApplication::factory()
                ->completed()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            $attrs = Asset::factory()->available()->make([
                'category_id' => $projectorCategory->id,
            ])->toArray();
            $asset = Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);

            $loanItem = LoanItem::factory()->returned()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);

            // Create transactions
            LoanTransaction::factory()->issue()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(30, 60)),
            ]);

            LoanTransaction::factory()->return()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(10, 25)),
            ]);
        }

        // Rejected applications (2)
        LoanApplication::factory()->count(2)->rejected()->create([
            'division_id' => $divisions->random()->id,
        ]);

        // Cross-module integration: Applications with helpdesk tickets (2)
        foreach (range(1, 2) as $i) {
            $application = LoanApplication::factory()
                ->returned()
                ->withHelpdeskIntegration()
                ->create([
                    'division_id' => $divisions->random()->id,
                ]);

            $attrs = Asset::factory()->maintenance()->make([
                'category_id' => $tabletCategory->id,
                'maintenance_tickets_count' => 1,
            ])->toArray();
            $asset = Asset::firstOrCreate(['asset_tag' => $attrs['asset_tag']], $attrs);

            $loanItem = LoanItem::factory()->damaged()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'unit_value' => $asset->current_value,
                'total_value' => $asset->current_value,
            ]);

            // Create transactions
            LoanTransaction::factory()->issue()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(20, 40)),
            ]);

            LoanTransaction::factory()->returnDamaged()->create([
                'loan_application_id' => $application->id,
                'asset_id' => $asset->id,
                'processed_by' => $users->random()->id,
                'processed_at' => now()->subDays(rand(1, 5)),
                'condition_before' => $loanItem->condition_before,
                'condition_after' => $loanItem->condition_after,
                'damage_report' => $loanItem->damage_report,
            ]);
        }

        $this->command->info('Created '.LoanApplication::count().' loan applications.');
        $this->command->info('Created '.LoanItem::count().' loan items.');
        $this->command->info('Created '.LoanTransaction::count().' loan transactions.');
        $this->command->info('Loan module seeding completed successfully!');
    }
}
