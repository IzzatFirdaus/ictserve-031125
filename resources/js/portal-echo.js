/**
 * Portal Echo Listeners
 *
 * This file contains all Laravel Echo event listeners for the authenticated portal.
 * It handles real-time updates for notifications, submission status changes, and comments.
 *
 * @trace D03 SRS-FR-008, D04 §5.3, D12 §4 (Requirements 6.1, 6.2, 7.4)
 * @author dev-team@motac.gov.my
 * @last-updated 2025-11-06
 */

/**
 * Initialize Echo listeners for authenticated users
 */
export function initializePortalEcho() {
    // Only initialize if user is authenticated
    const userId = document.querySelector('meta[name="user-id"]')?.content;

    if (!userId || !window.Echo) {
        console.warn(
            "Portal Echo: User not authenticated or Echo not initialized"
        );
        return;
    }

    console.log(`Portal Echo: Initializing listeners for user ${userId}`);

    /**
     * Listen for new notifications on private user channel
     *
     * Event: notification.created
     * Channel: private-user.{userId}
     *
     * Payload structure from NotificationCreated::broadcastWith():
     * {
     *   id: notification.id,
     *   type: notification.type,
     *   data: { title, message, url, ... },
     *   created_at: ISO timestamp,
     *   read_at: ISO timestamp or null
     * }
     *
     * @trace Requirements 6.1, 6.2, D03 SRS-FR-043
     */
    window.Echo.private(`user.${userId}`).listen(
        ".notification.created",
        (event) => {
            console.log("Portal Echo: Notification received", event);

            // Dispatch Livewire event to update NotificationBell component
            if (window.Livewire) {
                window.Livewire.dispatch("echo:notification-created", event);
            }

            // Extract notification data (directly from event.data - this is the payload from broadcastWith())
            const title = event.data?.title || event.data?.subject || "New Notification";
            const message = event.data?.message || event.data?.body || "";
            const notificationId = event.id;

            // Show browser notification if permission granted
            if (
                "Notification" in window &&
                Notification.permission === "granted"
            ) {
                new Notification(title, {
                    body: message,
                    icon: "/images/motac-logo-32.png",
                    tag: notificationId,
                });
            }

            // Update ARIA live region for screen readers
            announceNotification(title, message);
        }
    );

    /**
     * Listen for submission status updates
     *
     * Event: status.updated
     * Channel: private-user.{userId}
     *
     * Payload structure from StatusUpdated::broadcastWith():
     * {
     *   model_type: 'HelpdeskTicket' | 'LoanApplication',
     *   model_id: ID,
     *   old_status: previous status string,
     *   new_status: new status string,
     *   updated_at: ISO timestamp
     * }
     *
     * @trace Requirements 6.1, 10.1, D03 SRS-FR-043
     */
    window.Echo.private(`user.${userId}`).listen(".status.updated", (event) => {
        console.log("Portal Echo: Status update received", event);

        // Dispatch Livewire event to update submission components
        // Pass complete event data with both old and new payload formats for compatibility
        if (window.Livewire) {
            window.Livewire.dispatch("echo:status-updated", {
                ...event,
                // Add legacy keys for backward compatibility with existing components
                submission_type: event.model_type,
                submission_id: event.model_id,
            });
        }

        // Extract model info for announcement
        const modelType = event.model_type || 'Submission';
        const modelId = event.model_id || 'unknown';
        const newStatus = event.new_status || 'updated';

        // Update ARIA live region
        announceStatusUpdate(modelType, modelId, newStatus);
    });

    /**
     * Listen for new comments on submissions
     *
     * Event: comment.posted
     * Channel: private-submission.{submissionType}.{submissionId}
     *
     * Note: This listener is dynamically added when viewing submission details
     *
     * @trace Requirements 7.4
     */
    window.subscribeToSubmissionComments = function (
        submissionType,
        submissionId
    ) {
        const channelName = `submission.${submissionType}.${submissionId}`;

        console.log(`Portal Echo: Subscribing to ${channelName}`);

        window.Echo.private(channelName).listen(".comment.posted", (event) => {
            console.log("Portal Echo: Comment posted", event);

            // Dispatch Livewire event to update InternalComments component
            if (window.Livewire) {
                window.Livewire.dispatch("echo:comment-posted", event);
            }

            // Update ARIA live region
            announceNewComment(event.comment.user.name);
        });
    };

    /**
     * Unsubscribe from submission comments channel
     */
    window.unsubscribeFromSubmissionComments = function (
        submissionType,
        submissionId
    ) {
        const channelName = `submission.${submissionType}.${submissionId}`;

        console.log(`Portal Echo: Unsubscribing from ${channelName}`);

        window.Echo.leave(channelName);
    };

    /**
     * Subscribe to asset-specific updates
     * Channel: asset.{assetId}
     * Event: asset.returned.damaged
     */
    window.subscribeToAssetUpdates = function (assetId) {
        const channelName = `asset.${assetId}`;

        if (!window.Echo) return;

        console.log(`Portal Echo: Subscribing to ${channelName}`);

        window.Echo.private(channelName).listen('.asset.returned.damaged', (event) => {
            console.log('Portal Echo: Asset returned damaged', event);

            // Dispatch Livewire event to update any asset components
            if (window.Livewire) {
                window.Livewire.dispatch('echo:asset-returned-damaged', event);
            }

            // Announce to screen readers
            const liveRegion = document.getElementById('aria-live-notifications');
            if (liveRegion) {
                liveRegion.textContent = `Asset ${event.asset_tag} returned damaged.`;
                setTimeout(() => {
                    liveRegion.textContent = '';
                }, 5000);
            }
        });
    };

    /**
     * Unsubscribe from asset updates
     */
    window.unsubscribeFromAssetUpdates = function (assetId) {
        const channelName = `asset.${assetId}`;

        if (!window.Echo) return;

        console.log(`Portal Echo: Unsubscribing from ${channelName}`);
        window.Echo.leave(channelName);
    };

    /**
     * Request browser notification permission
     */
    if ("Notification" in window && Notification.permission === "default") {
        Notification.requestPermission().then((permission) => {
            console.log(`Portal Echo: Notification permission ${permission}`);
        });
    }
}

