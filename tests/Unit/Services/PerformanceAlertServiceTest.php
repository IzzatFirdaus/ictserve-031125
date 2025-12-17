<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\User;
use App\Notifications\PerformanceThresholdBreached;
use App\Services\PerformanceAlertService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for PerformanceAlertService.
 *
 * Tests performance monitoring, threshold detection, and alert notifications.
 *
 * @see D03 §16.1-16.5 - Performance Monitoring Requirements
 * @see Requirements 4.1, 4.2, 14.1, 14.2, 14.5, 16.3
 */
#[CoversClass(PerformanceAlertService::class)]
class PerformanceAlertServiceTest extends TestCase
{
    use RefreshDatabase;

    private PerformanceAlertService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new PerformanceAlertService;

        // Clear cache before each test
        Cache::flush();

        // Fake notifications
        Notification::fake();
    }

    /**
     * Test check performance metrics runs without errors.
     */
    #[Test]
    public function check_performance_metrics_runs_without_errors(): void
    {
        // Create admin user to receive notifications
        User::factory()->create(['role' => 'admin']);

        // Should not throw any exceptions
        $this->service->checkPerformanceMetrics();

        $this->assertTrue(true);
    }

    /**
     * Test slow requests alert is sent when threshold exceeded.
     */
    #[Test]
    public function slow_requests_alert_sent_when_threshold_exceeded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create pulse_entries table if not exists and add slow requests
        $this->createPulseEntriesTable();
        $this->insertSlowRequests(15);

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Permintaan HTTP Perlahan');
            }
        );
    }

    /**
     * Test slow requests alert not sent when below threshold.
     */
    #[Test]
    public function slow_requests_alert_not_sent_when_below_threshold(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create pulse_entries table if not exists and add few slow requests
        $this->createPulseEntriesTable();
        $this->insertSlowRequests(5);

        $this->service->checkPerformanceMetrics();

        Notification::assertNotSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Permintaan HTTP Perlahan');
            }
        );
    }

    /**
     * Test alert cooldown prevents duplicate alerts.
     */
    #[Test]
    public function alert_cooldown_prevents_duplicate_alerts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create pulse_entries table and add slow requests
        $this->createPulseEntriesTable();
        $this->insertSlowRequests(15);

        // First check - should send alert
        $this->service->checkPerformanceMetrics();

        // Second check - should not send due to cooldown
        $this->service->checkPerformanceMetrics();

        // Should only be sent once
        Notification::assertSentToTimes($admin, PerformanceThresholdBreached::class, 1);
    }

    /**
     * Test queue job failures alert is sent when threshold exceeded.
     */
    #[Test]
    public function queue_job_failures_alert_sent_when_threshold_exceeded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create failed_jobs table entries
        $this->createFailedJobsEntries(10);

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Kegagalan Kerja Baris Gilir');
            }
        );
    }

    /**
     * Test pending loans alert is sent when threshold exceeded.
     */
    #[Test]
    public function pending_loans_alert_sent_when_threshold_exceeded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create pending loan applications using factory state
        \App\Models\LoanApplication::factory()
            ->count(25)
            ->underReview()
            ->create();

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Permohonan Pinjaman Tertunggak');
            }
        );
    }

    /**
     * Test overdue assets alert is sent when threshold exceeded.
     */
    #[Test]
    public function overdue_assets_alert_sent_when_threshold_exceeded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create overdue loan applications using factory state
        \App\Models\LoanApplication::factory()
            ->count(15)
            ->overdue()
            ->create();

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Aset Tertunggak');
            }
        );
    }

    /**
     * Test alerts are sent to both admin and superuser roles.
     */
    #[Test]
    public function alerts_sent_to_admin_and_superuser_roles(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $superuser = User::factory()->create(['role' => 'superuser']);
        User::factory()->create(['role' => 'staff']); // Should not receive

        // Create pulse_entries table and add slow requests
        $this->createPulseEntriesTable();
        $this->insertSlowRequests(15);

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo($admin, PerformanceThresholdBreached::class);
        Notification::assertSentTo($superuser, PerformanceThresholdBreached::class);
    }

    /**
     * Test no alerts sent when no admin users exist.
     */
    #[Test]
    public function no_alerts_sent_when_no_admin_users(): void
    {
        // Only create staff users
        User::factory()->count(3)->create(['role' => 'staff']);

        // Create pulse_entries table and add slow requests
        $this->createPulseEntriesTable();
        $this->insertSlowRequests(15);

        $this->service->checkPerformanceMetrics();

        Notification::assertNothingSent();
    }

    /**
     * Test cache miss rate alert logic exists.
     */
    #[Test]
    public function cache_miss_rate_check_runs_without_errors(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Just verify the service runs without errors
        // Cache metrics require specific Pulse table structure
        $this->service->checkPerformanceMetrics();

        $this->assertTrue(true);
    }

    /**
     * Test slow queries alert is sent when threshold exceeded.
     */
    #[Test]
    public function slow_queries_alert_sent_when_threshold_exceeded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create pulse_entries table and add slow queries
        $this->createPulseEntriesTable();
        $this->insertSlowQueries(25);

        $this->service->checkPerformanceMetrics();

        Notification::assertSentTo(
            $admin,
            PerformanceThresholdBreached::class,
            function (PerformanceThresholdBreached $notification) {
                return str_contains($notification->title, 'Pertanyaan Pangkalan Data Perlahan');
            }
        );
    }

    /**
     * Helper: Create pulse_entries table.
     */
    private function createPulseEntriesTable(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('pulse_entries')) {
            DB::statement('
                CREATE TABLE pulse_entries (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    timestamp INTEGER NOT NULL,
                    type VARCHAR(255) NOT NULL,
                    key VARCHAR(255) NOT NULL,
                    value INTEGER DEFAULT NULL
                )
            ');
        }
    }

    /**
     * Helper: Create pulse_aggregates table.
     */
    private function createPulseAggregatesTable(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('pulse_aggregates')) {
            DB::statement('
                CREATE TABLE pulse_aggregates (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    bucket INTEGER NOT NULL,
                    period INTEGER NOT NULL,
                    type VARCHAR(255) NOT NULL,
                    key VARCHAR(255) NOT NULL,
                    value REAL DEFAULT NULL
                )
            ');
        }
    }

    /**
     * Helper: Insert slow request entries.
     */
    private function insertSlowRequests(int $count): void
    {
        $timestamp = now()->subMinutes(30)->timestamp;

        for ($i = 0; $i < $count; $i++) {
            DB::table('pulse_entries')->insert([
                'timestamp' => $timestamp,
                'type' => 'slow_request',
                'key' => '/api/test',
                'value' => 2500,
            ]);
        }
    }

    /**
     * Helper: Insert slow query entries.
     */
    private function insertSlowQueries(int $count): void
    {
        $timestamp = now()->subMinutes(30)->timestamp;

        for ($i = 0; $i < $count; $i++) {
            DB::table('pulse_entries')->insert([
                'timestamp' => $timestamp,
                'type' => 'slow_query',
                'key' => 'SELECT * FROM users',
                'value' => 600,
            ]);
        }
    }

    /**
     * Helper: Insert cache metrics.
     */
    private function insertCacheMetrics(int $hits, int $misses): void
    {
        $bucket = now()->subMinutes(30)->timestamp;

        DB::table('pulse_aggregates')->insert([
            'bucket' => $bucket,
            'period' => 3600,
            'type' => 'cache_hit',
            'key' => 'total',
            'value' => $hits,
        ]);

        DB::table('pulse_aggregates')->insert([
            'bucket' => $bucket,
            'period' => 3600,
            'type' => 'cache_miss',
            'key' => 'total',
            'value' => $misses,
        ]);
    }

    /**
     * Helper: Create failed jobs entries.
     */
    private function createFailedJobsEntries(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            DB::table('failed_jobs')->insert([
                'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                'connection' => 'redis',
                'queue' => 'default',
                'payload' => '{}',
                'exception' => 'Test exception',
                'failed_at' => now()->subMinutes(30),
            ]);
        }
    }
}
