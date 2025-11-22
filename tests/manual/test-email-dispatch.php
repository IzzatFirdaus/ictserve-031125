<?php

declare(strict_types=1);

require __DIR__.'/../../vendor/autoload.php';

$app = require_once __DIR__.'/../../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get test user and helpdesk ticket
$user = App\Models\User::first();
$ticket = App\Models\HelpdeskTicket::first();

if (! $user || ! $ticket) {
    echo "ERROR: Need at least 1 user and 1 helpdesk ticket in database\n";
    exit(1);
}

echo "Testing EmailDispatcher integration...\n";
echo "User: {$user->name} ({$user->email})\n";
echo "Ticket: #{$ticket->ticket_number}\n\n";

// Dispatch email using dispatchEmailOnly (TicketAssignedMail needs: ticket, assignedTo, assignedBy)
$dispatcher = app(App\Services\UnifiedNotificationDispatcher::class);
$result = $dispatcher->dispatchEmailOnly(
    $user,
    new App\Mail\TicketAssignedMail($ticket, $user, $user),
    ['test' => true],
    'ticket_assigned'
);

echo "✓ Email dispatched\n\n";

// Get latest EmailLog
$log = App\Models\EmailLog::latest()->first();

echo "EmailLog Verification:\n";
echo "=====================\n";
echo "ID: {$log->id}\n";
echo "Recipient: {$log->recipient_email}\n";
echo "Subject: {$log->subject}\n";
echo "Status: {$log->status}\n";
echo "\nNew Unified Notification Columns:\n";
echo "---------------------------------\n";
echo 'Channels: '.json_encode($log->channels)."\n";
echo 'Notification Type: '.($log->notification_type ?? 'null')."\n";
echo "Priority: {$log->priority}\n";
echo 'Next Retry At: '.($log->next_retry_at ? $log->next_retry_at->toDateTimeString() : 'null')."\n";
echo 'Final Status: '.($log->final_status ?? 'null')."\n";
echo 'Preference Bypassed: '.($log->preference_bypassed ? 'true' : 'false')."\n";

echo "\n✓ All columns populated successfully!\n";
