<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Filament Performance Test
 *
 * Tests Core Web Vitals compliance, query optimization, and performance
 * metrics for Filament admin panel under various load conditions.
 *
 * Requirements: 18.4, 13.1, D03-FR-011.1
 */
class FilamentPerformanceTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Concerns\CreatesRoles;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createRoles();
        $this->admin = User::factory()->admin()->create();
    }

    #[Test]
    public function dashboard_loads_within_performance_targets(): void
    {
        $this->actingAs($this->admin);

        $startTime = microtime(true);

        $response = $this->get('/admin');

        $loadTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);

        // Target: LCP < 2500ms (2.5 seconds)
        $this->assertLessThan(2500, $loadTime, 'Dashboard should load within 2.5 seconds');
    }

    #[Test]
    public function helpdesk_tickets_table_performance_with_large_dataset(): void
    {
        // Create large dataset
        HelpdeskTicket::factory()->count(1000)->create();

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->get('/admin/helpdesk-tickets');

        $loadTime = (microtime(true) - $startTime) * 1000;
        $queryCount = count(DB::getQueryLog());

        $response->assertStatus(200);

        // Performance targets
        $this->assertLessThan(3000, $loadTime, 'Large table should load within 3 seconds');
        $this->assertLessThan(10, $queryCount, 'Should avoid N+1 queries');
    }

    #[Test]
    public function loan_applications_table_performance_with_relationships(): void
    {
        // Create dataset with relationships
        $users = User::factory()->count(50)->create();
        $applications = LoanApplication::factory()->count(500)->create([
            'user_id' => fn () => $users->random()->id,
        ]);

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->get('/admin/loan-applications');

        $loadTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Verify eager loading is working
        $this->assertLessThan(5, count($queries), 'Should use eager loading for relationships');
        $this->assertLessThan(2500, $loadTime, 'Table with relationships should load quickly');
    }

    #[Test]
    public function search_performance_across_large_dataset(): void
    {
        // Create searchable data
        HelpdeskTicket::factory()->count(2000)->create();

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->get('/admin/helpdesk-tickets?tableSearch=test');

        $searchTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Search performance targets
        $this->assertLessThan(1000, $searchTime, 'Search should complete within 1 second');
        $this->assertLessThan(3, count($queries), 'Search should use optimized queries');
    }

    #[Test]
    public function filtering_performance(): void
    {
        // Create diverse dataset for filtering
        HelpdeskTicket::factory()->count(500)->create(['status' => 'open']);
        HelpdeskTicket::factory()->count(300)->create(['status' => 'closed']);
        HelpdeskTicket::factory()->count(200)->create(['priority' => 'high']);

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->get('/admin/helpdesk-tickets?tableFilters[status][value]=open');

        $filterTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Filter performance targets
        $this->assertLessThan(800, $filterTime, 'Filtering should be fast');
        $this->assertLessThan(2, count($queries), 'Filtering should use indexed queries');
    }

    #[Test]
    public function pagination_performance(): void
    {
        HelpdeskTicket::factory()->count(1500)->create();

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        // Test different pages
        $response1 = $this->get('/admin/helpdesk-tickets?page=1');
        $response2 = $this->get('/admin/helpdesk-tickets?page=10');
        $response3 = $this->get('/admin/helpdesk-tickets?page=50');

        $paginationTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        $response3->assertStatus(200);

        // Pagination should be consistent across pages
        $this->assertLessThan(2000, $paginationTime, 'Pagination should be efficient');
    }

    #[Test]
    public function widget_loading_performance(): void
    {
        // Create data for widgets
        HelpdeskTicket::factory()->count(100)->create();
        LoanApplication::factory()->count(100)->create();

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->get('/admin');

        $widgetTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Widget performance targets
        $this->assertLessThan(1500, $widgetTime, 'Widgets should load quickly');
        $this->assertLessThan(8, count($queries), 'Widgets should use efficient queries');
    }

    #[Test]
    public function export_performance(): void
    {
        HelpdeskTicket::factory()->count(500)->create();

        $this->actingAs($this->admin);

        $startTime = microtime(true);

        $response = $this->post('/admin/helpdesk-tickets/export', [
            'format' => 'csv',
        ]);

        $exportTime = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(200);

        // Export should complete within reasonable time
        $this->assertLessThan(5000, $exportTime, 'Export should complete within 5 seconds');
    }

    #[Test]
    public function bulk_operations_performance(): void
    {
        $tickets = HelpdeskTicket::factory()->count(100)->create(['status' => 'open']);

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->post('/admin/helpdesk-tickets/bulk-update', [
            'records' => $tickets->pluck('id')->toArray(),
            'status' => 'in_progress',
        ]);

        $bulkTime = (microtime(true) - $startTime) * 1000;
        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Bulk operations should be efficient
        $this->assertLessThan(2000, $bulkTime, 'Bulk operations should be fast');
        $this->assertLessThan(5, count($queries), 'Should use batch updates');
    }

    #[Test]
    public function memory_usage_within_limits(): void
    {
        HelpdeskTicket::factory()->count(1000)->create();

        $this->actingAs($this->admin);

        $memoryBefore = memory_get_usage(true);

        $response = $this->get('/admin/helpdesk-tickets');

        $memoryAfter = memory_get_usage(true);
        $memoryUsed = ($memoryAfter - $memoryBefore) / 1024 / 1024; // Convert to MB

        $response->assertStatus(200);

        // Memory usage should be reasonable
        $this->assertLessThan(50, $memoryUsed, 'Memory usage should be under 50MB');
    }

    #[Test]
    public function concurrent_user_performance(): void
    {
        $users = User::factory()->count(10)->admin()->create();
        HelpdeskTicket::factory()->count(200)->create();

        $responses = [];
        $startTime = microtime(true);

        // Simulate concurrent requests
        foreach ($users as $user) {
            $this->actingAs($user);
            $responses[] = $this->get('/admin/helpdesk-tickets');
        }

        $totalTime = (microtime(true) - $startTime) * 1000;

        foreach ($responses as $response) {
            $response->assertStatus(200);
        }

        // Average response time should be reasonable
        $averageTime = $totalTime / count($users);
        $this->assertLessThan(3000, $averageTime, 'Average response time should be under 3 seconds');
    }

    #[Test]
    public function database_query_optimization(): void
    {
        HelpdeskTicket::factory()->count(100)->create();

        $this->actingAs($this->admin);

        DB::enableQueryLog();

        $response = $this->get('/admin/helpdesk-tickets');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Analyze query patterns
        $selectQueries = array_filter($queries, fn ($q) => str_starts_with(strtoupper($q['query']), 'SELECT'));

        // Should not have excessive SELECT queries
        $this->assertLessThan(5, count($selectQueries), 'Should minimize SELECT queries');

        // Check for N+1 patterns
        $duplicateQueries = [];
        foreach ($queries as $query) {
            $pattern = preg_replace('/\d+/', '?', $query['query']);
            $duplicateQueries[$pattern] = ($duplicateQueries[$pattern] ?? 0) + 1;
        }

        $maxDuplicates = max($duplicateQueries);
        $this->assertLessThan(10, $maxDuplicates, 'Should avoid N+1 query patterns');
    }

    #[Test]
    public function cache_effectiveness(): void
    {
        $this->actingAs($this->admin);

        // First request (cache miss)
        $startTime1 = microtime(true);
        $response1 = $this->get('/admin');
        $time1 = (microtime(true) - $startTime1) * 1000;

        // Second request (cache hit)
        $startTime2 = microtime(true);
        $response2 = $this->get('/admin');
        $time2 = (microtime(true) - $startTime2) * 1000;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        // Second request should be faster due to caching
        $this->assertLessThan($time1, $time2 + 100, 'Cached requests should be faster');
    }

    #[Test]
    public function large_form_submission_performance(): void
    {
        $this->actingAs($this->admin);

        $largeData = [
            'title' => str_repeat('Large title ', 100),
            'description' => str_repeat('Large description content. ', 1000),
            'priority' => 'high',
            'category' => 'hardware',
        ];

        $startTime = microtime(true);

        $response = $this->post('/admin/helpdesk-tickets', $largeData);

        $submissionTime = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(302); // Redirect after creation

        // Large form submission should complete quickly
        $this->assertLessThan(2000, $submissionTime, 'Large form submission should be under 2 seconds');
    }
}