/**
 * Announce notification to screen readers
 *
 * @param {string} title - Notification title
 * @param {string} message - Notification message
 */
function announceNotification(title, message) {
    const liveRegion = document.getElementById("aria-live-notifications");

    if (liveRegion) {
        liveRegion.textContent = `New notification: ${title}. ${message}`;

        // Clear after 5 seconds
        setTimeout(() => {
            liveRegion.textContent = "";
        }, 5000);
    }
}

/**
 * Announce status update to screen readers
 *
 * @param {string} modelType - Model type from broadcast (HelpdeskTicket, LoanApplication, etc.)
 * @param {string|number} modelId - Model ID
 * @param {string} status - New status
 */
function announceStatusUpdate(modelType, modelId, status) {
    const liveRegion = document.getElementById("aria-live-notifications");

    if (liveRegion) {
        // Convert model type to friendly label (supports all models from UnifiedNotificationDispatcher)
        let typeLabel = 'Submission';

        if (modelType === 'HelpdeskTicket' || modelType === 'Ticket') {
            typeLabel = 'Helpdesk ticket';
        } else if (modelType === 'LoanApplication' || modelType === 'Loan') {
            typeLabel = 'Loan application';
        } else if (modelType === 'Asset') {
            typeLabel = 'Asset';
        } else if (modelType === 'Submission') {
            typeLabel = 'Submission';
        }

        // Format status for screen readers (replace underscores/dashes with spaces)
        const readableStatus = status.replace(/[_-]/g, ' ').toLowerCase();

        liveRegion.textContent = `${typeLabel} #${modelId} status updated to ${readableStatus}`;

        // Clear after 5 seconds
        setTimeout(() => {
            liveRegion.textContent = "";
        }, 5000);
    }
}

/**
 * Announce new comment to screen readers
 *
 * @param {string} userName - Name of user who posted comment
 */
function announceNewComment(userName) {
    const liveRegion = document.getElementById("aria-live-notifications");

    if (liveRegion) {
        liveRegion.textContent = `New comment posted by ${userName}`;

        // Clear after 5 seconds
        setTimeout(() => {
            liveRegion.textContent = "";
        }, 5000);
    }
}

/**
 * Initialize Echo listeners when DOM is ready
 */
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializePortalEcho);
} else {
    initializePortalEcho();
}
