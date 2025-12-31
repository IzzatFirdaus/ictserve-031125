<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\EmailLog;
use App\Services\Notifications\EmailAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Email Analytics Service Test
 *
 * Tests for the EmailAnalyticsService including delivery metrics,
 * bounce rate monitoring, and queue health tracking.
 *
 * @see D03 SRS-FR-008
 * @see D04 §6.2
 *
 * @requirements 10.1, 10.3, 10.5
 */
class EmailAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private EmailAnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(EmailAnalyticsService::class);
        Cache::flush();
    }

    #[Test]
    public function get_delivery_metrics_returns_correct_structure(): void
    {
        EmailLog::factory()->count(5)->create(['status' => 'delivered']);
        EmailLog::factory()->count(2)->create(['status' => 'failed']);
        EmailLog::factory()->count(1)->create(['status' => 'bounced']);

        $metrics = $this->service->getDeliveryMetrics();

        $this->assertArrayHasKey('period', $metrics);
        $this->assertArrayHasKey('totals', $metrics);
        $this->assertArrayHasKey('rates', $metrics);
        $this->assertArrayHasKey('performance', $metrics);

        $this->assertArrayHasKey('total', $metrics['totals']);
        $this->assertArrayHasKey('delivered', $metrics['totals']);
        $this->assertArrayHasKey('failed', $metrics['totals']);
        $this->assertArrayHasKey('bounced', $metrics['totals']);

        $this->assertArrayHasKey('delivery_rate', $metrics['rates']);
        $this->assertArrayHasKey('failure_rate', $metrics['rates']);
        $this->assertArrayHasKey('bounce_rate', $metrics['rates']);
    }

    #[Test]
    public function delivery_rate_calculation_is_accurate(): void
    {
        EmailLog::factory()->count(80)->create(['status' => 'delivered']);
        EmailLog::factory()->count(20)->create(['status' => 'failed']);

        $metrics = $this->service->getDeliveryMetrics(useCache: false);

        $this->assertEquals(100, $metrics['totals']['total']);
        $this->assertEquals(80, $metrics['totals']['delivered']);
        $this->assertEquals(80.0, $metrics['rates']['delivery_rate']);
        $this->assertEquals(20.0, $metrics['rates']['failure_rate']);
    }

    #[Test]
    public function get_bounce_metrics_returns_correct_structure(): void
    {
        EmailLog::factory()->count(3)->create([
            'status' => 'bounced',
            'recipient_email' => 'test@example.com',
        ]);
        EmailLog::factory()->count(7)->create(['status' => 'delivered']);

        $metrics = $this->service->getBounceMetrics();

        $this->assertArrayHasKey('period', $metrics);
        $this->assertArrayHasKey('total_bounces', $metrics);
        $this->assertArrayHasKey('total_sent', $metrics);
        $this->assertArrayHasKey('bounce_rate', $metrics);
        $this->assertArrayHasKey('top_bounced_addresses', $metrics);
        $this->assertArrayHasKey('alert_triggered', $metrics);

        $this->assertEquals(3, $metrics['total_bounces']);
        $this->assertEquals(10, $metrics['total_sent']);
        $this->assertEquals(30.0, $metrics['bounce_rate']);
    }

    #[Test]
    public function bounce_alert_triggered_when_threshold_exceeded(): void
    {
        EmailLog::factory()->count(10)->create(['status' => 'bounced']);
        EmailLog::factory()->count(90)->create(['status' => 'delivered']);

        $metrics = $this->service->getBounceMetrics();

        $this->assertTrue($metrics['alert_triggered']);
    }

    #[Test]
    public function bounce_alert_not_triggered_when_below_threshold(): void
    {
        EmailLog::factory()->count(2)->create(['status' => 'bounced']);
        EmailLog::factory()->count(98)->create(['status' => 'delivered']);

        $metrics = $this->service->getBounceMetrics();

        $this->assertFalse($metrics['alert_triggered']);
    }

    #[Test]
    public function get_metrics_by_notification_type(): void
    {
        EmailLog::factory()->count(5)->create([
            'notification_type' => 'ticket_assigned',
            'status' => 'delivered',
        ]);
        EmailLog::factory()->count(3)->create([
            'notification_type' => 'loan_approved',
            'status' => 'delivered',
        ]);
        EmailLog::factory()->count(2)->create([
            'notification_type' => 'ticket_assigned',
            'status' => 'failed',
        ]);

        $metrics = $this->service->getMetricsByNotificationType();

        $this->assertArrayHasKey('ticket_assigned', $metrics);
        $this->assertArrayHasKey('loan_approved', $metrics);

        $this->assertEquals(7, $metrics['ticket_assigned']['total']);
        $this->assertEquals(5, $metrics['ticket_assigned']['delivered']);
        $this->assertEquals(2, $metrics['ticket_assigned']['failed']);
    }

    #[Test]
    public function get_metrics_by_priority(): void
    {
        EmailLog::factory()->count(5)->create([
            'priority' => 'high',
            'status' => 'delivered',
        ]);
        EmailLog::factory()->count(10)->create([
            'priority' => 'normal',
            'status' => 'delivered',
        ]);

        $metrics = $this->service->getMetricsByPriority();

        $this->assertArrayHasKey('high', $metrics);
        $this->assertArrayHasKey('normal', $metrics);

        $this->assertEquals(5, $metrics['high']['total']);
        $this->assertEquals(10, $metrics['normal']['total']);
    }

    #[Test]
    public function get_daily_breakdown(): void
    {
        EmailLog::factory()->count(5)->create([
            'status' => 'delivered',
            'created_at' => now()->subDays(2),
        ]);
        EmailLog::factory()->count(3)->create([
            'status' => 'delivered',
            'created_at' => now()->subDay(),
        ]);
        EmailLog::factory()->count(7)->create([
            'status' => 'delivered',
            'created_at' => now(),
        ]);

        $breakdown = $this->service->getDailyBreakdown(now()->subDays(3), now());

        $this->assertGreaterThanOrEqual(3, $breakdown->count());

        foreach ($breakdown as $day) {
            $this->assertArrayHasKey('date', $day);
            $this->assertArrayHasKey('total', $day);
            $this->assertArrayHasKey('delivered', $day);
            $this->assertArrayHasKey('delivery_rate', $day);
        }
    }

    #[Test]
    public function get_hourly_breakdown(): void
    {
        EmailLog::factory()->count(3)->create([
            'status' => 'delivered',
            'created_at' => now()->setHour(9),
        ]);
        EmailLog::factory()->count(5)->create([
            'status' => 'delivered',
            'created_at' => now()->setHour(14),
        ]);

        $breakdown = $this->service->getHourlyBreakdown(now());

        $this->assertGreaterThanOrEqual(2, $breakdown->count());

        foreach ($breakdown as $hour) {
            $this->assertArrayHasKey('hour', $hour);
            $this->assertArrayHasKey('hour_formatted', $hour);
            $this->assertArrayHasKey('total', $hour);
            $this->assertArrayHasKey('delivered', $hour);
        }
    }

    #[Test]
    public function check_delivery_alerts_triggers_on_high_failure_rate(): void
    {
        EmailLog::factory()->count(15)->create([
            'status' => 'failed',
            'created_at' => now()->subMinutes(30),
        ]);
        EmailLog::factory()->count(85)->create([
            'status' => 'delivered',
            'created_at' => now()->subMinutes(30),
        ]);

        $alerts = $this->service->checkDeliveryAlerts(now()->subHour(), now());

        $this->assertTrue($alerts['alert_triggered']);
        $this->assertGreaterThanOrEqual(10.0, $alerts['failure_rate']);
        $this->assertNotNull($alerts['message']);
    }

    #[Test]
    public function check_delivery_alerts_does_not_trigger_on_low_failure_rate(): void
    {
        EmailLog::factory()->count(5)->create([
            'status' => 'failed',
            'created_at' => now()->subMinutes(30),
        ]);
        EmailLog::factory()->count(95)->create([
            'status' => 'delivered',
            'created_at' => now()->subMinutes(30),
        ]);

        $alerts = $this->service->checkDeliveryAlerts(now()->subHour(), now());

        $this->assertFalse($alerts['alert_triggered']);
        $this->assertNull($alerts['message']);
    }

    #[Test]
    public function get_queue_health_returns_correct_structure(): void
    {
        $health = $this->service->getQueueHealth();

        $this->assertArrayHasKey('stuck_emails', $health);
        $this->assertArrayHasKey('pending_retries', $health);
        $this->assertArrayHasKey('avg_processing_time_seconds', $health);
        $this->assertArrayHasKey('throughput_per_minute', $health);
        $this->assertArrayHasKey('health_status', $health);
    }

    #[Test]
    public function queue_health_status_is_healthy_with_no_stuck_emails(): void
    {
        EmailLog::factory()->count(10)->create([
            'status' => 'delivered',
            'queued_at' => now()->subMinutes(2),
        ]);

        $health = $this->service->getQueueHealth();

        $this->assertEquals('healthy', $health['health_status']);
    }

    #[Test]
    public function queue_health_status_is_critical_with_many_stuck_emails(): void
    {
        EmailLog::factory()->count(150)->create([
            'status' => 'queued',
            'queued_at' => now()->subMinutes(10),
        ]);

        $health = $this->service->getQueueHealth();

        $this->assertEquals('critical', $health['health_status']);
    }

    #[Test]
    public function delivery_metrics_uses_cache(): void
    {
        EmailLog::factory()->count(10)->create(['status' => 'delivered']);

        $metrics1 = $this->service->getDeliveryMetrics();

        EmailLog::factory()->count(5)->create(['status' => 'delivered']);

        $metrics2 = $this->service->getDeliveryMetrics();

        $this->assertEquals($metrics1['totals']['total'], $metrics2['totals']['total']);
    }

    #[Test]
    public function delivery_metrics_bypasses_cache_when_requested(): void
    {
        EmailLog::factory()->count(10)->create(['status' => 'delivered']);

        $this->service->getDeliveryMetrics();

        EmailLog::factory()->count(5)->create(['status' => 'delivered']);

        $metrics2 = $this->service->getDeliveryMetrics(useCache: false);

        $this->assertEquals(15, $metrics2['totals']['total']);
    }

    #[Test]
    public function empty_data_returns_zero_rates(): void
    {
        $metrics = $this->service->getDeliveryMetrics(useCache: false);

        $this->assertEquals(0, $metrics['totals']['total']);
        $this->assertEquals(0.0, $metrics['rates']['delivery_rate']);
        $this->assertEquals(0.0, $metrics['rates']['failure_rate']);
        $this->assertEquals(0.0, $metrics['rates']['bounce_rate']);
    }

    #[Test]
    public function date_range_filtering_works_correctly(): void
    {
        EmailLog::factory()->count(5)->create([
            'status' => 'delivered',
            'created_at' => now()->subDays(60),
        ]);

        EmailLog::factory()->count(10)->create([
            'status' => 'delivered',
            'created_at' => now()->subDays(5),
        ]);

        $metrics = $this->service->getDeliveryMetrics(
            from: now()->subDays(30),
            to: now(),
            useCache: false
        );

        $this->assertEquals(10, $metrics['totals']['total']);
    }
}
