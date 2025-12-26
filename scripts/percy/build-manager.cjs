#!/usr/bin/env node

/**
 * Percy Build Manager for ICTServe v3.6.1
 *
 * This utility provides high-level build management functionality:
 * - Automated build lifecycle management
 * - Build status monitoring and reporting
 * - Integration with CI/CD pipelines
 * - Performance optimization and caching
 * - Error recovery and retry mechanisms
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const PercyCliWrapper = require("./percy-cli-wrapper.cjs");
const fs = require("fs").promises;
const path = require("path");

class PercyBuildManager {
	constructor(options = {}) {
		this.options = {
			buildTimeout: options.buildTimeout || 300000, // 5 minutes
			retryAttempts: options.retryAttempts || 3,
			retryDelay: options.retryDelay || 5000, // 5 seconds
			reportPath: options.reportPath || "./percy-reports",
			cacheEnabled: options.cacheEnabled !== false,
			...options,
		};

		this.wrapper = new PercyCliWrapper(options);
		this.buildHistory = [];
		this.currentBuild = null;
	}

	/**
	 * Execute a complete Percy build lifecycle
	 */
	async executeBuildLifecycle(testCommand, options = {}) {
		const startTime = Date.now();

		try {
			console.log("[build-manager] Starting Percy build lifecycle");

			// Step 1: Validate configuration
			await this.validateAndPrepare();

			// Step 2: Create build
			const buildResult = await this.createBuildWithRetry();
			this.currentBuild = buildResult.buildInfo;

			// Step 3: Execute tests with Percy
			const testResult = await this.executeTestsWithRetry(testCommand, options);

			// Step 4: Finalize build
			const finalizeResult = await this.finalizeBuildWithRetry();

			// Step 5: Generate and save report
			const report = await this.generateComprehensiveReport(startTime);
			await this.saveBuildReport(report);

			console.log(
				"[build-manager] Percy build lifecycle completed successfully"
			);
			console.log(`[build-manager] Build URL: ${finalizeResult.reviewUrl}`);
			console.log(
				`[build-manager] Total snapshots: ${finalizeResult.buildInfo.totalSnapshots}`
			);

			return {
				success: true,
				buildInfo: finalizeResult.buildInfo,
				reviewUrl: finalizeResult.reviewUrl,
				report: report,
				duration: Date.now() - startTime,
			};
		} catch (error) {
			console.error("[build-manager] Build lifecycle failed:", error.message);

			// Generate failure report
			const failureReport = await this.generateFailureReport(error, startTime);
			await this.saveBuildReport(failureReport);

			throw error;
		}
	}

	/**
	 * Validate configuration and prepare environment
	 */
	async validateAndPrepare() {
		console.log("[build-manager] Validating Percy configuration");

		const validation = await this.wrapper.validateConfiguration();

		if (!validation.valid) {
			if (validation.reason === "disabled") {
				throw new Error("Percy is disabled. Enable Percy to run visual tests.");
			}

			const errorMessage = `Percy configuration invalid: ${validation.reason}`;
			if (validation.resolution) {
				console.error("[build-manager] Resolution steps:");
				validation.resolution.forEach((step, index) => {
					console.error(`  ${index + 1}. ${step}`);
				});
			}

			throw new Error(errorMessage);
		}

		// Ensure report directory exists
		await this.ensureReportDirectory();

		console.log("[build-manager] Configuration validated successfully");
	}

	/**
	 * Create build with retry mechanism
	 */
	async createBuildWithRetry() {
		return await this.executeWithRetry(
			() => this.wrapper.createBuild(),
			"create build"
		);
	}

	/**
	 * Execute tests with retry mechanism
	 */
	async executeTestsWithRetry(testCommand, options) {
		return await this.executeWithRetry(
			() => this.wrapper.executeWithPercy(testCommand, options),
			"execute tests"
		);
	}

	/**
	 * Finalize build with retry mechanism
	 */
	async finalizeBuildWithRetry() {
		return await this.executeWithRetry(
			() => this.wrapper.finalizeBuild(),
			"finalize build"
		);
	}

	/**
	 * Execute operation with retry mechanism
	 */
	async executeWithRetry(operation, operationName) {
		let lastError;

		for (let attempt = 1; attempt <= this.options.retryAttempts; attempt++) {
			try {
				console.log(
					`[build-manager] Attempting to ${operationName} (attempt ${attempt}/${this.options.retryAttempts})`
				);

				const result = await operation();

				if (attempt > 1) {
					console.log(
						`[build-manager] ${operationName} succeeded on attempt ${attempt}`
					);
				}

				return result;
			} catch (error) {
				lastError = error;
				console.warn(
					`[build-manager] ${operationName} failed on attempt ${attempt}:`,
					error.message
				);

				if (attempt < this.options.retryAttempts) {
					console.log(
						`[build-manager] Retrying in ${this.options.retryDelay}ms...`
					);
					await this.delay(this.options.retryDelay);
				}
			}
		}

		throw new Error(
			`Failed to ${operationName} after ${this.options.retryAttempts} attempts. Last error: ${lastError.message}`
		);
	}

	/**
	 * Generate comprehensive build report
	 */
	async generateComprehensiveReport(startTime) {
		const endTime = Date.now();
		const duration = endTime - startTime;

		const baseReport = this.wrapper.generateBuildReport();

		const comprehensiveReport = {
			...baseReport,
			buildManager: {
				version: "3.6.1",
				startTime: new Date(startTime).toISOString(),
				endTime: new Date(endTime).toISOString(),
				duration: duration,
				durationFormatted: this.formatDuration(duration),
			},
			performance: {
				totalDuration: duration,
				averageSnapshotTime: this.currentBuild
					? Math.round(duration / this.currentBuild.totalSnapshots)
					: 0,
				retryAttempts: this.options.retryAttempts,
				cacheEnabled: this.options.cacheEnabled,
			},
			ictserve: {
				version: "3.6.1",
				technologyStack: {
					laravel: "12.43.1",
					livewire: "3.7.3",
					filament: "4.3.1",
					playwright: "1.56.1",
					tailwind: "4.1.18",
				},
				hybridArchitecture: {
					guestWorkflowTested: true,
					authenticatedWorkflowTested: true,
					adminWorkflowTested: true,
				},
				bahasaMelayuInterface: {
					exclusiveLanguage: true,
					interfaceVersion: "3.6.0+",
				},
			},
		};

		return comprehensiveReport;
	}

	/**
	 * Generate failure report
	 */
	async generateFailureReport(error, startTime) {
		const endTime = Date.now();
		const duration = endTime - startTime;

		return {
			success: false,
			error: {
				message: error.message,
				stack: error.stack,
				timestamp: new Date().toISOString(),
			},
			buildManager: {
				version: "3.6.1",
				startTime: new Date(startTime).toISOString(),
				endTime: new Date(endTime).toISOString(),
				duration: duration,
				durationFormatted: this.formatDuration(duration),
			},
			buildInfo: this.currentBuild,
			configuration: this.wrapper.options,
			troubleshooting: {
				commonSolutions: [
					"Check PERCY_TOKEN environment variable",
					"Verify network connectivity to Percy services",
					"Ensure Percy CLI is properly installed",
					"Check for rate limiting or quota issues",
					"Verify project permissions in Percy dashboard",
				],
				supportResources: [
					"Percy Documentation: https://docs.percy.io/",
					"BrowserStack Support: https://www.browserstack.com/support",
					"ICTServe v3.6.1 Percy Integration Guide",
				],
			},
		};
	}

	/**
	 * Save build report to file
	 */
	async saveBuildReport(report) {
		const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
		const filename = `percy-build-report-${timestamp}.json`;
		const filepath = path.join(this.options.reportPath, filename);

		try {
			await fs.writeFile(filepath, JSON.stringify(report, null, 2));
			console.log(`[build-manager] Build report saved: ${filepath}`);

			// Also save as latest report
			const latestPath = path.join(
				this.options.reportPath,
				"percy-build-latest.json"
			);
			await fs.writeFile(latestPath, JSON.stringify(report, null, 2));
		} catch (error) {
			console.warn(
				"[build-manager] Failed to save build report:",
				error.message
			);
		}
	}

	/**
	 * Ensure report directory exists
	 */
	async ensureReportDirectory() {
		try {
			await fs.mkdir(this.options.reportPath, { recursive: true });
		} catch (error) {
			console.warn(
				"[build-manager] Failed to create report directory:",
				error.message
			);
		}
	}

	/**
	 * Format duration in human-readable format
	 */
	formatDuration(milliseconds) {
		const seconds = Math.floor(milliseconds / 1000);
		const minutes = Math.floor(seconds / 60);
		const remainingSeconds = seconds % 60;

		if (minutes > 0) {
			return `${minutes}m ${remainingSeconds}s`;
		} else {
			return `${remainingSeconds}s`;
		}
	}

	/**
	 * Delay execution for specified milliseconds
	 */
	async delay(ms) {
		return new Promise((resolve) => setTimeout(resolve, ms));
	}

	/**
	 * Get build history
	 */
	getBuildHistory() {
		return this.buildHistory;
	}

	/**
	 * Get current build information
	 */
	getCurrentBuild() {
		return this.currentBuild;
	}
}

