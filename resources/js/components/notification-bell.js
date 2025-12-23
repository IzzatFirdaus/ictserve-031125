/**
 * Notification Bell Alpine.js Component
 *
 * Real-time notification bell component that subscribes to user channels
 * and updates the notification counter when new notifications are received.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 1.4)
 */

/**
 * Alpine.js Notification Bell Component
 *
 * Usage:
 * <div x-data="notificationBell(userId)" x-init="init()">
 *   <button type="button" class="relative" @click="toggleDropdown()">
 *     <svg class="h-6 w-6">...</svg>
 *     <span x-show="unreadCount > 0"
 *           x-text="displayCount"
 *           data-notification-counter
 *           class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
 *     </span>
 *   </button>
 * </div>
 */
export function notificationBell(userId = null) {
	return {
		// Component state
		userId: userId,
		unreadCount: 0,
		notifications: [],
		isDropdownOpen: false,
		isLoading: false,
		error: null,

		// Computed properties
		get displayCount() {
			return this.unreadCount > 99 ? "99+" : this.unreadCount.toString();
		},

		get hasNotifications() {
			return this.notifications.length > 0;
		},

		// Component initialization
		init() {
			console.log("Initializing notification bell for user:", this.userId);

			// Load initial notification count and recent notifications
			this.loadNotifications();

			// Set up Echo listeners if user is authenticated
			if (this.userId && window.Echo) {
				this.setupEchoListeners();
			}

			// Set up custom event listeners
			this.setupCustomEventListeners();

			// Set up click outside handler for dropdown
			this.setupClickOutsideHandler();
		},

		// Load notifications from server
		async loadNotifications() {
			if (!this.userId) {
				return;
			}

			this.isLoading = true;
			this.error = null;

			try {
				// Load unread count
				const countResponse = await window.axios.get(
					"/api/notifications/unread-count"
				);
				this.unreadCount = countResponse.data.count || 0;

				// Load recent notifications for dropdown
				const notificationsResponse = await window.axios.get(
					"/api/notifications/recent",
					{
						params: { limit: 10 },
					}
				);
				this.notifications = notificationsResponse.data.data || [];

				console.log(`Loaded ${this.unreadCount} unread notifications`);
			} catch (error) {
				console.error("Failed to load notifications:", error);
				this.error = "Gagal memuatkan notifikasi";
			} finally {
				this.isLoading = false;
			}
		},

		// Set up Echo WebSocket listeners
		setupEchoListeners() {
			if (!window.Echo || !this.userId) {
				console.warn("Echo not available or user not authenticated");
				return;
			}

			console.log(`Setting up Echo listeners for user ${this.userId}`);

			// Listen to user's private channel
			window.Echo.private(`user.${this.userId}`)
				.listen(".notification.created", (event) => {
					console.log("Notification created event received:", event);
					this.handleNotificationCreated(event);
				})
				.listen(".notification.read", (event) => {
					console.log("Notification read event received:", event);
					this.handleNotificationRead(event);
				});
		},

		// Set up custom event listeners
		setupCustomEventListeners() {
			// Listen for notification events from echo-handlers.js
			window.addEventListener("notification:created", (event) => {
				this.handleNotificationCreated(event.detail);
			});

			// Listen for Echo connection events
			window.addEventListener("echo:connected", () => {
				console.log("Echo connected, reloading notifications");
				this.loadNotifications();
			});

			window.addEventListener("echo:disconnected", () => {
				console.log("Echo disconnected");
			});
		},

		// Set up click outside handler for dropdown
		setupClickOutsideHandler() {
			document.addEventListener("click", (event) => {
				if (!this.$el.contains(event.target)) {
					this.isDropdownOpen = false;
				}
			});
		},

		// Handle new notification created
		handleNotificationCreated(event) {
			console.log("Handling notification created:", event);

			// Increment unread count
			this.unreadCount++;

			// Add to notifications list (prepend to show newest first)
			const notification = {
				id: event.notification_id,
				type: event.type,
				message: event.message,
				created_at: event.created_at,
				read_at: null,
			};

			this.notifications.unshift(notification);

			// Keep only latest 10 notifications in dropdown
			if (this.notifications.length > 10) {
				this.notifications = this.notifications.slice(0, 10);
			}

			// Update document title with notification count
			this.updateDocumentTitle();

			// Show browser notification if permission granted
			this.showBrowserNotification(notification);
		},

		// Handle notification read
		handleNotificationRead(event) {
			console.log("Handling notification read:", event);

			// Decrement unread count
			if (this.unreadCount > 0) {
				this.unreadCount--;
			}

			// Update notification in list
			const notification = this.notifications.find(
				(n) => n.id === event.notification_id
			);
			if (notification) {
				notification.read_at = event.read_at;
			}

			// Update document title
			this.updateDocumentTitle();
		},

		// Toggle notification dropdown
		toggleDropdown() {
			this.isDropdownOpen = !this.isDropdownOpen;

			// Load fresh notifications when opening dropdown
			if (this.isDropdownOpen) {
				this.loadNotifications();
			}
		},

		// Mark notification as read
		async markAsRead(notificationId) {
			try {
				await window.axios.post(`/api/notifications/${notificationId}/read`);

				// Update local state
				const notification = this.notifications.find(
					(n) => n.id === notificationId
				);
				if (notification && !notification.read_at) {
					notification.read_at = new Date().toISOString();
					if (this.unreadCount > 0) {
						this.unreadCount--;
					}
					this.updateDocumentTitle();
				}
			} catch (error) {
				console.error("Failed to mark notification as read:", error);
			}
		},

		// Mark all notifications as read
		async markAllAsRead() {
			if (this.unreadCount === 0) {
				return;
			}

			try {
				await window.axios.post("/api/notifications/mark-all-read");

				// Update local state
				this.unreadCount = 0;
				this.notifications.forEach((notification) => {
					if (!notification.read_at) {
						notification.read_at = new Date().toISOString();
					}
				});

				this.updateDocumentTitle();
			} catch (error) {
				console.error("Failed to mark all notifications as read:", error);
			}
		},

		// Update document title with notification count
		updateDocumentTitle() {
			const baseTitle = document.title.replace(/^\(\d+\)\s*/, "");

			if (this.unreadCount > 0) {
				document.title = `(${this.unreadCount}) ${baseTitle}`;
			} else {
				document.title = baseTitle;
			}
		},

		// Show browser notification
		showBrowserNotification(notification) {
			// Check if browser notifications are supported and permitted
			if ("Notification" in window && Notification.permission === "granted") {
				new Notification("ICTServe - Notifikasi Baharu", {
					body: notification.message,
					icon: "/favicon.ico",
					tag: `notification-${notification.id}`,
					requireInteraction: false,
				});
			}
		},

		// Request browser notification permission
		async requestNotificationPermission() {
			if ("Notification" in window && Notification.permission === "default") {
				const permission = await Notification.requestPermission();
				console.log("Notification permission:", permission);
				return permission === "granted";
			}
			return Notification.permission === "granted";
		},

		// Format notification time
		formatTime(timestamp) {
			const date = new Date(timestamp);
			const now = new Date();
			const diffInMinutes = Math.floor((now - date) / (1000 * 60));

			if (diffInMinutes < 1) {
				return "Baru sahaja";
			} else if (diffInMinutes < 60) {
				return `${diffInMinutes} minit yang lalu`;
			} else if (diffInMinutes < 1440) {
				const hours = Math.floor(diffInMinutes / 60);
				return `${hours} jam yang lalu`;
			} else {
				const days = Math.floor(diffInMinutes / 1440);
				return `${days} hari yang lalu`;
			}
		},

		// Get notification icon based on type
		getNotificationIcon(type) {
			const icons = {
				ticket_created: "🎫",
				ticket_updated: "📝",
				ticket_resolved: "✅",
				loan_approved: "✅",
				loan_rejected: "❌",
				comment_posted: "💬",
				system_alert: "⚠️",
				default: "📢",
			};

			return icons[type] || icons.default;
		},

		// Get notification color class based on type
		getNotificationColorClass(type) {
			const colorClasses = {
				ticket_created: "bg-blue-50 border-blue-200",
				ticket_updated: "bg-yellow-50 border-yellow-200",
				ticket_resolved: "bg-green-50 border-green-200",
				loan_approved: "bg-green-50 border-green-200",
				loan_rejected: "bg-red-50 border-red-200",
				comment_posted: "bg-purple-50 border-purple-200",
				system_alert: "bg-orange-50 border-orange-200",
				default: "bg-gray-50 border-gray-200",
			};

			return colorClasses[type] || colorClasses.default;
		},
	};
}

// Register Alpine.js component globally
document.addEventListener("alpine:init", () => {
	window.Alpine.data("notificationBell", notificationBell);
});

// Export for manual registration
export default notificationBell;
