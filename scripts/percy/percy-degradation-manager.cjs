#!/usr/bin/env node

/**
 * Percy Degradation Manager for ICTServe v3.6.1
 *
 * This script provides comprehensive management of Percy graceful degradation features:
 * - Command-line options to disable Percy integration
 * - Configuration-based Percy enabling/disabling
 * - Fallback modes for local development
 * - Environment-specific degradation settings
 * - Test execution with degradation support
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const { spawn } = require("child_process");
const fs = require("fs");
const path = require("path");

class PercyDegradationManager {
	constructor(options = {}) {
		this.options = {
			// Default degradation settings
			enabled: process.env.PERCY_ENABLED !== "false",
			gracefulDegradation: process.env.PERCY_GRACEFUL_DEGRADATION !== "false",
			skipUploads: process.env.PERCY_SKIP_UPLOADS === "true",
			localOnly: process.env.PERCY_LOCAL_ONLY === "true",
			fallbackMode: process.env.PERCY_FALLBACK_MODE === "true",
			debug: process.env.PERCY_DEBUG === "true",
			...options,
		};

		this.logger = this.createLogger();
		this.degradationReasons = new Set();
	}

	/**
	 * Create a logger instance
	 */
	createLogger() {
		return {
			info: (message, data = {}) => {
				console.log(`[percy-degradation] ${message}`, data);
			},
			warn: (message, data = {}) => {
				console.warn(`[percy-degradation] WARNING: ${message}`, data);
			},
			error: (message, data = {}) => {
				console.error(`[percy-degradation] ERROR: ${message}`, data);
			},
			debug: (message, data = {}) => {
				if (this.options.debug) {
					console.log(`[percy-degradation] DEBUG: ${message}`, data);
				}
			},
		};
	}

	/**
	 * Check if Percy should be enabled based on current configuration
	 */
	shouldEnablePercy() {
		// Check explicit disable flags
		if (!this.options.enabled) {
			this.degradationReasons.add("explicitly-disabled");
			return false;
		}

		// Check for missing token
		if (!process.env.PERCY_TOKEN) {
			this.degradationReasons.add("missing-token");
			return false;
		}

		// Check for skip uploads without local mode
		if (this.options.skipUploads && !this.options.localOnly) {
			this.degradationReasons.add("skip-uploads");
			return false;
		}

		// Check for fallback mode
		if (this.options.fallbackMode) {
			this.degradationReasons.add("fallback-mode");
			return false;
		}

		return true;
	}

	/**
	 * Get current degradation status
	 */
	getDegradationStatus() {
		const percyEnabled = this.shouldEnablePercy();
		const reasons = Array.from(this.degradationReasons);

		return {
			percyEnabled,
			gracefulDegradation: this.options.gracefulDegradation,
			degradationReasons: reasons,
			modes: {
				skipUploads: this.options.skipUploads,
				localOnly: this.options.localOnly,
				fallbackMode: this.options.fallbackMode,
			},
			environment: {
				PERCY_ENABLED: process.env.PERCY_ENABLED,
				PERCY_TOKEN: process.env.PERCY_TOKEN ? "[CONFIGURED]" : "[MISSING]",
				PERCY_SKIP_UPLOADS: process.env.PERCY_SKIP_UPLOADS,
				PERCY_LOCAL_ONLY: process.env.PERCY_LOCAL_ONLY,
				PERCY_FALLBACK_MODE: process.env.PERCY_FALLBACK_MODE,
				PERCY_GRACEFUL_DEGRADATION: process.env.PERCY_GRACEFUL_DEGRADATION,
			},
		};
	}

	/**
	 * Execute command with appropriate Percy configuration
	 */
	async executeCommand(command, options = {}) {
		const status = this.getDegradationStatus();

		this.logger.info("Executing command with degradation management", {
			command,
			percyEnabled: status.percyEnabled,
			reasons: status.degradationReasons,
		});

		// Prepare environment variables
		const env = { ...process.env };

		if (!status.percyEnabled) {
			// Disable Percy completely
			env.PERCY_ENABLED = "false";
			env.PERCY_SKIP_UPLOADS = "true";
			this.logger.info(
				"Percy disabled, running command without visual testing",
				{
					reasons: status.degradationReasons,
				}
			);
		} else if (this.options.localOnly) {
			// Enable local-only mode
			env.PERCY_SKIP_UPLOADS = "true";
			this.logger.info("Percy local-only mode enabled");
		}

		// Ensure command uses npx for Playwright
		let execCommand = command;
		if (command.startsWith("playwright ")) {
			execCommand = `npx ${command}`;
		}

		// Execute the command
		return new Promise((resolve, reject) => {
			const child = spawn(execCommand, [], {
				shell: true,
				stdio: "inherit",
				env,
				...options,
			});

			child.on("close", (code) => {
				if (code === 0) {
					this.logger.info("Command executed successfully", { code });
					resolve({ success: true, code });
				} else {
					this.logger.error("Command failed", { code });
					reject(new Error(`Command failed with code ${code}`));
				}
			});

			child.on("error", (error) => {
				this.logger.error("Command execution error", { error: error.message });
				reject(error);
			});
		});
	}

	/**
	 * Run tests with Percy degradation support
	 */
	async runTests(testCommand, options = {}) {
		const {
			skipPercy = false,
			localOnly = false,
			fallbackOnError = true,
		} = options;

		this.logger.info("Running tests with Percy degradation support", {
			testCommand,
			skipPercy,
			localOnly,
			fallbackOnError,
		});

		// Override options for this test run
		const originalOptions = { ...this.options };
		if (skipPercy) {
			this.options.enabled = false;
		}
		if (localOnly) {
			this.options.localOnly = true;
		}

		try {
			const result = await this.executeCommand(testCommand);

			// Restore original options
			this.options = originalOptions;

			return result;
		} catch (error) {
			if (fallbackOnError && this.options.gracefulDegradation) {
				this.logger.warn("Test execution failed, retrying without Percy", {
					error: error.message,
				});

				// Disable Percy and retry
				this.options.enabled = false;
				try {
					const fallbackResult = await this.executeCommand(testCommand);

					// Restore original options
					this.options = originalOptions;

					return {
						...fallbackResult,
						fallbackUsed: true,
						originalError: error.message,
					};
				} catch (fallbackError) {
					// Restore original options
					this.options = originalOptions;
					throw fallbackError;
				}
			} else {
				// Restore original options
				this.options = originalOptions;
				throw error;
			}
		}
	}

	/**
	 * Create configuration file for degradation settings
	 */
	async createConfigFile(filePath = null) {
		const defaultPath = path.join(
			process.cwd(),
			"percy-degradation.config.json"
		);
		const targetPath = filePath || defaultPath;

		const config = {
			degradation: {
				enabled: this.options.gracefulDegradation,
				skipUploads: this.options.skipUploads,
				localOnly: this.options.localOnly,
				fallbackMode: this.options.fallbackMode,
			},
			environments: {
				development: {
					enabled: true,
					localOnly: true,
					skipUploads: false,
				},
				testing: {
					enabled: true,
					localOnly: false,
					skipUploads: false,
				},
				ci: {
					enabled: true,
					localOnly: false,
					skipUploads: false,
					gracefulDegradation: true,
				},
				production: {
					enabled: false,
					localOnly: false,
					skipUploads: true,
				},
			},
			commands: {
				"test:e2e": {
					fallbackOnError: true,
					localOnly: false,
				},
				"test:e2e:debug": {
					fallbackOnError: false,
					localOnly: true,
				},
				"test:accessibility": {
					fallbackOnError: true,
					localOnly: false,
				},
			},
		};

		try {
			fs.writeFileSync(targetPath, JSON.stringify(config, null, 2));
			this.logger.info(`Degradation configuration saved to: ${targetPath}`);
			return targetPath;
		} catch (error) {
			this.logger.error("Failed to save configuration file", {
				targetPath,
				error: error.message,
			});
			throw error;
		}
	}

	/**
	 * Load configuration from file
	 */
	loadConfigFile(filePath = null) {
		const defaultPath = path.join(
			process.cwd(),
			"percy-degradation.config.json"
		);
		const targetPath = filePath || defaultPath;

		try {
			if (!fs.existsSync(targetPath)) {
				this.logger.debug("Configuration file not found", { targetPath });
				return null;
			}

			const configData = fs.readFileSync(targetPath, "utf8");
			const config = JSON.parse(configData);

			this.logger.info("Configuration loaded from file", { targetPath });
			return config;
		} catch (error) {
			this.logger.error("Failed to load configuration file", {
				targetPath,
				error: error.message,
			});
			return null;
		}
	}

	/**
	 * Apply environment-specific configuration
	 */
	applyEnvironmentConfig(environment = "development") {
		const config = this.loadConfigFile();
		if (!config || !config.environments || !config.environments[environment]) {
			this.logger.debug("No environment configuration found", { environment });
			return;
		}

		const envConfig = config.environments[environment];
		this.logger.info("Applying environment configuration", {
			environment,
			config: envConfig,
		});

		// Apply environment-specific settings
		Object.assign(this.options, envConfig);

		// Set environment variables
		if (envConfig.enabled !== undefined) {
			process.env.PERCY_ENABLED = envConfig.enabled.toString();
		}
		if (envConfig.skipUploads !== undefined) {
			process.env.PERCY_SKIP_UPLOADS = envConfig.skipUploads.toString();
		}
		if (envConfig.localOnly !== undefined) {
			process.env.PERCY_LOCAL_ONLY = envConfig.localOnly.toString();
		}
	}

	/**
	 * Generate degradation report
	 */
	generateReport() {
		const status = this.getDegradationStatus();
		const report = {
			timestamp: new Date().toISOString(),
			status,
			configuration: this.options,
			recommendations: this.generateRecommendations(status),
		};

		this.logger.info("Degradation report generated", report);
		return report;
	}

	/**
	 * Generate recommendations based on current status
	 */
	generateRecommendations(status) {
		const recommendations = [];

		if (!status.percyEnabled) {
			if (status.degradationReasons.includes("missing-token")) {
				recommendations.push({
					type: "configuration",
					message: "Set PERCY_TOKEN environment variable to enable Percy",
					priority: "high",
				});
			}

			if (status.degradationReasons.includes("explicitly-disabled")) {
				recommendations.push({
					type: "configuration",
					message: "Set PERCY_ENABLED=true to enable Percy",
					priority: "medium",
				});
			}
		}

		if (status.modes.localOnly) {
			recommendations.push({
				type: "optimization",
				message: "Local-only mode is enabled - snapshots won't be uploaded",
				priority: "info",
			});
		}

		if (status.modes.fallbackMode) {
			recommendations.push({
				type: "performance",
				message:
					"Fallback mode is enabled - consider disabling for full Percy features",
				priority: "low",
			});
		}

		return recommendations;
	}
}

