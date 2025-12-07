/**
 * Submission Echo Listeners
 *
 * This file handles dynamic subscription to submission-specific Echo channels
 * when viewing submission details. It manages subscribing and unsubscribing
 * from comment channels based on the current submission being viewed.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 7.4)
 * @author dev-team@motac.gov.my
 * @last-updated 2025-11-06
 */

/**
 * Subscribe to submission comments channel
 *
 * This function is called when a submission detail page is loaded.
 * It subscribes to the submission-specific channel for real-time comment updates.
 *
 * @param {string} submissionType - Type of submission ('helpdesk' or 'loans')
 * @param {number} submissionId - ID of the submission
 */
export function subscribeToSubmissionComments(submissionType, submissionId) {
    if (!window.Echo) {
        console.warn("Submission Echo: Echo not initialized");
        return;
    }

    if (typeof window.subscribeToSubmissionComments === "function") {
        window.subscribeToSubmissionComments(submissionType, submissionId);
    }
}

/**
 * Unsubscribe from submission comments channel
 *
 * This function is called when leaving a submission detail page.
 * It unsubscribes from the submission-specific channel to prevent memory leaks.
 *
 * @param {string} submissionType - Type of submission ('helpdesk' or 'loans')
 * @param {number} submissionId - ID of the submission
 */
export function unsubscribeFromSubmissionComments(
    submissionType,
    submissionId
) {
    if (!window.Echo) {
        console.warn("Submission Echo: Echo not initialized for unsubscribe");
        return;
    }

    if (typeof window.unsubscribeFromSubmissionComments === "function") {
        window.unsubscribeFromSubmissionComments(submissionType, submissionId);
    }
}

/**
 * Auto-subscribe when submission detail page loads
 */
document.addEventListener("DOMContentLoaded", () => {
    // Check if we're on a submission detail page
    const submissionDetailElement = document.querySelector(
        "[data-submission-type][data-submission-id]"
    );

    if (submissionDetailElement) {
        const submissionType = submissionDetailElement.dataset.submissionType;
        const submissionId = parseInt(
            submissionDetailElement.dataset.submissionId,
            10
        );

        if (submissionType && submissionId) {
            console.log(
                `Submission Echo: Auto-subscribing to ${submissionType} ${submissionId}`
            );
            subscribeToSubmissionComments(submissionType, submissionId);

            // Unsubscribe when leaving the page
            window.addEventListener("beforeunload", () => {
                unsubscribeFromSubmissionComments(submissionType, submissionId);
            });
        }
    }
});

/**
 * Handle Livewire navigation (for SPA-like behavior)
 */
if (window.Livewire) {
    document.addEventListener("livewire:navigated", () => {
        // Re-check for submission detail page after Livewire navigation
        const submissionDetailElement = document.querySelector(
            "[data-submission-type][data-submission-id]"
        );

        if (submissionDetailElement) {
            const submissionType =
                submissionDetailElement.dataset.submissionType;
            const submissionId = parseInt(
                submissionDetailElement.dataset.submissionId,
                10
            );

            if (submissionType && submissionId) {
                subscribeToSubmissionComments(submissionType, submissionId);
            }
        }
    });
}

/**
 * Guest submission tracking via UUID-based channels (v3.5.0)
 *
 * This function allows guests to track their submission status in real-time
 * using the submission UUID and status token.
 *
 * @param {string} submissionType - 'ticket' or 'loan'
 * @param {string} uuid - Submission UUID
 * @param {string} statusToken - Status tracking token
 */
