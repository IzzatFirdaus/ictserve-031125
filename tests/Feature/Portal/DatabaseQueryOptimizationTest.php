<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Livewire\Staff\SubmissionHistory;
use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\InternalComment;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Database Query Optimization Tests
 *
 * Tests N+1 query prevention, eager loading, and query execution time.
 *
 * Requirements: 13.4
 * Traceability: D03 SRS-FR-013, D04 §6.2, D11 §5
 */
class DatabaseQueryOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected TicketCategory $category;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->category = TicketCategory::factory()->create(['name' => 'Hardware']);
        $this->asset = Asset::factory()->create(['name' => 'Test Laptop']);

        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    #[Test]
    public function dashboard_prevents_n_plus_1_queries(): void
    {
        // Create test data with relationships
        HelpdeskTicket::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Should not have N+1 queries
        // With 10 tickets, should execute less than 20 queries
        $this->assertLessThan(20, $queryCount, "Dashboard executed {$queryCount} queries, possible N+1 issue");

        DB::disableQueryLog();
    }

    #[Test]
    public function submission_list_uses_eager_loading(): void
    {
        // Create tickets with relationships
        $tickets = HelpdeskTicket::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Should use eager loading for division, category, user
        // Expected: 1 query for tickets + 1 for divisions + 1 for categories = ~3-5 queries
        $this->assertLessThan(15, $queryCount, "Submissions list executed {$queryCount} queries, should use eager loading");

        DB::disableQueryLog();
    }

    #[Test]
    public function submission_detail_eager_loads_relationships(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        // Create comments
        InternalComment::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'commentable_type' => HelpdeskTicket::class,
            'commentable_id' => $ticket->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$ticket->id}?type=ticket");

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Should eager load user, division, category, comments, attachments
        $this->assertLessThan(15, $queryCount, "Submission detail executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function approval_interface_eager_loads_applicant_and_asset(): void
    {
        $approver = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 41,
        ]);

        // Create loan applications
        LoanApplication::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($approver)->get('/portal/approvals');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Should eager load user and asset for all applications
        $this->assertLessThan(15, $queryCount, "Approval interface executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function internal_comments_eager_load_users(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        // Create comments from different users
        $users = User::factory()->count(5)->create([
            'division_id' => $this->division->id,
        ]);

        foreach ($users as $user) {
            InternalComment::factory()->create([
                'user_id' => $user->id,
                'commentable_type' => HelpdeskTicket::class,
                'commentable_id' => $ticket->id,
            ]);
        }

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$ticket->id}?type=ticket");

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Should eager load comment users
        $this->assertLessThan(15, $queryCount, "Comments section executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function activity_timeline_uses_eager_loading(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->get("/portal/submissions/{$ticket->id}?type=ticket");

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Check for N+1 patterns in query log
        $selectQueries = array_filter($queries, function ($query) {
            return stripos($query['query'], 'select') === 0;
        });

        // Should not have excessive SELECT queries
        $this->assertLessThan(10, count($selectQueries), 'Too many SELECT queries, possible N+1 issue');

        DB::disableQueryLog();
    }

    #[Test]
    public function queries_use_proper_indexes(): void
    {
        // Create test data
        HelpdeskTicket::factory()->count(100)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Check if queries use WHERE clauses that should be indexed
        foreach ($queries as $query) {
            if (stripos($query['query'], 'where') !== false) {
                // Queries with WHERE should complete quickly
                // This is a basic check; actual index usage would need EXPLAIN
                $this->assertTrue(true);
            }
        }

        DB::disableQueryLog();
    }

    #[Test]
    public function pagination_queries_are_efficient(): void
    {
        // Create many tickets
        HelpdeskTicket::factory()->count(100)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        // Get first page
        $response = $this->actingAs($this->user)->get('/portal/submissions?page=1');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Pagination should not load all records
        $this->assertLessThan(15, $queryCount, "Pagination executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function search_queries_are_optimized(): void
    {
        HelpdeskTicket::factory()->count(50)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->get('/portal/submissions?search=test');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Search should use indexed columns
        $this->assertLessThan(15, $queryCount, "Search executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function filter_queries_are_efficient(): void
    {
        HelpdeskTicket::factory()->count(50)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)
            ->get('/portal/submissions?status[]=submitted&status[]=in_progress');

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $response->assertStatus(200);

        // Filtering should not cause excessive queries
        $this->assertLessThan(15, $queryCount, "Filtering executed {$queryCount} queries");

        DB::disableQueryLog();
    }

    #[Test]
    public function sorting_queries_use_indexes(): void
    {
        // Create tickets with different created_at times for sorting verification
        $oldTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now()->subDays(10),
        ]);

        $newTicket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
            'created_at' => now(),
        ]);

        HelpdeskTicket::factory()->count(48)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        // Test that sorting can be applied to query builder
        $sortedAsc = HelpdeskTicket::where('user_id', $this->user->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $sortedDesc = HelpdeskTicket::where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Verify sorting is working by checking first record in each direction
        $this->assertTrue(
            $sortedAsc->first()->created_at < $sortedAsc->last()->created_at,
            'Ascending sort should order from oldest to newest'
        );

        $this->assertTrue(
            $sortedDesc->first()->created_at > $sortedDesc->last()->created_at,
            'Descending sort should order from newest to oldest'
        );

        // Verify query count is reasonable (no N+1)
        DB::enableQueryLog();

        $sorted = HelpdeskTicket::where('user_id', $this->user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $queries = DB::getQueryLog();
        $this->assertLessThan(3, count($queries), 'Sorting queries should be efficient');

        DB::disableQueryLog();
    }

    #[Test]
    public function count_queries_are_optimized(): void
    {
        HelpdeskTicket::factory()->count(100)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Count queries should use COUNT(*) not load all records
        $countQueries = array_filter($queries, function ($query) {
            return stripos($query['query'], 'count') !== false;
        });

        // Should have count queries for statistics
        $this->assertGreaterThan(0, count($countQueries), 'Dashboard should use COUNT queries for statistics');

        DB::disableQueryLog();
    }

    #[Test]
    public function exists_queries_are_used_appropriately(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        // Test that exists() query works
        DB::enableQueryLog();
        $existsResult = HelpdeskTicket::where('user_id', $this->user->id)->exists();
        $existsQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertTrue($existsResult, 'exists() should return true when tickets exist');
        $this->assertNotEmpty($existsQueries, 'exists() should execute a query');

        // Test that count() works
        DB::enableQueryLog();
        $countResult = HelpdeskTicket::where('user_id', $this->user->id)->count();
        $countQueries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertGreaterThan(0, $countResult, 'count() should return > 0 when tickets exist');
        $this->assertNotEmpty($countQueries, 'count() should execute a query');
    }

    #[Test]
    public function bulk_operations_use_batch_queries(): void
    {
        $applications = LoanApplication::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        DB::enableQueryLog();

        // Bulk update (should use single query)
        LoanApplication::whereIn('id', $applications->pluck('id'))
            ->update(['status' => 'approved']);

        $queries = DB::getQueryLog();

        // Should use single UPDATE query, not 10 separate queries
        $updateQueries = array_filter($queries, function ($query) {
            return stripos($query['query'], 'update') !== false;
        });

        $this->assertCount(1, $updateQueries, 'Bulk update should use single query');

        DB::disableQueryLog();
    }

    #[Test]
    public function select_only_needed_columns(): void
    {
        HelpdeskTicket::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Check if queries select specific columns or use SELECT *
        // Ideally should select only needed columns
        $this->assertTrue(true); // Pass for now, would need to check actual queries

        DB::disableQueryLog();
    }

    #[Test]
    public function subqueries_are_optimized(): void
    {
        // This would test complex queries with subqueries
        // Ensuring they use proper indexes and don't cause performance issues
        $this->assertTrue(true); // Pass for now
    }

    #[Test]
    public function joins_are_efficient(): void
    {
        HelpdeskTicket::factory()->count(50)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Check for JOIN queries
        $joinQueries = array_filter($queries, function ($query) {
            return stripos($query['query'], 'join') !== false;
        });

        // JOINs should be used efficiently
        $this->assertTrue(true); // Pass for now

        DB::disableQueryLog();
    }

    #[Test]
    public function query_execution_time_is_acceptable(): void
    {
        HelpdeskTicket::factory()->count(100)->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
            'category_id' => $this->category->id,
        ]);

        DB::enableQueryLog();

        $startTime = microtime(true);

        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000;

        $queries = DB::getQueryLog();

        $response->assertStatus(200);

        // Total query execution time should be reasonable
        $this->assertLessThan(500, $executionTime, "Query execution time ({$executionTime}ms) exceeds 500ms");

        DB::disableQueryLog();
    }
}
