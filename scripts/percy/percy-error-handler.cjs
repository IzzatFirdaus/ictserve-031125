#!/usr/bin/env node

/**
 * Percy Error Handler for ICTServe v3.6.1
 *
 * This class provides comprehensive error handling and resilience features for Percy integration:
 * - Configuration error handling with resolution steps
 * - Network error handling with automatic retry mechanisms
 * - Graceful degradation when Percy services are unavailable
 * - Detailed logging for debugging Percy integration issues
 * - Exponential backoff retry logic
 * - Service failure resilience
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const fs = require("fs");
const path = require("path");

/**
 * Base error class for Percy-related errors
 */
class PercyError extends Error {
	constructor(message, type = "generic", isCritical = false) {
		super(message);
		this.name = "PercyError";
		this.type = type;
		this.critical = isCritical;
		this.timestamp = new Date();
	}

	isCritical() {
		return this.critical;
	}

	getResolutionSteps() {
		return [
			"Check Percy configuration",
			"Verify network connectivity",
			"Contact support if issue persists",
		];
	}
}

/**
 * Configuration-specific error class
 */
class ConfigurationError extends PercyError {
	constructor(message, configField = null, resolutionSteps = []) {
		super(message, "configuration", true);
		this.name = "ConfigurationError";
		this.configField = configField;
		this.resolutionSteps = resolutionSteps;
	}

	getResolutionSteps() {
		if (this.resolutionSteps.length > 0) {
			return this.resolutionSteps;
		}

		switch (this.configField) {
			case "token":
				return [
					"1. Obtain Percy token from BrowserStack Percy dashboard: https://percy.io/",
					"2. Set PERCY_TOKEN environment variable",
					"3. Ensure token has proper permissions for the project",
					"4. Verify token is not expired",
				];
			case "project":
				return [
					"1. Verify PERCY_PROJECT environment variable is set",
					"2. Check project name matches Percy dashboard",
					"3. Ensure project exists and is accessible",
				];
			case "cli":
				return [
					"1. Install Percy CLI: npm install --save-dev @percy/cli",
					"2. Ensure npm dependencies are installed",
					"3. Check PATH configuration",
					"4. Verify Node.js version compatibility",
				];
			default:
				return super.getResolutionSteps();
		}
	}
}

/**
 * Network-specific error class
 */
class NetworkError extends PercyError {
	constructor(message, operation = null, retryCount = 0, maxRetries = 3) {
		super(message, "network", false);
		this.name = "NetworkError";
		this.operation = operation;
		this.retryCount = retryCount;
		this.maxRetries = maxRetries;
		this.nextRetryDelay = this.calculateRetryDelay();
	}

	shouldRetry() {
		return this.retryCount < this.maxRetries;
	}

	calculateRetryDelay() {
		// Exponential backoff: 1s, 2s, 4s, 8s, etc.
		return Math.min(1000 * Math.pow(2, this.retryCount), 30000);
	}

	incrementRetry() {
		this.retryCount++;
		this.nextRetryDelay = this.calculateRetryDelay();
		return this;
	}
}

/**
 * Service-specific error class
 */
class ServiceError extends PercyError {
	constructor(message, service = "percy", statusCode = null) {
		super(message, "service", false);
		this.name = "ServiceError";
		this.service = service;
		this.statusCode = statusCode;
	}

	getResolutionSteps() {
		const steps = [
			"1. Check Percy service status: https://status.percy.io/",
			"2. Verify network connectivity",
			"3. Check firewall and proxy settings",
		];

		if (this.statusCode) {
			switch (this.statusCode) {
				case 401:
					steps.unshift("Authentication failed - check PERCY_TOKEN");
					break;
				case 403:
					steps.unshift("Access forbidden - verify project permissions");
					break;
				case 429:
					steps.unshift("Rate limit exceeded - wait before retrying");
					break;
				case 500:
				case 502:
				case 503:
					steps.unshift("Percy service temporarily unavailable");
					break;
			}
		}

		return steps;
	}
}

/**
 * Critical error class that should stop execution
 */
class PercyCriticalError extends PercyError {
	constructor(message) {
		super(message, "critical", true);
		this.name = "PercyCriticalError";
	}
}

