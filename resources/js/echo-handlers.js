/**
 * Echo Event Handlers
 *
 * Centralized event handlers for Laravel Echo real-time notifications.
 * Handles notification bell counter updates, status badge updates, and toast notifications.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 1.3, 1.4)
 */

/**
 * Notification Handler
 * Handles notification.created events for bell counter and toast notifications
 *
 * @param {Object} event - The notification event data
 * @param {number} event.notification_id - Notification ID
 * @param {string} event.type - Notification type
 * @param {string} event.message - Notification message
 * @param {string} event.created_at - Creation timestamp
 */
export function handleNotificationCreated(event) {
	console.log("Notification created:", event);

	// Update notification bell counter
	updateNotificationBellCounter();

	// Show toast notification
	showNotificationToast(event);

	// Dispatch custom event for Alpine.js components
	window.dispatchEvent(
		new CustomEvent("notification:created", {
			detail: event,
		})
	);
}

/**
 * Status Update Handler
 * Handles status.updated events for badge updates
 *
 * @param {Object} event - The status update event data
 * @param {string} event.entity_type - Entity type (ticket, loan)
 * @param {number} event.entity_id - Entity ID
 * @param {string} event.entity_uuid - Entity UUID
 * @param {string} event.old_status - Previous status
 * @param {string} event.new_status - New status
 * @param {string} event.updated_at - Update timestamp
 */
export function handleStatusUpdated(event) {
	console.log("Status updated:", event);

	// Update status badges
	updateStatusBadges(event);

	// Show status update toast
	showStatusUpdateToast(event);

	// Dispatch custom event for Alpine.js components
	window.dispatchEvent(
		new CustomEvent("status:updated", {
			detail: event,
		})
	);
}

/**
 * Update notification bell counter
 * Fetches current unread count and updates UI elements
 */
function updateNotificationBellCounter() {
	// Find notification bell elements
	const bellCounters = document.querySelectorAll("[data-notification-counter]");

	if (bellCounters.length === 0) {
		return;
	}

	// Fetch current unread count
	window.axios
		.get("/api/notifications/unread-count")
		.then((response) => {
			const count = response.data.count || 0;

			bellCounters.forEach((counter) => {
				// Update counter text
				counter.textContent = count > 99 ? "99+" : count.toString();

				// Show/hide counter based on count
				if (count > 0) {
					counter.classList.remove("hidden");
					counter.setAttribute(
						"aria-label",
						`${count} notifikasi belum dibaca`
					);
				} else {
					counter.classList.add("hidden");
					counter.removeAttribute("aria-label");
				}
			});
		})
		.catch((error) => {
			console.error("Failed to fetch notification count:", error);
		});
}

/**
 * Update status badges for the given entity
 *
 * @param {Object} event - Status update event data
 */
function updateStatusBadges(event) {
	const { entity_type, entity_id, entity_uuid, new_status } = event;

	// Find status badge elements by entity ID or UUID
	const selectors = [
		`[data-status-badge="${entity_type}-${entity_id}"]`,
		`[data-status-badge="${entity_type}-${entity_uuid}"]`,
		`[data-entity-status="${entity_type}"][data-entity-id="${entity_id}"]`,
		`[data-entity-status="${entity_type}"][data-entity-uuid="${entity_uuid}"]`,
	];

	selectors.forEach((selector) => {
		const badges = document.querySelectorAll(selector);

		badges.forEach((badge) => {
			// Update badge text
			const statusText = getStatusText(new_status);
			badge.textContent = statusText;

			// Update badge classes
			updateBadgeClasses(badge, new_status);

			// Update accessibility attributes
			badge.setAttribute("aria-label", `Status: ${statusText}`);
		});
	});
}

/**
 * Get localized status text
 *
 * @param {string} status - Status key
 * @returns {string} Localized status text
 */
function getStatusText(status) {
	const statusTexts = {
		open: "Terbuka",
		in_progress: "Dalam Proses",
		resolved: "Diselesaikan",
		closed: "Ditutup",
		pending: "Menunggu",
		approved: "Diluluskan",
		rejected: "Ditolak",
		cancelled: "Dibatalkan",
	};

	return statusTexts[status] || status;
}

