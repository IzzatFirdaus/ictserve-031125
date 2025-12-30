import { defineConfig, devices } from "@playwright/test";

/**
 * Percy-Specific Playwright Configuration for ICTServe v3.6.1
 *
 * This configuration is optimized specifically for Percy visual testing:
 * - Reduced parallelism for consistent snapshots
 * - Optimized viewport sizes for visual regression testing
 * - Enhanced stability settings for reliable visual comparisons
 * - ICTServe v3.6.1 specific optimizations
 */

export default defineConfig({
	testDir: "./tests/percy",
	/* Percy-specific test patterns */
	testMatch: ["**/tests/percy/**/*.spec.ts"],
	/* Disable parallelism for consistent Percy snapshots */
	fullyParallel: false,
	/* No retries for Percy tests to avoid duplicate snapshots */
	retries: 0,
	/* Single worker for consistent visual testing */
	workers: 1,
	/* Minimal reporting for Percy-focused runs */
	reporter: [
		["list"],
		["json", { outputFile: "test-results/percy-results.json" }],
	],

	use: {
		/* Base URL for all page.goto() calls */
		baseURL: "http://127.0.0.1:8000",
		/* No trace for Percy-only runs to improve performance */
		trace: "off",
		/* No screenshots - Percy handles visual capture */
		screenshot: "off",
		/* No video for Percy runs */
		video: "off",
		/* Increased timeouts for stable Percy snapshots */
		actionTimeout: 30000,
		navigationTimeout: 60000,

		/* Percy-optimized headers */
		extraHTTPHeaders: {
			"X-Percy-Test": "enabled",
			"X-Percy-Mode": "visual-testing-only",
			"X-ICTServe-Version": "3.6.1",
		},

		/* Consistent viewport for baseline snapshots */
		viewport: { width: 1280, height: 720 },
		deviceScaleFactor: 1,

		/* Bahasa Melayu locale */
		locale: "ms-MY",
		timezoneId: "Asia/Kuala_Lumpur",

		/* Consistent visual settings */
		colorScheme: "light",
		reducedMotion: "reduce",

		/* Wait for network idle for Livewire components */
		waitForLoadState: "networkidle",
	},

	/* Extended timeout for Percy snapshot processing */
	timeout: 120000,
	expect: { timeout: 10000 },

	/* Percy global setup and teardown */
	globalSetup: "./tests/percy/percy-global-setup.ts",
	globalTeardown: "./tests/percy/percy-global-teardown.ts",

	projects: [
		/* Primary Percy testing on Chrome - ONLY BROWSER FOR SCREENSHOTS */
		{
			name: "percy-chrome",
			use: {
				...devices["Desktop Chrome"],
				viewport: { width: 1280, height: 720 },
				deviceScaleFactor: 1,
			},
		},

		/* Responsive Percy testing - Chrome only for screenshots */
		{
			name: "percy-mobile",
			use: {
				...devices["Desktop Chrome"], // Changed from iPhone 12 to Chrome for consistency
				viewport: { width: 375, height: 667 },
				deviceScaleFactor: 1,
			},
		},
		{
			name: "percy-tablet",
			use: {
				...devices["Desktop Chrome"], // Changed from iPad Pro to Chrome for consistency
				viewport: { width: 768, height: 1024 },
				deviceScaleFactor: 1,
			},
		},
		{
			name: "percy-desktop-wide",
			use: {
				...devices["Desktop Chrome"],
				viewport: { width: 1920, height: 1080 },
				deviceScaleFactor: 1,
			},
		},
	],

	/* Web server with optimized settings for Percy */
	webServer: {
		command: "php artisan serve --host=127.0.0.1 --port=8000",
		url: "http://127.0.0.1:8000",
		reuseExistingServer: true,
		timeout: 120000,
	},
});