/**
 * Comprehensive error handler for Percy integration
 */
class PercyErrorHandler {
	constructor(options = {}) {
		this.options = {
			enableGracefulDegradation: options.enableGracefulDegradation !== false,
			maxRetries: options.maxRetries || 3,
			retryDelay: options.retryDelay || 1000,
			logLevel: options.logLevel || "info",
			failOnError: options.failOnError === true,
			...options,
		};

		this.percyEnabled = true;
		this.retryOperations = new Map();
		this.logger = this.createLogger();
		this.errorStats = {
			total: 0,
			byType: {},
			recovered: 0,
			critical: 0,
		};
	}

	/**
	 * Create a logger instance with appropriate log levels
	 */
	createLogger() {
		const logLevels = ["error", "warn", "info", "debug"];
		const currentLevelIndex = logLevels.indexOf(this.options.logLevel);

		return {
			error: (message, data = {}) => {
				if (currentLevelIndex >= 0) {
					console.error(`[percy-error-handler] ERROR: ${message}`, data);
				}
			},
			warn: (message, data = {}) => {
				if (currentLevelIndex >= 1) {
					console.warn(`[percy-error-handler] WARNING: ${message}`, data);
				}
			},
			info: (message, data = {}) => {
				if (currentLevelIndex >= 2) {
					console.log(`[percy-error-handler] INFO: ${message}`, data);
				}
			},
			debug: (message, data = {}) => {
				if (currentLevelIndex >= 3) {
					console.log(`[percy-error-handler] DEBUG: ${message}`, data);
				}
			},
		};
	}

	/**
	 * Handle configuration errors with detailed resolution steps
	 */
	handleConfigurationError(error) {
		this.updateErrorStats("configuration");

		this.logger.error("Percy Configuration Error", {
			type: error.type,
			message: error.message,
			configField: error.configField,
			resolution: error.getResolutionSteps(),
			timestamp: error.timestamp,
		});

		if (error.isCritical()) {
			this.errorStats.critical++;

			if (this.options.failOnError) {
				throw new PercyCriticalError(
					`Critical configuration error: ${error.message}`
				);
			} else {
				this.logger.warn(
					"Critical configuration error detected, enabling graceful degradation"
				);
				this.gracefulDegradation();
			}
		}

		return {
			handled: true,
			critical: error.isCritical(),
			resolution: error.getResolutionSteps(),
			gracefulDegradation:
				!error.isCritical() || this.options.enableGracefulDegradation,
		};
	}

	/**
	 * Handle network errors with automatic retry logic
	 */
	async handleNetworkError(error) {
		this.updateErrorStats("network");

		this.logger.warn("Percy Network Error", {
			message: error.message,
			operation: error.operation,
			retryCount: error.retryCount,
			maxRetries: error.maxRetries,
			nextRetryIn: error.nextRetryDelay,
			timestamp: error.timestamp,
		});

		if (error.shouldRetry()) {
			return await this.retryOperation(error);
		} else {
			this.logger.error("Network error retry limit exceeded", {
				operation: error.operation,
				finalRetryCount: error.retryCount,
			});

			if (this.options.enableGracefulDegradation) {
				this.gracefulDegradation();
				return { handled: true, gracefulDegradation: true };
			} else {
				throw error;
			}
		}
	}

	/**
	 * Handle service errors with appropriate responses
	 */
	handleServiceError(error) {
		this.updateErrorStats("service");

		this.logger.error("Percy Service Error", {
			service: error.service,
			statusCode: error.statusCode,
			message: error.message,
			resolution: error.getResolutionSteps(),
			timestamp: error.timestamp,
		});

		// Determine if this is a temporary or permanent failure
		const temporaryErrors = [429, 500, 502, 503, 504];
		const isTemporary = temporaryErrors.includes(error.statusCode);

		if (isTemporary && this.options.enableGracefulDegradation) {
			this.logger.info(
				"Temporary service error detected, enabling graceful degradation"
			);
			this.gracefulDegradation();
			return { handled: true, temporary: true, gracefulDegradation: true };
		}

		if (this.options.failOnError) {
			throw error;
		} else {
			this.gracefulDegradation();
			return { handled: true, gracefulDegradation: true };
		}
	}

