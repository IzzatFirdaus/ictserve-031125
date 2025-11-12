<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Enums\LoanStatus;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Dashboard Service Unit Tests
 *
 * Tests dashboard statistics calculation, caching behavior, and role-specific widgets.
 *
 * @traceability Requirements 1.1, 1.2, 1.5
 */
class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles for tests
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->service = new DashboardService;
    }

    /**
     * Test statistics calculation returns correct counts
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_get_statistics_counts_open_tickets_correctly(): void
    {
        $user = User::factory()->create();

        // Create open/in-progress tickets (pending) - using valid status values
        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        // Create resolved ticket
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'resolved',
        ]);

        Cache::flush();
        $statistics = $this->service->getStatistics($user);

        // Test nested structure: helpdesk.pending should be 5 (open + in_progress)
        $this->assertEquals(5, $statistics['helpdesk']['pending']);
        // Test total tickets: 6 (all tickets)
        $this->assertEquals(6, $statistics['helpdesk']['total']);
        // Test resolved: 1
        $this->assertEquals(1, $statistics['helpdesk']['resolved']);
    }

    /**
     * Test statistics caching behavior
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_statistics_are_cached_for_five_minutes(): void
    {
        $user = User::factory()->create();

        Cache::flush();

        // Create initial tickets
        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        // First call - should query database and cache result
        $statistics1 = $this->service->getStatistics($user);
        $this->assertEquals(3, $statistics1['helpdesk']['pending']);

        // Verify cache was created
        $cachedValue = Cache::get("portal.statistics.{$user->id}");
        $this->assertNotNull($cachedValue);
        $this->assertEquals(3, $cachedValue['helpdesk']['pending']);

        // Clear cache to force fresh query
        Cache::forget("portal.statistics.{$user->id}");

        // Create more tickets
        HelpdeskTicket::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        // Third call - should query database again and get new count
        $statistics3 = $this->service->getStatistics($user);
        $this->assertEquals(5, $statistics3['helpdesk']['pending']);
    }

    /**
     * Test recent activity retrieval
     *
     *
     * @traceability Requirement 1.2
     */
    #[Test]
    public function test_get_recent_activity_returns_limited_results(): void
    {
        $user = User::factory()->create();

        // Create 15 activities
        for ($i = 0; $i < 15; $i++) {
            $user->portalActivities()->create([
                'activity_type' => 'ticket_submitted',
                'subject_type' => HelpdeskTicket::class,
                'subject_id' => 1,
                'metadata' => [],
            ]);
        }

        $activities = $this->service->getRecentActivity($user, 10);

        $this->assertCount(10, $activities);
    }

    /**
     * Test role-specific widgets for approver
     *
     *
     * @traceability Requirement 1.5
     */
    #[Test]
    public function test_role_specific_widgets_for_approver(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);

        LoanApplication::factory()->count(3)->create([
            'status' => LoanStatus::SUBMITTED,
        ]);

        $widgets = $this->service->getRoleSpecificWidgets($approver);

        $this->assertArrayHasKey('pending_approvals', $widgets);
        $this->assertEquals(3, $widgets['pending_approvals']);
    }

    /**
     * Test role-specific widgets for admin
     *
     *
     * @traceability Requirement 1.5
     */
    #[Test]
    public function test_role_specific_widgets_for_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $widgets = $this->service->getRoleSpecificWidgets($admin);

        $this->assertArrayHasKey('system_overview', $widgets);
        $this->assertIsArray($widgets['system_overview']);
    }

    /**
     * Test statistics for user with no submissions
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_statistics_for_user_with_no_submissions(): void
    {
        $user = User::factory()->create();

        $statistics = $this->service->getStatistics($user);

        // Test nested structure for empty user
        $this->assertEquals(0, $statistics['helpdesk']['total']);
        $this->assertEquals(0, $statistics['helpdesk']['pending']);
        $this->assertEquals(0, $statistics['loans']['total']);
        $this->assertEquals(0, $statistics['loans']['pending']);
        $this->assertEquals(0, $statistics['summary']['total_submissions']);
    }
}
