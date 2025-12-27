<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Events\NotificationCreated;
use App\Models\Asset;
use App\Models\EmailLog;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Services\Notifications\EmailDispatcher;
use App\Services\Notifications\TicketNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketNotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_broadcasts_user_notifications_for_maintenance_tickets(): void
    {
        // Create recipients
        $admin = User::factory()->create(['role' => 'admin']);
        $super = User::factory()->create(['role' => 'superuser']);

        // Create ticket and supporting models
        $asset = Asset::factory()->create();
        $application = LoanApplication::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
        ]);

        // Fake events AFTER model creation to avoid counting setup events
        Event::fake([NotificationCreated::class]);

        // Mock dispatcher to prevent actual mail queuing
        $dispatcher = Mockery::mock(EmailDispatcher::class);
        $dispatcher->shouldReceive('queue')
            ->andReturn(EmailLog::factory()->create());

        $service = new TicketNotificationService($dispatcher);

        // Act: send maintenance notifications
        $service->sendMaintenanceNotification($ticket, $asset, $application);

        // Assert: NotificationCreated event dispatched for both admins
        Event::assertDispatched(NotificationCreated::class, 2);
        Event::assertDispatched(NotificationCreated::class, function ($event) use ($admin) {
            return $event->user->id === $admin->id;
        });
    }
}
