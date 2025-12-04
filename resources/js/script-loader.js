/**
 * Script Loader Utility
 *
 * Optimizes JavaScript loading for FID (First Input Delay) optimization.
 * Defers non-critical scripts and uses requestIdleCallback for low-priority tasks.
 *
 * Target: FID <100ms per Requirement 10.2
 *
 * @trace Requirements: 10.2 (FID optimization)
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
 */

/**
 * Load a script asynchronously
 *
 * @param {string} src - Script source URL
 * @param {Object} options - Loading options
 * @param {boolean} options.async - Load asynchronously (default: true)
 * @param {boolean} options.defer - Defer execution (default: false)
 * @param {string} options.type - Script type (default: 'text/javascript')
 * @returns {Promise<void>}
 */
export function loadScript(src, options = {}) {
	return new Promise((resolve, reject) => {
		const script = document.createElement("script");
		script.src = src;
		script.async = options.async !== false;
		script.defer = options.defer === true;
		script.type = options.type || "text/javascript";

		script.onload = () => resolve();
		script.onerror = () => reject(new Error(`Failed to load script: ${src}`));

		document.head.appendChild(script);
	});
}

/**
 * Execute callback when browser is idle
 * Falls back to setTimeout for browsers without requestIdleCallback
 *
 * @param {Function} callback - Function to execute
 * @param {Object} options - Options for requestIdleCallback
 * @param {number} options.timeout - Maximum wait time in ms (default: 2000)
 */
export function whenIdle(callback, options = {}) {
	const timeout = options.timeout || 2000;

	if ("requestIdleCallback" in window) {
		window.requestIdleCallback(callback, { timeout });
	} else {
		// Fallback for Safari and older browsers
		setTimeout(callback, 1);
	}
}

/**
 * Defer non-critical initialization
 * Waits for user interaction or idle time before executing
 *
 * @param {Function} callback - Function to execute
 */
export function deferInit(callback) {
	const events = ["mouseover", "touchstart", "scroll", "keydown"];
	let executed = false;

	const execute = () => {
		if (executed) return;
		executed = true;

		// Remove all event listeners
		events.forEach((event) => {
			document.removeEventListener(event, execute, { passive: true });
		});

		// Execute callback
		callback();
	};

	// Execute on first user interaction
	events.forEach((event) => {
		document.addEventListener(event, execute, {
			once: true,
			passive: true,
		});
	});

	// Or execute when idle (fallback)
	whenIdle(execute, { timeout: 5000 });
}

/**
 * Chunk long-running tasks to prevent blocking main thread
 * Yields to browser between chunks to maintain responsiveness
 *
 * @param {Array} items - Items to process
 * @param {Function} processor - Function to process each item
 * @param {number} chunkSize - Items per chunk (default: 5)
 * @returns {Promise<void>}
 */
export async function processInChunks(items, processor, chunkSize = 5) {
	for (let i = 0; i < items.length; i += chunkSize) {
		const chunk = items.slice(i, i + chunkSize);

		// Process chunk
		for (const item of chunk) {
			await processor(item);
		}

		// Yield to browser
		await new Promise((resolve) => {
			if ("requestIdleCallback" in window) {
				window.requestIdleCallback(resolve, { timeout: 100 });
			} else {
				setTimeout(resolve, 0);
			}
		});
	}
}

/**
 * Debounce function to limit execution frequency
 * Helps prevent excessive event handler calls
 *
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @param {boolean} immediate - Execute immediately on first call
 * @returns {Function}
 */
export function debounce(func, wait, immediate = false) {
	let timeout;

	return function executedFunction(...args) {
		const context = this;

		const later = () => {
			timeout = null;
			if (!immediate) func.apply(context, args);
		};

		const callNow = immediate && !timeout;

		clearTimeout(timeout);
		timeout = setTimeout(later, wait);

		if (callNow) func.apply(context, args);
	};
}

/**
 * Throttle function to limit execution rate
 * Ensures function is called at most once per interval
 *
 * @param {Function} func - Function to throttle
 * @param {number} limit - Minimum time between calls in ms
 * @returns {Function}
 */
export function throttle(func, limit) {
	let inThrottle;

	return function executedFunction(...args) {
		const context = this;

		if (!inThrottle) {
			func.apply(context, args);
			inThrottle = true;
			setTimeout(() => (inThrottle = false), limit);
		}
	};
}

/**
 * Initialize FID optimizations
 * Sets up passive event listeners and deferred loading
 */
export function initFIDOptimizations() {
	// Make scroll and touch events passive by default
	const passiveSupported = (() => {
		let supported = false;
		try {
			const options = {
				get passive() {
					supported = true;
					return false;
				},
			};
			window.addEventListener("test", null, options);
			window.removeEventListener("test", null, options);
		} catch (e) {
			supported = false;
		}
		return supported;
	})();

	if (passiveSupported) {
		// Override addEventListener to use passive by default for scroll/touch
		const originalAddEventListener = EventTarget.prototype.addEventListener;
		EventTarget.prototype.addEventListener = function (
			type,
			listener,
			options
		) {
			const passiveEvents = ["scroll", "touchstart", "touchmove", "wheel"];
			if (passiveEvents.includes(type) && typeof options !== "object") {
				options = { passive: true };
			}
			return originalAddEventListener.call(this, type, listener, options);
		};
	}

	// Log FID optimization status in development
	if (import.meta.env.DEV) {
		console.log("[FID Optimization] Initialized with passive event support");
	}
}

// Auto-initialize on module load
if (typeof window !== "undefined") {
	initFIDOptimizations();
}
