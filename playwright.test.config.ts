import { defineConfig, devices } from "@playwright/test";

/**
 * Temporary Playwright Configuration for Test Validation
 * Simplified config without webServer auto-start for debugging
 */
export default defineConfig({
	testDir: "./tests/e2e",
	fullyParallel: false, // Disable parallel for debugging
	forbidOnly: process.env["CI"] === "true",
	retries: 0, // No retries for debugging
	workers: 1, // Single worker for debugging
	reporter: [["list"]],
	use: {
		baseURL: "http://localhost:8000",
		trace: "on-first-retry",
		screenshot: "only-on-failure",
		video: "retain-on-failure",
		actionTimeout: 30000, // Reduced timeout
		navigationTimeout: 60000, // Reduced timeout
	},
	timeout: 120000, // Reduced timeout
	expect: {
		timeout: 10000, // Reduced timeout
	},
	projects: [
		{
			name: "chromium",
			use: { ...devices["Desktop Chrome"] },
		},
	],
	// Remove webServer to avoid auto-start issues
});
