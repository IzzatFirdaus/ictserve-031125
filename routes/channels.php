<?php

declare(strict_types=1);

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels - ICTServe v4.0 PKS 5.2.1 Compliant
|--------------------------------------------------------------------------
|
| This file defines the authorization callbacks for private and presence
| channels. ICTServe v4.0 uses AUTHENTICATED-ONLY channels per PKS 5.2.1:
|
| - Authenticated Users: Listen to private-user.{userId}
| - Ticket Updates: Listen to ticket.{userId}.{ticketId}
| - Loan Updates: Listen to loan.{userId}.{loanId}
|
| NO GUEST CHANNELS - All channels require authenticated user_id per PKS 5.2.1
|
| @see D16_BROADCASTING_SETUP.md - WebSocket configuration
| @see Requirements 6.4, 6.5, 24.5, 24.6, 25.1 - Authenticated-only channels
|
*/

/**
 * Private user channel for authenticated users
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 6.1, 6.2, 8.1)
 * @see PKS 5.2.1 - Mandatory user_id linkage
 */
Broadcast::channel('user.{userId}', function (User $user, string $userId): bool {
    return (int) $user->id === (int) $userId;
});

/**
 * Admin broadcast channel for high-priority alerts
 *
 * @see Requirements 8.1, 8.2 - High-priority ticket broadcast, SLA breach notification
 * @see PKS 5.2.1 - Admin access requires authenticated user
 */
