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
     * @trace Requirements 6.1, 6.2
     */
    window.Echo.private(`user.${userId}`).listen(
        ".notification.created",
        (event) => {
            console.log("Portal Echo: Notification received", event);

            // Dispatch Livewire event to update NotificationBell component
            if (window.Livewire) {
                window.Livewire.dispatch("echo:notification-created", event);
            }

            // Show browser notification if permission granted
            if (
                "Notification" in window &&
                Notification.permission === "granted"
            ) {
                new Notification(
                    event.notification.data.title || "New Notification",
                    {
                        body: event.notification.data.message || "",
                        icon: "/images/motac-logo.png",
                        tag: event.notification.id,
                    }
                );
            }

            // Update ARIA live region for screen readers
            announceNotification(
                event.notification.data.title,
                event.notification.data.message
            );
        }
    );

    /**
     * Listen for submission status updates
     *
     * Event: status.updated
     * Channel: private-user.{userId}
     *
     * @trace Requirements 6.1, 10.1
     */
    window.Echo.private(`user.${userId}`).listen(".status.updated", (event) => {
        console.log("Portal Echo: Status update received", event);

        // Dispatch Livewire event to update submission components
        if (window.Livewire) {
            window.Livewire.dispatch("echo:status-updated", event);
        }

        // Update ARIA live region
        announceStatusUpdate(
            event.submission_type,
            event.submission_number,
            event.new_status
        );
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
 * @param {string} type - Submission type (ticket or loan)
 * @param {string} number - Submission number
 * @param {string} status - New status
 */
function announceStatusUpdate(type, number, status) {
    const liveRegion = document.getElementById("aria-live-notifications");

    if (liveRegion) {
        const typeLabel =
            type === "ticket" ? "Helpdesk ticket" : "Loan application";
        liveRegion.textContent = `${typeLabel} ${number} status updated to ${status}`;

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