	/**
	 * Retry operation with exponential backoff
	 */
	async retryOperation(error) {
		const operationKey = error.operation || "unknown";

		this.logger.info(`Retrying operation: ${operationKey}`, {
			attempt: error.retryCount + 1,
			maxRetries: error.maxRetries,
			delay: error.nextRetryDelay,
		});

		// Wait for the calculated delay
		await this.sleep(error.nextRetryDelay);

		// Increment retry count
		error.incrementRetry();

		// Store retry information
		this.retryOperations.set(operationKey, {
			error,
			lastRetry: new Date(),
			totalRetries: error.retryCount,
		});

		return {
			shouldRetry: true,
			retryCount: error.retryCount,
			nextDelay: error.nextRetryDelay,
		};
	}

	/**
	 * Enable graceful degradation mode
	 */
	gracefulDegradation() {
		if (!this.options.enableGracefulDegradation) {
			this.logger.warn(
				"Graceful degradation is disabled, errors will be thrown"
			);
			return false;
		}

		this.logger.info(
			"Percy unavailable, continuing tests without visual captures"
		);
		this.percyEnabled = false;

		// Set environment variable to indicate Percy is disabled
		process.env.PERCY_ENABLED = "false";
		process.env.PERCY_SKIP_UPLOADS = "true";

		return true;
	}

	/**
	 * Check if Percy is currently enabled
	 */
	isPercyEnabled() {
		return this.percyEnabled && process.env.PERCY_ENABLED !== "false";
	}

	/**
	 * Handle generic errors with appropriate classification
	 */
	handleError(error) {
		// Classify the error type
		if (error instanceof ConfigurationError) {
			return this.handleConfigurationError(error);
		} else if (error instanceof NetworkError) {
			return this.handleNetworkError(error);
		} else if (error instanceof ServiceError) {
			return this.handleServiceError(error);
		} else if (error instanceof PercyCriticalError) {
			this.updateErrorStats("critical");
			this.logger.error("Critical Percy Error", {
				message: error.message,
				timestamp: error.timestamp,
			});
			throw error;
		} else {
			// Handle unknown errors
			return this.handleUnknownError(error);
		}
	}

	/**
	 * Handle unknown errors with generic recovery
	 */
	handleUnknownError(error) {
		this.updateErrorStats("unknown");

		this.logger.error("Unknown Percy Error", {
			name: error.name,
			message: error.message,
			stack: error.stack,
			timestamp: new Date(),
		});

		if (this.options.enableGracefulDegradation) {
			this.logger.info("Unknown error detected, enabling graceful degradation");
			this.gracefulDegradation();
			return { handled: true, gracefulDegradation: true };
		} else {
			throw error;
		}
	}

	/**
	 * Update error statistics
	 */
	updateErrorStats(type) {
		this.errorStats.total++;
		this.errorStats.byType[type] = (this.errorStats.byType[type] || 0) + 1;
	}

	/**
	 * Get error statistics
	 */
	getErrorStats() {
		return {
			...this.errorStats,
			percyEnabled: this.percyEnabled,
			retryOperations: Array.from(this.retryOperations.entries()).map(
				([key, value]) => ({
					operation: key,
					totalRetries: value.totalRetries,
					lastRetry: value.lastRetry,
				})
			),
		};
	}

	/**
	 * Reset error handler state
	 */
	reset() {
		this.percyEnabled = true;
		this.retryOperations.clear();
		this.errorStats = {
			total: 0,
			byType: {},
			recovered: 0,
			critical: 0,
		};

		// Reset environment variables
		if (process.env.PERCY_ENABLED === "false") {
			delete process.env.PERCY_ENABLED;
		}
		if (process.env.PERCY_SKIP_UPLOADS === "true") {
			delete process.env.PERCY_SKIP_UPLOADS;
		}

		this.logger.info("Percy error handler reset");
	}

