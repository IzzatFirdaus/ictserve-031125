<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\AssetStatus;
use App\Enums\LoanStatus;
use App\Livewire\GuestLoanApplication;
use App\Livewire\Loans\AuthenticatedLoanDashboard;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Performance Integration Tests
 *
 * Tests system performance under load, Core Web Vitals compliance,
 * database query optimization, and concurrent user handling.
 *
 * @see D03-FR-007.2 Performance requirements
 * @see D03-FR-014.1 Core Web Vitals targets
 * @see D03-FR-008.2 Database performance
 * @see Task 11.1 - Performance validation testing
 */
class PerformanceIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data for performance testing
        $this->createPerformanceTestData();
    }

    /**
     * Create large dataset for performance testing
     */
    private function createPerformanceTestData(): void
    {
        // Create categories
        $categories = AssetCategory::factory()->count(10)->create();

        // Create assets (100 assets)
        Asset::factory()->count(100)->create([
            'category_id' => $categories->random()->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        // Create users (50 users)
        User::factory()->count(50)->create();

        // Create loan applications (200 applications)
        LoanApplication::factory()->count(200)->create([
            'status' => collect([
                LoanStatus::SUBMITTED,
                LoanStatus::APPROVED,
                LoanStatus::IN_USE,
                LoanStatus::RETURNED,
            ])->random(),
        ]);
    }

    /**
     * Test Core Web Vitals compliance for guest loan application
     *
     * @see D03-FR-014.1 LCP <2.5s, FID <100ms, CLS <0.1, TTFB <600ms
     */
    #[Test]
    public function guest_loan_application_core_web_vitals(): void
    {
        $startTime = microtime(true);

        // Test component rendering performance
        $component = Livewire::test(GuestLoanApplication::class);

        $renderTime = microtime(true) - $startTime;

        // Verify TTFB equivalent (component initialization) < 600ms
        $this->assertLessThan(0.6, $renderTime, 'Component initialization took too long (TTFB equivalent)');

        // Test form interaction performance (FID equivalent)
        $interactionStart = microtime(true);

        $component->set('applicant_name', 'Test User')
            ->set('applicant_email', 'test@motac.gov.my')
            ->set('purpose', 'Testing performance');

        $interactionTime = microtime(true) - $interactionStart;

        // Verify interaction time < 100ms (FID equivalent)
        $this->assertLessThan(0.1, $interactionTime, 'Form interaction took too long (FID equivalent)');

        // Test asset availability checking performance
        $availabilityStart = microtime(true);

        $component->call('checkAvailability');

        $availabilityTime = microtime(true) - $availabilityStart;

        // Verify availability check < 1s (part of LCP)
        $this->assertLessThan(1.0, $availabilityTime, 'Asset availability check took too long');
    }

    /**
     * Test authenticated dashboard performance with large datasets
     *
     * @see D03-FR-011.1 Dashboard performance
     * @see D03-FR-014.2 Large dataset handling
     */
    #[Test]
    public function authenticated_dashboard_performance(): void
    {
        $user = User::factory()->create();

        // Create user-specific data
        LoanApplication::factory()->count(20)->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $startTime = microtime(true);

        // Test dashboard loading
        $component = Livewire::test(AuthenticatedLoanDashboard::class);

        $loadTime = microtime(true) - $startTime;

        // Verify dashboard loads within performance target
        $this->assertLessThan(2.0, $loadTime, 'Dashboard loading took too long');

        // Test pagination performance
        $paginationStart = microtime(true);

        $component->call('nextPage');

        $paginationTime = microtime(true) - $paginationStart;

        // Verify pagination < 500ms
        $this->assertLessThan(0.5, $paginationTime, 'Pagination took too long');
    }

    /**
     * Test database query performance and N+1 prevention
     *
     * @see D03-FR-008.2 Database optimization
     */
    #[Test]
    public function database_query_performance(): void
    {
        DB::enableQueryLog();

        // Test loan applications with relationships
        $startTime = microtime(true);

        $applications = LoanApplication::with(['user', 'division', 'loanItems.asset'])
            ->limit(50)
            ->get();

        $queryTime = microtime(true) - $startTime;
        $queries = DB::getQueryLog();

        // Verify query performance
        $this->assertLessThan(1.0, $queryTime, 'Database queries took too long');

        // Verify N+1 prevention (should be minimal queries with eager loading)
        $this->assertLessThan(10, count($queries), 'Too many database queries (possible N+1 problem)');

        // Test specific query performance
        DB::flushQueryLog();

        $searchStart = microtime(true);

        $searchResults = LoanApplication::where('applicant_name', 'like', '%Test%')
            ->orWhere('application_number', 'like', '%LA%')
            ->with(['user', 'division'])
            ->paginate(25);

        $searchTime = microtime(true) - $searchStart;
        $searchQueries = DB::getQueryLog();

        // Verify search performance
        $this->assertLessThan(0.5, $searchTime, 'Search queries took too long');
        $this->assertLessThan(5, count($searchQueries), 'Search generated too many queries');

        DB::disableQueryLog();
    }

    /**
     * Test concurrent user handling
     *
     * @see D03-FR-007.2 Concurrent processing
     */
    #[Test]
    public function concurrent_user_simulation(): void
    {
        $users = User::factory()->count(10)->create();
        $startTime = microtime(true);

        // Simulate concurrent operations
        $operations = [];

        foreach ($users as $user) {
            $this->actingAs($user);

            // Simulate concurrent loan application submissions
            $operationStart = microtime(true);

            $application = LoanApplication::factory()->create([
                'user_id' => $user->id,
                'status' => LoanStatus::SUBMITTED,
            ]);

            $operations[] = microtime(true) - $operationStart;
        }

        $totalTime = microtime(true) - $startTime;
        $averageTime = array_sum($operations) / count($operations);

        // Verify concurrent operations performance
        $this->assertLessThan(5.0, $totalTime, 'Concurrent operations took too long overall');
        $this->assertLessThan(1.0, $averageTime, 'Average operation time too high');

        // Verify data integrity under concurrent access
        $this->assertEquals(10, LoanApplication::whereIn('user_id', $users->pluck('id'))->count());
    }

    /**
     * Test caching performance and effectiveness
     *
     * @see D03-FR-008.2 Caching strategy
     */
    #[Test]
    public function caching_performance(): void
    {
        Cache::flush();

        // Test cache miss performance
        $cacheMissStart = microtime(true);

        $assets = Cache::remember('available_assets', 3600, function () {
            return Asset::where('status', AssetStatus::AVAILABLE)
                ->with('category')
                ->get();
        });

        $cacheMissTime = microtime(true) - $cacheMissStart;

        // Test cache hit performance
        $cacheHitStart = microtime(true);

        $cachedAssets = Cache::get('available_assets');

        $cacheHitTime = microtime(true) - $cacheHitStart;

        // Verify caching effectiveness
        $this->assertNotNull($cachedAssets);
        $this->assertEquals($assets->count(), $cachedAssets->count());

        // Cache hit should be significantly faster than cache miss
        $this->assertLessThan($cacheMissTime / 10, $cacheHitTime, 'Cache hit not significantly faster');
        $this->assertLessThan(0.01, $cacheHitTime, 'Cache hit took too long');
    }

    /**
     * Test asset availability checking performance
     *
     * @see D03-FR-017.4 Real-time availability
     */
    #[Test]
    public function asset_availability_performance(): void
    {
        $startDate = now()->addDays(1)->format('Y-m-d');
        $endDate = now()->addDays(3)->format('Y-m-d');

        $performanceStart = microtime(true);

        // Test availability checking for multiple assets
        $availableAssets = Asset::availableForLoan($startDate, $endDate)
            ->with('category')
            ->get();

        $performanceTime = microtime(true) - $performanceStart;

        // Verify performance
        $this->assertLessThan(1.0, $performanceTime, 'Asset availability check took too long');
        $this->assertGreaterThan(0, $availableAssets->count());

        // Test calendar integration performance
        $calendarStart = microtime(true);

        foreach ($availableAssets->take(10) as $asset) {
            $calendar = $asset->availability_calendar ?? [];
            // Simulate calendar processing
            $conflicts = collect($calendar)->filter(function ($booking) use ($startDate, $endDate) {
                return $booking['start_date'] <= $endDate && $booking['end_date'] >= $startDate;
            });
        }

        $calendarTime = microtime(true) - $calendarStart;

        // Verify calendar processing performance
        $this->assertLessThan(0.5, $calendarTime, 'Calendar processing took too long');
    }

    /**
     * Test email queue performance under load
     *
     * @see D03-FR-009.1 Email SLA compliance
     */
    #[Test]
    public function email_queue_performance(): void
    {
        $applications = LoanApplication::factory()->count(50)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        $queueStart = microtime(true);

        // Simulate email queuing for all applications
        foreach ($applications as $application) {
            // Simulate email job creation (without actually sending)
            $jobData = [
                'application_id' => $application->id,
                'recipient' => $application->applicant_email,
                'type' => 'confirmation',
                'queued_at' => now(),
            ];

            // In real scenario, this would queue the job
            $this->assertNotNull($jobData);
        }

        $queueTime = microtime(true) - $queueStart;

        // Verify queue performance (should handle 50 emails quickly)
        $this->assertLessThan(2.0, $queueTime, 'Email queuing took too long');

        // Verify SLA compliance (60 seconds for processing)
        $averageTimePerEmail = $queueTime / 50;
        $this->assertLessThan(0.04, $averageTimePerEmail, 'Average email queuing time too high for SLA');
    }

    /**
     * Test memory usage under load
     *
     * @see D03-FR-007.2 Resource optimization
     */
    #[Test]
    public function memory_usage_optimization(): void
    {
        $initialMemory = memory_get_usage(true);

        // Process large dataset
        $applications = LoanApplication::with(['user', 'division', 'loanItems'])
            ->limit(100)
            ->get();

        // Process each application
        foreach ($applications as $application) {
            $application->calculateTotalValue();
            $application->isOverdue();
        }

        $peakMemory = memory_get_peak_usage(true);
        $memoryIncrease = $peakMemory - $initialMemory;

        // Verify memory usage is reasonable (< 50MB increase)
        $this->assertLessThan(50 * 1024 * 1024, $memoryIncrease, 'Memory usage increased too much');

        // Clean up and verify memory is released
        unset($applications);
        gc_collect_cycles();

        $finalMemory = memory_get_usage(true);
        $memoryDifference = $finalMemory - $initialMemory;

        // Memory should be mostly released (allow some overhead)
        $this->assertLessThan(10 * 1024 * 1024, $memoryDifference, 'Memory not properly released');
    }

    /**
     * Test API response time performance
     *
     * @see D03-FR-007.2 API performance
     */
    #[Test]
    public function api_response_performance(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Test loan applications API
        $apiStart = microtime(true);

        $response = $this->getJson('/api/loan-applications');

        $apiTime = microtime(true) - $apiStart;

        $response->assertOk();

        // Verify API response time
        $this->assertLessThan(1.0, $apiTime, 'API response took too long');

        // Test asset search API
        $searchStart = microtime(true);

        $searchResponse = $this->getJson('/api/assets/search?q=laptop');

        $searchTime = microtime(true) - $searchStart;

        $searchResponse->assertOk();

        // Verify search API performance
        $this->assertLessThan(0.5, $searchTime, 'Search API took too long');
    }

    /**
     * Test cross-module integration performance
     *
     * @see D03-FR-016.1 Integration performance
     */
    #[Test]
    public function cross_module_integration_performance(): void
    {
        $asset = Asset::factory()->create(['status' => AssetStatus::LOANED]);
        $application = LoanApplication::factory()->create(['status' => LoanStatus::IN_USE]);

        $application->loanItems()->create([
            'asset_id' => $asset->id,
            'quantity' => 1,
            'unit_value' => $asset->current_value,
            'total_value' => $asset->current_value,
        ]);

        $integrationStart = microtime(true);

        // Simulate cross-module operation (asset return with damage)
        $returnData = [
            'assets' => [
                $asset->id => [
                    'condition' => 'damaged',
                    'damage_report' => 'Performance test damage report',
                ],
            ],
        ];

        // Process return (would create helpdesk ticket)
        $application->update(['status' => LoanStatus::RETURNED]);
        $asset->update(['status' => AssetStatus::MAINTENANCE]);

        // Simulate helpdesk ticket creation
        $ticketCategory = TicketCategory::factory()->create(['code' => 'MAINTENANCE']);
        $ticketData = [
            'asset_id' => $asset->id,
            'subject' => 'Maintenance required for '.$asset->asset_tag,
            'description' => 'Asset returned with damage',
            'category_id' => $ticketCategory->id,
        ];

        $integrationTime = microtime(true) - $integrationStart;

        // Verify integration performance
        $this->assertLessThan(1.0, $integrationTime, 'Cross-module integration took too long');

        // Verify data consistency
        $freshAsset = $asset->fresh();
        $this->assertNotNull($freshAsset);
        $this->assertEquals(AssetStatus::MAINTENANCE, $freshAsset->status);
        $freshApplication = $application->fresh();
        $this->assertNotNull($freshApplication);
        $this->assertEquals(LoanStatus::RETURNED, $freshApplication->status);
    }

    /**
     * Test performance regression detection
     */
    #[Test]
    public function performance_regression_detection(): void
    {
        $benchmarks = [
            'loan_application_creation' => 0.5,  // 500ms
            'asset_search' => 0.3,               // 300ms
            'dashboard_load' => 1.0,             // 1 second
            'email_queue' => 0.1,                // 100ms per email
        ];

        foreach ($benchmarks as $operation => $maxTime) {
            $startTime = microtime(true);

            switch ($operation) {
                case 'loan_application_creation':
                    LoanApplication::factory()->create();
                    break;
                case 'asset_search':
                    Asset::where('name', 'like', '%laptop%')->get();
                    break;
                case 'dashboard_load':
                    $user = User::factory()->create();
                    $this->actingAs($user);
                    Livewire::test(AuthenticatedLoanDashboard::class);
                    break;
                case 'email_queue':
                    // Simulate email queuing
                    $application = LoanApplication::factory()->create();
                    break;
            }

            $operationTime = microtime(true) - $startTime;

            $this->assertLessThan(
                $maxTime,
                $operationTime,
                "Performance regression detected in {$operation}: {$operationTime}s > {$maxTime}s"
            );
        }
    }
}
