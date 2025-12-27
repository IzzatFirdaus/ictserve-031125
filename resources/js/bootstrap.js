import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Configure CSRF token for axios requests (including broadcasting auth)
// Requirement 7.1: Ensure auth endpoint validates CSRF token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
	window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
	console.error(
		"CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
	);
}

/**
 * Alpine.js Configuration
 *
 * Import Alpine.js from Livewire bundle for manual control over initialization.
 * This allows us to register custom Alpine components and plugins before starting.
 *
 * @trace D03-FR-011 (Frontend Interactivity), D13 §3.2 (Alpine.js Integration)
 */
import {
	Livewire,
	Alpine,
} from "../../vendor/livewire/livewire/dist/livewire.esm";

// Make Alpine available globally for components
window.Alpine = Alpine;
window.Livewire = Livewire;

/**
 * Laravel Echo Configuration
 *
 * Echo allows you to easily build real-time event-driven applications.
 * We'll use Laravel Reverb as the WebSocket server for broadcasting.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 5.1, 5.2, 1.5)
 */
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

// Only initialize Echo if Reverb is configured
const reverbAppKey = import.meta.env.VITE_REVERB_APP_KEY;
const reverbHost = import.meta.env.VITE_REVERB_HOST;

if (reverbAppKey && reverbHost) {
	window.Echo = new Echo({
		broadcaster: "reverb",
		key: reverbAppKey,
		wsHost: reverbHost,
		wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
		wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
		forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
		enabledTransports: ["ws", "wss"],
		disableStats: true,
		authorizer: (channel) => {
			return {
				authorize: (socketId, callback) => {
					// Include status token for guest channel authorization
					const statusToken =
						new URLSearchParams(window.location.search).get("status_token") ||
						sessionStorage.getItem("status_token") ||
						localStorage.getItem("status_token");

					const authData = {
						socket_id: socketId,
						channel_name: channel.name,
					};

					// Add status token for guest channels
					if (
						statusToken &&
						(channel.name.includes("ticket.") ||
							channel.name.includes("loan.") ||
							channel.name.includes("conversation."))
					) {
						authData.status_token = statusToken;
					}

					window.axios
						.post("/broadcasting/auth", authData)
						.then((response) => {
							callback(null, response.data);
						})
						.catch((error) => {
							console.error("Echo authorization failed:", error);
							callback(error);
						});
				},
			};
		},
	});

	// Connection state management (v3.5.0)
	window.echoConnectionState = {
		connected: false,
		reconnecting: false,
		reconnectAttempts: 0,
		maxReconnectAttempts: 10,
		reconnectDelay: 1000, // Start with 1 second
		maxReconnectDelay: 30000, // Cap at 30 seconds
	};

	// Connection event handlers (v3.5.0)
	if (window.Echo.connector && window.Echo.connector.pusher) {
		const pusher = window.Echo.connector.pusher;

		// Connected successfully
		pusher.connection.bind("connected", () => {
			console.log("Echo: Connected to Reverb server");
			window.echoConnectionState.connected = true;
			window.echoConnectionState.reconnecting = false;
			window.echoConnectionState.reconnectAttempts = 0;
			window.echoConnectionState.reconnectDelay = 1000; // Reset delay

			// Dispatch custom event for UI components
			window.dispatchEvent(new CustomEvent("echo:connected"));

			// Hide reconnection toast if showing
			hideReconnectionToast();
		});

		// Disconnected
		pusher.connection.bind("disconnected", () => {
			console.warn("Echo: Disconnected from Reverb server");
			window.echoConnectionState.connected = false;

			// Dispatch custom event
			window.dispatchEvent(new CustomEvent("echo:disconnected"));

			// Show user-facing notification
			showReconnectionToast("Connection lost. Attempting to reconnect...");
		});

		// Connection unavailable
		pusher.connection.bind("unavailable", () => {
			console.error("Echo: Connection unavailable");
			window.echoConnectionState.connected = false;

			// Dispatch custom event
			window.dispatchEvent(new CustomEvent("echo:unavailable"));
		});

		// Connection error
		pusher.connection.bind("error", (error) => {
			console.error("Echo: Connection error", error);

			// Exponential backoff for reconnection
			if (
				window.echoConnectionState.reconnectAttempts <
				window.echoConnectionState.maxReconnectAttempts
			) {
				window.echoConnectionState.reconnecting = true;
				window.echoConnectionState.reconnectAttempts++;

				// Calculate backoff delay (exponential with jitter)
				const baseDelay = window.echoConnectionState.reconnectDelay;
				const exponentialDelay = Math.min(
					baseDelay *
						Math.pow(2, window.echoConnectionState.reconnectAttempts - 1),
					window.echoConnectionState.maxReconnectDelay
				);
				const jitter = Math.random() * 1000; // Add 0-1s jitter
				const delay = exponentialDelay + jitter;

				console.log(
					`Echo: Reconnecting in ${Math.round(delay / 1000)}s (attempt ${
						window.echoConnectionState.reconnectAttempts
					}/${window.echoConnectionState.maxReconnectAttempts})`
				);

				setTimeout(() => {
					pusher.connect();
				}, delay);

				showReconnectionToast(
					`Reconnecting... (attempt ${window.echoConnectionState.reconnectAttempts})`
				);
			} else {
				console.error("Echo: Max reconnection attempts reached. Giving up.");
				showReconnectionToast(
					"Unable to connect. Please refresh the page.",
					true
				);
			}
		});

		// State change handler
		pusher.connection.bind("state_change", (states) => {
			console.log(
				`Echo: State changed from ${states.previous} to ${states.current}`
			);
		});
	}
} else {
	// Echo not initialized - Reverb not configured
	// This prevents Pusher errors when broadcasting is not set up
	window.Echo = null;
	window.echoConnectionState = { connected: false };
	if (import.meta.env.DEV) {
		console.warn(
			"Laravel Echo not initialized: REVERB environment variables not configured. " +
				"Real-time features will be disabled."
		);
	}
}