/**
 * Update badge CSS classes based on status
 *
 * @param {HTMLElement} badge - Badge element
 * @param {string} status - New status
 */
function updateBadgeClasses(badge, status) {
	// Remove existing status classes
	const statusClasses = [
		"bg-red-100",
		"text-red-800",
		"border-red-200",
		"bg-yellow-100",
		"text-yellow-800",
		"border-yellow-200",
		"bg-green-100",
		"text-green-800",
		"border-green-200",
		"bg-blue-100",
		"text-blue-800",
		"border-blue-200",
		"bg-gray-100",
		"text-gray-800",
		"border-gray-200",
	];

	badge.classList.remove(...statusClasses);

	// Add new status classes
	const statusColorMap = {
		open: ["bg-red-100", "text-red-800", "border-red-200"],
		in_progress: ["bg-yellow-100", "text-yellow-800", "border-yellow-200"],
		resolved: ["bg-green-100", "text-green-800", "border-green-200"],
		closed: ["bg-gray-100", "text-gray-800", "border-gray-200"],
		pending: ["bg-yellow-100", "text-yellow-800", "border-yellow-200"],
		approved: ["bg-green-100", "text-green-800", "border-green-200"],
		rejected: ["bg-red-100", "text-red-800", "border-red-200"],
		cancelled: ["bg-gray-100", "text-gray-800", "border-gray-200"],
	};

	const newClasses = statusColorMap[status] || statusColorMap["open"];
	badge.classList.add(...newClasses);
}

/**
 * Show notification toast
 *
 * @param {Object} event - Notification event data
 */
function showNotificationToast(event) {
	const { type, message } = event;

	// Create toast element
	const toast = createToastElement("notification", {
		type: getNotificationToastType(type),
		title: "Notifikasi Baharu",
		message: message,
		duration: 5000,
	});

	// Add to page
	document.body.appendChild(toast);

	// Animate in
	requestAnimationFrame(() => {
		toast.classList.remove("translate-x-full");
	});

	// Auto-remove
	setTimeout(() => {
		removeToast(toast);
	}, 5000);
}

/**
 * Show status update toast
 *
 * @param {Object} event - Status update event data
 */
function showStatusUpdateToast(event) {
	const { entity_type, new_status, old_status } = event;

	const entityTypeText =
		entity_type === "ticket" ? "Tiket" : "Permohonan Pinjaman";
	const message = `${entityTypeText} dikemaskini dari "${getStatusText(
		old_status
	)}" kepada "${getStatusText(new_status)}"`;

	// Create toast element
	const toast = createToastElement("status-update", {
		type: "info",
		title: "Status Dikemaskini",
		message: message,
		duration: 4000,
	});

	// Add to page
	document.body.appendChild(toast);

	// Animate in
	requestAnimationFrame(() => {
		toast.classList.remove("translate-x-full");
	});

	// Auto-remove
	setTimeout(() => {
		removeToast(toast);
	}, 4000);
}

/**
 * Get notification toast type based on notification type
 *
 * @param {string} notificationType - Notification type
 * @returns {string} Toast type
 */
function getNotificationToastType(notificationType) {
	const typeMap = {
		ticket_created: "info",
		ticket_updated: "info",
		ticket_resolved: "success",
		loan_approved: "success",
		loan_rejected: "warning",
		comment_posted: "info",
		system_alert: "warning",
		error: "error",
	};

	return typeMap[notificationType] || "info";
}

/**
 * Create toast element
 *
 * @param {string} id - Toast ID prefix
 * @param {Object} options - Toast options
 * @param {string} options.type - Toast type (info, success, warning, error)
 * @param {string} options.title - Toast title
 * @param {string} options.message - Toast message
 * @param {number} options.duration - Auto-hide duration in ms
 * @returns {HTMLElement} Toast element
 */
