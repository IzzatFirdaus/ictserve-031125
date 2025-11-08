<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\SubmissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Submission Service Unit Tests
 *
 * Tests filtering logic, search functionality, and eager loading.
 *
 * @traceability Requirements 2.1, 8.1, 8.2
 */
class SubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubmissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SubmissionService;
    }

    /**
     * Test filtering by status
     *
     *
     * @traceability Requirement 8.2
     */
    #[Test]
    public function test_filter_submissions_by_status(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'resolved',
        ]);

        $filters = ['status' => ['submitted']];
        $submissions = $this->service->getUserSubmissions($user, 'tickets', $filters);

        $this->assertCount(3, $submissions);
    }

    /**
     * Test filtering by date range
     *
     *
     * @traceability Requirement 8.2
     */
    #[Test]
    public function test_filter_submissions_by_date_range(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(10),
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(5),
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDays(2),
        ]);

        $filters = [
            'date_from' => now()->subDays(7)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ];

        $submissions = $this->service->getUserSubmissions($user, 'tickets', $filters);

        $this->assertCount(2, $submissions);
    }

    /**
     * Test search functionality
     *
     *
     * @traceability Requirement 8.1
     */
    #[Test]
    public function test_search_submissions_by_ticket_number(): void
    {
        $user = User::factory()->create();

        $ticket1 = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'ticket_number' => 'HD2025000001',
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'ticket_number' => 'HD2025000002',
        ]);

        $results = $this->service->searchSubmissions($user, 'HD2025000001');

        $this->assertCount(1, $results);
        $this->assertEquals($ticket1->id, $results->first()->id);
    }

    /**
     * Test search by subject
     *
     *
     * @traceability Requirement 8.1
     */
    #[Test]
    public function test_search_submissions_by_subject(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Printer not working',
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'subject' => 'Network issue',
        ]);

        $results = $this->service->searchSubmissions($user, 'printer');

        $this->assertCount(1, $results);
        $this->assertStringContainsString('Printer', $results->first()->subject);
    }

    /**
     * Test eager loading prevents N+1 queries
     *
     *
     * @traceability Requirement 2.1
     */
    #[Test]
    public function test_eager_loading_prevents_n_plus_one_queries(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);

        // Enable query logging
        \DB::enableQueryLog();

        $submissions = $this->service->getUserSubmissions($user, 'tickets', []);

        // Access relationships
        foreach ($submissions as $submission) {
            $submission->division;
            $submission->category;
        }

        $queries = \DB::getQueryLog();

        // Should be minimal queries due to eager loading
        // 1 for submissions, 1 for divisions, 1 for categories
        $this->assertLessThanOrEqual(5, count($queries));
    }

    /**
     * Test filtering multiple criteria
     *
     *
     * @traceability Requirement 8.2
     */
    #[Test]
    public function test_filter_submissions_with_multiple_criteria(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
            'priority' => 'high',
            'created_at' => now()->subDays(2),
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
            'priority' => 'low',
            'created_at' => now()->subDays(2),
        ]);

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'resolved',
            'priority' => 'high',
            'created_at' => now()->subDays(2),
        ]);

        $filters = [
            'status' => ['submitted'],
            'priority' => ['high'],
            'date_from' => now()->subDays(7)->format('Y-m-d'),
        ];

        $submissions = $this->service->getUserSubmissions($user, 'tickets', $filters);

        $this->assertCount(1, $submissions);
    }

    /**
     * Test loan submissions filtering
     *
     *
     * @traceability Requirement 2.1
     */
    #[Test]
    public function test_get_user_loan_submissions(): void
    {
        $user = User::factory()->create();

        LoanApplication::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        LoanApplication::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $filters = ['status' => ['pending']];
        $submissions = $this->service->getUserSubmissions($user, 'loans', $filters);

        $this->assertCount(3, $submissions);
    }
}
