/**
 * Widget Real-Time Integration - ICTServe v3.6.1
 *
 * Provides WebSocket integration for dashboard widgets with fallback polling,
 * connection management, and accessibility features. Integrates with Laravel
 * Reverb and the WidgetRealtimeManager service.
 *
 * @see app/Services/WidgetRealtimeManager.php - Backend service
 * @see app/Events/WidgetDataUpdated.php - Broadcast event
 * @trace D03 SRS-FR-008, D04 §5.3 - Real-time dashboard requirements
 * @requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 *
 * @version 3.6.1
 * @since 3.6.0
 */

/**
 * Widget Real-Time Manager
 * Handles WebSocket subscriptions and fallback polling for dashboard widgets
 */
class WidgetRealtimeManager {
	constructor() {
		this.subscriptions = new Map();
		this.pollingIntervals = new Map();
		this.isPollingActive = false;
		this.pollingInterval = 30000; // 30 seconds as per requirements
		this.maxRetryAttempts = 3;
		this.retryAttempts = new Map();

		// Connection state tracking
		this.isConnected = false;
		this.reconnectAttempts = 0;
		this.maxReconnectAttempts = 10;

		// Accessibility features
		this.announceUpdates = true;
		this.updateCounter = 0;

		this.init();
	}

	/**
	 * Initialize the widget real-time manager
	 */
	init() {
		if (!window.Echo) {
			console.warn(
				"Widget Real-Time: Echo not available, using polling fallback"
			);
			this.startFallbackPolling();
			return;
		}

		// Listen for Echo connection events
		this.setupConnectionListeners();

		// Initialize based on current connection state
		if (window.echoConnectionState?.connected) {
			this.isConnected = true;
			console.log("Widget Real-Time: Echo connected, WebSocket mode active");
		} else {
			console.log(
				"Widget Real-Time: Echo not connected, starting fallback polling"
			);
			this.startFallbackPolling();
		}
	}

	/**
	 * Setup Echo connection event listeners
	 */
	setupConnectionListeners() {
		// Connection established
		window.addEventListener("echo:connected", () => {
			console.log("Widget Real-Time: WebSocket connected");
			this.isConnected = true;
			this.reconnectAttempts = 0;
			this.stopFallbackPolling();
			this.resubscribeAllWidgets();
			this.announceToScreenReader("Sambungan masa nyata dipulihkan", "polite");
		});

		// Connection lost
		window.addEventListener("echo:disconnected", () => {
			console.warn("Widget Real-Time: WebSocket disconnected");
			this.isConnected = false;
			this.startFallbackPolling();
			this.announceToScreenReader(
				"Sambungan masa nyata terputus, menggunakan mod sandaran",
				"assertive"
			);
		});

		// Connection unavailable
		window.addEventListener("echo:unavailable", () => {
			console.error("Widget Real-Time: WebSocket unavailable");
			this.isConnected = false;
			this.startFallbackPolling();
		});
	}

	/**
	 * Subscribe to widget updates
	 * @param {string} widgetId - Widget identifier
	 * @param {Function} callback - Update callback function
	 * @param {Object} options - Subscription options
	 */
	subscribeToWidget(widgetId, callback, options = {}) {
		if (!widgetId || typeof callback !== "function") {
			console.error("Widget Real-Time: Invalid subscription parameters");
			return false;
		}

		try {
			// Store subscription info
			this.subscriptions.set(widgetId, {
				callback,
				options,
				subscribedAt: Date.now(),
				updateCount: 0,
				lastUpdate: null,
			});

			// Subscribe via WebSocket if connected
			if (this.isConnected && window.Echo) {
				this.subscribeViaWebSocket(widgetId, callback);
			}

			// Add to polling if fallback is active
			if (this.isPollingActive) {
				this.addToPolling(widgetId);
			}

			console.log(`Widget Real-Time: Subscribed to widget ${widgetId}`);
			return true;
		} catch (error) {
			console.error("Widget Real-Time: Subscription failed", error);
			return false;
		}
	}

