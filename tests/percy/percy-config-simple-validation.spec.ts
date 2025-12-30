/**
 * Simple Percy Configuration Validation Test
 *
 * This test validates the Playwright configuration updates for Percy
 * without requiring a running web server.
 */

import { test, expect } from "@playwright/test";
import { getPercyEnvironment, isPercyEnabled } from "./percy-utils";

test.describe("Percy Configuration Simple Validation", () => {
	test("Percy environment detection works correctly", async () => {
		const percyEnv = getPercyEnvironment();

		// Validate environment object structure
		expect(percyEnv).toHaveProperty("enabled");
		expect(percyEnv).toHaveProperty("token");
		expect(percyEnv).toHaveProperty("project");
		expect(percyEnv).toHaveProperty("branch");
		expect(percyEnv).toHaveProperty("build");

		// Validate default values
		expect(percyEnv.project).toBe("ictserve");
		expect(percyEnv.branch).toBe("develop");
		expect(percyEnv.build).toBe("playwright-build");

		console.log("✅ Percy environment configuration is valid");
		console.log("Percy Environment:", percyEnv);
	});

	test("Percy utilities are properly configured", async () => {
		const enabled = isPercyEnabled();

		// Should be boolean
		expect(typeof enabled).toBe("boolean");

		// Log current state
		if (enabled) {
			console.log("✅ Percy is enabled and configured");
			expect(process.env.PERCY_TOKEN).toBeDefined();
		} else {
			console.log("ℹ️  Percy is disabled or not configured");
			console.log("   This is expected in development without PERCY_TOKEN");
		}
	});

	test("Playwright configuration constants are properly set", async ({
		browserName,
	}) => {
		// Validate browser name is available
		expect(browserName).toBeDefined();
		// Updated to only expect chromium for screenshot-only configuration
		expect(["chromium"].includes(browserName)).toBe(true);

		console.log(`✅ Running on browser: ${browserName}`);
		console.log("✅ Playwright configuration is properly loaded");
		console.log("ℹ️  Configuration updated for Chrome-only screenshots");
	});

	test("Percy configuration file structure is valid", async () => {
		// Test that we can import Percy config without errors
		try {
			const percyConfig = await import("../../percy.config.js");

			// Validate basic structure
			expect(percyConfig.default).toBeDefined();
			expect(percyConfig.default.version).toBe(2);
			expect(percyConfig.default.projectName).toBe("ictserve");
			expect(percyConfig.default.snapshot).toBeDefined();
			expect(percyConfig.default.snapshot.widths).toEqual([
				375, 768, 1024, 1280, 1920,
			]);

			console.log("✅ Percy configuration file is valid");
		} catch (error) {
			console.error("❌ Percy configuration file has issues:", error);
			throw error;
		}
	});
});
