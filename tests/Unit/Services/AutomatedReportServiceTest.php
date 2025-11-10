<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\ReportSchedule;
use App\Services\AutomatedReportService;
use App\Services\ReportBuilderService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class AutomatedReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    #[Test]
    public function process_due_reports_updates_schedules_and_returns_success_summary(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-11-07 09:00:00'));

        $dueSchedule = ReportSchedule::create([
            'name' => 'Daily Asset Digest',
            'description' => 'Summarizes asset activity every morning',
            'module' => 'assets',
            'frequency' => 'daily',
            'schedule_time' => '07:30:00',
            'schedule_day_of_week' => null,
            'schedule_day_of_month' => null,
            'recipients' => ['ops@example.test'],
            'filters' => ['status' => ['available']],
            'format' => 'pdf',
            'is_active' => true,
            'last_run_at' => null,
            'next_run_at' => now()->subMinutes(10),
        ]);

        $upcomingSchedule = ReportSchedule::create([
            'name' => 'Weekly Loan Digest',
            'description' => 'Only runs on Mondays',
            'module' => 'loans',
            'frequency' => 'weekly',
            'schedule_time' => '08:00:00',
            'schedule_day_of_week' => now()->dayOfWeekIso,
            'schedule_day_of_month' => null,
            'recipients' => ['finance@example.test'],
            'filters' => ['status' => ['approved']],
            'format' => 'excel',
            'is_active' => true,
            'last_run_at' => null,
            'next_run_at' => now()->addDay(),
        ]);

        $service = $this->createServicePartial();

        $service->expects($this->once())
            ->method('generateAndSendReport')
            ->with($this->callback(fn (ReportSchedule $schedule): bool => $schedule->is($dueSchedule)));

        $result = $service->processDueReports();

        $this->assertSame(1, $result['processed']);
        $this->assertSame(0, $result['failed']);
        $this->assertEmpty($result['errors']);

        $dueSchedule->refresh();
        $this->assertNotNull($dueSchedule->last_run_at);
        $this->assertTrue($dueSchedule->next_run_at->greaterThan(now()));

        $this->assertNull($upcomingSchedule->fresh()->last_run_at);
    }

    #[Test]
    public function process_due_reports_captures_failures_without_interrupting_queue(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-11-07 10:00:00'));

        $failingSchedule = ReportSchedule::create([
            'name' => 'Loan SLA Tracker',
            'description' => 'Highlights slow approvals',
            'module' => 'loans',
            'frequency' => 'daily',
            'schedule_time' => '06:45:00',
            'schedule_day_of_week' => null,
            'schedule_day_of_month' => null,
            'recipients' => ['sla@example.test'],
            'filters' => ['status' => ['submitted']],
            'format' => 'csv',
            'is_active' => true,
            'last_run_at' => null,
            'next_run_at' => now()->subMinutes(5),
        ]);

        $successfulSchedule = ReportSchedule::create([
            'name' => 'Helpdesk Volume Snapshot',
            'description' => 'Shared with service desk each day',
            'module' => 'helpdesk',
            'frequency' => 'daily',
            'schedule_time' => '07:00:00',
            'schedule_day_of_week' => null,
            'schedule_day_of_month' => null,
            'recipients' => ['helpdesk@example.test'],
            'filters' => ['status' => ['open', 'resolved']],
            'format' => 'pdf',
            'is_active' => true,
            'last_run_at' => null,
            'next_run_at' => now()->subMinutes(2),
        ]);

        $service = $this->createServicePartial();

        $service->expects($this->exactly(2))
            ->method('generateAndSendReport')
            ->willReturnCallback(function (ReportSchedule $schedule) use ($failingSchedule): void {
                if ($schedule->is($failingSchedule)) {
                    throw new \RuntimeException('Failed to generate report');
                }
            });

        $result = $service->processDueReports();

        $this->assertSame(1, $result['processed']);
        $this->assertSame(1, $result['failed']);
        $this->assertCount(1, $result['errors']);
        $this->assertSame($failingSchedule->id, $result['errors'][0]['schedule_id']);

        $this->assertNull($failingSchedule->fresh()->last_run_at);

        $successfulSchedule->refresh();
        $this->assertNotNull($successfulSchedule->last_run_at);
        $this->assertTrue($successfulSchedule->next_run_at->greaterThan(now()));
    }

    /**
     * @return AutomatedReportService&MockObject
     */
    private function createServicePartial(): AutomatedReportService
    {
        $reportBuilder = $this->createMock(ReportBuilderService::class);

        return $this->getMockBuilder(AutomatedReportService::class)
            ->setConstructorArgs([$reportBuilder])
            ->onlyMethods(['generateAndSendReport'])
            ->getMock();
    }
}
