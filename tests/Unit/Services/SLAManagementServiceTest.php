<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\HelpdeskTicket;
use App\Models\TicketCategory;
use App\Services\Notifications\SLANotificationService;
use App\Services\SLAManagementService;
use Carbon\Carbon;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for SLAManagementService.
 *
 * Tests SLA tracking, escalation, and auto-close workflows.
 */
#[CoversClass(SLAManagementService::class)]
class SLAManagementServiceTest extends TestCase
{
    private SLAManagementService $service;

    /** @var SLANotificationService&MockInterface */
    private MockInterface $notificationServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->notificationServiceMock = Mockery::mock(SLANotificationService::class);
        $this->notificationServiceMock->shouldReceive('sendSlaBreachWarning')->byDefault();

        $this->service = new SLAManagementService($this->notificationServiceMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function calculate_due_dates_with_default_hours(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['priority' => 'normal']);

        Carbon::setTestNow(now());
        $dueDates = $this->service->calculateDueDates($ticket);

        $this->assertArrayHasKey('sla_response_due_at', $dueDates);
        $this->assertArrayHasKey('sla_resolution_due_at', $dueDates);

        Carbon::setTestNow();
    }

    #[Test]
    public function calculate_due_dates_with_category_sla(): void
    {
        $category = TicketCategory::factory()->create([
            'sla_response_hours' => 2,
            'sla_resolution_hours' => 12,
        ]);

        $ticket = HelpdeskTicket::factory()->create([
            'category_id' => $category->id,
            'priority' => 'normal',
        ]);

        $dueDates = $this->service->calculateDueDates($ticket, $category);

        $this->assertNotNull($dueDates['sla_response_due_at']);
        $this->assertNotNull($dueDates['sla_resolution_due_at']);
    }

    #[Test]
    public function check_sla_status_returns_on_track_for_new_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'sla_response_due_at' => now()->addHours(4),
            'sla_resolution_due_at' => now()->addHours(24),
            'first_response_at' => null,
            'resolved_at' => null,
        ]);

        $status = $this->service->checkSLAStatus($ticket);

        $this->assertEquals('on_track', $status['status']);
        $this->assertFalse($status['at_risk']);
    }

    #[Test]
    public function check_sla_status_returns_breached_when_overdue(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'sla_response_due_at' => now()->subHours(1),
            'sla_resolution_due_at' => now()->addHours(20),
            'first_response_at' => null,
            'resolved_at' => null,
        ]);

        $status = $this->service->checkSLAStatus($ticket);

        $this->assertEquals('breached', $status['status']);
    }

    #[Test]
    public function get_sla_breach_risk_returns_zero_for_resolved(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['resolved_at' => now()]);

        $risk = $this->service->getSLABreachRisk($ticket);

        $this->assertEquals(0.0, $risk);
    }

    #[Test]
    public function record_sla_breach_updates_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'sla_response_due_at' => now()->subHours(1),
            'sla_resolution_due_at' => now()->addHours(20),
            'first_response_at' => null,
            'sla_breached_at' => null,
        ]);

        $this->service->recordSLABreach($ticket);

        $ticket->refresh();
        $this->assertNotNull($ticket->sla_breached_at);
    }

    #[Test]
    public function auto_close_closes_old_resolved_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'resolved',
            'resolved_at' => now()->subDays(8),
        ]);

        $closedCount = $this->service->autoClose();

        $this->assertEquals(1, $closedCount);
        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
    }

    #[Test]
    public function auto_close_does_not_close_recent_tickets(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'resolved',
            'resolved_at' => now()->subDays(3),
        ]);

        $closedCount = $this->service->autoClose();

        $this->assertEquals(0, $closedCount);
        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
    }

    #[Test]
    public function get_sla_metrics_returns_statistics(): void
    {
        HelpdeskTicket::factory()->create([
            'created_at' => now()->subDays(5),
            'sla_breached_at' => null,
        ]);

        HelpdeskTicket::factory()->create([
            'created_at' => now()->subDays(3),
            'sla_breached_at' => now()->subDays(2),
        ]);

        $metrics = $this->service->getSLAMetrics('month');

        $this->assertArrayHasKey('total', $metrics);
        $this->assertArrayHasKey('compliance_rate', $metrics);
        $this->assertEquals(2, $metrics['total']);
    }

    #[Test]
    public function update_sla_on_response_records_first_response(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['first_response_at' => null]);

        $this->service->updateSLAOnResponse($ticket);

        $ticket->refresh();
        $this->assertNotNull($ticket->first_response_at);
    }

    #[Test]
    public function pause_sla_records_pause_time(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $this->service->pauseSLA($ticket, 'Waiting for customer');

        $ticket->refresh();
        $this->assertNotNull($ticket->sla_paused_at);
        $this->assertEquals('Waiting for customer', $ticket->sla_pause_reason);
    }

    #[Test]
    public function escalate_ticket_updates_escalation_level(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'escalation_level' => 0,
            'escalation_notified_at' => null,
            'sla_response_due_at' => now()->addHours(1),
            'sla_resolution_due_at' => now()->addHours(6),
        ]);

        $this->service->escalateTicket($ticket);

        $ticket->refresh();
        $this->assertEquals(1, $ticket->escalation_level);
        $this->assertNotNull($ticket->escalation_notified_at);
    }
}