	/**
	 * Generate comprehensive error report
	 */
	generateErrorReport() {
		const report = {
			summary: {
				totalErrors: this.errorStats.total,
				criticalErrors: this.errorStats.critical,
				recoveredErrors: this.errorStats.recovered,
				percyEnabled: this.percyEnabled,
				gracefulDegradationEnabled: this.options.enableGracefulDegradation,
			},
			errorsByType: this.errorStats.byType,
			retryOperations: this.getErrorStats().retryOperations,
			configuration: {
				maxRetries: this.options.maxRetries,
				retryDelay: this.options.retryDelay,
				failOnError: this.options.failOnError,
				logLevel: this.options.logLevel,
			},
			timestamp: new Date().toISOString(),
			environment: {
				nodeVersion: process.version,
				platform: process.platform,
				percyToken: process.env.PERCY_TOKEN ? "[CONFIGURED]" : "[MISSING]",
				percyProject: process.env.PERCY_PROJECT || "[NOT SET]",
			},
		};

		this.logger.info("Error report generated", report);
		return report;
	}

	/**
	 * Save error report to file
	 */
	async saveErrorReport(filePath = null) {
		const report = this.generateErrorReport();
		const defaultPath = path.join(
			process.cwd(),
			"percy-reports",
			`error-report-${Date.now()}.json`
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

			this.logger.info(`Error report saved to: ${targetPath}`);
			return targetPath;
		} catch (error) {
			this.logger.error("Failed to save error report", {
				targetPath,
				error: error.message,
			});
			throw error;
		}
	}

	/**
	 * Utility function for async sleep
	 */
	sleep(ms) {
		return new Promise((resolve) => setTimeout(resolve, ms));
	}

	/**
	 * Create error instances for different scenarios
	 */
	static createConfigurationError(
		message,
		configField = null,
		resolutionSteps = []
	) {
		return new ConfigurationError(message, configField, resolutionSteps);
	}

	static createNetworkError(
		message,
		operation = null,
		retryCount = 0,
		maxRetries = 3
	) {
		return new NetworkError(message, operation, retryCount, maxRetries);
	}

	static createServiceError(message, service = "percy", statusCode = null) {
		return new ServiceError(message, service, statusCode);
	}

	static createCriticalError(message) {
		return new PercyCriticalError(message);
	}
}

// Export classes and error handler
module.exports = {
	PercyErrorHandler,
	PercyError,
	ConfigurationError,
	NetworkError,
	ServiceError,
	PercyCriticalError,
};

// CLI interface for testing error handler
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const errorHandler = new PercyErrorHandler({
		enableGracefulDegradation: true,
		maxRetries: 3,
		logLevel: "debug",
	});

	async function main() {
		try {
			switch (command) {
				case "test-config-error":
					const configError = PercyErrorHandler.createConfigurationError(
						"Percy token is missing",
						"token"
					);
					errorHandler.handleConfigurationError(configError);
					break;

				case "test-network-error":
					const networkError = PercyErrorHandler.createNetworkError(
						"Connection timeout",
						"snapshot-upload",
						0,
						3
					);
					await errorHandler.handleNetworkError(networkError);
					break;

				case "test-service-error":
					const serviceError = PercyErrorHandler.createServiceError(
						"Percy API unavailable",
						"percy",
						503
					);
					errorHandler.handleServiceError(serviceError);
					break;

				case "generate-report":
					const report = errorHandler.generateErrorReport();
					console.log(JSON.stringify(report, null, 2));
					break;

				case "save-report":
					const filePath = await errorHandler.saveErrorReport();
					console.log(`Report saved to: ${filePath}`);
					break;

				case "stats":
					const stats = errorHandler.getErrorStats();
					console.log(JSON.stringify(stats, null, 2));
					break;

				default:
					console.log(`
Percy Error Handler for ICTServe v3.6.1

Usage:
  node percy-error-handler.cjs <command>

Commands:
  test-config-error     Test configuration error handling
  test-network-error    Test network error handling with retry
  test-service-error    Test service error handling
  generate-report       Generate error report
  save-report          Save error report to file
  stats                Show error statistics

Examples:
  node percy-error-handler.cjs test-config-error
  node percy-error-handler.cjs generate-report
  node percy-error-handler.cjs save-report
                    `);
					break;
			}
		} catch (error) {
			console.error("Error Handler Test Failed:", error.message);
			process.exit(1);
		}
	}

	main();
}
