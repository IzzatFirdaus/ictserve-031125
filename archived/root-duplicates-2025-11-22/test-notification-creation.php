<?php

declare(strict_types=1);

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\HelpdeskTicket;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;

// Create test user
$user = User::factory()->create(['email' => 'test@example.com']);
$ticket = HelpdeskTicket::factory()->create();

echo "User ID: {$user->id}\n";
echo "Ticket ID: {$ticket->id}\n";

// Check notifications before
$countBefore = $user->notifications()->count();
echo "Notifications before: $countBefore\n";

// Send notification
$notification = new TicketAssignedNotification($ticket);
$user->notify($notification);

// Check notifications after
$countAfter = $user->notifications()->count();
echo "Notifications after: $countAfter\n";

// Retrieve latest notification
$latest = $user->notifications()->latest('created_at')->first();
if ($latest) {
    echo "Latest notification ID: {$latest->id}\n";
    echo "Latest notification type: {$latest->type}\n";
} else {
    echo "❌ No notifications found!\n";
}

// Cleanup
$user->delete();
$ticket->delete();

echo "\n✅ Test completed\n";