	/**
	 * Subscribe to widget via WebSocket
	 * @param {string} widgetId - Widget identifier
	 * @param {Function} callback - Update callback function
	 */
	subscribeViaWebSocket(widgetId, callback) {
		if (!window.Echo) return;

		try {
			// Subscribe to widget-specific channel
			window.Echo.private(`dashboard.widgets.${widgetId}`).listen(
				".WidgetDataUpdated",
				(data) => {
					this.handleWidgetUpdate(widgetId, data, callback);
				}
			);

			// Subscribe to user-specific channel if user is authenticated
			const userId = this.getCurrentUserId();
			if (userId) {
				window.Echo.private(`dashboard.widgets.${userId}`).listen(
					".WidgetDataUpdated",
					(data) => {
						if (data.widget_id === widgetId) {
							this.handleWidgetUpdate(widgetId, data, callback);
						}
					}
				);
			}

			// Subscribe to global channel if user has admin access
			if (this.hasAdminAccess()) {
				window.Echo.private("dashboard.widgets.global").listen(
					".WidgetDataUpdated",
					(data) => {
						if (data.widget_id === widgetId) {
							this.handleWidgetUpdate(widgetId, data, callback);
						}
					}
				);
			}
		} catch (error) {
			console.error(
				`Widget Real-Time: WebSocket subscription failed for ${widgetId}`,
				error
			);
		}
	}

	/**
	 * Handle widget update from WebSocket or polling
	 * @param {string} widgetId - Widget identifier
	 * @param {Object} data - Update data
	 * @param {Function} callback - Update callback function
	 */
	handleWidgetUpdate(widgetId, data, callback) {
		try {
			const subscription = this.subscriptions.get(widgetId);
			if (!subscription) return;

			// Update subscription stats
			subscription.updateCount++;
			subscription.lastUpdate = Date.now();

			// Call the callback with the update data
			callback(data);

			// Accessibility: Announce significant updates
			if (this.shouldAnnounceUpdate(data)) {
				this.announceWidgetUpdate(widgetId, data);
			}

			// Reset retry attempts on successful update
			this.retryAttempts.set(widgetId, 0);

			console.log(`Widget Real-Time: Update received for ${widgetId}`, data);
		} catch (error) {
			console.error(
				`Widget Real-Time: Update handling failed for ${widgetId}`,
				error
			);
		}
	}

	/**
	 * Unsubscribe from widget updates
	 * @param {string} widgetId - Widget identifier
	 */
	unsubscribeFromWidget(widgetId) {
		try {
			// Remove from subscriptions
			this.subscriptions.delete(widgetId);

			// Leave WebSocket channels if connected
			if (this.isConnected && window.Echo) {
				window.Echo.leave(`dashboard.widgets.${widgetId}`);
			}

			// Remove from polling
			this.removeFromPolling(widgetId);

			console.log(`Widget Real-Time: Unsubscribed from widget ${widgetId}`);
			return true;
		} catch (error) {
			console.error(
				`Widget Real-Time: Unsubscription failed for ${widgetId}`,
				error
			);
			return false;
		}
	}

	/**
	 * Start fallback polling for all subscribed widgets
	 */
	startFallbackPolling() {
		if (this.isPollingActive) return;

		console.log("Widget Real-Time: Starting fallback polling");
		this.isPollingActive = true;

		// Poll every 30 seconds
		this.pollingTimer = setInterval(() => {
			this.pollWidgets();
		}, this.pollingInterval);

		// Immediate poll
		this.pollWidgets();
	}

	/**
	 * Stop fallback polling
	 */
	stopFallbackPolling() {
		if (!this.isPollingActive) return;

		console.log("Widget Real-Time: Stopping fallback polling");
		this.isPollingActive = false;

		if (this.pollingTimer) {
			clearInterval(this.pollingTimer);
			this.pollingTimer = null;
		}
	}

	/**
	 * Poll widgets for updates
	 */
	async pollWidgets() {
		if (this.subscriptions.size === 0) return;

		const widgetIds = Array.from(this.subscriptions.keys());

		try {
			const response = await fetch("/api/widgets/polling-data", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-CSRF-TOKEN": document
						.querySelector('meta[name="csrf-token"]')
						?.getAttribute("content"),
					"X-Requested-With": "XMLHttpRequest",
				},
				body: JSON.stringify({ widget_ids: widgetIds }),
			});

