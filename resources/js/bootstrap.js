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
} else {
    // Echo not initialized - Reverb not configured
    // This prevents Pusher errors when broadcasting is not set up
    window.Echo = null;
    if (import.meta.env.DEV) {
        console.warn(
            "Laravel Echo not initialized: REVERB environment variables not configured. " +
            "Real-time features will be disabled."
        );
    }
}
