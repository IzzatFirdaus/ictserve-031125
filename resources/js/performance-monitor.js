/**
 * Core Web Vitals Performance Monitor
 *
 * Tracks and reports Core Web Vitals metrics for the authenticated portal:
 * - LCP (Largest Contentful Paint): Target <2.5s
 * - FID (First Input Delay): Target <100ms
 * - CLS (Cumulative Layout Shift): Target <0.1
 * - TTFB (Time to First Byte): Target <600ms
 *
 * Metrics are sent to the backend for analysis and monitoring.
 *
 * @see D12 §9 Performance optimization patterns
 * @see D13 §6 Performance monitoring
 *
 * @requirements 13.5 Core Web Vitals optimization
 *
 * @version 1.0.0
 *
 * @created 2025-11-06
 *
 * @author Frontend Engineering Team
 */

/**
 * Send metric to backend analytics endpoint
 *
 * @param {Object} metric - Web Vitals metric object
 */
function sendToAnalytics(metric) {
    const body = JSON.stringify({
        name: metric.name,
        value: metric.value,
        rating: metric.rating,
        delta: metric.delta,
        id: metric.id,
        page: window.location.pathname,
        timestamp: Date.now(),
    });

    // Use sendBeacon if available (non-blocking)
    if (navigator.sendBeacon) {
        navigator.sendBeacon("/api/analytics/web-vitals", body);
    } else {
        // Fallback to fetch with keepalive
        fetch("/api/analytics/web-vitals", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body,
            keepalive: true,
        }).catch(console.error);
    }

    // Log to console in development
    if (import.meta.env.DEV) {
        console.log(`[Web Vitals] ${metric.name}:`, {
            value: metric.value,
            rating: metric.rating,
            target: getTarget(metric.name),
        });
    }
}

/**
 * Get target value for metric
 *
 * @param {string} name - Metric name
 * @returns {number} Target value in milliseconds or ratio
 */
function getTarget(name) {
    const targets = {
        LCP: 2500, // 2.5 seconds
        FID: 100, // 100 milliseconds
        CLS: 0.1, // 0.1 ratio
        TTFB: 600, // 600 milliseconds
    };
    return targets[name] || 0;
}

/**
 * Initialize performance monitoring
 * Dynamically imports web-vitals library and sets up listeners
 */
export function initPerformanceMonitoring() {
    // Only monitor in production or when explicitly enabled
    if (
        import.meta.env.PROD ||
        import.meta.env.VITE_ENABLE_PERFORMANCE_MONITORING === "true"
    ) {
        import("web-vitals")
            .then(({ onCLS, onFID, onLCP, onTTFB }) => {
                onCLS(sendToAnalytics);
                onFID(sendToAnalytics);
                onLCP(sendToAnalytics);
                onTTFB(sendToAnalytics);
            })
            .catch((error) => {
                console.error(
                    "[Performance Monitor] Failed to load web-vitals:",
                    error
                );
            });
    }
}

/**
 * Track custom performance marks
 *
 * @param {string} name - Mark name
 */
export function markPerformance(name) {
    if (performance && performance.mark) {
        performance.mark(name);
    }
}

/**
 * Measure performance between two marks
 *
 * @param {string} name - Measure name
 * @param {string} startMark - Start mark name
 * @param {string} endMark - End mark name
 * @returns {number|null} Duration in milliseconds
 */
export function measurePerformance(name, startMark, endMark) {
    if (performance && performance.measure) {
        try {
            performance.measure(name, startMark, endMark);
            const measure = performance.getEntriesByName(name)[0];
            return measure ? measure.duration : null;
        } catch (error) {
            console.error("[Performance Monitor] Measurement failed:", error);
            return null;
        }
    }
    return null;
}

/**
 * Clear performance marks and measures
 */
export function clearPerformanceMarks() {
    if (performance) {
        performance.clearMarks();
        performance.clearMeasures();
    }
}

// Auto-initialize on module load
if (typeof window !== "undefined") {
    // Wait for DOM to be ready
    if (document.readyState === "loading") {
        document.addEventListener(
            "DOMContentLoaded",
            initPerformanceMonitoring
        );
    } else {
        initPerformanceMonitoring();
    }
}
