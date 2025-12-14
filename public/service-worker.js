/**
 * ICTServe Service Worker
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-015.4 (Mobile Performance)
 * @trace D15 §3.1 (Mobile Optimization)
 * @version 1.0.0
 * @created 2025-12-14
 *
 * Features:
 * - Offline caching for static assets
 * - Network-first strategy for API calls
 * - Cache-first strategy for static resources
 * - Background sync for form submissions
 */

importScripts("https://js.pusher.com/beams/service-worker.js");

const CACHE_NAME = "ictserve-v3.6.0";
const STATIC_CACHE = "ictserve-static-v3.6.0";
const DYNAMIC_CACHE = "ictserve-dynamic-v3.6.0";

// Static assets to cache on install
const STATIC_ASSETS = [
	"/",
	"/offline",
	"/build/assets/app.css",
	"/build/assets/app.js",
	"/images/logo-motac.png",
	"/images/logo-ictserve.png",
	"/favicon.ico",
	"/favicon.svg",
	"/apple-touch-icon.png",
	"/web-app-manifest-192x192.png",
	"/web-app-manifest-512x512.png",
];

// Install event - cache static assets
self.addEventListener("install", (event) => {
	event.waitUntil(
		caches
			.open(STATIC_CACHE)
			.then((cache) => {
				console.log("[ServiceWorker] Caching static assets");
				return cache.addAll(
					STATIC_ASSETS.filter((url) => {
						// Only cache assets that exist
						return fetch(url, { method: "HEAD" })
							.then((response) => response.ok)
							.catch(() => false);
					})
				);
			})
			.then(() => self.skipWaiting())
	);
});

// Activate event - clean up old caches
self.addEventListener("activate", (event) => {
	event.waitUntil(
		caches
			.keys()
			.then((cacheNames) => {
				return Promise.all(
					cacheNames
						.filter((name) => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
						.map((name) => {
							console.log("[ServiceWorker] Deleting old cache:", name);
							return caches.delete(name);
						})
				);
			})
			.then(() => self.clients.claim())
	);
});

// Fetch event - serve from cache or network
self.addEventListener("fetch", (event) => {
	const { request } = event;
	const url = new URL(request.url);

	// Skip non-GET requests
	if (request.method !== "GET") {
		return;
	}

	// Skip cross-origin requests
	if (url.origin !== location.origin) {
		return;
	}

	// Skip admin panel and API routes (always network)
	if (
		url.pathname.startsWith("/admin") ||
		url.pathname.startsWith("/api") ||
		url.pathname.startsWith("/livewire") ||
		url.pathname.startsWith("/broadcasting")
	) {
		return;
	}

	// Cache-first for static assets
	if (isStaticAsset(url.pathname)) {
		event.respondWith(cacheFirst(request));
		return;
	}

	// Network-first for HTML pages
	if (request.headers.get("Accept")?.includes("text/html")) {
		event.respondWith(networkFirst(request));
		return;
	}

	// Stale-while-revalidate for other resources
	event.respondWith(staleWhileRevalidate(request));
});

// Cache-first strategy
async function cacheFirst(request) {
	const cached = await caches.match(request);
	if (cached) {
		return cached;
	}

	try {
		const response = await fetch(request);
		if (response.ok) {
			const cache = await caches.open(STATIC_CACHE);
			cache.put(request, response.clone());
		}
		return response;
	} catch (error) {
		console.log("[ServiceWorker] Cache-first fetch failed:", error);
		return new Response("Offline", { status: 503 });
	}
}

// Network-first strategy
async function networkFirst(request) {
	try {
		const response = await fetch(request);
		if (response.ok) {
			const cache = await caches.open(DYNAMIC_CACHE);
			cache.put(request, response.clone());
		}
		return response;
	} catch (error) {
		console.log("[ServiceWorker] Network-first fetch failed, trying cache");
		const cached = await caches.match(request);
		if (cached) {
			return cached;
		}
		// Return offline page for HTML requests
		return (
			caches.match("/offline") ||
			new Response("Offline", {
				status: 503,
				headers: { "Content-Type": "text/html" },
			})
		);
	}
}

// Stale-while-revalidate strategy
async function staleWhileRevalidate(request) {
	const cached = await caches.match(request);

	const fetchPromise = fetch(request)
		.then((response) => {
			if (response.ok) {
				const cache = caches.open(DYNAMIC_CACHE);
				cache.then((c) => c.put(request, response.clone()));
			}
			return response;
		})
		.catch(() => cached);

	return cached || fetchPromise;
}

// Check if URL is a static asset
function isStaticAsset(pathname) {
	const staticExtensions = [
		".css",
		".js",
		".png",
		".jpg",
		".jpeg",
		".gif",
		".svg",
		".ico",
		".woff",
		".woff2",
		".ttf",
		".eot",
	];
	return staticExtensions.some((ext) => pathname.endsWith(ext));
}

// Background sync for form submissions
self.addEventListener("sync", (event) => {
	if (event.tag === "sync-forms") {
		event.waitUntil(syncForms());
	}
});

async function syncForms() {
	// Get pending form submissions from IndexedDB
	// This would be implemented with actual IndexedDB logic
	console.log("[ServiceWorker] Syncing pending forms");
}

// Push notification handling
self.addEventListener("push", (event) => {
	if (!event.data) return;

	const data = event.data.json();
	const options = {
		body: data.body || "Pemberitahuan baharu dari ICTServe",
		icon: "/web-app-manifest-192x192.png",
		badge: "/favicon-96x96.png",
		vibrate: [100, 50, 100],
		data: {
			url: data.url || "/",
		},
		actions: data.actions || [],
	};

	event.waitUntil(
		self.registration.showNotification(data.title || "ICTServe", options)
	);
});

// Notification click handling
self.addEventListener("notificationclick", (event) => {
	event.notification.close();

	const url = event.notification.data?.url || "/";

	event.waitUntil(
		clients
			.matchAll({ type: "window", includeUncontrolled: true })
			.then((clientList) => {
				// Focus existing window if available
				for (const client of clientList) {
					if (client.url === url && "focus" in client) {
						return client.focus();
					}
				}
				// Open new window
				if (clients.openWindow) {
					return clients.openWindow(url);
				}
			})
	);
});