// Fallback: support Pusher / Laravel Websockets if PUSHER env is configured
const pusherAppKey = import.meta.env.VITE_PUSHER_APP_KEY;
const pusherHost = import.meta.env.VITE_PUSHER_HOST;
if (!window.Echo && pusherAppKey && pusherHost) {
	window.Echo = new Echo({
		broadcaster: "pusher",
		key: pusherAppKey,
		wsHost: pusherHost,
		wsPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
		wssPort: import.meta.env.VITE_PUSHER_PORT ?? 6001,
		forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
		encrypted: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
		enabledTransports: ["ws", "wss"],
		cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? undefined,
		disableStats: true,
	});
}

/**
 * UI helpers for connection status (v3.5.0)
 */
let reconnectionToastElement = null;

function showReconnectionToast(message, isPermanent = false) {
	// Remove existing toast
	hideReconnectionToast();

	// Create toast element
	reconnectionToastElement = document.createElement("div");
	reconnectionToastElement.id = "echo-reconnection-toast";
	reconnectionToastElement.className =
		"fixed bottom-4 right-4 z-50 max-w-sm bg-yellow-50 border-l-4 border-yellow-400 p-4 shadow-lg rounded-md";
	reconnectionToastElement.setAttribute("role", "status");
	reconnectionToastElement.setAttribute("aria-live", "polite");

	const iconColor = isPermanent ? "text-red-400" : "text-yellow-400";
	const icon = isPermanent
		? '<svg class="h-5 w-5 ' +
		  iconColor +
		  '" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
		: '<svg class="animate-spin h-5 w-5 ' +
		  iconColor +
		  '" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

	reconnectionToastElement.innerHTML = `
        <div class="flex items-start">
            <div class="shrink-0">
                ${icon}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium ${
									isPermanent ? "text-red-800" : "text-yellow-800"
								}">
                    ${message}
                </p>
            </div>
        </div>
    `;

	document.body.appendChild(reconnectionToastElement);

	// Auto-hide after 5 seconds for non-permanent toasts
	if (!isPermanent) {
		setTimeout(() => {
			hideReconnectionToast();
		}, 5000);
	}
}

function hideReconnectionToast() {
	if (reconnectionToastElement && reconnectionToastElement.parentNode) {
		reconnectionToastElement.parentNode.removeChild(reconnectionToastElement);
		reconnectionToastElement = null;
	}
}

// Export for use in other modules
window.showReconnectionToast = showReconnectionToast;
window.hideReconnectionToast = hideReconnectionToast;

/**
 * AI Broadcasting Channel Listeners - v3.6.0 Ollama Integration
 *
 * Listeners for AI-specific WebSocket channels including:
 * - ai-status: Document processing and FAQ operations
 * - ai-alerts: Performance degradation and system errors
 * - ai-performance: Real-time performance metrics
 * - ai-approvals: Auto-reply approval workflow
 *
 * @see config/ai-broadcasting.php - AI channel configuration
 * @see D16 Broadcasting Setup v3.6.0
 * @requirements 11.1, 11.2, 11.3
 */

/**
 * Initialize AI broadcasting channels for admin/superuser users
 * Called from Livewire components or admin panel
 */
