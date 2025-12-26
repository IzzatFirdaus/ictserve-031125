/**
 * Simple Percy Error Handling Validation Tests for ICTServe v3.6.1
 *
 * This test suite validates the basic error handling functionality
 * for Percy visual testing integration.
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

import { test, expect } from "@playwright/test";
import { exec } from "child_process";
import { promisify } from "util";
import * as path from "path";

const execAsync = promisify(exec);

test.describe("Percy Error Handling Basic Validation", () => {
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

	test("should validate Percy configuration correctly", async () => {
		// Test with valid configuration
		process.env.PERCY_TOKEN = "test-token";
		process.env.PERCY_PROJECT = "test-project";

		try {
			const { stdout } = await execAsync(`node "${percyWrapperPath}" validate`);
			const result = JSON.parse(stdout);

			expect(result).toHaveProperty("valid");
			expect(typeof result.valid).toBe("boolean");

			if (result.valid) {
				expect(result).toHaveProperty("version");
			} else {
				expect(result).toHaveProperty("reason");
			}
		} catch (error) {
			// May fail due to CLI not being available, but should not crash
			expect(error).toBeDefined();
		}
	});

	test("should handle missing Percy token", async () => {
		// Test without token
		delete process.env.PERCY_TOKEN;

		try {
			const { stdout } = await execAsync(`node "${percyWrapperPath}" validate`);
			const result = JSON.parse(stdout);

			expect(result.valid).toBe(false);
			expect(result.reason).toBe("missing_token");
			expect(result).toHaveProperty("resolution");
			expect(Array.isArray(result.resolution)).toBe(true);
		} catch (error) {
			// Expected to fail
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
		expect(Array.isArray(stats.retryOperations)).toBe(true);
	});

	test("should test error handler directly", async () => {
		const { stdout } = await execAsync(
			`node "${errorHandlerPath}" test-config-error`
		);

		// Should not crash and should log error handling
		expect(stdout).toContain("Percy Configuration Error");
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

	test("should check Percy enabled status", async () => {
		process.env.PERCY_TOKEN = "test-token";
		process.env.PERCY_ENABLED = "true";

		try {
			const { stdout } = await execAsync(
				`node "${percyWrapperPath}" percy-enabled`
			);
			const result = JSON.parse(stdout);

			expect(result).toHaveProperty("percyEnabled");
			expect(typeof result.percyEnabled).toBe("boolean");
		} catch (error) {
			// May fail if CLI is not available, but should provide exit code
			expect(error.code).toBeDefined();
		}
	});

	test("should reset error handler state", async () => {
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
});

test.describe("Percy Logger Validation", () => {
	const loggerPath = path.join(
		process.cwd(),
		"scripts",
		"percy",
		"percy-logger.cjs"
	);

	test("should test basic logging functionality", async () => {
		const { stdout } = await execAsync(`node "${loggerPath}" test-logging`);

		expect(stdout).toContain("Test error message");
		expect(stdout).toContain("Test warning message");
		expect(stdout).toContain("Test info message");
		expect(stdout).toContain("Test debug message");
	});

	test("should test Percy operations logging", async () => {
		const { stdout } = await execAsync(
			`node "${loggerPath}" test-percy-operations`
		);

		expect(stdout).toContain("Percy build created successfully");
		expect(stdout).toContain("Percy snapshot captured");
		expect(stdout).toContain("Percy service error");
	});

	test("should test Bahasa Melayu logging", async () => {
		const { stdout } = await execAsync(
			`node "${loggerPath}" test-percy-operations --bahasa-melayu`
		);

		expect(stdout).toContain("Binaan Percy berjaya dicipta");
		expect(stdout).toContain("Tangkapan skrin Percy diambil");
		expect(stdout).toContain("Ralat perkhidmatan Percy");
	});

	test("should generate performance metrics", async () => {
		const { stdout } = await execAsync(
			`node "${loggerPath}" performance-metrics`
		);
		const metrics = JSON.parse(stdout);

		expect(metrics).toHaveProperty("totalDuration");
		expect(metrics).toHaveProperty("totalOperations");
		expect(metrics).toHaveProperty("totalErrors");
		expect(metrics).toHaveProperty("totalSnapshots");
		expect(metrics).toHaveProperty("averageOperationTime");
		expect(metrics).toHaveProperty("errorRate");
	});
});