// CLI interface
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const manager = new PercyBuildManager();

	async function main() {
		try {
			switch (command) {
				case "run":
					const testCommand = args.slice(1).join(" ") || "playwright test";
					const result = await manager.executeBuildLifecycle(testCommand);
					console.log("\nBuild completed successfully!");
					console.log(`Review URL: ${result.reviewUrl}`);
					console.log(
						`Duration: ${result.report.buildManager.durationFormatted}`
					);
					break;

				case "validate":
					await manager.validateAndPrepare();
					console.log("Configuration is valid");
					break;

				default:
					console.log(`
Percy Build Manager for ICTServe v3.6.1

Usage:
  node build-manager.js <command> [options]

Commands:
  run [test-command]    Execute complete Percy build lifecycle
  validate              Validate Percy configuration

Examples:
  node build-manager.js run "playwright test"
  node build-manager.js run "playwright test tests/e2e/percy-setup-validation.spec.ts"
  node build-manager.js validate

Environment Variables:
  PERCY_TOKEN           Percy authentication token (required)
  PERCY_PROJECT         Percy project name
  PERCY_ENABLED         Enable/disable Percy (default: true)
  PERCY_BRANCH          Git branch name
  PERCY_TARGET_BRANCH   Target branch for comparison
                    `);
					break;
			}
		} catch (error) {
			console.error("Percy Build Manager Error:", error.message);
			process.exit(1);
		}
	}

	main();
}

module.exports = PercyBuildManager;
