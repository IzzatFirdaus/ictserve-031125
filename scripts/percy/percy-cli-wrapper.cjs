#!/usr/bin/env node

/**
 * Percy CLI Wrapper for ICTServe v3.6.1
 *
 * This wrapper provides comprehensive Percy CLI integration with:
 * - Build creation and finalization logic
 * - Snapshot upload and processing functionality
 * - Build status reporting and review link generation
 * - Error handling and graceful degradation
 * - Performance optimization
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const { spawn, exec } = require("child_process");
const fs = require("fs");
const path = require("path");
const util = require("util");
const {
	PercyErrorHandler,
	ConfigurationError,
	NetworkError,
	ServiceError,
	PercyCriticalError,
} = require("./percy-error-handler.cjs");
const PercyPerformanceOptimizer = require("./percy-performance-optimizer.cjs");

const execAsync = util.promisify(exec);

class PercyCliWrapper {
	constructor(options = {}) {
		this.options = {
			projectName: process.env.PERCY_PROJECT || "ictserve",
			token: process.env.PERCY_TOKEN,
			enabled: process.env.PERCY_ENABLED !== "false",
			branch: process.env.PERCY_BRANCH || "develop",
			targetBranch: process.env.PERCY_TARGET_BRANCH || "develop",
			parallelNonce: process.env.PERCY_PARALLEL_NONCE,
			parallelTotal: process.env.PERCY_PARALLEL_TOTAL,
			failOnError: process.env.PERCY_FAIL_ON_ERROR === "true",
			debug: process.env.PERCY_DEBUG === "true",
			// Graceful degradation options
			skipUploads: process.env.PERCY_SKIP_UPLOADS === "true",
			localOnly: process.env.PERCY_LOCAL_ONLY === "true",
			fallbackMode: process.env.PERCY_FALLBACK_MODE === "true",
			gracefulDegradation: process.env.PERCY_GRACEFUL_DEGRADATION !== "false",
			// Performance optimization options
			enablePerformanceOptimization:
				process.env.PERCY_ENABLE_PERFORMANCE_OPTIMIZATION !== "false",
			maxConcurrentUploads:
				parseInt(process.env.PERCY_MAX_CONCURRENT_UPLOADS) || 3,
			batchSize: parseInt(process.env.PERCY_BATCH_SIZE) || 5,
			enableCaching: process.env.PERCY_ENABLE_CACHING !== "false",
			enableAsyncUploads: process.env.PERCY_ENABLE_ASYNC_UPLOADS !== "false",
			compressionEnabled: process.env.PERCY_COMPRESSION_ENABLED !== "false",
			...options,
		};

		this.buildInfo = {
			id: null,
			number: null,
			state: "pending",
			webUrl: null,
			totalSnapshots: 0,
			createdAt: null,
			finishedAt: null,
		};

		this.logger = this.createLogger();

		// Initialize error handler with comprehensive resilience features
		this.errorHandler = new PercyErrorHandler({
			enableGracefulDegradation: this.options.gracefulDegradation,
			maxRetries: this.options.maxRetries || 3,
			retryDelay: this.options.retryDelay || 1000,
			logLevel: this.options.debug ? "debug" : "info",
			failOnError: this.options.failOnError,
		});

		// Initialize performance optimizer
		this.performanceOptimizer = null;
		if (this.options.enablePerformanceOptimization) {
			this.performanceOptimizer = new PercyPerformanceOptimizer({
				maxConcurrentUploads: this.options.maxConcurrentUploads,
				batchSize: this.options.batchSize,
				enableCaching: this.options.enableCaching,
				enableAsyncUploads: this.options.enableAsyncUploads,
				compressionEnabled: this.options.compressionEnabled,
				enablePerformanceMonitoring: true,
			});
		}

		// Initialize degradation state
		this.degradationState = {
			active: false,
			reason: null,
			timestamp: null,
			fallbackMode: this.options.fallbackMode,
			localOnly: this.options.localOnly,
		};
	}

	/**
	 * Create a logger instance for Percy operations
	 */
	createLogger() {
		return {
			info: (message, data = {}) => {
				console.log(`[percy-wrapper] ${message}`, data);
			},
			warn: (message, data = {}) => {
				console.warn(`[percy-wrapper] WARNING: ${message}`, data);
			},
			error: (message, data = {}) => {
				console.error(`[percy-wrapper] ERROR: ${message}`, data);
			},
			debug: (message, data = {}) => {
				if (this.options.debug) {
					console.log(`[percy-wrapper] DEBUG: ${message}`, data);
				}
			},
		};
	}

	/**
	 * Validate Percy configuration and environment with enhanced error handling
	 */
	async validateConfiguration() {
		this.logger.debug("Validating Percy configuration");

		if (!this.options.enabled) {
			this.logger.info("Percy is disabled, skipping validation");
			return { valid: false, reason: "disabled" };
		}

		if (!this.options.token) {
			const error = PercyErrorHandler.createConfigurationError(
				"Percy token is not configured. Set PERCY_TOKEN environment variable.",
				"token"
			);

			try {
				this.errorHandler.handleConfigurationError(error);
				return {
					valid: false,
					reason: "missing_token",
					resolution: error.getResolutionSteps(),
					gracefulDegradation: this.errorHandler.isPercyEnabled() === false,
				};
			} catch (criticalError) {
				throw criticalError;
			}
		}

		try {
			// Test Percy CLI availability with timeout
			const { stdout } = await this.executeWithTimeout(
				"npx percy --version",
				10000
			);
			this.logger.debug("Percy CLI is available", { version: stdout.trim() });

			return { valid: true, version: stdout.trim() };
		} catch (error) {
			const cliError = PercyErrorHandler.createConfigurationError(
				`Percy CLI is not available: ${error.message}`,
				"cli"
			);

			try {
				this.errorHandler.handleConfigurationError(cliError);
				return {
					valid: false,
					reason: "cli_unavailable",
					resolution: cliError.getResolutionSteps(),
					gracefulDegradation: this.errorHandler.isPercyEnabled() === false,
				};
			} catch (criticalError) {
				throw criticalError;
			}
		}
	}
	/**
	 * Create a new Percy build with enhanced error handling
	 */
	async createBuild() {
		this.logger.info("Creating Percy build");

		const validation = await this.validateConfiguration();
		if (!validation.valid) {
			if (validation.reason === "disabled") {
				return { success: true, disabled: true };
			}
			if (validation.gracefulDegradation) {
				return { success: true, gracefulDegradation: true };
			}
			throw new Error(`Percy configuration invalid: ${validation.reason}`);
		}

		const maxRetries = 3;
		let lastError = null;

		for (let attempt = 0; attempt < maxRetries; attempt++) {
			try {
				const buildCommand = this.buildPercyCommand("build:create");
				const result = await this.executeWithTimeout(buildCommand, 30000, {
					env: { ...process.env, ...this.buildEnvironmentVariables() },
				});

				// Parse build information from output
				this.parseBuildInfo(result.stdout);

				this.logger.info("Percy build created successfully", {
					buildId: this.buildInfo.id,
					buildNumber: this.buildInfo.number,
					attempt: attempt + 1,
				});

				return {
					success: true,
					buildInfo: this.buildInfo,
				};
			} catch (error) {
				lastError = error;

				// Classify and handle the error
				const networkError = PercyErrorHandler.createNetworkError(
					`Failed to create Percy build: ${error.message}`,
					"build-create",
					attempt,
					maxRetries
				);

				if (attempt < maxRetries - 1) {
					try {
						const retryResult = await this.errorHandler.handleNetworkError(
							networkError
						);
						if (retryResult.shouldRetry) {
							this.logger.info(
								`Retrying build creation (attempt ${attempt + 2}/${maxRetries})`
							);
							continue;
						}
					} catch (handlerError) {
						// Error handler decided not to retry
						break;
					}
				}
			}
		}

		// All retries exhausted
		const serviceError = PercyErrorHandler.createServiceError(
			`Failed to create Percy build after ${maxRetries} attempts: ${lastError.message}`,
			"percy"
		);

		try {
			const result = this.errorHandler.handleServiceError(serviceError);
			if (result.gracefulDegradation) {
				return { success: true, gracefulDegradation: true };
			}
		} catch (criticalError) {
			throw criticalError;
		}

		throw lastError;
	}

	/**
	 * Execute tests with Percy snapshot capture and enhanced error handling
	 */
	async executeWithPercy(command, options = {}) {
		this.logger.info("Executing command with Percy", { command });

		const validation = await this.validateConfiguration();
		if (!validation.valid) {
			if (validation.reason === "disabled") {
				this.logger.info("Percy disabled, running command without Percy");
				return await this.executeCommand(command);
			}
			if (validation.gracefulDegradation) {
				this.logger.info("Percy unavailable, running command without Percy");
				return await this.executeCommand(command);
			}
			throw new Error(`Percy configuration invalid: ${validation.reason}`);
		}

		try {
			const percyCommand = `npx percy exec -- ${command}`;
			const result = await this.executeCommand(percyCommand, {
				env: this.buildEnvironmentVariables(),
				...options,
			});

			// Parse snapshot information from output
			this.parseSnapshotInfo(result.stdout);

			return result;
		} catch (error) {
			this.logger.error("Failed to execute command with Percy", {
				command,
				error: error.message,
			});

			// Handle the error through error handler
			const serviceError = PercyErrorHandler.createServiceError(
				`Percy execution failed: ${error.message}`,
				"percy-exec"
			);

			try {
				const result = this.errorHandler.handleServiceError(serviceError);
				if (result.gracefulDegradation) {
					this.logger.warn("Continuing with command execution without Percy");
					return await this.executeCommand(command, options);
				}
			} catch (criticalError) {
				throw criticalError;
			}

			if (this.options.failOnError) {
				throw error;
			} else {
				this.logger.warn("Continuing despite Percy error (failOnError=false)");
				return await this.executeCommand(command, options);
			}
		}
	}

	/**
	 * Finalize Percy build and get results
	 */
	async finalizeBuild() {
		this.logger.info("Finalizing Percy build");

		const validation = await this.validateConfiguration();
		if (!validation.valid) {
			if (validation.reason === "disabled") {
				return { success: true, disabled: true };
			}
			throw new Error(`Percy configuration invalid: ${validation.reason}`);
		}

		try {
			const finalizeCommand = this.buildPercyCommand("build:finalize");
			const result = await execAsync(finalizeCommand, {
				env: { ...process.env, ...this.buildEnvironmentVariables() },
			});

			// Parse finalization results
			this.parseBuildFinalization(result.stdout);

			this.logger.info("Percy build finalized successfully", {
				buildId: this.buildInfo.id,
				webUrl: this.buildInfo.webUrl,
				totalSnapshots: this.buildInfo.totalSnapshots,
			});

			return {
				success: true,
				buildInfo: this.buildInfo,
				reviewUrl: this.buildInfo.webUrl,
			};
		} catch (error) {
			this.logger.error("Failed to finalize Percy build", {
				error: error.message,
			});
			throw error;
		}
	}

	/**
	 * Get build status and information
	 */
	async getBuildStatus(buildId = null) {
		const targetBuildId = buildId || this.buildInfo.id;

		if (!targetBuildId) {
			throw new Error("No build ID available for status check");
		}

		this.logger.debug("Getting build status", { buildId: targetBuildId });

		try {
			const statusCommand = `npx percy build:status ${targetBuildId}`;
			const result = await execAsync(statusCommand, {
				env: { ...process.env, ...this.buildEnvironmentVariables() },
			});

			const status = this.parseBuildStatus(result.stdout);

			this.logger.debug("Build status retrieved", status);

			return status;
		} catch (error) {
			this.logger.error("Failed to get build status", {
				buildId: targetBuildId,
				error: error.message,
			});
			throw error;
		}
	}
	/**
	 * Upload snapshots to Percy build with performance optimization
	 */
	async uploadSnapshots(snapshotPaths) {
		this.logger.info("Uploading snapshots to Percy", {
			count: snapshotPaths.length,
		});

		const validation = await this.validateConfiguration();
		if (!validation.valid) {
			if (validation.reason === "disabled") {
				return { success: true, disabled: true };
			}
			throw new Error(`Percy configuration invalid: ${validation.reason}`);
		}

		try {
			// Use performance optimizer if available
			if (this.performanceOptimizer && this.options.enableAsyncUploads) {
				return await this.uploadSnapshotsWithOptimization(snapshotPaths);
			}

			// Fallback to traditional upload
			const uploadPromises = snapshotPaths.map(async (snapshotPath) => {
				const uploadCommand = `npx percy upload ${snapshotPath}`;
				return await execAsync(uploadCommand, {
					env: { ...process.env, ...this.buildEnvironmentVariables() },
				});
			});

			const results = await Promise.all(uploadPromises);

			this.logger.info("Snapshots uploaded successfully", {
				uploaded: results.length,
				total: snapshotPaths.length,
			});

			return {
				success: true,
				uploaded: results.length,
				results: results,
			};
		} catch (error) {
			this.logger.error("Failed to upload snapshots", { error: error.message });
			throw error;
		}
	}

	/**
	 * Upload snapshots using performance optimization
	 */
	async uploadSnapshotsWithOptimization(snapshotPaths) {
		this.logger.info("Using performance-optimized snapshot upload", {
			count: snapshotPaths.length,
			maxConcurrent: this.options.maxConcurrentUploads,
			batchSize: this.options.batchSize,
		});

		// Convert snapshot paths to snapshot data objects
		const snapshots = snapshotPaths.map((snapshotPath, index) => ({
			name: path.basename(snapshotPath),
			path: snapshotPath,
			size: this.getFileSize(snapshotPath),
			type: this.getFileType(snapshotPath),
			priority: index < 3 ? 2 : 1, // Higher priority for first few snapshots
		}));

		// Use batch processing for optimal performance
		const result = await this.performanceOptimizer.batchProcessSnapshots(
			snapshots
		);

		this.logger.info("Performance-optimized upload completed", {
			total: result.total,
			successful: result.successful,
			failed: result.failed,
			duration: `${Math.round(result.duration)}ms`,
		});

		return {
			success: result.successful > 0,
			uploaded: result.successful,
			failed: result.failed,
			total: result.total,
			duration: result.duration,
			optimized: true,
			performanceReport:
				this.performanceOptimizer.getPerformanceReport().summary,
		};
	}

	/**
	 * Get file size safely
	 */
	getFileSize(filePath) {
		try {
			if (fs.existsSync(filePath)) {
				return fs.statSync(filePath).size;
			}
		} catch (error) {
			this.logger.debug(
				`Could not get file size for ${filePath}: ${error.message}`
			);
		}
		return 0;
	}

	/**
	 * Get file type from extension
	 */
	getFileType(filePath) {
		const ext = path.extname(filePath).toLowerCase();
		const imageExtensions = [".png", ".jpg", ".jpeg", ".gif", ".webp", ".svg"];
		return imageExtensions.includes(ext) ? "image" : "file";
	}

	/**
	 * Wait for all performance-optimized uploads to complete
	 */
	async waitForUploadsComplete(timeout = 60000) {
		if (!this.performanceOptimizer) {
			return { completed: true, message: "No performance optimizer active" };
		}

		this.logger.info("Waiting for all uploads to complete", { timeout });

		try {
			const result = await this.performanceOptimizer.waitForUploadsComplete(
				timeout
			);
			this.logger.info("All uploads completed successfully", {
				duration: `${result.duration}ms`,
			});
			return result;
		} catch (error) {
			this.logger.warn("Upload completion timeout", { error: error.message });
			throw error;
		}
	}

	/**
	 * Get performance metrics from optimizer
	 */
	getPerformanceMetrics() {
		if (!this.performanceOptimizer) {
			return {
				available: false,
				message: "Performance optimization not enabled",
			};
		}

		return {
			available: true,
			metrics: this.performanceOptimizer.getPerformanceReport(),
		};
	}

	/**
	 * Save performance report
	 */
	async savePerformanceReport(filePath = null) {
		if (!this.performanceOptimizer) {
			throw new Error("Performance optimization not enabled");
		}

		const reportPath = await this.performanceOptimizer.savePerformanceReport(
			filePath
		);
		this.logger.info("Performance report saved", { path: reportPath });
		return reportPath;
	}

	/**
	 * Build Percy command with environment variables
	 */
	buildPercyCommand(action) {
		// For Windows, we need to handle environment variables differently
		const isWindows = process.platform === "win32";

		if (isWindows) {
			// On Windows, set environment variables in the spawn options instead
			return `npx percy ${action}`;
		} else {
			// On Unix-like systems, use the traditional approach
			const envVars = Object.entries(this.buildEnvironmentVariables())
				.map(([key, value]) => `${key}=${value}`)
				.join(" ");
			return `${envVars} npx percy ${action}`;
		}
	}

	/**
	 * Build environment variables for Percy
	 */
	buildEnvironmentVariables() {
		const env = {
			PERCY_TOKEN: this.options.token,
			PERCY_PROJECT: this.options.projectName,
			PERCY_BRANCH: this.options.branch,
			PERCY_TARGET_BRANCH: this.options.targetBranch,
		};

		if (this.options.parallelNonce) {
			env.PERCY_PARALLEL_NONCE = this.options.parallelNonce;
		}

		if (this.options.parallelTotal) {
			env.PERCY_PARALLEL_TOTAL = this.options.parallelTotal;
		}

		if (this.options.debug) {
			env.PERCY_DEBUG = "1";
		}

		return env;
	}

	/**
	 * Execute a command with timeout and enhanced error handling
	 */
	async executeWithTimeout(command, timeout = 30000, options = {}) {
		// Ensure command uses npx for Playwright
		let execCommand = command;
		if (command.startsWith("playwright ")) {
			execCommand = `npx ${command}`;
		}

		return new Promise((resolve, reject) => {
			const child = spawn(execCommand, [], {
				shell: true,
				stdio: "pipe",
				...options,
			});

			let stdout = "";
			let stderr = "";
			let timeoutId = null;

			// Set up timeout
			if (timeout > 0) {
				timeoutId = setTimeout(() => {
					child.kill("SIGTERM");
					reject(new Error(`Command timed out after ${timeout}ms: ${command}`));
				}, timeout);
			}

			child.stdout.on("data", (data) => {
				const output = data.toString();
				stdout += output;
				if (this.options.debug) {
					console.log(output);
				}
			});

			child.stderr.on("data", (data) => {
				const output = data.toString();
				stderr += output;
				if (this.options.debug) {
					console.error(output);
				}
			});

			child.on("close", (code) => {
				if (timeoutId) {
					clearTimeout(timeoutId);
				}

				if (code === 0) {
					resolve({ stdout, stderr, code });
				} else {
					reject(
						new Error(`Command failed with code ${code}: ${stderr || stdout}`)
					);
				}
			});

			child.on("error", (error) => {
				if (timeoutId) {
					clearTimeout(timeoutId);
				}
				reject(error);
			});
		});
	}
	/**
	 * Parse build information from Percy CLI output
	 */
	parseBuildInfo(output) {
		// Parse build ID, number, and URL from Percy CLI output
		const buildIdMatch = output.match(/Build ID: (\w+)/);
		const buildNumberMatch = output.match(/Build #(\d+)/);
		const webUrlMatch = output.match(
			/View build: (https:\/\/percy\.io\/[^\s]+)/
		);

		if (buildIdMatch) {
			this.buildInfo.id = buildIdMatch[1];
		}

		if (buildNumberMatch) {
			this.buildInfo.number = parseInt(buildNumberMatch[1]);
		}

		if (webUrlMatch) {
			this.buildInfo.webUrl = webUrlMatch[1];
		}

		this.buildInfo.createdAt = new Date();
		this.buildInfo.state = "processing";
	}

	/**
	 * Parse snapshot information from Percy CLI output
	 */
	parseSnapshotInfo(output) {
		const snapshotMatches = output.match(/\[percy\] Snapshot taken: (.+)/g);

		if (snapshotMatches) {
			this.buildInfo.totalSnapshots += snapshotMatches.length;
			this.logger.debug("Snapshots captured", {
				count: snapshotMatches.length,
				total: this.buildInfo.totalSnapshots,
			});
		}
	}

	/**
	 * Parse build finalization results
	 */
	parseBuildFinalization(output) {
		const finalizedMatch = output.match(
			/Finalized build #(\d+): (https:\/\/percy\.io\/[^\s]+)/
		);

		if (finalizedMatch) {
			this.buildInfo.number = parseInt(finalizedMatch[1]);
			this.buildInfo.webUrl = finalizedMatch[2];
			this.buildInfo.state = "finished";
			this.buildInfo.finishedAt = new Date();
		}
	}

	/**
	 * Parse build status from Percy CLI output
	 */
	parseBuildStatus(output) {
		// Parse status information from Percy CLI output
		const statusMatch = output.match(/Status: (\w+)/);
		const snapshotsMatch = output.match(/Snapshots: (\d+)/);
		const comparisonsMatch = output.match(/Comparisons: (\d+)/);

		return {
			state: statusMatch ? statusMatch[1] : "unknown",
			totalSnapshots: snapshotsMatch ? parseInt(snapshotsMatch[1]) : 0,
			totalComparisons: comparisonsMatch ? parseInt(comparisonsMatch[1]) : 0,
			webUrl: this.buildInfo.webUrl,
		};
	}

	/**
	 * Execute a command with proper error handling (legacy method for backward compatibility)
	 */
	async executeCommand(command, options = {}) {
		return this.executeWithTimeout(command, 30000, options);
	}

	/**
	 * Get error handler statistics and status
	 */
	getErrorHandlerStats() {
		return this.errorHandler.getErrorStats();
	}

	/**
	 * Generate comprehensive error report
	 */
	generateErrorReport() {
		const wrapperReport = this.generateBuildReport();
		const errorReport = this.errorHandler.generateErrorReport();

		return {
			percy: wrapperReport,
			errorHandling: errorReport,
			timestamp: new Date().toISOString(),
		};
	}

	/**
	 * Save comprehensive error report to file
	 */
	async saveErrorReport(filePath = null) {
		const report = this.generateErrorReport();
		const defaultPath = path.join(
			process.cwd(),
			"percy-reports",
			`comprehensive-error-report-${Date.now()}.json`
		);
		const targetPath = filePath || defaultPath;

		try {
			// Ensure directory exists
			const dir = path.dirname(targetPath);
			if (!fs.existsSync(dir)) {
				fs.mkdirSync(dir, { recursive: true });
			}

			// Write report to file
			fs.writeFileSync(targetPath, JSON.stringify(report, null, 2));

			this.logger.info(`Comprehensive error report saved to: ${targetPath}`);
			return targetPath;
		} catch (error) {
			this.logger.error("Failed to save comprehensive error report", {
				targetPath,
				error: error.message,
			});
			throw error;
		}
	}

	/**
	 * Reset error handler state
	 */
	resetErrorHandler() {
		this.errorHandler.reset();
		this.logger.info("Error handler state reset");
	}

	/**
	 * Check if Percy is enabled (considering error handler state)
	 */
	isPercyEnabled() {
		// Check multiple conditions for Percy enablement
		if (!this.options.enabled) {
			return false;
		}

		if (this.degradationState.active) {
			return false;
		}

		if (this.options.skipUploads && !this.options.localOnly) {
			return false;
		}

		return this.errorHandler.isPercyEnabled();
	}

	/**
	 * Enable graceful degradation with specific reason
	 */
	enableGracefulDegradation(reason = "unknown") {
		this.degradationState = {
			active: true,
			reason: reason,
			timestamp: new Date(),
			fallbackMode: this.options.fallbackMode,
			localOnly: this.options.localOnly,
		};

		this.logger.warn("Percy graceful degradation enabled", {
			reason: reason,
			fallbackMode: this.degradationState.fallbackMode,
			localOnly: this.degradationState.localOnly,
		});

		// Set environment variables to indicate degradation
		process.env.PERCY_ENABLED = "false";
		process.env.PERCY_SKIP_UPLOADS = "true";

		return this.degradationState;
	}

	/**
	 * Disable graceful degradation and restore normal operation
	 */
	disableGracefulDegradation() {
		const previousState = { ...this.degradationState };

		this.degradationState = {
			active: false,
			reason: null,
			timestamp: null,
			fallbackMode: this.options.fallbackMode,
			localOnly: this.options.localOnly,
		};

		// Restore environment variables if they were set by degradation
		if (process.env.PERCY_ENABLED === "false") {
			delete process.env.PERCY_ENABLED;
		}
		if (process.env.PERCY_SKIP_UPLOADS === "true") {
			delete process.env.PERCY_SKIP_UPLOADS;
		}

		this.logger.info("Percy graceful degradation disabled", {
			previousReason: previousState.reason,
			duration: previousState.timestamp
				? Date.now() - previousState.timestamp.getTime()
				: 0,
		});

		return previousState;
	}

	/**
	 * Get current degradation state
	 */
	getDegradationState() {
		return {
			...this.degradationState,
			percyEnabled: this.isPercyEnabled(),
			options: {
				enabled: this.options.enabled,
				skipUploads: this.options.skipUploads,
				localOnly: this.options.localOnly,
				fallbackMode: this.options.fallbackMode,
				gracefulDegradation: this.options.gracefulDegradation,
			},
		};
	}

	/**
	 * Execute command with fallback mode support
	 */
	async executeWithFallback(command, options = {}) {
		this.logger.info("Executing command with fallback support", { command });

		// If Percy is disabled or in degradation, run command directly
		if (!this.isPercyEnabled() || this.degradationState.active) {
			this.logger.info(
				"Percy disabled or degraded, running command without Percy"
			);
			return await this.executeCommand(command, options);
		}

		// If local-only mode is enabled, run without Percy uploads
		if (this.options.localOnly) {
			this.logger.info(
				"Local-only mode enabled, running command without uploads"
			);
			const localEnv = {
				...this.buildEnvironmentVariables(),
				PERCY_SKIP_UPLOADS: "true",
			};
			return await this.executeCommand(command, {
				...options,
				env: { ...process.env, ...localEnv },
			});
		}

		// Try to execute with Percy, fall back on error
		try {
			return await this.executeWithPercy(command, options);
		} catch (error) {
			if (this.options.gracefulDegradation) {
				this.logger.warn(
					"Percy execution failed, falling back to normal execution",
					{
						error: error.message,
					}
				);
				this.enableGracefulDegradation("execution-failure");
				return await this.executeCommand(command, options);
			} else {
				throw error;
			}
		}
	}

	/**
	 * Generate build report with performance metrics
	 */
	generateBuildReport() {
		const report = {
			buildInfo: this.buildInfo,
			configuration: {
				projectName: this.options.projectName,
				branch: this.options.branch,
				targetBranch: this.options.targetBranch,
				enabled: this.options.enabled,
				percyEnabled: this.isPercyEnabled(),
				performanceOptimizationEnabled:
					this.options.enablePerformanceOptimization,
			},
			performance: this.getPerformanceMetrics(),
			timestamp: new Date().toISOString(),
			environment: {
				nodeVersion: process.version,
				platform: process.platform,
				arch: process.arch,
			},
		};

		this.logger.info("Build report generated", report);

		return report;
	}

	/**
	 * Shutdown wrapper and cleanup resources
	 */
	async shutdown() {
		this.logger.info("Shutting down Percy CLI wrapper...");

		try {
			// Wait for uploads to complete if performance optimizer is active
			if (this.performanceOptimizer) {
				await this.performanceOptimizer.shutdown();
			}

			// Generate final report
			const finalReport = this.generateBuildReport();
			this.logger.info("Percy CLI wrapper shutdown complete");

			return finalReport;
		} catch (error) {
			this.logger.error("Error during shutdown", { error: error.message });
			throw error;
		}
	}
}
// CLI interface
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const wrapper = new PercyCliWrapper();

	async function main() {
		try {
			switch (command) {
				case "validate":
					const validation = await wrapper.validateConfiguration();
					console.log(JSON.stringify(validation, null, 2));
					process.exit(validation.valid ? 0 : 1);
					break;

				case "create-build":
					const buildResult = await wrapper.createBuild();
					console.log(JSON.stringify(buildResult, null, 2));
					break;

				case "exec":
					const testCommand = args.slice(1).join(" ");
					const execResult = await wrapper.executeWithPercy(testCommand);
					console.log("Command executed successfully");
					break;

				case "finalize":
					const finalizeResult = await wrapper.finalizeBuild();
					console.log(JSON.stringify(finalizeResult, null, 2));
					break;

				case "status":
					const buildId = args[1];
					const statusResult = await wrapper.getBuildStatus(buildId);
					console.log(JSON.stringify(statusResult, null, 2));
					break;

				case "report":
					const report = wrapper.generateBuildReport();
					console.log(JSON.stringify(report, null, 2));
					break;

				case "error-report":
					const errorReport = wrapper.generateErrorReport();
					console.log(JSON.stringify(errorReport, null, 2));
					break;

				case "save-error-report":
					const errorReportPath = await wrapper.saveErrorReport();
					console.log(`Error report saved to: ${errorReportPath}`);
					break;

				case "error-stats":
					const errorStats = wrapper.getErrorHandlerStats();
					console.log(JSON.stringify(errorStats, null, 2));
					break;

				case "reset-errors":
					wrapper.resetErrorHandler();
					console.log("Error handler state reset");
					break;

				case "percy-enabled":
					const enabled = wrapper.isPercyEnabled();
					console.log(JSON.stringify({ percyEnabled: enabled }, null, 2));
					process.exit(enabled ? 0 : 1);
					break;

				case "enable-degradation":
					const reason = args[1] || "manual";
					const degradationState = wrapper.enableGracefulDegradation(reason);
					console.log(JSON.stringify(degradationState, null, 2));
					break;

				case "disable-degradation":
					const previousState = wrapper.disableGracefulDegradation();
					console.log(JSON.stringify(previousState, null, 2));
					break;

				case "degradation-state":
					const currentState = wrapper.getDegradationState();
					console.log(JSON.stringify(currentState, null, 2));
					break;

				case "exec-fallback":
					const fallbackCommand = args.slice(1).join(" ");
					const fallbackResult = await wrapper.executeWithFallback(
						fallbackCommand
					);
					console.log("Command executed successfully with fallback support");
					break;

				case "performance-metrics":
					const performanceMetrics = wrapper.getPerformanceMetrics();
					console.log(JSON.stringify(performanceMetrics, null, 2));
					break;

				case "save-performance-report":
					const performanceReportPath = await wrapper.savePerformanceReport();
					console.log(`Performance report saved to: ${performanceReportPath}`);
					break;

				case "wait-uploads":
					const timeout = parseInt(args[1]) || 60000;
					const uploadResult = await wrapper.waitForUploadsComplete(timeout);
					console.log(JSON.stringify(uploadResult, null, 2));
					break;

				case "shutdown":
					const shutdownResult = await wrapper.shutdown();
					console.log(JSON.stringify(shutdownResult, null, 2));
					process.exit(0);
					break;

				default:
					console.log(`
Percy CLI Wrapper for ICTServe v3.6.1 with Enhanced Error Handling, Graceful Degradation, and Performance Optimization

Usage:
  node percy-cli-wrapper.js <command> [options]

Commands:
  validate              Validate Percy configuration
  create-build          Create a new Percy build
  exec <command>        Execute command with Percy
  exec-fallback <cmd>   Execute command with fallback support
  finalize              Finalize Percy build
  status [build-id]     Get build status
  report                Generate build report
  error-report          Generate comprehensive error report
  save-error-report     Save error report to file
  error-stats           Show error handler statistics
  reset-errors          Reset error handler state
  percy-enabled         Check if Percy is currently enabled
  enable-degradation [reason]   Enable graceful degradation
  disable-degradation   Disable graceful degradation
  degradation-state     Show current degradation state
  performance-metrics   Show performance optimization metrics
  save-performance-report  Save performance report to file
  wait-uploads [timeout]   Wait for all uploads to complete (ms)
  shutdown              Shutdown wrapper and save final reports

Environment Variables:
  PERCY_TOKEN           Percy authentication token (required)
  PERCY_PROJECT         Percy project name
  PERCY_ENABLED         Enable/disable Percy (default: true)
  PERCY_BRANCH          Git branch name
  PERCY_TARGET_BRANCH   Target branch for comparison
  PERCY_DEBUG           Enable debug logging
  PERCY_FAIL_ON_ERROR   Fail on Percy errors (default: false)

Graceful Degradation Options:
  PERCY_SKIP_UPLOADS    Skip uploading snapshots (default: false)
  PERCY_LOCAL_ONLY      Run in local-only mode (default: false)
  PERCY_FALLBACK_MODE   Enable fallback mode (default: false)
  PERCY_GRACEFUL_DEGRADATION  Enable graceful degradation (default: true)

Performance Optimization Options:
  PERCY_ENABLE_PERFORMANCE_OPTIMIZATION  Enable performance optimization (default: true)
  PERCY_MAX_CONCURRENT_UPLOADS  Maximum concurrent uploads (default: 3)
  PERCY_BATCH_SIZE      Batch size for processing (default: 5)
  PERCY_ENABLE_CACHING  Enable caching mechanisms (default: true)
  PERCY_ENABLE_ASYNC_UPLOADS  Enable asynchronous uploads (default: true)
  PERCY_COMPRESSION_ENABLED  Enable compression (default: true)

Error Handling Options:
  PERCY_MAX_RETRIES     Maximum retry attempts (default: 3)
  PERCY_RETRY_DELAY     Initial retry delay in ms (default: 1000)

Examples:
  node percy-cli-wrapper.js validate
  node percy-cli-wrapper.js exec "playwright test"
  node percy-cli-wrapper.js exec-fallback "playwright test"
  node percy-cli-wrapper.js enable-degradation "service-unavailable"
  node percy-cli-wrapper.js performance-metrics
  node percy-cli-wrapper.js save-performance-report
  node percy-cli-wrapper.js wait-uploads 30000
  node percy-cli-wrapper.js shutdown
  node percy-cli-wrapper.js finalize
                    `);
					break;
			}
		} catch (error) {
			console.error("Percy CLI Wrapper Error:", error.message);
			process.exit(1);
		}
	}

	main();
}

module.exports = PercyCliWrapper;
