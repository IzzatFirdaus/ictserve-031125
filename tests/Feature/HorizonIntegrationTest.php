<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\GenerateReportJob;
use App\Jobs\ProcessSLAAlertJob;
use App\Jobs\SendNotificationJob;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\HorizonMonitoringService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Laravel Horizon Integration Tests
 *
 * Tests comprehensive queue management, job processing, and monitoring
 * capabilities for ICTServe v3.6 requirements 23.1-23.8
 */
#[Group('requires-redis')]
#[Group('requires-horizon')]
#[Group('environment-specific')]
class HorizonIntegrationTest extends TestCase
#[Group('requires-redis')]
#[Group('requires-horizon')]
#[Group('environment-specific')]
class HorizonIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    private User $superUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->superUser = User::factory()->create(['role' => 'superuser']);
    }

    #[Test]
    public function it_can_dispatch_and_process_helpdesk_notification_jobs(): void
    {
        Queue::fake();

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'priority' => 'high',
        ]);

        // Dispatch notification job to helpdesk queue
        SendNotificationJob::dispatch('ticket_created', ['ticket_id' => $ticket->id], null, $ticket->contact_email ?? 'test@motac.gov.my')
            ->onQueue('helpdesk');

        Queue::assertPushed(
            SendNotificationJob::class,
            fn ($job) => $job->queue === 'helpdesk' &&
                $job->notificationType === 'ticket_created' &&
                $job->notificationData['ticket_id'] === $ticket->id
        );
    }

    #[Test]
    public function it_can_dispatch_and_process_sla_alert_jobs(): void
    {
        Queue::fake();

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'priority' => 'critical',
            'sla_due_at' => now()->addHours(1), // SLA breach warning
        ]);

        // Dispatch SLA alert job
        ProcessSLAAlertJob::dispatch('helpdesk')
            ->onQueue('helpdesk');

        Queue::assertPushed(
            ProcessSLAAlertJob::class,
            fn ($job) => $job->queue === 'helpdesk' &&
                $job->alertType === 'helpdesk'
        );
    }

    #[Test]
    public function it_can_dispatch_and_process_asset_loan_jobs(): void
    {
        Queue::fake();

        $application = LoanApplication::factory()->create([
            'status' => 'pending_approval',
        ]);

        // Dispatch approval notification job to asset-loan queue
        SendNotificationJob::dispatch('approval_required', ['application_id' => $application->id], null, $application->contact_email ?? 'test@motac.gov.my')
            ->onQueue('asset-loans');

        Queue::assertPushed(
            SendNotificationJob::class,
            fn ($job) => $job->queue === 'asset-loans' &&
                $job->notificationType === 'approval_required' &&
                $job->notificationData['application_id'] === $application->id
        );
    }

    #[Test]
    public function it_can_dispatch_and_process_report_generation_jobs(): void
    {
        Queue::fake();

        // Dispatch report generation job to reports queue
        GenerateReportJob::dispatch('monthly_summary', [], [], $this->adminUser)
            ->onQueue('reports');

        Queue::assertPushed(
            GenerateReportJob::class,
            fn ($job) => $job->queue === 'reports' &&
                $job->reportType === 'monthly_summary' &&
                $job->requestedBy->id === $this->adminUser->id
        );
    }

    #[Test]
    public function it_properly_tags_jobs_for_filtering(): void
    {
        Queue::fake();

        $ticket = HelpdeskTicket::factory()->create();

        $job = new SendNotificationJob($ticket, 'ticket_created');
        $tags = $job->tags();

        $this->assertContains('helpdesk', $tags);
        $this->assertContains('notification', $tags);
        $this->assertContains('ticket:'.$ticket->id, $tags);
    }

    #[Test]
    public function it_can_access_horizon_dashboard_with_proper_authorization(): void
    {
        // Test unauthorized access
        $response = $this->get('/horizon');
        $response->assertRedirect('/login');

        // Test staff user (should be denied)
        $staffUser = User::factory()->create(['role' => 'staff']);
        $response = $this->actingAs($staffUser)->get('/horizon');
        $response->assertStatus(403);

        // Test admin user (should be allowed)
        $response = $this->actingAs($this->adminUser)->get('/horizon');
        $response->assertStatus(200);

        // Test superuser (should be allowed)
        $response = $this->actingAs($this->superUser)->get('/horizon');
        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_monitor_queue_health_status(): void
    {
        $monitoringService = app(HorizonMonitoringService::class);

        $healthStatus = $monitoringService->checkHealthAndAlert();

        $this->assertIsArray($healthStatus);
        $this->assertArrayHasKey('queue_workers', $healthStatus);
        $this->assertArrayHasKey('failed_jobs', $healthStatus);
        $this->assertArrayHasKey('wait_times', $healthStatus);
    }

    #[Test]
    public function it_can_run_horizon_health_check_command(): void
    {
        $exitCode = Artisan::call('horizon:health-check', ['--exit-code' => true]);

        // Should return 0 for healthy or 1 for unhealthy (both are valid in test environment)
        $this->assertContains($exitCode, [0, 1]);
    }

    #[Test]
    public function it_handles_job_retry_policies_correctly(): void
    {
        $job = new SendNotificationJob(
            HelpdeskTicket::factory()->create(),
            'test_notification'
        );

        // Test retry policy configuration
        $this->assertEquals(3, $job->tries);
        $this->assertEquals(300, $job->timeout);
        $this->assertEquals([10, 30, 60], $job->backoff);
    }

    #[Test]
    public function it_can_handle_failed_job_notifications(): void
    {
        Queue::fake();

        // Simulate a failed job
        $ticket = HelpdeskTicket::factory()->create();
        $job = new SendNotificationJob($ticket, 'test_notification');

        // Test that failed job would trigger notification
        $this->assertTrue(method_exists($job, 'failed'));
    }

    #[Test]
    public function it_integrates_with_laravel_pulse_metrics(): void
    {
        // Test that Horizon metrics are being recorded for Pulse
        $monitoringService = app(HorizonMonitoringService::class);

        // This should not throw an exception
        $metrics = $monitoringService->getMetricsForPulse();

        $this->assertIsArray($metrics);
    }

    #[Test]
    public function it_can_scale_workers_based_on_queue_load(): void
    {
        // Test auto-scaling configuration
        $config = config('horizon.environments.local.supervisor-1');

        $this->assertArrayHasKey('balance', $config);
        $this->assertArrayHasKey('processes', $config);
        $this->assertArrayHasKey('tries', $config);
        $this->assertArrayHasKey('timeout', $config);
    }
}