			if (!response.ok) {
				throw new Error(`Polling request failed: ${response.status}`);
			}

			const pollingData = await response.json();

			// Process polling data for each widget
			for (const [widgetId, data] of Object.entries(pollingData)) {
				const subscription = this.subscriptions.get(widgetId);
				if (subscription && data.data) {
					this.handleWidgetUpdate(
						widgetId,
						{
							widget_id: widgetId,
							data: data.data,
							timestamp: data.timestamp,
							source: "polling",
						},
						subscription.callback
					);
				}
			}
		} catch (error) {
			console.error("Widget Real-Time: Polling failed", error);

			// Implement exponential backoff for failed polling
			this.handlePollingError();
		}
	}

	/**
	 * Handle polling errors with exponential backoff
	 */
	handlePollingError() {
		this.reconnectAttempts++;

		if (this.reconnectAttempts >= this.maxReconnectAttempts) {
			console.error("Widget Real-Time: Max polling retry attempts reached");
			this.announceToScreenReader(
				"Kemaskini widget gagal, sila muat semula halaman",
				"assertive"
			);
			return;
		}

		// Exponential backoff: 30s, 60s, 120s, etc.
		const backoffDelay =
			this.pollingInterval * Math.pow(2, this.reconnectAttempts - 1);
		const maxDelay = 300000; // 5 minutes max
		const delay = Math.min(backoffDelay, maxDelay);

		console.log(
			`Widget Real-Time: Retrying polling in ${delay / 1000}s (attempt ${
				this.reconnectAttempts
			})`
		);

		setTimeout(() => {
			this.pollWidgets();
		}, delay);
	}

	/**
	 * Resubscribe all widgets when connection is restored
	 */
	resubscribeAllWidgets() {
		console.log("Widget Real-Time: Resubscribing all widgets");

		for (const [widgetId, subscription] of this.subscriptions) {
			this.subscribeViaWebSocket(widgetId, subscription.callback);
		}
	}

	/**
	 * Add widget to polling list
	 * @param {string} widgetId - Widget identifier
	 */
	addToPolling(widgetId) {
		// Polling is handled collectively, no individual tracking needed
		console.log(`Widget Real-Time: Added ${widgetId} to polling`);
	}

	/**
	 * Remove widget from polling list
	 * @param {string} widgetId - Widget identifier
	 */
	removeFromPolling(widgetId) {
		// Polling is handled collectively, no individual tracking needed
		console.log(`Widget Real-Time: Removed ${widgetId} from polling`);
	}

	/**
	 * Check if update should be announced to screen readers
	 * @param {Object} data - Update data
	 * @returns {boolean} Whether to announce
	 */
	shouldAnnounceUpdate(data) {
		// Only announce significant updates to avoid spam
		return (
			data.widget_type === "alert" ||
			data.widget_type === "critical_metric" ||
			(data.data && data.data.alert_level === "high")
		);
	}

	/**
	 * Announce widget update to screen readers
	 * @param {string} widgetId - Widget identifier
	 * @param {Object} data - Update data
	 */
	announceWidgetUpdate(widgetId, data) {
		if (!this.announceUpdates) return;

		let message = "Widget dikemaskini";

		if (data.data && data.data.title) {
			message = `${data.data.title} dikemaskini`;
		} else if (data.widget_type) {
			message = `Widget ${data.widget_type} dikemaskini`;
		}

		this.announceToScreenReader(message, "polite");
	}

	/**
	 * Announce message to screen readers
	 * @param {string} message - Message to announce
	 * @param {string} priority - 'polite' or 'assertive'
	 */
	announceToScreenReader(message, priority = "polite") {
		if (!this.announceUpdates) return;

		const announcement = document.createElement("div");
		announcement.setAttribute("aria-live", priority);
		announcement.setAttribute("aria-atomic", "true");
		announcement.className = "sr-only";
		announcement.textContent = message;

		document.body.appendChild(announcement);

		// Remove after announcement
		setTimeout(() => {
			document.body.removeChild(announcement);
		}, 1000);
	}

	/**
	 * Get current user ID from meta tag or global variable
	 * @returns {string|null} User ID
	 */
	getCurrentUserId() {
		// Try to get from meta tag
		const userIdMeta = document.querySelector('meta[name="user-id"]');
		if (userIdMeta) {
			return userIdMeta.getAttribute("content");
		}

		// Try to get from global variable
		if (window.Laravel && window.Laravel.user && window.Laravel.user.id) {
			return window.Laravel.user.id.toString();
		}

		return null;
	}

	/**
	 * Check if current user has admin access
	 * @returns {boolean} Whether user has admin access
	 */
	hasAdminAccess() {
		// Try to get from meta tag
		const hasAdminMeta = document.querySelector('meta[name="user-has-admin"]');
		if (hasAdminMeta) {
			return hasAdminMeta.getAttribute("content") === "true";
		}

		// Try to get from global variable
		if (window.Laravel && window.Laravel.user && window.Laravel.user.roles) {
			const roles = window.Laravel.user.roles;
			return roles.includes("admin") || roles.includes("superuser");
		}

		return false;
	}

	/**
	 * Get subscription statistics
	 * @returns {Object} Statistics object
	 */
	getStats() {
		const stats = {
			total_subscriptions: this.subscriptions.size,
			is_connected: this.isConnected,
			is_polling_active: this.isPollingActive,
			reconnect_attempts: this.reconnectAttempts,
			widgets: {},
		};

		for (const [widgetId, subscription] of this.subscriptions) {
			stats.widgets[widgetId] = {
				update_count: subscription.updateCount,
				last_update: subscription.lastUpdate,
				subscribed_at: subscription.subscribedAt,
			};
		}

		return stats;
	}

	/**
	 * Enable or disable screen reader announcements
	 * @param {boolean} enabled - Whether to enable announcements
	 */
	setAnnounceUpdates(enabled) {
		this.announceUpdates = enabled;
		console.log(
			`Widget Real-Time: Screen reader announcements ${
				enabled ? "enabled" : "disabled"
			}`
		);
	}
}