// CLI interface
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const manager = new PercyDegradationManager();

	async function main() {
		try {
			switch (command) {
				case "status":
					const status = manager.getDegradationStatus();
					console.log(JSON.stringify(status, null, 2));
					break;

				case "run":
					const testCommand = args.slice(1).join(" ");
					if (!testCommand) {
						throw new Error("Test command is required");
					}
					const result = await manager.runTests(testCommand);
					console.log("Tests completed successfully", result);
					break;

				case "run-skip-percy":
					const skipCommand = args.slice(1).join(" ");
					if (!skipCommand) {
						throw new Error("Test command is required");
					}
					const skipResult = await manager.runTests(skipCommand, {
						skipPercy: true,
					});
					console.log("Tests completed without Percy", skipResult);
					break;

				case "run-local-only":
					const localCommand = args.slice(1).join(" ");
					if (!localCommand) {
						throw new Error("Test command is required");
					}
					const localResult = await manager.runTests(localCommand, {
						localOnly: true,
					});
					console.log("Tests completed in local-only mode", localResult);
					break;

				case "create-config":
					const configPath = args[1];
					const createdPath = await manager.createConfigFile(configPath);
					console.log(`Configuration file created: ${createdPath}`);
					break;

				case "apply-env":
					const environment = args[1] || "development";
					manager.applyEnvironmentConfig(environment);
					console.log(`Environment configuration applied: ${environment}`);
					break;

				case "report":
					const report = manager.generateReport();
					console.log(JSON.stringify(report, null, 2));
					break;

				default:
					console.log(`
Percy Degradation Manager for ICTServe v3.6.1

Usage:
  node percy-degradation-manager.cjs <command> [options]

Commands:
  status                Show current degradation status
  run <command>         Run command with degradation support
  run-skip-percy <cmd>  Run command without Percy
  run-local-only <cmd>  Run command in local-only mode
  create-config [path]  Create degradation configuration file
  apply-env [env]       Apply environment-specific configuration
  report                Generate degradation report

Environment Variables:
  PERCY_ENABLED         Enable/disable Percy (default: true)
  PERCY_GRACEFUL_DEGRADATION  Enable graceful degradation (default: true)
  PERCY_SKIP_UPLOADS    Skip uploading snapshots (default: false)
  PERCY_LOCAL_ONLY      Run in local-only mode (default: false)
  PERCY_FALLBACK_MODE   Enable fallback mode (default: false)

Examples:
  node percy-degradation-manager.cjs status
  node percy-degradation-manager.cjs run "playwright test"
  node percy-degradation-manager.cjs run-skip-percy "playwright test"
  node percy-degradation-manager.cjs run-local-only "playwright test"
  node percy-degradation-manager.cjs create-config
  node percy-degradation-manager.cjs apply-env development
  node percy-degradation-manager.cjs report
                    `);
					break;
			}
		} catch (error) {
			console.error("Percy Degradation Manager Error:", error.message);
			process.exit(1);
		}
	}

	main();
}

module.exports = PercyDegradationManager;
