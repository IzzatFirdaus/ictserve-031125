import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Laravel Echo Configuration
 *
 * Echo allows you to easily build real-time event-driven applications.
 * We'll use Laravel Reverb as the WebSocket server for broadcasting.
 *
 * @trace D03 SRS-FR-008, D04 §5.3 (Requirements 6.1, 6.2)
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
        wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
        wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https",
        enabledTransports: ["ws", "wss"],
        disableStats: true,
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
            if (window.echoConnectionState.reconnectAttempts < window.echoConnectionState.maxReconnectAttempts) {
                window.echoConnectionState.reconnecting = true;
                window.echoConnectionState.reconnectAttempts++;

                // Calculate backoff delay (exponential with jitter)
                const baseDelay = window.echoConnectionState.reconnectDelay;
                const exponentialDelay = Math.min(
                    baseDelay * Math.pow(2, window.echoConnectionState.reconnectAttempts - 1),
                    window.echoConnectionState.maxReconnectDelay
                );
                const jitter = Math.random() * 1000; // Add 0-1s jitter
                const delay = exponentialDelay + jitter;

                console.log(`Echo: Reconnecting in ${Math.round(delay / 1000)}s (attempt ${window.echoConnectionState.reconnectAttempts}/${window.echoConnectionState.maxReconnectAttempts})`);

                setTimeout(() => {
                    pusher.connect();
                }, delay);

                showReconnectionToast(`Reconnecting... (attempt ${window.echoConnectionState.reconnectAttempts})`);
            } else {
                console.error("Echo: Max reconnection attempts reached. Giving up.");
                showReconnectionToast("Unable to connect. Please refresh the page.", true);
            }
        });

        // State change handler
        pusher.connection.bind("state_change", (states) => {
            console.log(`Echo: State changed from ${states.previous} to ${states.current}`);
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
    reconnectionToastElement.className = "fixed bottom-4 right-4 z-50 max-w-sm bg-yellow-50 border-l-4 border-yellow-400 p-4 shadow-lg rounded-md";
    reconnectionToastElement.setAttribute("role", "status");
    reconnectionToastElement.setAttribute("aria-live", "polite");

    const iconColor = isPermanent ? "text-red-400" : "text-yellow-400";
    const icon = isPermanent 
        ? '<svg class="h-5 w-5 ' + iconColor + '" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>'
        : '<svg class="animate-spin h-5 w-5 ' + iconColor + '" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    reconnectionToastElement.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                ${icon}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium ${isPermanent ? 'text-red-800' : 'text-yellow-800'}">
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
