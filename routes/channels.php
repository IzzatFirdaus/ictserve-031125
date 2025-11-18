<?php

use App\Models\Asset;
use Illuminate\Support\Facades\Broadcast;

/**
 * Private user channel for notifications and status updates
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 6.1, 6.2)
 */
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

/**
 * Private channel for submission comments
 * Authorizes based on user's access to the commentable resource
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 */
Broadcast::channel('submission.{type}.{id}', function ($user, $type, $id) {
    // Authorize based on resource type and user permissions
    return match ($type) {
        'ticket' => $user->can('view', \App\Models\HelpdeskTicket::find($id)),
        'loan' => $user->can('view', \App\Models\LoanApplication::find($id)),
        default => false,
    };
});

/**
 * Private channel for asset updates (maintenance / status updates)
 * Authorize on the user's ability to view the asset. Useful for broadcasts
 * related to maintenance, damage reports, and asset lifecycle events.
 *
 * @see D03 SRS-FR-018.3, D04 §5.3
 */
Broadcast::channel('asset.{id}', function ($user, $id) {
    return $user->can('view', Asset::find($id));
});

// Legacy channel - keep for backward compatibility if needed
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
