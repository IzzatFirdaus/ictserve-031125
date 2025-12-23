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
| Broadcast Channels - ICTServe v3.6.0 Laravel Reverb Integration
|--------------------------------------------------------------------------
|
| This file defines the authorization callbacks for private and presence
| channels. ICTServe uses a Dual Channel Strategy:
|
| - Authenticated Users: Listen to private-user.{id}
| - Guests: Listen to private-ticket.{uuid} or private-loan.{uuid}
|
| @see D16_BROADCASTING_SETUP.md - WebSocket configuration
| @see Requirements 6.1, 6.2, 6.3, 8.1, 8.2 - Real-time notifications
|
*/

/**
 * Private user channel for authenticated users
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 6.1, 6.2, 8.1)
 */
Broadcast::channel('private-user.{userId}', function (User $user, string $userId): bool {
    return (int) $user->id === (int) $userId;
});

/**
 * Admin broadcast channel for high-priority alerts
 *
 * @see Requirements 8.1, 8.2 - High-priority ticket broadcast, SLA breach notification
 */
Broadcast::channel('private-admin.notifications', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * Private ticket channel for guests (UUID-based with status token)
 *
 * @see D16_BROADCASTING_SETUP.md §6.1 - Hybrid channel authorization
 * @see Requirements 2.1, 2.3 - Status checking and notifications
 */
Broadcast::channel('private-ticket.{uuid}', function (?User $user, string $uuid): bool {
    $ticket = HelpdeskTicket::where('uuid', $uuid)->first();

    if (! $ticket) {
        return false;
    }

    // Check for status token in query parameters for guest access
    $statusToken = request()->query('status_token');
    if ($statusToken && $ticket->status_token_hash) {
        if (Hash::check($statusToken, $ticket->status_token_hash)) {
            return true;
        }
    }

    // Authenticated user access - check policy
    if ($user && $user->can('view', $ticket)) {
        return true;
    }

    return false;
});

/**
 * Private loan channel for guests (UUID-based with status token)
 *
 * @see D16_BROADCASTING_SETUP.md §6.1 - Hybrid channel authorization
 * @see Requirements 4.5, 8.3 - Loan notifications and overdue reminders
 */
Broadcast::channel('private-loan.{uuid}', function (?User $user, string $uuid): bool {
    $loan = LoanApplication::where('uuid', $uuid)->first();

    if (! $loan) {
        return false;
    }

    // Check for status token in query parameters for guest access
    $statusToken = request()->query('status_token');
    if ($statusToken && $loan->status_token_hash) {
        if (Hash::check($statusToken, $loan->status_token_hash)) {
            return true;
        }
    }

    // Authenticated user access - check policy
    if ($user && $user->can('view', $loan)) {
        return true;
    }

    return false;
});

/**
 * Private channel for submission comments
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 */
Broadcast::channel('submission.{type}.{id}', function (User $user, string $type, string $id): bool {
    $submissionId = (int) $id;

    return match ($type) {
        'ticket' => ($ticket = HelpdeskTicket::find($submissionId)) ? $user->can('view', $ticket) : false,
        'loan' => ($loan = LoanApplication::find($submissionId)) ? $user->can('view', $loan) : false,
        default => false,
    };
});

/**
 * Private channel for asset updates
 *
 * @see D03 SRS-FR-018.3, D04 §5.3
 */
Broadcast::channel('asset.{id}', function (User $user, string $id): bool {
    $asset = Asset::find((int) $id);

    return $asset && $user->can('view', $asset);
});

/*
|--------------------------------------------------------------------------
| AI Broadcasting Channels - v3.6.0 Ollama Integration
|--------------------------------------------------------------------------
|
| Channels for AI operations including document processing, FAQ responses,
| auto-reply generation, and performance monitoring. Selaras dengan D16 v3.6.0.
|
| @see config/ai-broadcasting.php - AI channel configuration
| @see Requirements 8.4, 11.1, 11.2 - Real-time AI notifications
|
*/

/**
 * AI Status Channel - Document processing and FAQ operations
 *
 * @see Requirements 11.1, 11.2 - AI processing notifications
 * @see D16 Broadcasting Setup v3.6.0
 */
Broadcast::channel('ai-status', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Alerts Channel - Performance degradation and system errors
 *
 * @see Requirements 8.4 - Graceful degradation notifications
 * @see D11 Technical Design v3.6.0
 */
Broadcast::channel('ai-alerts', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Performance Channel - Real-time performance metrics
 *
 * @see Requirements 8.7 - Performance monitoring dashboard
 * @see Laravel Pulse integration
 */
Broadcast::channel('ai-performance', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Approvals Channel - Auto-reply approval workflow
 *
 * @see Requirements 3.4, 3.6 - Email-based approval workflow
 * @see D00 Four-tier role system v3.6.0
 */
Broadcast::channel('ai-approvals', function (User $user): bool {
    // Approver (Grade 41+), Admin, and Superuser can receive approval notifications
    return $user->canApprove();
});

/*
|--------------------------------------------------------------------------
| Dashboard Widget Broadcasting Channels - v3.6.1 Real-Time Updates
|--------------------------------------------------------------------------
|
| Channels for real-time dashboard widget updates including performance
| metrics, system statistics, and user-specific dashboard data.
| Integrates with WidgetRealtimeManager for rate limiting and caching.
|
| @see app/Services/WidgetRealtimeManager.php - Widget broadcasting service
| @see Requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
| @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
|
*/

/**
 * User-specific widget channel for personal dashboard updates
 *
 * @see Requirements R8, R19 - Real-time widget updates
 * @see D16 Broadcasting Setup v3.6.1
 */
Broadcast::channel('dashboard.widgets.{userId}', function (User $user, string $userId): bool {
    // Users can only access their own widget channel
    return (int) $user->id === (int) $userId;
});

/**
 * Global widget channel for admin/system-wide updates
 *
 * @see Requirements R8, R19 - Admin dashboard real-time updates
 * @see D00 Four-tier role system v3.6.1
 */
Broadcast::channel('dashboard.widgets.global', function (User $user): bool {
    // Only admin and superuser can access global widget updates
    return $user->hasAdminAccess();
});

/**
 * Widget-specific channel for targeted updates
 *
 * @see Requirements R8, R19 - Widget-specific real-time updates
 * @see app/Services/WidgetRegistry.php - Widget authorization
 */
Broadcast::channel('dashboard.widgets.{widgetId}', function (User $user, string $widgetId): bool {
    // Check if user has access to this specific widget
    // This integrates with the WidgetRegistry system from Task 2.1

    // For now, allow access based on role hierarchy
    // More granular widget-level permissions can be added later
    if ($user->hasRole(['admin', 'superuser'])) {
        return true;
    }

    // Staff users can access non-admin widgets
    // AI widgets are restricted to admin/superuser (handled in widget logic)
    $adminOnlyWidgets = [
        'ai_performance_widget',
        'ai_cost_widget',
        'ai_health_widget',
        'system_metrics_widget',
        'audit_log_widget',
    ];

    if (in_array($widgetId, $adminOnlyWidgets)) {
        return false;
    }

    // Staff can access general widgets
    return $user->hasRole(['staff', 'admin', 'superuser']);
});

/*
|--------------------------------------------------------------------------
| AI Conversation Broadcasting Channels - v3.6.0 Real-Time AI Streaming
|--------------------------------------------------------------------------
|
| Channels for AI conversation streaming including response chunks,
| completion events, and error handling. Supports both authenticated
| and guest access patterns for hybrid architecture.
|
| @see Requirements 6.1, 6.2, 6.3, 6.5 - AI streaming responses
| @see D18 AI Chatbot Ollama-Bedrock integration
|
*/

/**
 * AI conversation channel for streaming responses
 *
 * @see Requirements 6.1, 7.1 - AI streaming and channel authorization
 * @see D18 AI Chatbot integration - Hybrid access pattern
 */
Broadcast::channel('conversation.{conversationId}', function (?User $user, string $conversationId): bool {
    $conversation = \App\Models\BedrockConversation::find((int) $conversationId);

    if (! $conversation) {
        return false;
    }

    // Authenticated user access - verify ownership
    if ($user && $conversation->user_id) {
        return (int) $user->id === (int) $conversation->user_id;
    }

    // Guest access - for now, allow access to conversations without user_id
    // In future implementation, this would validate session_token when that field is added
    if (! $user && ! $conversation->user_id) {
        return true;
    }

    return false;
});
