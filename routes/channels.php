<?php

declare(strict_types=1);

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Broadcast Channels - True Hybrid Architecture v3.5.0
|--------------------------------------------------------------------------
|
| This file defines the authorization callbacks for private and presence
| channels. ICTServe uses a Dual Channel Strategy:
|
| - Authenticated Users: Listen to private-user.{id}
| - Guests: Listen to private-ticket.{uuid} or private-loan.{uuid}
|
| @see D16_BROADCASTING_SETUP.md - WebSocket configuration
| @see Requirements 8.1, 8.2 - Real-time notifications
|
*/

/**
 * Private user channel for authenticated users
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 6.1, 6.2, 8.1)
 */
Broadcast::channel('user.{userId}', function (User $user, int $userId): bool|array {
    if ((int) $user->id === $userId) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role ?? 'staff',
        ];
    }

    return false;
});

/**
 * Admin broadcast channel for high-priority alerts
 *
 * @see Requirements 8.1, 8.2 - High-priority ticket broadcast, SLA breach notification
 */
Broadcast::channel('admin.notifications', function (User $user): bool|array {
    if (in_array($user->role, ['admin', 'superuser'], true)) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'role' => $user->role,
        ];
    }

    return false;
});

/**
 * Private ticket channel for guests (UUID-based with status token)
 *
 * @see D16_BROADCASTING_SETUP.md §6.1 - Hybrid channel authorization
 * @see Requirements 2.1, 2.3 - Status checking and notifications
 */
Broadcast::channel('ticket.{uuid}', function (?User $user, string $uuid): bool|array {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();

    if (! $ticket) {
        return false;
    }

    $statusToken = request()->query('status_token');
    if ($statusToken && $ticket->status_token_hash) {
        if (Hash::check($statusToken, $ticket->status_token_hash)) {
            return ['uuid' => $ticket->uuid, 'type' => 'guest'];
        }
    }

    if ($user && $user->can('view', $ticket)) {
        return ['uuid' => $ticket->uuid, 'role' => $user->role ?? 'admin'];
    }

    return false;
});

/**
 * Private loan channel for guests (UUID-based with status token)
 *
 * @see D16_BROADCASTING_SETUP.md §6.1 - Hybrid channel authorization
 * @see Requirements 4.5, 8.3 - Loan notifications and overdue reminders
 */
Broadcast::channel('loan.{uuid}', function (?User $user, string $uuid): bool|array {
    $loan = LoanApplication::where('uuid', $uuid)->first();

    if (! $loan) {
        return false;
    }

    $statusToken = request()->query('status_token');
    if ($statusToken && $loan->status_token_hash) {
        if (Hash::check($statusToken, $loan->status_token_hash)) {
            return ['uuid' => $loan->uuid, 'type' => 'guest'];
        }
    }

    if ($user && $user->can('view', $loan)) {
        return ['uuid' => $loan->uuid, 'role' => $user->role ?? 'admin'];
    }

    return false;
});

/**
 * Private channel for submission comments
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 */
Broadcast::channel('submission.{type}.{id}', function (User $user, string $type, int $id): bool {
    return match ($type) {
        'ticket' => $user->can('view', HelpdeskTicket::find($id)),
        'loan' => $user->can('view', LoanApplication::find($id)),
        default => false,
    };
});

/**
 * Private channel for asset updates
 *
 * @see D03 SRS-FR-018.3, D04 §5.3
 */
Broadcast::channel('asset.{id}', function (User $user, int $id): bool {
    $asset = Asset::find($id);

    return $asset && $user->can('view', $asset);
});
