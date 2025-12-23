/**
 * Status Display Alpine.js Component
 *
 * Real-time status display component that subscribes to appropriate channels
 * (user or ticket/loan UUID) and updates status badges when status changes occur.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 1.3, 2.3)
 */

/**
 * Alpine.js Status Display Component
 *
 * Usage:
 * <!-- For authenticated users -->
 * <div x-data="statusDisplay({ userId: 123, entityType: 'ticket', entityId: 456 })" x-init="init()">
 *   <span x-text="statusText"
 *         :class="statusClasses"
 *         class="px-2 py-1 rounded-full text-xs font-medium">
 *   </span>
 * </div>
 *
 * <!-- For guest users -->
 * <div x-data="statusDisplay({ entityType: 'ticket', entityUuid: 'uuid-123', statusToken: 'token-456' })" x-init="init()">
 *   <span x-text="statusText"
 *         :class="statusClasses"
 *         class="px-2 py-1 rounded-full text-xs font-medium">
 *   </span>
 * </div>
 */
export function statusDisplay(options = {}) {
	return {
		// Component configuration
		userId: options.userId || null,
		entityType: options.entityType || null, // 'ticket' or 'loan'
		entityId: options.entityId || null,
		entityUuid: options.entityUuid || null,
		statusToken: options.statusToken || null,

		// Component state
		currentStatus: options.initialStatus || "unknown",
		isLoading: false,
		error: null,
		lastUpdated: null,

		// Computed properties
		get statusText() {
			return this.getStatusText(this.currentStatus);
		},

		get statusClasses() {
			return this.getStatusClasses(this.currentStatus);
		},

		get channelName() {
			if (this.userId) {
				return `user.${this.userId}`;
			} else if (this.entityUuid) {
				return `${this.entityType}.${this.entityUuid}`;
			}
			return null;
		},

		get isGuest() {
			return !this.userId && this.entityUuid;
		},

		// Component initialization
		init() {
			console.log("Initializing status display:", {
				userId: this.userId,
				entityType: this.entityType,
				entityId: this.entityId,
				entityUuid: this.entityUuid,
				isGuest: this.isGuest,
			});

			// Load current status if not provided
			if (this.currentStatus === "unknown") {
				this.loadCurrentStatus();
			}

			// Set up Echo listeners
			this.setupEchoListeners();

			// Set up custom event listeners
			this.setupCustomEventListeners();

			// Set up data attributes for echo-handlers.js
			this.setupDataAttributes();
		},

		// Load current status from server
		async loadCurrentStatus() {
			if (!this.entityType || (!this.entityId && !this.entityUuid)) {
				return;
			}

			this.isLoading = true;
			this.error = null;

			try {
				let url;
				let params = {};

				if (this.entityId) {
					url = `/api/${this.entityType}s/${this.entityId}/status`;
				} else if (this.entityUuid) {
					url = `/api/${this.entityType}s/${this.entityUuid}/status`;
					if (this.statusToken) {
						params.status_token = this.statusToken;
					}
				}

				const response = await window.axios.get(url, { params });
				this.currentStatus = response.data.status;
				this.lastUpdated = new Date().toISOString();

				console.log(`Loaded current status: ${this.currentStatus}`);
			} catch (error) {
				console.error("Failed to load current status:", error);
				this.error = "Gagal memuatkan status";
				this.currentStatus = "error";
			} finally {
				this.isLoading = false;
			}
		},

		// Set up Echo WebSocket listeners
		setupEchoListeners() {
			if (!window.Echo || !this.channelName) {
				console.warn("Echo not available or channel name not determined");
				return;
			}

			console.log(`Setting up Echo listeners for channel: ${this.channelName}`);

			// Listen to appropriate channel
			window.Echo.private(this.channelName).listen(
				".status.updated",
				(event) => {
					console.log("Status updated event received:", event);
					this.handleStatusUpdated(event);
				}
			);
		},

		// Set up custom event listeners
		setupCustomEventListeners() {
			// Listen for status update events from echo-handlers.js
			window.addEventListener("status:updated", (event) => {
				this.handleStatusUpdated(event.detail);
			});

			// Listen for Echo connection events
			window.addEventListener("echo:connected", () => {
				console.log("Echo connected, reloading status");
				this.loadCurrentStatus();
			});

			window.addEventListener("echo:disconnected", () => {
				console.log("Echo disconnected");
			});
		},

		// Set up data attributes for echo-handlers.js compatibility
		setupDataAttributes() {
			if (!this.$el) return;

			// Add data attributes that echo-handlers.js looks for
			if (this.entityId) {
				this.$el.setAttribute(
					"data-status-badge",
					`${this.entityType}-${this.entityId}`
				);
				this.$el.setAttribute("data-entity-status", this.entityType);
				this.$el.setAttribute("data-entity-id", this.entityId);
			}

			if (this.entityUuid) {
				this.$el.setAttribute(
					"data-status-badge",
					`${this.entityType}-${this.entityUuid}`
				);
				this.$el.setAttribute("data-entity-status", this.entityType);
				this.$el.setAttribute("data-entity-uuid", this.entityUuid);
			}
		},

		// Handle status updated event
		handleStatusUpdated(event) {
			// Check if this event is for our entity
			const isForThisEntity =
				((this.entityId && event.entity_id === this.entityId) ||
					(this.entityUuid && event.entity_uuid === this.entityUuid)) &&
				event.entity_type === this.entityType;

			if (!isForThisEntity) {
				return;
			}

			console.log("Updating status from event:", event);

			// Update status
			const oldStatus = this.currentStatus;
			this.currentStatus = event.new_status;
			this.lastUpdated = event.updated_at;
			this.error = null;

			// Dispatch custom event for other components
			this.$dispatch("status-changed", {
				entityType: this.entityType,
				entityId: this.entityId,
				entityUuid: this.entityUuid,
				oldStatus: oldStatus,
				newStatus: this.currentStatus,
				updatedAt: this.lastUpdated,
			});

			// Show visual feedback
			this.showStatusChangeAnimation();
		},

		// Show visual feedback for status change
		showStatusChangeAnimation() {
			if (!this.$el) return;

			// Add pulse animation class
			this.$el.classList.add("animate-pulse");

			// Remove animation after 2 seconds
			setTimeout(() => {
				this.$el.classList.remove("animate-pulse");
			}, 2000);
		},

		// Get localized status text
		getStatusText(status) {
			const statusTexts = {
				open: "Terbuka",
				in_progress: "Dalam Proses",
				resolved: "Diselesaikan",
				closed: "Ditutup",
				pending: "Menunggu",
				approved: "Diluluskan",
				rejected: "Ditolak",
				cancelled: "Dibatalkan",
				draft: "Draf",
				submitted: "Dihantar",
				under_review: "Dalam Semakan",
				returned: "Dipulangkan",
				overdue: "Tertunggak",
				unknown: "Tidak Diketahui",
				error: "Ralat",
				loading: "Memuatkan...",
			};

			return statusTexts[status] || status;
		},

		// Get status CSS classes
		getStatusClasses(status) {
			const baseClasses = "px-2 py-1 rounded-full text-xs font-medium border";

			const statusClasses = {
				open: "bg-red-100 text-red-800 border-red-200",
				in_progress: "bg-yellow-100 text-yellow-800 border-yellow-200",
				resolved: "bg-green-100 text-green-800 border-green-200",
				closed: "bg-gray-100 text-gray-800 border-gray-200",
				pending: "bg-yellow-100 text-yellow-800 border-yellow-200",
				approved: "bg-green-100 text-green-800 border-green-200",
				rejected: "bg-red-100 text-red-800 border-red-200",
				cancelled: "bg-gray-100 text-gray-800 border-gray-200",
				draft: "bg-blue-100 text-blue-800 border-blue-200",
				submitted: "bg-blue-100 text-blue-800 border-blue-200",
				under_review: "bg-purple-100 text-purple-800 border-purple-200",
				returned: "bg-orange-100 text-orange-800 border-orange-200",
				overdue: "bg-red-100 text-red-800 border-red-200",
				unknown: "bg-gray-100 text-gray-800 border-gray-200",
				error: "bg-red-100 text-red-800 border-red-200",
				loading: "bg-gray-100 text-gray-800 border-gray-200 animate-pulse",
			};

			return `${baseClasses} ${
				statusClasses[status] || statusClasses["unknown"]
			}`;
		},

		// Get status icon
		getStatusIcon(status) {
			const icons = {
				open: "🔴",
				in_progress: "🟡",
				resolved: "🟢",
				closed: "⚫",
				pending: "🟡",
				approved: "✅",
				rejected: "❌",
				cancelled: "⚫",
				draft: "📝",
				submitted: "📤",
				under_review: "👀",
				returned: "↩️",
				overdue: "⏰",
				unknown: "❓",
				error: "⚠️",
				loading: "⏳",
			};

			return icons[status] || icons["unknown"];
		},

		// Format last updated time
		formatLastUpdated() {
			if (!this.lastUpdated) {
				return "";
			}

			const date = new Date(this.lastUpdated);
			const now = new Date();
			const diffInMinutes = Math.floor((now - date) / (1000 * 60));

			if (diffInMinutes < 1) {
				return "Baru sahaja dikemaskini";
			} else if (diffInMinutes < 60) {
				return `Dikemaskini ${diffInMinutes} minit yang lalu`;
			} else if (diffInMinutes < 1440) {
				const hours = Math.floor(diffInMinutes / 60);
				return `Dikemaskini ${hours} jam yang lalu`;
			} else {
				const days = Math.floor(diffInMinutes / 1440);
				return `Dikemaskini ${days} hari yang lalu`;
			}
		},

		// Refresh status manually
		async refreshStatus() {
			await this.loadCurrentStatus();
		},

		// Check if status indicates completion
		isCompleted() {
			return ["resolved", "closed", "approved", "returned"].includes(
				this.currentStatus
			);
		},

		// Check if status indicates pending action
		isPending() {
			return ["pending", "under_review", "submitted"].includes(
				this.currentStatus
			);
		},

		// Check if status indicates error or rejection
		isError() {
			return ["rejected", "cancelled", "error", "overdue"].includes(
				this.currentStatus
			);
		},
	};
}

// Register Alpine.js component globally
document.addEventListener("alpine:init", () => {
	window.Alpine.data("statusDisplay", statusDisplay);
});

// Export for manual registration
export default statusDisplay;
