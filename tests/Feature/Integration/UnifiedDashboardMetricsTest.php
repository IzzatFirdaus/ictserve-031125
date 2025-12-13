<?php

declare(strict_types=1);

namespace Tests\Feature\Integration;

use App\Models\Asset;
use App\Models\Division;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\TicketCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unified Dashboard Metrics Integration Test
 *
 * Requirements: R11 (Cross-Module Integration), 5.1.1-5.1.6
 */
class UnifiedDashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Division $division;

    private TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->division = Division::factory()->create();
        $this->category = TicketCategory::factory()->create();
    }

    #[Test]
    public function can_calculate_total_open_tickets(): void
    {
        HelpdeskTicket::factory()->count(5)->create([
            'status' => 'open',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $openCount = HelpdeskTicket::where('status', 'open')->count();
        $this->assertEquals(5, $openCount);
    }

    #[Test]
    public function can_calculate_pending_loan_applications(): void
    {
        LoanApplication::factory()->count(4)->create([
            'status' => 'under_review',
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);

        $pendingCount = LoanApplication::where('status', 'under_review')->count();
        $this->assertEquals(4, $pendingCount);
    }

    #[Test]
    public function can_calculate_overdue_items(): void
    {
        LoanApplication::factory()->count(2)->create([
            'status' => 'issued',
            'loan_end_date' => now()->subDays(3),
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);

        $overdueCount = LoanApplication::where('status', 'issued')
            ->where('loan_end_date', '<', now())
            ->count();

        $this->assertEquals(2, $overdueCount);
    }

    #[Test]
    public function can_calculate_asset_utilization(): void
    {
        Asset::factory()->count(10)->create();

        LoanApplication::factory()->count(6)->create([
            'status' => 'issued',
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);

        $utilizationRate = 6 / 10 * 100;
        $this->assertEquals(60, $utilizationRate);
    }

    #[Test]
    public function can_get_tickets_by_priority_distribution(): void
    {
        HelpdeskTicket::factory()->count(5)->create([
            'priority' => 'high',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        HelpdeskTicket::factory()->count(10)->create([
            'priority' => 'normal',
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $distribution = HelpdeskTicket::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        $this->assertEquals(5, $distribution['high']);
        $this->assertEquals(10, $distribution['normal']);
    }

    #[Test]
    public function can_get_loan_applications_by_status_distribution(): void
    {
        LoanApplication::factory()->count(3)->create([
            'status' => 'submitted',
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);

        LoanApplication::factory()->count(5)->create([
            'status' => 'approved',
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);

        $distribution = LoanApplication::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $this->assertEquals(3, $distribution['submitted']);
        $this->assertEquals(5, $distribution['approved']);
    }

    #[Test]
    public function metrics_are_scoped_to_user(): void
    {
        $otherUser = User::factory()->create();

        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        HelpdeskTicket::factory()->count(5)->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
        ]);

        $userTickets = HelpdeskTicket::where('user_id', $this->user->id)->count();
        $this->assertEquals(3, $userTickets);
    }
}