export function subscribeToGuestSubmission(submissionType, uuid, statusToken) {
    if (!window.Echo) {
        console.warn("Submission Echo: Echo not initialized for guest subscription");
        return;
    }

    const channelName = `${submissionType}.${uuid}`;
    console.log(`Submission Echo: Guest subscribing to ${channelName}`);

    // Subscribe with status token in query string
    const channel = window.Echo.private(channelName + `?status_token=${statusToken}`);

    // Listen for status updates
    channel.listen(".status.updated", (event) => {
        console.log("Submission Echo: Guest status update received", event);

        // Dispatch to Livewire components
        if (window.Livewire) {
            window.Livewire.dispatch("echo:guest-status-updated", {
                ...event,
                uuid: uuid,
                submission_type: submissionType,
            });
        }

        // Update page without reload if submission detail visible
        const statusElement = document.querySelector("[data-submission-status]");
        if (statusElement && event.new_status) {
            statusElement.textContent = event.new_status;
            statusElement.className = `badge badge-${getStatusClass(event.new_status)}`;
        }

        // Show user-facing notification
        const message = `Your ${submissionType === 'ticket' ? 'ticket' : 'loan application'} status updated to: ${event.new_status}`;
        showGuestNotification("Status Updated", message);
    });

    // Listen for comments (if guest has access)
    channel.listen(".comment.posted", (event) => {
        console.log("Submission Echo: Guest comment received", event);

        if (window.Livewire) {
            window.Livewire.dispatch("echo:guest-comment-posted", event);
        }

        showGuestNotification("New Comment", "A staff member has added a comment to your submission");
    });

    // Handle connection errors gracefully for guests
    channel.error((error) => {
        console.error(`Submission Echo: Error subscribing to ${channelName}`, error);

        // Show user-friendly message
        if (error.type === "AuthError") {
            showGuestNotification(
                "Tracking Unavailable",
                "Unable to connect to real-time updates. Please refresh to see latest status.",
                true
            );
        }
    });

    // Store channel reference for cleanup
    window.guestSubmissionChannel = channel;
}

/**
 * Unsubscribe from guest submission channel
 */
export function unsubscribeFromGuestSubmission(submissionType, uuid) {
    if (!window.Echo || !window.guestSubmissionChannel) {
        return;
    }

    const channelName = `${submissionType}.${uuid}`;
    console.log(`Submission Echo: Guest unsubscribing from ${channelName}`);

    window.Echo.leave(channelName);
    window.guestSubmissionChannel = null;
}

/**
 * Helper: Get status badge color class
 */
function getStatusClass(status) {
    const statusLower = status.toLowerCase();

    if (statusLower.includes("approved") || statusLower.includes("completed")) {
        return "success";
    } else if (statusLower.includes("rejected") || statusLower.includes("cancelled")) {
        return "danger";
    } else if (statusLower.includes("pending")) {
        return "warning";
    } else if (statusLower.includes("in_progress") || statusLower.includes("processing")) {
        return "info";
    }

    return "secondary";
}

/**
 * Show guest-friendly notification
 */
function showGuestNotification(title, message, isPersistent = false) {
    // Try browser notification first
    if ("Notification" in window && Notification.permission === "granted") {
        new Notification(title, {
            body: message,
            icon: "/images/motac-logo-32.png",
            badge: "/images/motac-logo-32.png",
        });
    }

    // Fallback to in-page toast
    const toast = document.createElement("div");
    toast.className = "fixed top-4 right-4 z-50 max-w-sm bg-white border border-gray-200 rounded-lg shadow-lg p-4";
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-live", "polite");

    toast.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-gray-900">${title}</p>
                <p class="mt-1 text-sm text-gray-500">${message}</p>
            </div>
            ${!isPersistent ? `
                <button type="button" class="ml-3 flex-shrink-0 text-gray-400 hover:text-gray-500" onclick="this.parentElement.parentElement.remove()">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            ` : ''}
        </div>
    `;

    document.body.appendChild(toast);

    // Auto-remove after 5 seconds if not persistent
    if (!isPersistent) {
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
}

/**
 * Auto-initialize guest tracking if on guest tracking page
 */
document.addEventListener("DOMContentLoaded", () => {
    const guestTrackingElement = document.querySelector(
        "[data-guest-submission-uuid][data-guest-submission-type][data-status-token]"
    );

    if (guestTrackingElement) {
        const uuid = guestTrackingElement.dataset.guestSubmissionUuid;
        const submissionType = guestTrackingElement.dataset.guestSubmissionType;
        const statusToken = guestTrackingElement.dataset.statusToken;

        if (uuid && submissionType && statusToken) {
            console.log(`Submission Echo: Auto-subscribing guest to ${submissionType} ${uuid}`);
            subscribeToGuestSubmission(submissionType, uuid, statusToken);

            // Cleanup on page unload
            window.addEventListener("beforeunload", () => {
                unsubscribeFromGuestSubmission(submissionType, uuid);
            });
        }
    }
});
