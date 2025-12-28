<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\EnhancedUnifiedDashboardService;
use App\Services\PerformanceMonitoringService;
use App\Services\UnifiedAnalyticsService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EnhancedUnifiedDashboardServiceTest extends TestCase
{
    private EnhancedUnifiedDashboardService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $this->service = new EnhancedUnifiedDashboardService(
            Mockery::mock(UnifiedAnalyticsService::class),
            Mockery::mock(PerformanceMonitoringService::class)
        );

        Cache::flush();
    }

    #[Test]
    public function it_includes_ticket_and_loan_actions_in_pending_actions(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending_user',
            'ticket_number' => 'T-100',
            'subject' => 'Need response',
            'sla_resolution_due_at' => now()->addDay(),
        ]);

        LoanApplication::factory()->create([
            'user_id' => $user->id,
            'status' => 'issued',
            'loan_end_date' => now()->subDay()->toDateString(),
        ]);

        $metrics = $this->service->getStaffDashboardMetrics($user);

        $this->assertArrayHasKey('pending_actions', $metrics);
        $this->assertCount(2, $metrics['pending_actions']);

        $types = array_column($metrics['pending_actions'], 'type');
        sort($types);

        $this->assertSame(['loan_overdue', 'ticket_response'], $types);

        foreach ($metrics['pending_actions'] as $action) {
            $this->assertArrayHasKey('type', $action);
            $this->assertArrayHasKey('priority', $action);
            $this->assertArrayHasKey('title', $action);
            $this->assertArrayHasKey('description', $action);
            $this->assertArrayHasKey('url', $action);
            $this->assertArrayHasKey('due_date', $action);
        }
    }

    #[Test]
    public function it_includes_approval_actions_for_approvers(): void
    {
        $user = User::factory()->create();
        $user->assignRole('approver');

        LoanApplication::factory()->create([
            'status' => 'under_review',
            'approved_at' => null,
        ]);

        $metrics = $this->service->getStaffDashboardMetrics($user);

        $types = array_column($metrics['pending_actions'], 'type');

        $this->assertContains('approval_pending', $types);
    }
}
