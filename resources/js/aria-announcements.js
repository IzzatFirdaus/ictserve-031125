/**
 * ARIA Live Region Announcements
 *
 * Provides screen reader announcements for dynamic content updates
 * in the authenticated portal.
 *
 * @trace D12 §4.3, D14 §9 (WCAG 2.2 SC 4.1.3)
 * @author dev-team@motac.gov.my
 * @created 2025-11-06
 */

/**
 * Announce message to screen readers via ARIA live region
 *
 * @param {string} message - Message to announce
 * @param {string} priority - 'polite' (default) or 'assertive'
 */
export function announceToScreenReader(message, priority = "polite") {
    const regionId =
        priority === "assertive"
            ? "aria-error-announcements"
            : "aria-announcements";

    const region = document.getElementById(regionId);

    if (!region) {
        console.warn(`ARIA live region #${regionId} not found`);
        return;
    }

    // Clear previous announcement
    region.textContent = "";

    // Add new announcement after brief delay (allows screen reader to detect change)
    setTimeout(() => {
        region.textContent = message;
    }, 100);

    // Clear announcement after 5 seconds
    setTimeout(() => {
        region.textContent = "";
    }, 5000);
}

/**
 * Announce notification to screen readers
 *
 * @param {Object} notification - Notification object with title and message
 */
export function announceNotification(notification) {
    const message = `${notification.title}. ${notification.message || ""}`;
    announceToScreenReader(message, "polite");

    // Also update notification-specific live region
    const notificationRegion = document.getElementById(
        "aria-notification-announcements"
    );
    if (notificationRegion) {
        notificationRegion.textContent = "";
        setTimeout(() => {
            notificationRegion.textContent = message;
        }, 100);
        setTimeout(() => {
            notificationRegion.textContent = "";
        }, 5000);
    }
}

/**
 * Announce form validation errors
 *
 * @param {Array} errors - Array of error messages
 */
export function announceFormErrors(errors) {
    const message = `Form validation failed. ${errors.length} error${
        errors.length > 1 ? "s" : ""
    } found: ${errors.join(", ")}`;
    announceToScreenReader(message, "assertive");
}

/**
 * Announce successful action
 *
 * @param {string} action - Action description
 */
export function announceSuccess(action) {
    announceToScreenReader(`Success: ${action}`, "polite");
}

/**
 * Announce loading state
 *
 * @param {string} content - Content being loaded
 */
export function announceLoading(content) {
    announceToScreenReader(`Loading ${content}...`, "polite");
}

/**
 * Announce content loaded
 *
 * @param {string} content - Content that was loaded
 */
export function announceLoaded(content) {
    announceToScreenReader(`${content} loaded`, "polite");
}

// Listen for Livewire events and announce them
document.addEventListener("livewire:init", () => {
    // Notification events
    Livewire.on("notification-received", (event) => {
        if (event && event.notification) {
            announceNotification(event.notification);
        }
    });

    // Success events
    Livewire.on("success", (event) => {
        if (event && event.message) {
            announceSuccess(event.message);
        }
    });

    // Error events
    Livewire.on("error", (event) => {
        if (event && event.message) {
            announceToScreenReader(`Error: ${event.message}`, "assertive");
        }
    });

    // Loading events
    Livewire.hook("message.sent", (message, component) => {
        const action = message.updateQueue[0]?.method || "content";
        announceLoading(action);
    });

    Livewire.hook("message.processed", (message, component) => {
        const action = message.updateQueue[0]?.method || "content";
        announceLoaded(action);
    });
});

// Export for global use
window.ariaAnnounce = announceToScreenReader;
window.ariaAnnounceNotification = announceNotification;
window.ariaAnnounceFormErrors = announceFormErrors;
window.ariaAnnounceSuccess = announceSuccess;
