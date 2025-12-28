<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\Asset;
use App\Models\EmailLog;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Notifications\MaintenanceTicketCreated;
use App\Services\Notifications\EmailDispatcher;
use App\Services\Notifications\TicketNotificationService;
use Illuminate\Support\Facades\Notification;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketNotificationServiceTest extends TestCase
{
    #[Test]
    public function it_broadcasts_user_notifications_for_maintenance_tickets(): void
    {
        // Fake notifications to prevent actual sending and capture what was sent
        Notification::fake();

        // Create recipients
        $admin = User::factory()->create(['role' => 'admin']);
        $super = User::factory()->create(['role' => 'superuser']);

        // Create ticket and supporting models
        $asset = Asset::factory()->create();
        $application = LoanApplication::factory()->create();
        $ticket = HelpdeskTicket::factory()->create([
            'asset_id' => $asset->id,
        ]);

        // Mock dispatcher to prevent actual mail queuing
        $dispatcher = Mockery::mock(EmailDispatcher::class);
        $dispatcher->shouldReceive('queue')
            ->andReturn(EmailLog::factory()->create());

        $service = new TicketNotificationService($dispatcher);

        // Act: send maintenance notifications
        $service->sendMaintenanceNotification($ticket, $asset, $application);

        // Assert: MaintenanceTicketCreated notification sent to both admins
        Notification::assertSentTo($admin, MaintenanceTicketCreated::class);
        Notification::assertSentTo($super, MaintenanceTicketCreated::class);
        Notification::assertCount(2);
    }
}
