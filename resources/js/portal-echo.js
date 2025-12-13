/**
 * Portal Echo Listeners
 *
 * This file contains all Laravel Echo event listeners for the authenticated portal.
 * It handles real-time updates for notifications, submission status changes, and comments.
 *
 * True Hybrid Architecture v3.5.0:
 * - Authenticated Users: Listen to private-user.{id}
 * - Admin/Superuser: Also listen to admin.notifications for high-priority alerts
 * - Guests: Listen to private-ticket.{uuid} or private-loan.{uuid}
 *
 * @trace D03 SRS-FR-008, D04 §5.3, D12 §4, D16 (Requirements 6.1, 6.2, 7.4, 8.1, 8.2)
 * @author dev-team@motac.gov.my
 * @last-updated 2025-12-03
 */

/**
 * Initialize Echo listeners for authenticated users
 */
export function initializePortalEcho() {
	// Only initialize if user is authenticated
	const userId = document.querySelector('meta[name="user-id"]')?.content;

	if (!userId || !window.Echo) {
		console.warn(
			"Portal Echo: User not authenticated or Echo not initialized",
			{
				userId,
				echoExists: !!window.Echo,
			}
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
	 * Payload structure from NotificationCreated::broadcastWith():
	 * {
	 *   id: notification.id,
	 *   type: notification.type,
	 *   data: { title, message, url, ... },
	 *   created_at: ISO timestamp,
	 *   read_at: ISO timestamp or null
	 * }
	 *
	 * @trace Requirements 6.1, 6.2, D03 SRS-FR-043
	 */
	window.Echo.private(`user.${userId}`).listen(
		".notification.created",
		(event) => {
			console.log("Portal Echo: Notification received", event);

			// Dispatch Livewire event to update NotificationBell component
			if (window.Livewire) {
				window.Livewire.dispatch("echo:notification-created", event);
			}

			// Extract notification data (directly from event.data - this is the payload from broadcastWith())
			const title =
				event.data?.title || event.data?.subject || "New Notification";
			const message = event.data?.message || event.data?.body || "";
			const notificationId = event.id;

			// Show browser notification if permission granted
			if ("Notification" in window && Notification.permission === "granted") {
				new Notification(title, {
					body: message,
					icon: "/images/motac-logo-32.png",
					badge: "/images/motac-logo-32.png",
					tag: notificationId,
				});
			}

			// Update ARIA live region for screen readers
			announceNotification(title, message);
		}
	);

	/**
	 * Listen for submission status updates
	 *
	 * Event: status.updated
	 * Channel: private-user.{userId}
	 *
	 * Payload structure from StatusUpdated::broadcastWith():
	 * {
	 *   model_type: 'HelpdeskTicket' | 'LoanApplication',
	 *   model_id: ID,
	 *   old_status: previous status string,
	 *   new_status: new status string,
	 *   updated_at: ISO timestamp
	 * }
	 *
	 * @trace Requirements 6.1, 10.1, D03 SRS-FR-043
	 */
	window.Echo.private(`user.${userId}`).listen(".status.updated", (event) => {
		console.log("Portal Echo: Status update received", event);

		// Dispatch Livewire event to update submission components
		// Pass complete event data with both old and new payload formats for compatibility
		if (window.Livewire) {
			window.Livewire.dispatch("echo:status-updated", {
				...event,
				// Add legacy keys for backward compatibility with existing components
				submission_type: event.model_type,
				submission_id: event.model_id,
			});
		}

		// Extract model info for announcement
		const modelType = event.model_type || "Submission";
		const modelId = event.model_id || "unknown";
		const newStatus = event.new_status || "updated";

		// Update ARIA live region
		announceStatusUpdate(modelType, modelId, newStatus);
	});

	/**
	 * Listen for email verification events (v3.5.0)
	 *
	 * Event: email.verified
	 * Channel: private-user.{userId}
	 *
	 * Payload:
	 * {
	 *   user_id: ID,
	 *   email: verified email,
	 *   verified_at: ISO timestamp
	 * }
	 *
	 * @trace D03 SRS-FR-001 (Self-Registration), v3.5.0 Feature
	 */
	window.Echo.private(`user.${userId}`).listen(".email.verified", (event) => {
		console.log("Portal Echo: Email verified", event);

		// Dispatch to Livewire components
		if (window.Livewire) {
			window.Livewire.dispatch("echo:email-verified", event);
		}

		// Show success notification
		if ("Notification" in window && Notification.permission === "granted") {
			new Notification("Email Verified", {
				body: "Your email has been successfully verified. You now have full access to ICTServe.",
				icon: "/images/motac-logo-32.png",
				badge: "/images/motac-logo-32.png",
				tag: `email-verified-${event.user_id}`,
			});
		}

		announceNotification(
			"Email Verified",
			"Your email has been successfully verified"
		);
	});

	/**
	 * Listen for account linking events (v3.5.0)
	 *
	 * Event: account.linked
	 * Channel: private-user.{userId}
	 *
	 * Payload:
	 * {
	 *   user_id: ID,
	 *   linked_submissions: count of submissions linked,
	 *   submission_types: ['helpdesk', 'loan'],
	 *   linked_at: ISO timestamp
	 * }
	 *
	 * @trace D03 SRS-FR-001.5 (Account Linking), v3.5.0 Feature
	 */
	window.Echo.private(`user.${userId}`).listen(".account.linked", (event) => {
		console.log("Portal Echo: Account linked", event);

		// Dispatch to Livewire components
		if (window.Livewire) {
			window.Livewire.dispatch("echo:account-linked", event);
		}

		// Show success notification
		const submissionCount = event.linked_submissions || 0;
		const message = `Successfully linked ${submissionCount} guest ${
			submissionCount === 1 ? "submission" : "submissions"
		} to your account.`;

		if ("Notification" in window && Notification.permission === "granted") {
			new Notification("Account Linked", {
				body: message,
				icon: "/images/motac-logo-32.png",
				badge: "/images/motac-logo-32.png",
				tag: `account-linked-${event.user_id}`,
			});
		}

		announceNotification("Account Linked", message);
	});

	/**
	 * Listen for API token creation events (v3.5.0)
	 *
	 * Event: api.token.created
	 * Channel: private-user.{userId}
	 *
	 * Payload:
	 * {
	 *   token_id: ID,
	 *   token_name: string,
	 *   abilities: array of scopes,
	 *   expires_at: ISO timestamp or null,
	 *   created_at: ISO timestamp
	 * }
	 *
	 * @trace v3.5.0 Feature (API Token Management)
	 */
	window.Echo.private(`user.${userId}`).listen(
		".api.token.created",
		(event) => {
			console.log("Portal Echo: API token created", event);

			// Dispatch to Livewire components
			if (window.Livewire) {
				window.Livewire.dispatch("echo:api-token-created", event);
			}

			// Show notification
			if ("Notification" in window && Notification.permission === "granted") {
				new Notification("API Token Created", {
					body: `Token "${event.token_name}" has been created successfully.`,
					icon: "/images/motac-logo-32.png",
					badge: "/images/motac-logo-32.png",
					tag: `api-token-created-${event.token_id}`,
				});
			}

			announceNotification(
				"API Token Created",
				`Token ${event.token_name} created`
			);
		}
	);

	/**
	 * Listen for API token revocation events (v3.5.0)
	 *
	 * Event: api.token.revoked
	 * Channel: private-user.{userId}
	 *
	 * @trace v3.5.0 Feature (API Token Management)
	 */
	window.Echo.private(`user.${userId}`).listen(
		".api.token.revoked",
		(event) => {
			console.log("Portal Echo: API token revoked", event);

			// Dispatch to Livewire components
			if (window.Livewire) {
				window.Livewire.dispatch("echo:api-token-revoked", event);
			}

			announceNotification(
				"API Token Revoked",
				`Token ${event.token_name} has been revoked`
			);
		}
	);

	/**
	 * Listen for Google SSO linking events (v3.5.0)
	 *
	 * Event: google.sso.linked
	 * Channel: private-user.{userId}
	 *
	 * Payload:
	 * {
	 *   user_id: ID,
	 *   google_email: string,
	 *   linked_at: ISO timestamp
	 * }
	 *
	 * @trace v3.5.0 Feature (Google OAuth Integration)
	 */
	window.Echo.private(`user.${userId}`).listen(
		".google.sso.linked",
		(event) => {
			console.log("Portal Echo: Google SSO linked", event);

			// Dispatch to Livewire components
			if (window.Livewire) {
				window.Livewire.dispatch("echo:google-sso-linked", event);
			}

			// Show success notification
			if ("Notification" in window && Notification.permission === "granted") {
				new Notification("Google Account Linked", {
					body: `Your Google account (${event.google_email}) has been linked successfully.`,
					icon: "/images/motac-logo-32.png",
					badge: "/images/motac-logo-32.png",
					tag: `google-sso-linked-${event.user_id}`,
				});
			}

			announceNotification(
				"Google Account Linked",
				"Google account linked successfully"
			);
		}
	);

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
	 * Subscribe to asset-specific updates
	 * Channel: asset.{assetId}
	 * Event: asset.returned.damaged
	 */
	window.subscribeToAssetUpdates = function (assetId) {
		const channelName = `asset.${assetId}`;

		if (!window.Echo) return;

		console.log(`Portal Echo: Subscribing to ${channelName}`);

		window.Echo.private(channelName).listen(
			".asset.returned.damaged",
			(event) => {
				console.log("Portal Echo: Asset returned damaged", event);

				// Dispatch Livewire event to update any asset components
				if (window.Livewire) {
					window.Livewire.dispatch("echo:asset-returned-damaged", event);
				}

				// Announce to screen readers
				const liveRegion = document.getElementById("aria-live-notifications");
				if (liveRegion) {
					liveRegion.textContent = `Asset ${event.asset_tag} returned damaged.`;
					setTimeout(() => {
						liveRegion.textContent = "";
					}, 5000);
				}
			}
		);
	};

	/**
	 * Unsubscribe from asset updates
	 */
	window.unsubscribeFromAssetUpdates = function (assetId) {
		const channelName = `asset.${assetId}`;

		if (!window.Echo) return;

		console.log(`Portal Echo: Unsubscribing from ${channelName}`);
		window.Echo.leave(channelName);
	};

	/**
	 * Listen for admin notifications (high-priority alerts, SLA breaches)
	 *
	 * Event: ticket.assigned, sla.breach, ticket.high-priority
	 * Channel: private-admin.notifications
	 *
	 * Only admin and superuser roles can subscribe to this channel.
	 *
	 * @trace Requirements 8.1, 8.2 - High-priority ticket broadcast, SLA breach notification
	 */
	const userRole = document.querySelector('meta[name="user-role"]')?.content;

	if (userRole && ["admin", "superuser"].includes(userRole)) {
		console.log(`Portal Echo: Initializing admin channel for ${userRole}`);

		window.Echo.private("admin.notifications")
			.listen(".ticket.assigned", (event) => {
				console.log("Portal Echo: Ticket assigned", event);

				if (window.Livewire) {
					window.Livewire.dispatch("echo:ticket-assigned", event);
				}

				// Show browser notification for ticket assignment
				if ("Notification" in window && Notification.permission === "granted") {
					new Notification("Ticket Assigned", {
						body: `Ticket #${event.ticket_number} has been assigned to ${
							event.assigned_to?.name || "you"
						}`,
						icon: "/images/motac-logo-32.png",
						badge: "/images/motac-logo-32.png",
						tag: `ticket-assigned-${event.ticket_id}`,
					});
				}

				announceNotification(
					"Ticket Assigned",
					`Ticket #${event.ticket_number} assigned`
				);
			})
			.listen(".ticket.high-priority", (event) => {
				console.log("Portal Echo: High-priority ticket created", event);

				if (window.Livewire) {
					window.Livewire.dispatch("echo:high-priority-ticket", event);
				}

				// Show urgent browser notification
				if ("Notification" in window && Notification.permission === "granted") {
					new Notification("⚠️ High Priority Ticket", {
						body: `New ${event.priority} ticket: ${
							event.subject || event.ticket_number
						}`,
						icon: "/images/motac-logo-32.png",
						badge: "/images/motac-logo-32.png",
						tag: `high-priority-${event.ticket_id}`,
						requireInteraction: true,
					});
				}

				announceNotification(
					"High Priority Ticket",
					`New ${event.priority} ticket created`
				);
			})
			.listen(".sla.breach", (event) => {
				console.log("Portal Echo: SLA breach detected", event);

				if (window.Livewire) {
					window.Livewire.dispatch("echo:sla-breach", event);
				}

				// Show urgent browser notification for SLA breach
				if ("Notification" in window && Notification.permission === "granted") {
					new Notification("🚨 SLA Breach Alert", {
						body: `Ticket #${event.ticket_number} has breached SLA`,
						icon: "/images/motac-logo-32.png",
						badge: "/images/motac-logo-32.png",
						tag: `sla-breach-${event.ticket_id}`,
						requireInteraction: true,
					});
				}

				announceNotification(
					"SLA Breach",
					`Ticket #${event.ticket_number} has breached SLA`
				);
			})
			.listen(".asset.overdue", (event) => {
				console.log("Portal Echo: Asset overdue", event);

				if (window.Livewire) {
					window.Livewire.dispatch("echo:asset-overdue", event);
				}

				// Show browser notification for overdue asset
				if ("Notification" in window && Notification.permission === "granted") {
					new Notification("Asset Overdue", {
						body: `Loan #${event.loan_reference} is overdue`,
						icon: "/images/motac-logo-32.png",
						badge: "/images/motac-logo-32.png",
						tag: `asset-overdue-${event.loan_id}`,
					});
				}

				announceNotification(
					"Asset Overdue",
					`Loan #${event.loan_reference} is overdue`
				);
			});
	}

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
 * @param {string} modelType - Model type from broadcast (HelpdeskTicket, LoanApplication, etc.)
 * @param {string|number} modelId - Model ID
 * @param {string} status - New status
 */
function announceStatusUpdate(modelType, modelId, status) {
	const liveRegion = document.getElementById("aria-live-notifications");

	if (liveRegion) {
		// Convert model type to friendly label (supports all models from UnifiedNotificationDispatcher)
		let typeLabel = "Submission";

		if (modelType === "HelpdeskTicket" || modelType === "Ticket") {
			typeLabel = "Helpdesk ticket";
		} else if (modelType === "LoanApplication" || modelType === "Loan") {
			typeLabel = "Loan application";
		} else if (modelType === "Asset") {
			typeLabel = "Asset";
		} else if (modelType === "Submission") {
			typeLabel = "Submission";
		}

		// Format status for screen readers (replace underscores/dashes with spaces)
		const readableStatus = status.replace(/[_-]/g, " ").toLowerCase();

		liveRegion.textContent = `${typeLabel} #${modelId} status updated to ${readableStatus}`;

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
 * Initialize Echo listeners when DOM is ready AND Echo is connected
 * Only initialize on authenticated pages (pages with user-id meta tag)
 */
function waitForEchoAndInitialize() {
	// Check if this is an authenticated page
	const userId = document.querySelector('meta[name="user-id"]')?.content;

	if (!userId) {
		// This is a guest page, don't initialize Portal Echo
		return;
	}

	if (
		window.Echo &&
		window.Echo.connector?.pusher?.connection?.state === "connected"
	) {
		initializePortalEcho();
	} else {
		// Wait for Echo connection event
		window.addEventListener(
			"echo:connected",
			() => {
				initializePortalEcho();
			},
			{ once: true }
		);
	}
}

if (document.readyState === "loading") {
	document.addEventListener("DOMContentLoaded", waitForEchoAndInitialize);
} else {
	waitForEchoAndInitialize();
}
