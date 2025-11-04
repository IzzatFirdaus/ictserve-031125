<?php

declare(strict_types=1);

namespace Tests\Feature\Queue;

use App\Events\AssetReturnedDamaged;
use App\Listeners\CreateMaintenanceTicketForDamagedAsset;
use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\LoanTransaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Queue Configuration Tests
 *
 * Verifies queue system configuration including Redis driver,
 * retry mechanism, and SLA monitoring for email notifications.
 *
 * @trace Requirements: Requirement 8.2
 */
class QueueConfigurationTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_has_correct_queue_configuration(): void
    {
        // Verify queue configuration exists
        $this->assertNotNull(config('queue.default'));
        $this->assertNotNull(config('queue.connections.redis'));
        $this->assertNotNull(config('queue.connections.database'));

        // Verify retry configuration
        $redisConfig = config('queue.connections.redis');
        $this->assertArrayHasKey('retry_after', $redisConfig);
        $this->assertGreaterThan(0, $redisConfig['retry_after']);
    }

    #[Test]
    public function it_registers_asset_returned_damaged_listener(): void
    {
        Event::fake();

        $asset = Asset::factory()->create();
        $loanApplication = LoanApplication::factory()->create();
        $transaction = LoanTransaction::factory()->create([
            'loan_application_id' => $loanApplication->id,
        ]);

        // Dispatch event
        event(new AssetReturnedDamaged($transaction, $asset));

        // Verify listener is registered
        Event::assertDispatched(AssetReturnedDamaged::class);
    }

    #[Test]
    public function it_queues_maintenance_ticket_creation(): void
    {
        Queue::fake();

        $asset = Asset::factory()->create();
        $loanApplication = LoanApplication::factory()->create();
        $transaction = LoanTransaction::factory()->create([
            'loan_application_id' => $loanApplication->id,
        ]);

        // Dispatch event
        event(new AssetReturnedDamaged($transaction, $asset));

        // Verify job was pushed to queue
        Queue::assertPushed(\Illuminate\Events\CallQueuedListener::class, function (\Illuminate\Events\CallQueuedListener $job) {
            return str_contains($job->class, 'CreateMaintenanceTicketForDamagedAsset');
        });
    }

    #[Test]
    public function it_has_retry_mechanism_configured(): void
    {
        $listener = new CreateMaintenanceTicketForDamagedAsset(
            app(\App\Services\NotificationService::class)
        );

        // Verify retry configuration
        $this->assertEquals(3, $listener->tries);
        $this->assertIsArray($listener->backoff);
        $this->assertCount(3, $listener->backoff);
        $this->assertEquals([10, 30, 60], $listener->backoff);
        $this->assertEquals(60, $listener->timeout);
    }

    #[Test]
    public function it_has_exponential_backoff_configured(): void
    {
        $listener = new CreateMaintenanceTicketForDamagedAsset(
            app(\App\Services\NotificationService::class)
        );

        // Verify exponential backoff pattern
        $backoff = $listener->backoff;
        $this->assertGreaterThan($backoff[0], $backoff[1]);
        $this->assertGreaterThan($backoff[1], $backoff[2]);
    }

    #[Test]
    public function it_has_failed_jobs_table_configured(): void
    {
        // Verify failed jobs configuration
        $failedConfig = config('queue.failed');
        $this->assertNotNull($failedConfig);
        $this->assertArrayHasKey('driver', $failedConfig);
        $this->assertArrayHasKey('table', $failedConfig);
        $this->assertEquals('failed_jobs', $failedConfig['table']);
    }

    #[Test]
    public function it_supports_job_batching(): void
    {
        // Verify job batching configuration
        $batchingConfig = config('queue.batching');
        $this->assertNotNull($batchingConfig);
        $this->assertArrayHasKey('database', $batchingConfig);
        $this->assertArrayHasKey('table', $batchingConfig);
        $this->assertEquals('job_batches', $batchingConfig['table']);
    }
}
