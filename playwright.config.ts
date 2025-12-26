import { defineConfig, devices } from "@playwright/test";

/**
 * Playwright Configuration for ICTServe E2E Testing with Percy Integration
 * Best practices: parallelism optimization, trace debugging, comprehensive reporting, visual testing
 *
 * Trace: Research findings per Playwright v1.56.1 official docs
 * - trace: 'on-first-retry' (recommended for CI - lightweight debugging)
 * - fullyParallel: true (parallel execution within files for speed)
 * - workers: adjusted per environment (CI vs local development)
 * - retries: 2 on CI (resilience to temporary failures)
 *
 * Percy Integration:
 * - Percy snapshots are captured via @percy/playwright
 * - Configuration supports ICTServe v3.6.1 True Hybrid Architecture
 * - Responsive testing across multiple viewport sizes
 * - Bahasa Melayu interface visual validation
 * - Environment-based Percy enabling/disabling
 * - Optimized for visual regression testing workflows
 */

// Percy environment detection
const isPercyEnabled =
	process.env.PERCY_TOKEN && process.env.PERCY_TOKEN.length > 0;
const isCI = process.env.CI === "true";
const skipPercy = process.env.SKIP_PERCY === "true";
export default defineConfig({
	testDir: "./tests/e2e",
	/* Additional test directories for Percy-specific tests */
	testMatch: ["**/tests/e2e/**/*.spec.ts", "**/tests/percy/**/*.spec.ts"],
	/* Skip performance tests in dev environment (set SKIP_PERFORMANCE=true to skip) */
	testIgnore:
		process.env["SKIP_PERFORMANCE"] === "true"
			? ["**/performance/**", "**/*performance*.spec.ts"]
			: undefined,
	/* Run tests in parallel within files for faster execution */
	fullyParallel: true,
	/* Fail the build on CI if you accidentally left test.only in the source code */
	forbidOnly: process.env["CI"] === "true",
	/* Retry on CI only to reduce infrastructure costs */
	retries: process.env["CI"] ? 2 : 0,
	/* Adjust workers: 1 per worker on CI for isolation, local can use 2 for speed but limit for Laravel server load */
	workers: process.env["CI"] ? 1 : 2,
	/* Reporters: HTML (primary), JSON (CI), and list (terminal) */
	reporter: [
		["html"],
		["json", { outputFile: "test-results/results.json" }],
		["list"],
	],
	use: {
		/* Base URL for all page.goto() calls */
		baseURL: "http://127.0.0.1:8000",
		/* Trace viewer: captures actions, DOM snapshots, network for failed tests */
		trace: "on-first-retry",
		/* Screenshot only on failure to save space */
		screenshot: "only-on-failure",
		/* Video recording for visual debugging */
		video: "retain-on-failure",
		/* Action timeout: time to perform click, fill, etc. (increased from 45s to 60s) */
		actionTimeout: 60000,
		/* Navigation timeout: time for page loads (increased from 120s to 180s for Laravel server response) */
		navigationTimeout: 180000,

		/* Percy integration: Enhanced visual testing capabilities */
		extraHTTPHeaders: {
			// Percy integration headers
			"X-Percy-Test": isPercyEnabled ? "enabled" : "disabled",
			"X-Percy-Environment": isCI ? "ci" : "local",
			"X-ICTServe-Version": "3.6.1",
			"X-Laravel-Version": "12.43.1",
			"X-Livewire-Version": "3.7.3",
			"X-Filament-Version": "4.3.1",
		},

		/* Percy-optimized viewport configuration */
		viewport: isPercyEnabled
			? { width: 1280, height: 720 }
			: { width: 1280, height: 720 },

		/* Device scale factor for consistent Percy snapshots */
		deviceScaleFactor: 1,

		/* Locale for Bahasa Melayu interface testing */
		locale: "ms-MY",

		/* Timezone for consistent timestamp handling */
		timezoneId: "Asia/Kuala_Lumpur",

		/* Color scheme preference */
		colorScheme: "light",

		/* Reduced motion for consistent visual testing */
		reducedMotion: isPercyEnabled ? "reduce" : undefined,
	},

	/* Global timeout for all tests (5 minutes for comprehensive flows) */
	timeout: 300000,

	/* Expect timeout: time for assertions to pass (increased from 10s to 15s for auto-wait) */
	expect: {
		timeout: 15000,
	},

	/* Percy-specific global setup */
	globalSetup: isPercyEnabled
		? "./tests/percy/percy-global-setup.ts"
		: undefined,
	globalTeardown: isPercyEnabled
		? "./tests/percy/percy-global-teardown.ts"
		: undefined,

	projects: [
		/* Chrome 90+ - Primary browser for Percy visual testing */
		{
			name: "chromium",
			use: {
				...devices["Desktop Chrome"],
				// Percy-optimized Chrome configuration
				viewport: { width: 1280, height: 720 },
				deviceScaleFactor: 1,
				// headless: false, // Uncomment for debugging
			},
		},
		/* Firefox 88+ - Cross-browser testing with Percy */
		{
			name: "firefox",
			use: {
				...devices["Desktop Firefox"],
				// Percy-optimized Firefox configuration
				viewport: { width: 1280, height: 720 },
				deviceScaleFactor: 1,
			},
		},
		/* Safari 14+ - WebKit engine (macOS/iOS) with Percy support */
		{
			name: "webkit",
			use: {
				...devices["Desktop Safari"],
				// Percy-optimized Safari configuration
				viewport: { width: 1280, height: 720 },
				deviceScaleFactor: 1,
			},
		},
		/* Edge 90+ - Chromium-based Microsoft Edge with Percy */
		{
			name: "edge",
			use: {
				...devices["Desktop Edge"],
				channel: "msedge",
				// Percy-optimized Edge configuration
				viewport: { width: 1280, height: 720 },
				deviceScaleFactor: 1,
			},
		},

		/* Percy-specific responsive testing projects */
		...(isPercyEnabled
			? [
					{
						name: "percy-mobile",
						use: {
							...devices["iPhone 12"],
							viewport: { width: 375, height: 667 },
							deviceScaleFactor: 1,
						},
					},
					{
						name: "percy-tablet",
						use: {
							...devices["iPad Pro"],
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
			  ]
			: []),
	],

	/* Web server: Auto-start Laravel during test runs */
	webServer: {
		command: "php artisan serve --host=127.0.0.1 --port=8000",
		url: "http://127.0.0.1:8000",
		reuseExistingServer: !process.env["CI"],
		timeout: 180000, // 3 minutes for server startup
	},
});