window.initAIBroadcasting = function (userRole) {
	if (!window.Echo) {
		console.warn("AI Broadcasting: Echo not initialized");
		return;
	}

	const allowedRoles = ["admin", "superuser"];
	const approverRoles = ["approver", "admin", "superuser"];

	// AI Status Channel - Document processing and FAQ operations
	if (allowedRoles.includes(userRole)) {
		window.Echo.private("ai-status")
			.listen(".AIProcessingStarted", (data) => {
				console.log("AI Processing Started:", data);
				window.dispatchEvent(
					new CustomEvent("ai:processing:started", { detail: data })
				);
				showAINotification(
					"info",
					data.message || "Pemprosesan AI dimulakan...",
					data
				);
			})
			.listen(".AIProcessingCompleted", (data) => {
				console.log("AI Processing Completed:", data);
				window.dispatchEvent(
					new CustomEvent("ai:processing:completed", { detail: data })
				);
				showAINotification(
					"success",
					data.message || "Pemprosesan AI selesai",
					data
				);
			})
			.listen(".AIProcessingFailed", (data) => {
				console.log("AI Processing Failed:", data);
				window.dispatchEvent(
					new CustomEvent("ai:processing:failed", { detail: data })
				);
				showAINotification(
					"error",
					data.message || "Pemprosesan AI gagal",
					data
				);
			});

		console.log("AI Broadcasting: Subscribed to ai-status channel");
	}

	// AI Alerts Channel - Performance degradation and system errors
	if (allowedRoles.includes(userRole)) {
		window.Echo.private("ai-alerts")
			.listen(".AIPerformanceAlert", (data) => {
				console.warn("AI Performance Alert:", data);
				window.dispatchEvent(
					new CustomEvent("ai:performance:alert", { detail: data })
				);
				showAINotification(
					"warning",
					data.message || "Amaran prestasi AI",
					data
				);
			})
			.listen(".AIErrorOccurred", (data) => {
				console.error("AI Error Occurred:", data);
				window.dispatchEvent(
					new CustomEvent("ai:error:occurred", { detail: data })
				);
				showAINotification("error", data.message || "Ralat sistem AI", data);
			})
			.listen(".AIServiceDegraded", (data) => {
				console.warn("AI Service Degraded:", data);
				window.dispatchEvent(
					new CustomEvent("ai:service:degraded", { detail: data })
				);
				showAINotification(
					"warning",
					data.message || "Perkhidmatan AI dalam mod degradasi",
					data
				);
			})
			.listen(".AIServiceRestored", (data) => {
				console.log("AI Service Restored:", data);
				window.dispatchEvent(
					new CustomEvent("ai:service:restored", { detail: data })
				);
				showAINotification(
					"success",
					data.message || "Perkhidmatan AI dipulihkan",
					data
				);
			});

		console.log("AI Broadcasting: Subscribed to ai-alerts channel");
	}

	// AI Performance Channel - Real-time performance metrics
	if (allowedRoles.includes(userRole)) {
		window.Echo.private("ai-performance")
			.listen(".AIPerformanceUpdate", (data) => {
				window.dispatchEvent(
					new CustomEvent("ai:performance:update", { detail: data })
				);
			})
			.listen(".AICacheStatsUpdate", (data) => {
				window.dispatchEvent(
					new CustomEvent("ai:cache:update", { detail: data })
				);
			})
			.listen(".AIResourceUsageUpdate", (data) => {
				window.dispatchEvent(
					new CustomEvent("ai:resource:update", { detail: data })
				);
			});

		console.log("AI Broadcasting: Subscribed to ai-performance channel");
	}

	// AI Approvals Channel - Auto-reply approval workflow
	if (approverRoles.includes(userRole)) {
		window.Echo.private("ai-approvals")
			.listen(".AutoReplyDraftCreated", (data) => {
				console.log("Auto-Reply Draft Created:", data);
				window.dispatchEvent(
					new CustomEvent("ai:autoreply:created", { detail: data })
				);
				showAINotification(
					"info",
					data.message || "Draf balasan automatik baharu dicipta",
					data
				);
			})
			.listen(".AutoReplyApprovalRequired", (data) => {
				console.log("Auto-Reply Approval Required:", data);
				window.dispatchEvent(
					new CustomEvent("ai:autoreply:approval:required", { detail: data })
				);
				showAINotification(
					"warning",
					data.message || "Kelulusan balasan automatik diperlukan",
					data
				);
			})
			.listen(".AutoReplyApproved", (data) => {
				console.log("Auto-Reply Approved:", data);
				window.dispatchEvent(
					new CustomEvent("ai:autoreply:approved", { detail: data })
				);
				showAINotification(
					"success",
					data.message || "Balasan automatik diluluskan",
					data
				);
			})
			.listen(".AutoReplyRejected", (data) => {
				console.log("Auto-Reply Rejected:", data);
				window.dispatchEvent(
					new CustomEvent("ai:autoreply:rejected", { detail: data })
				);
				showAINotification(
					"info",
					data.message || "Balasan automatik ditolak",
					data
				);
			});

		console.log("AI Broadcasting: Subscribed to ai-approvals channel");
	}
};

