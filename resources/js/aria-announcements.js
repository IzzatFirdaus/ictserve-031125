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

// Constants for timing to improve readability and maintainability
const ANNOUNCEMENT_DELAY = 100; // Brief delay for screen reader detection
const ANNOUNCEMENT_CLEAR_DELAY = 5000; // Clear announcement after 5 seconds

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
	}, ANNOUNCEMENT_DELAY);

	// Clear announcement after timeout
	setTimeout(() => {
		region.textContent = "";
	}, ANNOUNCEMENT_CLEAR_DELAY);
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
		}, ANNOUNCEMENT_DELAY);
		setTimeout(() => {
			notificationRegion.textContent = "";
		}, ANNOUNCEMENT_CLEAR_DELAY);
	}
}

/**
 * Announce form validation errors
 *
 * @param {Array} errors - Array of error messages
 */
export function announceFormErrors(errors) {
	// Use Bahasa Melayu for form validation announcements
	const errorCount = errors.length;
	const errorText = errorCount > 1 ? "ralat" : "ralat";
	const message = `Pengesahan borang gagal. ${errorCount} ${errorText} dijumpai: ${errors.join(
		", "
	)}`;
	announceToScreenReader(message, "assertive");
}

/**
 * Announce successful action
 *
 * @param {string} action - Action description
 */
export function announceSuccess(action) {
	announceToScreenReader(`Berjaya: ${action}`, "polite");
}

/**
 * Announce loading state
 *
 * @param {string} content - Content being loaded
 */
export function announceLoading(content) {
	announceToScreenReader(`Memuatkan ${content}...`, "polite");
}

/**
 * Announce content loaded
 *
 * @param {string} content - Content that was loaded
 */
export function announceLoaded(content) {
	announceToScreenReader(`${content} telah dimuatkan`, "polite");
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
			announceToScreenReader(`Ralat: ${event.message}`, "assertive");
		}
	});

	// Loading events
	Livewire.hook("message.sent", (message, component) => {
		const action = message.updateQueue[0]?.method || "kandungan";
		announceLoading(action);
	});

	Livewire.hook("message.processed", (message, component) => {
		const action = message.updateQueue[0]?.method || "kandungan";
		announceLoaded(action);
	});
});

// Export for global use
window.ariaAnnounce = announceToScreenReader;
window.ariaAnnounceNotification = announceNotification;
window.ariaAnnounceFormErrors = announceFormErrors;
window.ariaAnnounceSuccess = announceSuccess;
