<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Contracts\NotificationSchedulingServiceInterface;
use App\Models\ScheduledNotification;
use App\Models\User;
use App\Services\NotificationSchedulingService;
use Illuminate\Notifications\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for NotificationSchedulingService
 *
 * Validates Requirements 2.7 - Notification scheduling for future delivery
 */
class NotificationSchedulingServiceTest extends TestCase
{
    private NotificationSchedulingServiceInterface $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(NotificationSchedulingServiceInterface::class);
    }

    #[Test]
    public function service_is_bound_in_container(): void
    {
        $service = app(NotificationSchedulingServiceInterface::class);

        $this->assertInstanceOf(NotificationSchedulingService::class, $service);
    }

    #[Test]
    public function schedule_creates_scheduled_notification(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');
        $scheduledAt = now()->addHour();

        $scheduleId = $this->service->schedule($user, $notification, $scheduledAt);

        $this->assertNotEmpty($scheduleId);

        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();
        $this->assertNotNull($scheduled);
        $this->assertEquals($user->id, $scheduled->user_id);
        $this->assertEquals(TestNotification::class, $scheduled->notification_class);
        $this->assertEquals(ScheduledNotification::STATUS_PENDING, $scheduled->status);
        $this->assertFalse($scheduled->is_recurring);
    }

    #[Test]
    public function schedule_with_metadata(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');
        $scheduledAt = now()->addHour();

        $scheduleId = $this->service->schedule($user, $notification, $scheduledAt, [
            'type' => 'ticket_updates',
            'priority' => 'high',
        ]);

        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();
        $this->assertEquals('ticket_updates', $scheduled->notification_type);
        $this->assertEquals('high', $scheduled->priority);
    }

    #[Test]
    public function schedule_recurring_creates_recurring_notification(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');
        $startAt = now()->addHour();

        $scheduleId = $this->service->scheduleRecurring(
            $user,
            $notification,
            $startAt,
            ScheduledNotification::RECURRENCE_DAILY
        );

        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();
        $this->assertTrue($scheduled->is_recurring);
        $this->assertEquals(ScheduledNotification::RECURRENCE_DAILY, $scheduled->recurrence_pattern);
        $this->assertNotNull($scheduled->next_occurrence_at);
    }

    #[Test]
    public function schedule_recurring_throws_for_invalid_pattern(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid recurrence pattern');

        $this->service->scheduleRecurring($user, $notification, now(), 'invalid');
    }

    #[Test]
    public function cancel_cancels_pending_notification(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');
        $scheduleId = $this->service->schedule($user, $notification, now()->addHour());

        $result = $this->service->cancel($scheduleId);

        $this->assertTrue($result);

        $scheduled = ScheduledNotification::where('schedule_id', $scheduleId)->first();
        $this->assertTrue($scheduled->isCancelled());
        $this->assertNotNull($scheduled->cancelled_at);
    }

    #[Test]
    public function cancel_returns_false_for_nonexistent_schedule(): void
    {
        $result = $this->service->cancel('nonexistent-id');

        $this->assertFalse($result);
    }

    #[Test]
    public function cancel_returns_false_for_already_sent_notification(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_SENT,
        ]);

        $result = $this->service->cancel($scheduled->schedule_id);

        $this->assertFalse($result);
    }

    #[Test]
    public function get_returns_scheduled_notification(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');
        $scheduleId = $this->service->schedule($user, $notification, now()->addHour());

        $scheduled = $this->service->get($scheduleId);

        $this->assertNotNull($scheduled);
        $this->assertEquals($scheduleId, $scheduled->schedule_id);
    }

    #[Test]
    public function get_returns_null_for_nonexistent_schedule(): void
    {
        $scheduled = $this->service->get('nonexistent-id');

        $this->assertNull($scheduled);
    }

    #[Test]
    public function get_pending_for_user_returns_pending_notifications(): void
    {
        $user = User::factory()->create();
        $notification = new TestNotification('Test message');

        $this->service->schedule($user, $notification, now()->addHour());
        $this->service->schedule($user, $notification, now()->addHours(2));

        $pending = $this->service->getPendingForUser($user);

        $this->assertCount(2, $pending);
    }

    #[Test]
    public function get_pending_for_user_excludes_sent_notifications(): void
    {
        $user = User::factory()->create();

        ScheduledNotification::factory()->create([
            'user_id' => $user->id,
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);

        ScheduledNotification::factory()->create([
            'user_id' => $user->id,
            'status' => ScheduledNotification::STATUS_SENT,
        ]);

        $pending = $this->service->getPendingForUser($user);

        $this->assertCount(1, $pending);
    }

    #[Test]
    public function reschedule_reschedules_failed_notification(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_FAILED,
            'retry_count' => 1,
        ]);

        $newTime = now()->addHours(2);
        $result = $this->service->reschedule($scheduled->schedule_id, $newTime);

        $this->assertTrue($result);

        $scheduled->refresh();
        $this->assertEquals(ScheduledNotification::STATUS_PENDING, $scheduled->status);
        $this->assertEquals($newTime->toDateTimeString(), $scheduled->scheduled_at->toDateTimeString());
    }

    #[Test]
    public function reschedule_returns_false_when_max_retries_exceeded(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_FAILED,
            'retry_count' => 3,
        ]);

        $result = $this->service->reschedule($scheduled->schedule_id);

        $this->assertFalse($result);
    }

    #[Test]
    public function scheduled_notification_model_status_helpers(): void
    {
        $pending = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);

        $sent = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_SENT,
        ]);

        $cancelled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_CANCELLED,
        ]);

        $failed = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_FAILED,
        ]);

        $this->assertTrue($pending->isPending());
        $this->assertTrue($sent->isSent());
        $this->assertTrue($cancelled->isCancelled());
        $this->assertTrue($failed->isFailed());
    }

    #[Test]
    public function scheduled_notification_mark_as_sent(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);

        $scheduled->markAsSent();

        $this->assertEquals(ScheduledNotification::STATUS_SENT, $scheduled->status);
        $this->assertNotNull($scheduled->sent_at);
    }

    #[Test]
    public function scheduled_notification_mark_as_cancelled(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_PENDING,
        ]);

        $scheduled->markAsCancelled();

        $this->assertEquals(ScheduledNotification::STATUS_CANCELLED, $scheduled->status);
        $this->assertNotNull($scheduled->cancelled_at);
    }

    #[Test]
    public function scheduled_notification_mark_as_failed(): void
    {
        $scheduled = ScheduledNotification::factory()->create([
            'status' => ScheduledNotification::STATUS_PENDING,
            'retry_count' => 0,
        ]);

        $scheduled->markAsFailed('Test error message');

        $this->assertEquals(ScheduledNotification::STATUS_FAILED, $scheduled->status);
        $this->assertEquals('Test error message', $scheduled->error_message);
        $this->assertEquals(1, $scheduled->retry_count);
    }

    #[Test]
    public function scheduled_notification_scopes(): void
    {
        $user = User::factory()->create();

        ScheduledNotification::factory()->create([
            'user_id' => $user->id,
            'status' => ScheduledNotification::STATUS_PENDING,
            'scheduled_at' => now()->subHour(),
        ]);

        ScheduledNotification::factory()->create([
            'user_id' => $user->id,
            'status' => ScheduledNotification::STATUS_PENDING,
            'scheduled_at' => now()->addHour(),
        ]);

        $pending = ScheduledNotification::pending()->get();
        $due = ScheduledNotification::due()->get();
        $forUser = ScheduledNotification::forUser($user->id)->get();

        $this->assertCount(2, $pending);
        $this->assertCount(1, $due);
        $this->assertCount(2, $forUser);
    }
}

/**
 * Test notification class for testing purposes
 */
class TestNotification extends Notification
{
    public function __construct(public string $message) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
        ];
    }
}
