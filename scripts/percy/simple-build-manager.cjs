#!/usr/bin/env node

/**
 * Simple Percy Build Manager for ICTServe v3.6.1
 *
 * This utility provides simplified build management using Percy exec:
 * - Automated test execution with Percy
 * - Build status reporting
 * - Error handling and logging
 * - Performance monitoring
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const { spawn } = require("child_process");
const fs = require("fs").promises;
const path = require("path");

class SimplePercyBuildManager {
	constructor(options = {}) {
		this.options = {
			reportPath: options.reportPath || "./percy-reports",
			timeout: options.timeout || 300000, // 5 minutes
			...options,
		};
	}

	/**
	 * Execute tests with Percy using percy exec
	 */
	async executeWithPercy(testCommand) {
		const startTime = Date.now();

		console.log("[simple-build-manager] Starting Percy test execution");
		console.log(`[simple-build-manager] Command: ${testCommand}`);

		try {
			// Validate Percy configuration first
			await this.validatePercy();

			// Execute tests with Percy
			const result = await this.runPercyExec(testCommand);

			// Generate report
			const report = await this.generateReport(startTime, result);
			await this.saveReport(report);

			console.log(
				"[simple-build-manager] Percy test execution completed successfully"
			);

			return {
				success: true,
				duration: Date.now() - startTime,
				report: report,
			};
		} catch (error) {
			console.error(
				"[simple-build-manager] Percy test execution failed:",
				error.message
			);

			// Generate failure report
			const failureReport = await this.generateFailureReport(error, startTime);
			await this.saveReport(failureReport);

			throw error;
		}
	}

	/**
	 * Validate Percy configuration
	 */
	async validatePercy() {
		const token = process.env.PERCY_TOKEN;

		if (!token) {
			throw new Error("PERCY_TOKEN environment variable is not set");
		}

		console.log("[simple-build-manager] Percy configuration validated");
	}

	/**
	 * Run percy exec command
	 */
	async runPercyExec(testCommand) {
		return new Promise((resolve, reject) => {
			const command = `npx percy exec -- ${testCommand}`;
			console.log(`[simple-build-manager] Executing: ${command}`);

			const child = spawn(command, [], {
				shell: true,
				stdio: "pipe",
				env: process.env,
			});

			let stdout = "";
			let stderr = "";

			child.stdout.on("data", (data) => {
				const output = data.toString();
				stdout += output;
				console.log(output);
			});

			child.stderr.on("data", (data) => {
				const output = data.toString();
				stderr += output;
				console.error(output);
			});

			child.on("close", (code) => {
				if (code === 0) {
					resolve({ stdout, stderr, code });
				} else {
					reject(new Error(`Percy exec failed with code ${code}: ${stderr}`));
				}
			});

			child.on("error", (error) => {
				reject(error);
			});

			// Set timeout
			setTimeout(() => {
				child.kill();
				reject(
					new Error(`Percy exec timed out after ${this.options.timeout}ms`)
				);
			}, this.options.timeout);
		});
	}

	/**
	 * Generate success report
	 */
	async generateReport(startTime, result) {
		const endTime = Date.now();
		const duration = endTime - startTime;

		// Parse Percy output for build information
		const buildUrl = this.extractBuildUrl(result.stdout);
		const snapshotCount = this.extractSnapshotCount(result.stdout);

		return {
			success: true,
			timestamp: new Date().toISOString(),
			duration: duration,
			durationFormatted: this.formatDuration(duration),
			percy: {
				buildUrl: buildUrl,
				snapshotCount: snapshotCount,
				output: result.stdout,
			},
			ictserve: {
				version: "3.6.1",
				technologyStack: {
					laravel: "12.43.1",
					livewire: "3.7.3",
					filament: "4.3.1",
					playwright: "1.56.1",
				},
			},
			environment: {
				nodeVersion: process.version,
				platform: process.platform,
				arch: process.arch,
			},
		};
	}

	/**
	 * Generate failure report
	 */
	async generateFailureReport(error, startTime) {
		const endTime = Date.now();
		const duration = endTime - startTime;

		return {
			success: false,
			timestamp: new Date().toISOString(),
			duration: duration,
			durationFormatted: this.formatDuration(duration),
			error: {
				message: error.message,
				stack: error.stack,
			},
			troubleshooting: {
				commonSolutions: [
					"Check PERCY_TOKEN environment variable",
					"Verify network connectivity to Percy services",
					"Ensure Percy CLI is properly installed",
					"Check for rate limiting or quota issues",
				],
			},
		};
	}

	/**
	 * Extract build URL from Percy output
	 */
	extractBuildUrl(output) {
		const match = output.match(
			/Finalized build #\d+: (https:\/\/percy\.io\/[^\s]+)/
		);
		return match ? match[1] : null;
	}

	/**
	 * Extract snapshot count from Percy output
	 */
	extractSnapshotCount(output) {
		const matches = output.match(/\[percy\] Snapshot taken:/g);
		return matches ? matches.length : 0;
	}

	/**
	 * Save report to file
	 */
	async saveReport(report) {
		try {
			// Ensure report directory exists
			await fs.mkdir(this.options.reportPath, { recursive: true });

			const timestamp = new Date().toISOString().replace(/[:.]/g, "-");
			const filename = `percy-simple-report-${timestamp}.json`;
			const filepath = path.join(this.options.reportPath, filename);

			await fs.writeFile(filepath, JSON.stringify(report, null, 2));
			console.log(`[simple-build-manager] Report saved: ${filepath}`);

			// Also save as latest report
			const latestPath = path.join(
				this.options.reportPath,
				"percy-simple-latest.json"
			);
			await fs.writeFile(latestPath, JSON.stringify(report, null, 2));
		} catch (error) {
			console.warn(
				"[simple-build-manager] Failed to save report:",
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
}

// CLI interface
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const manager = new SimplePercyBuildManager();

	async function main() {
		try {
			switch (command) {
				case "run":
					const testCommand = args.slice(1).join(" ") || "playwright test";
					const result = await manager.executeWithPercy(testCommand);
					console.log("\nPercy test execution completed successfully!");
					if (result.report.percy.buildUrl) {
						console.log(`Review URL: ${result.report.percy.buildUrl}`);
					}
					console.log(`Duration: ${result.report.durationFormatted}`);
					console.log(`Snapshots: ${result.report.percy.snapshotCount}`);
					break;

				default:
					console.log(`
Simple Percy Build Manager for ICTServe v3.6.1

Usage:
  node simple-build-manager.cjs <command> [options]

Commands:
  run [test-command]    Execute tests with Percy

Examples:
  node simple-build-manager.cjs run "playwright test"
  node simple-build-manager.cjs run "playwright test tests/e2e/percy-setup-validation.spec.ts"

Environment Variables:
  PERCY_TOKEN           Percy authentication token (required)
                    `);
					break;
			}
		} catch (error) {
			console.error("Simple Percy Build Manager Error:", error.message);
			process.exit(1);
		}
	}

	main();
}

module.exports = SimplePercyBuildManager;