/**
 * Show AI notification toast
 * @param {string} type - 'info', 'success', 'warning', 'error'
 * @param {string} message - Notification message (Bahasa Melayu)
 * @param {object} data - Additional event data
 */
function showAINotification(type, message, data = {}) {
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

	const toastId = "ai-notification-" + Date.now();
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
                <p class="text-sm font-medium ${color.text}">${message}</p>
                ${
									data.requestId
										? `<p class="mt-1 text-xs ${color.text} opacity-75">ID: ${data.requestId}</p>`
										: ""
								}
            </div>
            <button type="button" class="ml-4 inline-flex ${
							color.text
						} hover:opacity-75 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-${
		type === "error"
			? "red"
			: type === "warning"
			? "yellow"
			: type === "success"
			? "green"
			: "blue"
	}-500" onclick="this.parentElement.parentElement.remove()">
                <span class="sr-only">Tutup</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </button>
        </div>
    `;

	document.body.appendChild(toast);

	// Animate in
	requestAnimationFrame(() => {
		toast.classList.remove("translate-x-full");
	});

	// Auto-remove after 8 seconds (longer for AI notifications)
	setTimeout(() => {
		toast.classList.add("translate-x-full");
		setTimeout(() => toast.remove(), 300);
	}, 8000);
}

// Export AI notification function
window.showAINotification = showAINotification;

/**
 * Widget Real-Time Broadcasting Integration - v3.6.1
 *
 * Initialize widget real-time updates for dashboard widgets including
 * performance metrics, system statistics, and user-specific data.
 * Integrates with WidgetRealtimeManager service and provides fallback polling.
 *
 * @see resources/js/widget-realtime.js - Widget real-time manager
 * @see app/Services/WidgetRealtimeManager.php - Backend service
 * @requirements R8 (Real-time Updates), R19 (Real-Time Widget Updates)
 */

/**
 * Initialize widget broadcasting for authenticated users
 * Called from dashboard pages and admin panel
 */
window.initWidgetBroadcasting = function (userRole, userId) {
	if (!window.Echo) {
		console.warn(
			"Widget Broadcasting: Echo not initialized, using polling fallback"
		);
		return;
	}

	const allowedRoles = ["staff", "admin", "superuser"];
	const adminRoles = ["admin", "superuser"];

	if (!allowedRoles.includes(userRole)) {
		console.warn(
			"Widget Broadcasting: User role not authorized for widget updates"
		);
		return;
	}

	// User-specific widget channel for personal dashboard
	if (userId) {
		window.Echo.private(`dashboard.widgets.${userId}`).listen(
			".WidgetDataUpdated",
			(data) => {
				console.log("Widget Update (User Channel):", data);
				window.dispatchEvent(
					new CustomEvent("widget:update:user", { detail: data })
				);
			}
		);

		console.log(`Widget Broadcasting: Subscribed to user channel ${userId}`);
	}

	// Global widget channel for admin users
	if (adminRoles.includes(userRole)) {
		window.Echo.private("dashboard.widgets.global").listen(
			".WidgetDataUpdated",
			(data) => {
				console.log("Widget Update (Global Channel):", data);
				window.dispatchEvent(
					new CustomEvent("widget:update:global", { detail: data })
				);
			}
		);

		console.log("Widget Broadcasting: Subscribed to global admin channel");
	}

	// Widget-specific channels are handled by the WidgetRealtimeManager
	// This provides the foundation for targeted widget updates
};

/**
 * Auto-initialize widget broadcasting if user data is available
 */
document.addEventListener("DOMContentLoaded", function () {
	// Try to get user data from meta tags
	const userIdMeta = document.querySelector('meta[name="user-id"]');
	const userRoleMeta = document.querySelector('meta[name="user-role"]');

	if (userIdMeta && userRoleMeta) {
		const userId = userIdMeta.getAttribute("content");
		const userRole = userRoleMeta.getAttribute("content");

		if (userId && userRole) {
			// Initialize both AI and Widget broadcasting
			window.initAIBroadcasting?.(userRole);
			window.initWidgetBroadcasting?.(userRole, userId);
		}
	}
});