// Initialize global widget real-time manager
window.widgetRealtimeManager = new WidgetRealtimeManager();

// Export for use in other modules
export default WidgetRealtimeManager;

/**
 * Convenience functions for easy integration
 */

/**
 * Subscribe to widget updates
 * @param {string} widgetId - Widget identifier
 * @param {Function} callback - Update callback function
 * @param {Object} options - Subscription options
 */
window.subscribeToWidget = function (widgetId, callback, options = {}) {
	return window.widgetRealtimeManager.subscribeToWidget(
		widgetId,
		callback,
		options
	);
};

/**
 * Unsubscribe from widget updates
 * @param {string} widgetId - Widget identifier
 */
window.unsubscribeFromWidget = function (widgetId) {
	return window.widgetRealtimeManager.unsubscribeFromWidget(widgetId);
};

/**
 * Get widget real-time statistics
 */
window.getWidgetRealtimeStats = function () {
	return window.widgetRealtimeManager.getStats();
};

/**
 * Alpine.js directive for automatic widget subscription
 * Usage: <div x-widget-realtime="widgetId" x-on:widget-update="handleUpdate">
 */
if (window.Alpine) {
	window.Alpine.directive(
		"widget-realtime",
		(el, { expression }, { evaluate, cleanup }) => {
			const widgetId = evaluate(expression);

			if (!widgetId) {
				console.error("Widget Real-Time: No widget ID provided for directive");
				return;
			}

			// Subscribe to widget updates
			const success = window.subscribeToWidget(widgetId, (data) => {
				// Dispatch custom event for Alpine.js
				el.dispatchEvent(
					new CustomEvent("widget-update", {
						detail: data,
						bubbles: true,
					})
				);
			});

			if (!success) {
				console.error(
					`Widget Real-Time: Failed to subscribe to widget ${widgetId}`
				);
			}

			// Cleanup on element removal
			cleanup(() => {
				window.unsubscribeFromWidget(widgetId);
			});
		}
	);
}

console.log("Widget Real-Time: Module loaded successfully");
