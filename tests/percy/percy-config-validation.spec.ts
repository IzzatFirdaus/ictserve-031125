/**
 * Percy Configuration Validation Test for ICTServe v3.6.1
 *
 * This test validates that the updated Playwright configuration
 * works correctly with Percy visual testing integration.
 */

import { test, expect } from "@playwright/test";
import {
	takeICTServeSnapshot,
	takeResponsiveSnapshots,
	isPercyEnabled,
	getPercyEnvironment,
} from "./percy-utils";

test.describe("Percy Configuration Validation", () => {
	test.beforeEach(async ({ page }) => {
		// Log Percy environment information
		const percyEnv = getPercyEnvironment();
		console.log("Percy Environment:", percyEnv);
	});

	test("Percy configuration is properly loaded", async ({ page }) => {
		// Verify Percy environment
		const percyEnabled = isPercyEnabled();
		console.log(`Percy enabled: ${percyEnabled}`);

		if (percyEnabled) {
			expect(process.env.PERCY_TOKEN).toBeDefined();
			console.log("✅ Percy token is configured");
		} else {
			console.log("ℹ️  Percy is disabled or not configured");
		}
	});

	test("Basic Percy snapshot with updated configuration", async ({ page }) => {
		// Navigate to homepage
		await page.goto("/");

		// Wait for page to load completely
		await page.waitForLoadState("networkidle");

		// Take a basic Percy snapshot using the updated configuration
		await takeICTServeSnapshot(page, {
			name: "Homepage - Configuration Validation",
			widths: [1280],
			minHeight: 720,
			waitTime: 1000,
		});

		// Verify page loaded correctly
		await expect(page).toHaveTitle(/ICTServe/);
	});

	test("Responsive snapshots with updated viewport configuration", async ({
		page,
	}) => {
		// Navigate to a responsive page
		await page.goto("/");
		await page.waitForLoadState("networkidle");

		// Take responsive snapshots using the updated configuration
		await takeResponsiveSnapshots(
			page,
			"Homepage Responsive - Config Validation",
			{
				waitTime: 1000,
			}
		);

		// Verify responsive elements are present
		const mainContent = page.locator("main, #app, .container").first();
		await expect(mainContent).toBeVisible();
	});

	test("Percy configuration with Bahasa Melayu interface", async ({ page }) => {
		// Navigate to homepage
		await page.goto("/");
		await page.waitForLoadState("networkidle");

		// Take snapshot with Bahasa Melayu validation
		await takeICTServeSnapshot(page, {
			name: "Homepage - Bahasa Melayu Config Validation",
			validateBahasaMelayu: true,
			widths: [1280],
			minHeight: 720,
			waitTime: 1000,
		});

		// Verify Bahasa Melayu content is present
		const body = await page.textContent("body");
		expect(body).toBeTruthy();
	});

	test("Percy configuration with user type simulation", async ({ page }) => {
		// Navigate to homepage
		await page.goto("/");
		await page.waitForLoadState("networkidle");

		// Take snapshot simulating guest user
		await takeICTServeSnapshot(page, {
			name: "Homepage - Guest User Config Validation",
			userType: "guest",
			widths: [1280],
			minHeight: 720,
			waitTime: 1000,
		});

		// Verify page is accessible for guest users
		const guestElements = page
			.locator(".guest-accessible, .public-content, main")
			.first();
		await expect(guestElements).toBeVisible();
	});

	test("Percy configuration performance validation", async ({ page }) => {
		const startTime = Date.now();

		// Navigate to homepage
		await page.goto("/");
		await page.waitForLoadState("networkidle");

		// Take snapshot and measure performance
		await takeICTServeSnapshot(page, {
			name: "Homepage - Performance Config Validation",
			widths: [1280],
			minHeight: 720,
			waitTime: 500, // Reduced wait time for performance test
		});

		const endTime = Date.now();
		const duration = endTime - startTime;

		console.log(`Percy snapshot completed in ${duration}ms`);

		// Verify reasonable performance (should complete within 30 seconds)
		expect(duration).toBeLessThan(30000);
	});
});
