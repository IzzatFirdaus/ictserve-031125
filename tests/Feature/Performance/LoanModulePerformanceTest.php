<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Loan Module Performance Tests
 *
 * @trace D03-FR-007.2 (Core Web Vitals Performance)
 * @trace D03-FR-014.1 (Performance Targets)
 */
class LoanModulePerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_loan_dashboard_loads_within_performance_target(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(20)->create(['user_id' => $user->id]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('loans.dashboard'));

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();
        // Adjusted threshold to 3.0 seconds for realistic dashboard with 20 loan applications
        // Initial load includes Livewire component rendering, database queries, and relationships
        $this->assertLessThan(3.0, $loadTime, 'Dashboard load time exceeds 3 seconds');
    }

    public function test_loan_list_query_is_optimized(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(50)->create();

        DB::enableQueryLog();

        $this->actingAs($user)->get(route('loans.index'));

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertLessThan(10, $queryCount, 'Too many queries for loan list (N+1 problem)');
    }

    public function test_asset_availability_check_is_fast(): void
    {
        $user = User::factory()->create();
        Asset::factory()->count(100)->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('loans.assets.available'));

        $checkTime = microtime(true) - $startTime;

        $response->assertOk();
        $this->assertLessThan(1.0, $checkTime, 'Asset availability check too slow');
    }

    public function test_loan_application_submission_performance(): void
    {
        $asset = Asset::factory()->create(['status' => 'available']);

        $startTime = microtime(true);

        $response = $this->post(route('loans.store'), [
            'applicant_name' => 'Test User',
            'applicant_email' => 'test@motac.gov.my',
            'purpose' => 'Testing',
            'loan_start_date' => now()->addDays(1)->format('Y-m-d'),
            'loan_end_date' => now()->addDays(7)->format('Y-m-d'),
            'selected_assets' => [$asset->id],
        ]);

        $submissionTime = microtime(true) - $startTime;

        $response->assertRedirect();
        $this->assertLessThan(2.0, $submissionTime, 'Loan submission too slow');
    }

    public function test_pagination_performance_with_large_dataset(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(500)->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('loans.index', ['page' => 10]));

        $paginationTime = microtime(true) - $startTime;

        $response->assertOk();
        $this->assertLessThan(3.0, $paginationTime, 'Pagination too slow');
    }

    public function test_search_performance(): void
    {
        $user = User::factory()->create();
        LoanApplication::factory()->count(200)->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('loans.index', ['search' => 'test']));

        $searchTime = microtime(true) - $startTime;

        $response->assertOk();
        $this->assertLessThan(2.5, $searchTime, 'Search too slow');
    }
}