Broadcast::channel('admin.notifications', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * Authenticated ticket channel - PKS 5.2.1 Compliant
 *
 * Channel format: ticket.{userId}.{ticketId}
 * Requires authenticated user who owns the ticket
 *
 * @see Requirements 6.4, 6.5, 24.5, 24.6, 25.1 - Authenticated-only channels
 * @see PKS 5.2.1 - All channels require authenticated user_id
 */
Broadcast::channel('ticket.{userId}.{ticketId}', function (User $user, string $userId, string $ticketId): bool {
    // Verify user owns this channel
    if ((int) $user->id !== (int) $userId) {
        return false;
    }

    // Verify ticket exists and belongs to user
    $ticket = HelpdeskTicket::find((int) $ticketId);

    if (! $ticket) {
        return false;
    }

    // PKS 5.2.1: All tickets must have user_id (NOT NULL)
    return $ticket->user_id === (int) $userId;
});

/**
 * Authenticated loan channel - PKS 5.2.1 Compliant
 *
 * Channel format: loan.{userId}.{loanId}
 * Requires authenticated user who owns the loan application
 *
 * @see Requirements 6.4, 6.5, 24.5, 24.6, 25.1 - Authenticated-only channels
 * @see PKS 5.2.1 - All channels require authenticated user_id
 */
Broadcast::channel('loan.{userId}.{loanId}', function (User $user, string $userId, string $loanId): bool {
    // Verify user owns this channel
    if ((int) $user->id !== (int) $userId) {
        return false;
    }

    // Verify loan exists and belongs to user
    $loan = LoanApplication::find((int) $loanId);

    if (! $loan) {
        return false;
    }

    // PKS 5.2.1: All loans must have user_id (NOT NULL)
    return $loan->user_id === (int) $userId;
});

/**
 * Private channel for submission comments - PKS 5.2.1 Compliant
 *
 * @see D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 * @see PKS 5.2.1 - Requires authenticated user with view permission
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
 * Private channel for asset updates - PKS 5.2.1 Compliant
 *
 * @see D03 SRS-FR-018.3, D04 §5.3
 * @see PKS 5.2.1 - Requires authenticated user with view permission
 */
Broadcast::channel('asset.{id}', function (User $user, string $id): bool {
    $asset = Asset::find((int) $id);

    return $asset && $user->can('view', $asset);
});

/*
|--------------------------------------------------------------------------
| AI Broadcasting Channels - v4.0 PKS 5.2.1 Compliant
|--------------------------------------------------------------------------
|
| Channels for AI operations including document processing, FAQ responses,
| auto-reply generation, and performance monitoring. All channels require
| authenticated users per PKS 5.2.1.
|
| @see config/ai-broadcasting.php - AI channel configuration
| @see Requirements 8.4, 11.1, 11.2 - Real-time AI notifications
| @see PKS 5.2.1 - All channels require authenticated user_id
|
*/

/**
 * AI Status Channel - Document processing and FAQ operations
 *
 * @see Requirements 11.1, 11.2 - AI processing notifications
 * @see D16 Broadcasting Setup v4.0
 * @see PKS 5.2.1 - Admin access requires authenticated user
 */
Broadcast::channel('ai-status', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Alerts Channel - Performance degradation and system errors
 *
 * @see Requirements 8.4 - Graceful degradation notifications
 * @see D11 Technical Design v4.0
 * @see PKS 5.2.1 - Admin access requires authenticated user
 */
Broadcast::channel('ai-alerts', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Performance Channel - Real-time performance metrics
 *
 * @see Requirements 8.7 - Performance monitoring dashboard
 * @see Laravel Pulse integration
 * @see PKS 5.2.1 - Admin access requires authenticated user
 */
Broadcast::channel('ai-performance', function (User $user): bool {
    return $user->hasAdminAccess();
});

/**
 * AI Approvals Channel - Auto-reply approval workflow
 *
 * @see Requirements 3.4, 3.6 - Email-based approval workflow
 * @see D00 Four-tier role system v4.0
 * @see PKS 5.2.1 - Approver access requires authenticated user
 */
Broadcast::channel('ai-approvals', function (User $user): bool {
    // Approver (Grade 41+), Admin, and Superuser can receive approval notifications
    return $user->canApprove();
});

/*
|--------------------------------------------------------------------------
| Dashboard Widget Broadcasting Channels - v4.0 PKS 5.2.1 Compliant
|--------------------------------------------------------------------------
|
| Channels for real-time dashboard widget updates including performance
| metrics, system statistics, and user-specific dashboard data.
| All channels require authenticated users per PKS 5.2.1.
|
| @see app/Services/WidgetRealtimeManager.php - Widget broadcasting service
| @see Requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
| @see PKS 5.2.1 - All channels require authenticated user_id
| @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
|
*/

/**
 * User-specific widget channel for personal dashboard updates
 *
 * @see Requirements R8, R19 - Real-time widget updates
 * @see D16 Broadcasting Setup v4.0
 * @see PKS 5.2.1 - Requires authenticated user matching userId
 */
Broadcast::channel('dashboard.widgets.{userId}', function (User $user, string $userId): bool {
    // Users can only access their own widget channel
    return (int) $user->id === (int) $userId;
});

/**
 * Global widget channel for admin/system-wide updates
 *
 * @see Requirements R8, R19 - Admin dashboard real-time updates
 * @see D00 Four-tier role system v4.0
 * @see PKS 5.2.1 - Admin access requires authenticated user
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
 * @see PKS 5.2.1 - Requires authenticated user with appropriate role
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
| AI Conversation Broadcasting Channels - v4.0 PKS 5.2.1 Compliant
|--------------------------------------------------------------------------
|
| Channels for AI conversation streaming including response chunks,
| completion events, and error handling. All channels require
| authenticated users per PKS 5.2.1 - NO GUEST ACCESS.
|
| @see Requirements 6.1, 6.2, 6.3, 6.5 - AI streaming responses
| @see D18 AI Chatbot Ollama-Bedrock integration
| @see PKS 5.2.1 - All channels require authenticated user_id
|
*/

/**
 * AI conversation channel for streaming responses - PKS 5.2.1 Compliant
 *
 * @see Requirements 6.1, 7.1 - AI streaming and channel authorization
 * @see D18 AI Chatbot integration - Authenticated-only access
 * @see PKS 5.2.1 - All conversations must have user_id (NOT NULL)
 */
Broadcast::channel('conversation.{conversationId}', function (User $user, string $conversationId): bool {
    $conversation = \App\Models\BedrockConversation::find((int) $conversationId);

    if (! $conversation) {
        return false;
    }

    // PKS 5.2.1: All conversations must have user_id (NOT NULL)
    // Only authenticated user who owns the conversation can access
    if ($conversation->user_id === null) {
        return false; // Reject conversations without user_id
    }

    return (int) $user->id === (int) $conversation->user_id;
});
