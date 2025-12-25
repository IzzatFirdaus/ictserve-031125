/**
 * ARIA Announcements for ICTServe
 *
 * Provides screen reader announcements for dynamic content changes.
 * Ensures WCAG 2.2 AA compliance for assistive technologies.
 *
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §10.4 (ARIA Live Regions)
 * @wcag SC 4.1.3 (Status Messages)
 * @version 3.6.1
 */

class AriaAnnouncer {
	constructor() {
		this.liveRegion = null;
		this.init();
	}

	/**
	 * Initialize ARIA live region
	 */
	init() {
		// Create ARIA live region if it doesn't exist
		this.liveRegion = document.getElementById("aria-live-notifications");

		if (!this.liveRegion) {
			this.liveRegion = document.createElement("div");
			this.liveRegion.id = "aria-live-notifications";
			this.liveRegion.setAttribute("aria-live", "polite");
			this.liveRegion.setAttribute("aria-atomic", "true");
			this.liveRegion.className = "sr-only";
			document.body.appendChild(this.liveRegion);
		}

		// Create assertive live region for urgent announcements
		this.assertiveLiveRegion = document.getElementById("aria-live-assertive");

		if (!this.assertiveLiveRegion) {
			this.assertiveLiveRegion = document.createElement("div");
			this.assertiveLiveRegion.id = "aria-live-assertive";
			this.assertiveLiveRegion.setAttribute("aria-live", "assertive");
			this.assertiveLiveRegion.setAttribute("aria-atomic", "true");
			this.assertiveLiveRegion.className = "sr-only";
			document.body.appendChild(this.assertiveLiveRegion);
		}
	}

	/**
	 * Announce message to screen readers (polite)
	 * @param {string} message - Message to announce
	 * @param {number} delay - Delay before clearing (default: 5000ms)
	 */
	announce(message, delay = 5000) {
		if (!this.liveRegion || !message) return;

		this.liveRegion.textContent = message;

		// Clear after delay
		if (delay > 0) {
			setTimeout(() => {
				if (this.liveRegion) {
					this.liveRegion.textContent = "";
				}
			}, delay);
		}
	}

	/**
	 * Announce urgent message to screen readers (assertive)
	 * @param {string} message - Urgent message to announce
	 * @param {number} delay - Delay before clearing (default: 8000ms)
	 */
	announceUrgent(message, delay = 8000) {
		if (!this.assertiveLiveRegion || !message) return;

		this.assertiveLiveRegion.textContent = message;

		// Clear after delay
		if (delay > 0) {
			setTimeout(() => {
				if (this.assertiveLiveRegion) {
					this.assertiveLiveRegion.textContent = "";
				}
			}, delay);
		}
	}

	/**
	 * Clear all announcements
	 */
	clear() {
		if (this.liveRegion) {
			this.liveRegion.textContent = "";
		}
		if (this.assertiveLiveRegion) {
			this.assertiveLiveRegion.textContent = "";
		}
	}
}

// Initialize and expose globally
window.AriaAnnouncer = new AriaAnnouncer();

// Export for module usage
export default window.AriaAnnouncer;