function createToastElement(id, options) {
	const { type, title, message, duration } = options;

	const colors = {
		info: {
			bg: "bg-blue-50",
			border: "border-blue-400",
			text: "text-blue-800",
			icon: "text-blue-400",
		},
		success: {
			bg: "bg-green-50",
			border: "border-green-400",
			text: "text-green-800",
			icon: "text-green-400",
		},
		warning: {
			bg: "bg-yellow-50",
			border: "border-yellow-400",
			text: "text-yellow-800",
			icon: "text-yellow-400",
		},
		error: {
			bg: "bg-red-50",
			border: "border-red-400",
			text: "text-red-800",
			icon: "text-red-400",
		},
	};

	const color = colors[type] || colors.info;

	const icons = {
		info: '<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>',
		success:
			'<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>',
		warning:
			'<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>',
		error:
			'<svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>',
	};

	const toastId = `${id}-${Date.now()}`;
	const toast = document.createElement("div");
	toast.id = toastId;
	toast.className = `fixed bottom-4 right-4 z-50 max-w-sm ${color.bg} border-l-4 ${color.border} p-4 shadow-lg rounded-md transform transition-all duration-300 ease-out translate-x-full`;
	toast.setAttribute("role", "alert");
	toast.setAttribute("aria-live", "assertive");

	toast.innerHTML = `
        <div class="flex items-start">
            <div class="shrink-0 ${color.icon}">
                ${icons[type]}
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium ${color.text}">${title}</p>
                <p class="mt-1 text-sm ${color.text} opacity-90">${message}</p>
            </div>
            <button type="button" class="ml-4 inline-flex ${color.text} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" onclick="this.closest('[role=alert]').remove()">
                <span class="sr-only">Tutup</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;

	return toast;
}

/**
 * Remove toast with animation
 *
 * @param {HTMLElement} toast - Toast element to remove
 */
function removeToast(toast) {
	if (toast && toast.parentNode) {
		toast.classList.add("translate-x-full");
		setTimeout(() => {
			if (toast.parentNode) {
				toast.parentNode.removeChild(toast);
			}
		}, 300);
	}
}

/**
 * Initialize Echo event handlers
 * Sets up listeners for authenticated and guest users
 *
 * @param {Object} options - Initialization options
 * @param {number|null} options.userId - User ID for authenticated users
 * @param {string|null} options.ticketUuid - Ticket UUID for guest users
 * @param {string|null} options.loanUuid - Loan UUID for guest users
 * @param {string|null} options.conversationUuid - Conversation UUID for AI chat
 */
export function initializeEchoHandlers(options = {}) {
	if (!window.Echo) {
		console.warn("Echo not initialized, skipping event handlers setup");
		return;
	}

	const { userId, ticketUuid, loanUuid, conversationUuid } = options;

	// Authenticated user channels
	if (userId) {
		console.log(`Setting up Echo handlers for user ${userId}`);

		window.Echo.private(`user.${userId}`)
			.listen(".notification.created", handleNotificationCreated)
			.listen(".status.updated", handleStatusUpdated);
	}

	// Guest ticket channel
	if (ticketUuid) {
		console.log(`Setting up Echo handlers for ticket ${ticketUuid}`);

		window.Echo.private(`ticket.${ticketUuid}`).listen(
			".status.updated",
			handleStatusUpdated
		);
	}

	// Guest loan channel
	if (loanUuid) {
		console.log(`Setting up Echo handlers for loan ${loanUuid}`);

		window.Echo.private(`loan.${loanUuid}`).listen(
			".status.updated",
			handleStatusUpdated
		);
	}

	// AI conversation channel
	if (conversationUuid) {
		console.log(
			`Setting up Echo handlers for conversation ${conversationUuid}`
		);

		window.Echo.private(`conversation.${conversationUuid}`)
			.listen(".ai.streaming.started", (event) => {
				window.dispatchEvent(
					new CustomEvent("ai:streaming:started", { detail: event })
				);
			})
			.listen(".ai.streaming.chunk", (event) => {
				window.dispatchEvent(
					new CustomEvent("ai:streaming:chunk", { detail: event })
				);
			})
			.listen(".ai.streaming.completed", (event) => {
				window.dispatchEvent(
					new CustomEvent("ai:streaming:completed", { detail: event })
				);
			})
			.listen(".ai.error.occurred", (event) => {
				window.dispatchEvent(
					new CustomEvent("ai:error:occurred", { detail: event })
				);
			});
	}
}

// Make functions available globally for backward compatibility
window.handleNotificationCreated = handleNotificationCreated;
window.handleStatusUpdated = handleStatusUpdated;
window.initializeEchoHandlers = initializeEchoHandlers;
