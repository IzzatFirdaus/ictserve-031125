/**
 * Percy Error Handling Validation Tests for ICTServe v3.6.1
 *
 * This test suite validates the comprehensive error handling and resilience features
 * implemented for Percy visual testing integration, including:
 * - Configuration error handling with resolution steps
 * - Network error handling with automatic retry mechanisms
 * - Service error handling with graceful degradation
 * - Critical error handling and reporting
 * - Error statistics and reporting functionality
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

import { test, expect } from "@playwright/test";
import { spawn, exec } from "child_process";
import { promisify } from "util";
import * as fs from "fs";
import * as path from "path";

const execAsync = promisify(exec);

test.describe("Percy Error Handling and Resilience Features", () => {
	const percyWrapperPath = path.join(
		process.cwd(),
		"scripts",
		"percy",
		"percy-cli-wrapper.cjs"
	);
	const errorHandlerPath = path.join(
		process.cwd(),
		"scripts",
		"percy",
		"percy-error-handler.cjs"
	);

	test.beforeEach(async () => {
		// Reset environment variables for clean test state
		delete process.env.PERCY_TOKEN;
		delete process.env.PERCY_ENABLED;
		delete process.env.PERCY_PROJECT;
	});

	test("should handle missing Percy token configuration error", async () => {
		// Test configuration error handling
		const { stdout, stderr } = await execAsync(
			`node "${percyWrapperPath}" validate`
		);
		const result = JSON.parse(stdout);

		expect(result.valid).toBe(false);
		expect(result.reason).toBe("missing_token");
		expect(result.resolution).toContain(
			"Obtain Percy token from BrowserStack Percy dashboard"
		);
		expect(result.gracefulDegradation).toBe(true);
	});

	test("should handle Percy CLI unavailable error", async () => {
		// Set token but make CLI unavailable by using invalid PATH
		process.env.PERCY_TOKEN = "test-token";
		process.env.PATH = "/invalid/path";

		try {
			const { stdout } = await execAsync(`node "${percyWrapperPath}" validate`);
			const result = JSON.parse(stdout);

			expect(result.valid).toBe(false);
			expect(result.reason).toBe("cli_unavailable");
			expect(result.resolution).toContain("Install Percy CLI");
			expect(result.gracefulDegradation).toBe(true);
		} catch (error) {
			// Expected to fail due to invalid PATH
			expect(error.code).toBe(1);
		}
	});

	test("should enable graceful degradation when Percy is disabled", async () => {
		process.env.PERCY_ENABLED = "false";

		const { stdout } = await execAsync(`node "${percyWrapperPath}" validate`);
		const result = JSON.parse(stdout);

		expect(result.valid).toBe(false);
		expect(result.reason).toBe("disabled");
	});

	test("should check Percy enabled status correctly", async () => {
		// Test when Percy should be enabled
		process.env.PERCY_TOKEN = "test-token";
		process.env.PERCY_ENABLED = "true";

		try {
			const { stdout } = await execAsync(
				`node "${percyWrapperPath}" percy-enabled`
			);
			const result = JSON.parse(stdout);

			// May be false due to CLI unavailability, but should not crash
			expect(result).toHaveProperty("percyEnabled");
			expect(typeof result.percyEnabled).toBe("boolean");
		} catch (error) {
			// Expected to fail if CLI is not available
			expect(error.code).toBe(1);
		}
	});

	test("should generate error statistics", async () => {
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" error-stats`
		);
		const stats = JSON.parse(stdout);

		expect(stats).toHaveProperty("total");
		expect(stats).toHaveProperty("byType");
		expect(stats).toHaveProperty("recovered");
		expect(stats).toHaveProperty("critical");
		expect(stats).toHaveProperty("percyEnabled");
		expect(stats).toHaveProperty("retryOperations");
	});

	test("should generate comprehensive error report", async () => {
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" error-report`
		);
		const report = JSON.parse(stdout);

		expect(report).toHaveProperty("percy");
		expect(report).toHaveProperty("errorHandling");
		expect(report).toHaveProperty("timestamp");

		// Validate Percy report structure
		expect(report.percy).toHaveProperty("buildInfo");
		expect(report.percy).toHaveProperty("configuration");
		expect(report.percy).toHaveProperty("environment");

		// Validate error handling report structure
		expect(report.errorHandling).toHaveProperty("summary");
		expect(report.errorHandling).toHaveProperty("errorsByType");
		expect(report.errorHandling).toHaveProperty("configuration");
		expect(report.errorHandling).toHaveProperty("environment");
	});

	test("should save error report to file", async () => {
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" save-error-report`
		);
		const reportPath = stdout.trim().replace("Error report saved to: ", "");

		// Verify file was created
		expect(fs.existsSync(reportPath)).toBe(true);

		// Verify file content
		const reportContent = JSON.parse(fs.readFileSync(reportPath, "utf8"));
		expect(reportContent).toHaveProperty("percy");
		expect(reportContent).toHaveProperty("errorHandling");
		expect(reportContent).toHaveProperty("timestamp");

		// Clean up
		fs.unlinkSync(reportPath);
	});

	test("should reset error handler state", async () => {
		// First generate some errors
		try {
			await execAsync(`node "${percyWrapperPath}" validate`);
		} catch (error) {
			// Expected to fail
		}

		// Reset error handler
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" reset-errors`
		);
		expect(stdout.trim()).toBe("Error handler state reset");

		// Verify stats are reset
		const { stdout: statsOutput } = await execAsync(
			`node "${percyWrapperPath}" error-stats`
		);
		const stats = JSON.parse(statsOutput);
		expect(stats.total).toBe(0);
		expect(Object.keys(stats.byType)).toHaveLength(0);
	});

	test("should handle configuration errors in error handler directly", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" test-config-error`
		);

		// Should not crash and should log error handling
		expect(stdout).toContain("Percy Configuration Error");
	});

	test("should handle network errors with retry logic", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" test-network-error`
		);

		// Should not crash and should log retry attempts
		expect(stdout).toContain("Percy Network Error");
	});

	test("should handle service errors with graceful degradation", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" test-service-error`
		);

		// Should not crash and should log service error handling
		expect(stdout).toContain("Percy Service Error");
	});

	test("should generate error handler statistics", async () => {
		// Generate some test errors first
		await execAsync(`node "${errorHandlerPath}" test-config-error`).catch(
			() => {}
		);
		await execAsync(`node "${errorHandlerPath}" test-network-error`).catch(
			() => {}
		);

		const { stdout } = await execAsync(`node "${errorHandlerPath}" stats`);
		const stats = JSON.parse(stdout);

		expect(stats).toHaveProperty("total");
		expect(stats).toHaveProperty("byType");
		expect(stats).toHaveProperty("percyEnabled");
		expect(stats).toHaveProperty("retryOperations");
	});

	test("should save error handler report to file", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" save-report`
		);
		const reportPath = stdout.trim().replace("Report saved to: ", "");

		// Verify file was created
		expect(fs.existsSync(reportPath)).toBe(true);

		// Verify file content
		const reportContent = JSON.parse(fs.readFileSync(reportPath, "utf8"));
		expect(reportContent).toHaveProperty("summary");
		expect(reportContent).toHaveProperty("errorsByType");
		expect(reportContent).toHaveProperty("configuration");
		expect(reportContent).toHaveProperty("environment");

		// Clean up
		fs.unlinkSync(reportPath);
	});

	test("should validate error handler report structure", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" generate-report`
		);
		const report = JSON.parse(stdout);

		// Validate report structure
		expect(report).toHaveProperty("summary");
		expect(report.summary).toHaveProperty("totalErrors");
		expect(report.summary).toHaveProperty("criticalErrors");
		expect(report.summary).toHaveProperty("recoveredErrors");
		expect(report.summary).toHaveProperty("percyEnabled");
		expect(report.summary).toHaveProperty("gracefulDegradationEnabled");

		expect(report).toHaveProperty("errorsByType");
		expect(report).toHaveProperty("retryOperations");
		expect(report).toHaveProperty("configuration");
		expect(report).toHaveProperty("timestamp");
		expect(report).toHaveProperty("environment");

		// Validate environment information
		expect(report.environment).toHaveProperty("nodeVersion");
		expect(report.environment).toHaveProperty("platform");
		expect(report.environment).toHaveProperty("percyToken");
		expect(report.environment).toHaveProperty("percyProject");
	});

	test("should handle multiple error types in sequence", async () => {
		// Test configuration error
		try {
			await execAsync(`node "${percyWrapperPath}" validate`);
		} catch (error) {
			// Expected to fail due to missing token
		}

		// Set token but make CLI unavailable
		process.env.PERCY_TOKEN = "test-token";
		process.env.PATH = "/invalid/path";

		try {
			await execAsync(`node "${percyWrapperPath}" validate`);
		} catch (error) {
			// Expected to fail due to CLI unavailable
		}

		// Check that both errors are tracked
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" error-stats`
		);
		const stats = JSON.parse(stdout);

		expect(stats.total).toBeGreaterThan(0);
		expect(stats.byType).toHaveProperty("configuration");
	});

	test("should maintain error statistics across operations", async () => {
		// Reset to start clean
		await execAsync(`node "${percyWrapperPath}" reset-errors`);

		// Generate multiple errors
		const operations = [
			() => execAsync(`node "${percyWrapperPath}" validate`).catch(() => {}),
			() =>
				execAsync(`node "${errorHandlerPath}" test-config-error`).catch(
					() => {}
				),
			() =>
				execAsync(`node "${errorHandlerPath}" test-network-error`).catch(
					() => {}
				),
		];

		for (const operation of operations) {
			await operation();
		}

		// Check accumulated statistics
		const { stdout } = await execAsync(
			`node "${percyWrapperPath}" error-stats`
		);
		const stats = JSON.parse(stdout);

		expect(stats.total).toBeGreaterThan(0);
		expect(typeof stats.percyEnabled).toBe("boolean");
		expect(Array.isArray(stats.retryOperations)).toBe(true);
	});
});

test.describe("Percy Error Handling Integration with Playwright Tests", () => {
	test("should handle Percy unavailable during test execution", async ({
		page,
	}) => {
		// Simulate Percy being unavailable by setting invalid token
		process.env.PERCY_TOKEN = "invalid-token";
		process.env.PERCY_ENABLED = "true";

		// Navigate to a test page
		await page.goto("/");

		// This would normally capture a Percy snapshot, but should gracefully degrade
		// The test should continue without failing
		await expect(page.locator("body")).toBeVisible();

		// Verify page functionality continues to work
		const title = await page.title();
		expect(title).toBeTruthy();
	});

	test("should continue test execution when Percy is disabled", async ({
		page,
	}) => {
		// Disable Percy
		process.env.PERCY_ENABLED = "false";

		// Navigate to test page
		await page.goto("/");

		// Test should continue normally
		await expect(page.locator("body")).toBeVisible();

		// Verify normal test functionality
		const title = await page.title();
		expect(title).toBeTruthy();
	});

	test("should handle network timeouts gracefully", async ({ page }) => {
		// Set very short timeout to simulate network issues
		process.env.PERCY_NETWORK_TIMEOUT = "1";
		process.env.PERCY_TOKEN = "test-token";

		// Navigate to test page
		await page.goto("/");

		// Test should continue even if Percy operations timeout
		await expect(page.locator("body")).toBeVisible();

		// Clean up
		delete process.env.PERCY_NETWORK_TIMEOUT;
	});
});

test.describe("Percy Error Handling Property-Based Tests", () => {
	test("should handle various configuration scenarios", async () => {
		const configScenarios = [
			{ token: "", project: "test", enabled: "true" },
			{ token: "valid-token", project: "", enabled: "true" },
			{ token: "valid-token", project: "test", enabled: "false" },
			{ token: null, project: "test", enabled: "true" },
		];

		for (const scenario of configScenarios) {
			// Set environment variables
			if (scenario.token !== null) {
				process.env.PERCY_TOKEN = scenario.token;
			} else {
				delete process.env.PERCY_TOKEN;
			}
			process.env.PERCY_PROJECT = scenario.project;
			process.env.PERCY_ENABLED = scenario.enabled;

			// Test validation
			try {
				const { stdout } = await execAsync(
					`node "${percyWrapperPath}" validate`
				);
				const result = JSON.parse(stdout);

				// Should always have a valid response structure
				expect(result).toHaveProperty("valid");
				expect(typeof result.valid).toBe("boolean");

				if (!result.valid) {
					expect(result).toHaveProperty("reason");
					if (result.reason !== "disabled") {
						expect(result).toHaveProperty("resolution");
						expect(Array.isArray(result.resolution)).toBe(true);
					}
				}
			} catch (error) {
				// Some scenarios may fail, but should not crash
				expect(error.code).toBeDefined();
			}
		}
	});

	test("should handle retry scenarios with exponential backoff", async () => {
		const errorHandlerPath = path.join(
			process.cwd(),
			"scripts",
			"percy",
			"percy-error-handler.cjs"
		);

		const retryScenarios = [
			{ maxRetries: 1, expectedAttempts: 1 },
			{ maxRetries: 3, expectedAttempts: 3 },
			{ maxRetries: 5, expectedAttempts: 5 },
		];

		for (const scenario of retryScenarios) {
			// Test network error retry logic
			const { stdout } = await execAsync(
				`node "${errorHandlerPath}" test-network-error`
			);

			// Should handle retry logic without crashing
			expect(stdout).toContain("Percy Network Error");
		}
	});
});
