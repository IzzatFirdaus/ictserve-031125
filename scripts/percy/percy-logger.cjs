#!/usr/bin/env node

/**
 * Percy Logger for ICTServe v3.6.1
 *
 * This utility provides detailed logging for debugging Percy integration issues:
 * - Structured logging with different levels (error, warn, info, debug)
 * - File-based logging with rotation
 * - Performance metrics tracking
 * - Integration with error handler for comprehensive debugging
 * - Bahasa Melayu error messages for v3.6.1 compatibility
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const fs = require("fs");
const path = require("path");

/**
 * Comprehensive logging utility for Percy integration
 */
class PercyLogger {
	constructor(options = {}) {
		this.options = {
			logLevel: options.logLevel || "info",
			logToFile: options.logToFile !== false,
			logToConsole: options.logToConsole !== false,
			logDirectory:
				options.logDirectory || path.join(process.cwd(), "percy-logs"),
			maxLogFiles: options.maxLogFiles || 10,
			maxLogSize: options.maxLogSize || 10 * 1024 * 1024, // 10MB
			includeTimestamp: options.includeTimestamp !== false,
			includeLevel: options.includeLevel !== false,
			includeMetadata: options.includeMetadata !== false,
			colorOutput: options.colorOutput !== false,
			bahasaMelayu: options.bahasaMelayu === true, // v3.6.1 Bahasa Melayu support
			...options,
		};

		this.logLevels = {
			error: 0,
			warn: 1,
			info: 2,
			debug: 3,
		};

		this.currentLogLevel =
			this.logLevels[this.options.logLevel] || this.logLevels.info;
		this.logFiles = new Map();
		this.performanceMetrics = {
			startTime: Date.now(),
			operations: [],
			errors: [],
			snapshots: [],
		};

		// Colors for console output
		this.colors = {
			error: "\x1b[31m", // Red
			warn: "\x1b[33m", // Yellow
			info: "\x1b[36m", // Cyan
			debug: "\x1b[37m", // White
			reset: "\x1b[0m", // Reset
		};

		// Bahasa Melayu messages for v3.6.1
		this.messages = {
			en: {
				percyDisabled: "Percy is disabled, skipping operation",
				configError: "Percy configuration error",
				networkError: "Percy network error",
				serviceError: "Percy service error",
				buildCreated: "Percy build created successfully",
				snapshotCaptured: "Percy snapshot captured",
				buildFinalized: "Percy build finalized",
				gracefulDegradation:
					"Percy unavailable, continuing without visual captures",
			},
			ms: {
				percyDisabled: "Percy dimatikan, melangkau operasi",
				configError: "Ralat konfigurasi Percy",
				networkError: "Ralat rangkaian Percy",
				serviceError: "Ralat perkhidmatan Percy",
				buildCreated: "Binaan Percy berjaya dicipta",
				snapshotCaptured: "Tangkapan skrin Percy diambil",
				buildFinalized: "Binaan Percy diselesaikan",
				gracefulDegradation:
					"Percy tidak tersedia, meneruskan tanpa tangkapan visual",
			},
		};

		this.currentLanguage = this.options.bahasaMelayu ? "ms" : "en";

		this.ensureLogDirectory();
	}

	/**
	 * Ensure log directory exists
	 */
	ensureLogDirectory() {
		if (this.options.logToFile && !fs.existsSync(this.options.logDirectory)) {
			fs.mkdirSync(this.options.logDirectory, { recursive: true });
		}
	}

	/**
	 * Get localized message
	 */
	getMessage(key, fallback = null) {
		return (
			this.messages[this.currentLanguage][key] ||
			this.messages.en[key] ||
			fallback ||
			key
		);
	}

	/**
	 * Format log message with timestamp and metadata
	 */
	formatMessage(level, message, metadata = {}) {
		const parts = [];

		if (this.options.includeTimestamp) {
			parts.push(`[${new Date().toISOString()}]`);
		}

		if (this.options.includeLevel) {
			parts.push(`[${level.toUpperCase()}]`);
		}

		parts.push(`[percy-logger]`);
		parts.push(message);

		let formattedMessage = parts.join(" ");

		if (this.options.includeMetadata && Object.keys(metadata).length > 0) {
			formattedMessage += ` ${JSON.stringify(metadata)}`;
		}

		return formattedMessage;
	}

	/**
	 * Apply color to console output
	 */
	colorize(level, message) {
		if (!this.options.colorOutput) {
			return message;
		}

		const color = this.colors[level] || this.colors.info;
		return `${color}${message}${this.colors.reset}`;
	}

	/**
	 * Write log to file
	 */
	writeToFile(level, formattedMessage) {
		if (!this.options.logToFile) {
			return;
		}

		const logFileName = `percy-${level}-${
			new Date().toISOString().split("T")[0]
		}.log`;
		const logFilePath = path.join(this.options.logDirectory, logFileName);

		try {
			// Check file size and rotate if necessary
			if (fs.existsSync(logFilePath)) {
				const stats = fs.statSync(logFilePath);
				if (stats.size > this.options.maxLogSize) {
					this.rotateLogFile(logFilePath);
				}
			}

			// Append to log file
			fs.appendFileSync(logFilePath, formattedMessage + "\n");

			// Track log file
			this.logFiles.set(level, logFilePath);
		} catch (error) {
			console.error(`Failed to write to log file: ${error.message}`);
		}
	}

	/**
	 * Rotate log file when it gets too large
	 */
	rotateLogFile(logFilePath) {
		const timestamp = Date.now();
		const rotatedPath = logFilePath.replace(".log", `-${timestamp}.log`);

		try {
			fs.renameSync(logFilePath, rotatedPath);
			this.cleanupOldLogFiles();
		} catch (error) {
			console.error(`Failed to rotate log file: ${error.message}`);
		}
	}

	/**
	 * Clean up old log files
	 */
	cleanupOldLogFiles() {
		try {
			const files = fs
				.readdirSync(this.options.logDirectory)
				.filter((file) => file.startsWith("percy-") && file.endsWith(".log"))
				.map((file) => ({
					name: file,
					path: path.join(this.options.logDirectory, file),
					mtime: fs.statSync(path.join(this.options.logDirectory, file)).mtime,
				}))
				.sort((a, b) => b.mtime - a.mtime);

			// Remove old files beyond maxLogFiles
			if (files.length > this.options.maxLogFiles) {
				const filesToDelete = files.slice(this.options.maxLogFiles);
				filesToDelete.forEach((file) => {
					try {
						fs.unlinkSync(file.path);
					} catch (error) {
						console.error(
							`Failed to delete old log file ${file.name}: ${error.message}`
						);
					}
				});
			}
		} catch (error) {
			console.error(`Failed to cleanup old log files: ${error.message}`);
		}
	}

	/**
	 * Log message at specified level
	 */
	log(level, message, metadata = {}) {
		if (this.logLevels[level] > this.currentLogLevel) {
			return;
		}

		const formattedMessage = this.formatMessage(level, message, metadata);

		// Console output
		if (this.options.logToConsole) {
			const colorizedMessage = this.colorize(level, formattedMessage);

			switch (level) {
				case "error":
					console.error(colorizedMessage);
					break;
				case "warn":
					console.warn(colorizedMessage);
					break;
				default:
					console.log(colorizedMessage);
					break;
			}
		}

		// File output
		this.writeToFile(level, formattedMessage);

		// Track performance metrics
		this.trackMetrics(level, message, metadata);
	}

	/**
	 * Track performance metrics
	 */
	trackMetrics(level, message, metadata) {
		const timestamp = Date.now();
		const operation = {
			timestamp,
			level,
			message,
			metadata,
			duration: metadata.duration || null,
		};

		this.performanceMetrics.operations.push(operation);

		// Track specific types of operations
		if (level === "error") {
			this.performanceMetrics.errors.push(operation);
		}

		if (message.includes("snapshot") || message.includes("tangkapan")) {
			this.performanceMetrics.snapshots.push(operation);
		}

		// Limit metrics history to prevent memory issues
		const maxOperations = 1000;
		if (this.performanceMetrics.operations.length > maxOperations) {
			this.performanceMetrics.operations =
				this.performanceMetrics.operations.slice(-maxOperations);
		}
	}

	/**
	 * Error level logging
	 */
	error(message, metadata = {}) {
		this.log("error", message, metadata);
	}

	/**
	 * Warning level logging
	 */
	warn(message, metadata = {}) {
		this.log("warn", message, metadata);
	}

	/**
	 * Info level logging
	 */
	info(message, metadata = {}) {
		this.log("info", message, metadata);
	}

	/**
	 * Debug level logging
	 */
	debug(message, metadata = {}) {
		this.log("debug", message, metadata);
	}

	/**
	 * Log Percy-specific operations with localized messages
	 */
	logPercyOperation(operation, status, metadata = {}) {
		const operationMessages = {
			"build-create": {
				success: this.getMessage("buildCreated"),
				error: this.getMessage("configError"),
			},
			"snapshot-capture": {
				success: this.getMessage("snapshotCaptured"),
				error: this.getMessage("networkError"),
			},
			"build-finalize": {
				success: this.getMessage("buildFinalized"),
				error: this.getMessage("serviceError"),
			},
			"graceful-degradation": {
				info: this.getMessage("gracefulDegradation"),
			},
		};

		const message =
			operationMessages[operation]?.[status] || `Percy ${operation} ${status}`;
		const level =
			status === "error" ? "error" : status === "success" ? "info" : "warn";

		this.log(level, message, {
			operation,
			status,
			...metadata,
		});
	}

	/**
	 * Log performance timing
	 */
	logTiming(operation, startTime, endTime = Date.now(), metadata = {}) {
		const duration = endTime - startTime;

		this.info(`Performance: ${operation} completed`, {
			operation,
			duration: `${duration}ms`,
			startTime: new Date(startTime).toISOString(),
			endTime: new Date(endTime).toISOString(),
			...metadata,
		});

		return duration;
	}

	/**
	 * Start performance timing
	 */
	startTiming(operation) {
		const startTime = Date.now();
		this.debug(`Performance: Starting ${operation}`, { operation, startTime });
		return startTime;
	}

	/**
	 * End performance timing
	 */
	endTiming(operation, startTime, metadata = {}) {
		return this.logTiming(operation, startTime, Date.now(), metadata);
	}

	/**
	 * Get performance metrics
	 */
	getPerformanceMetrics() {
		const currentTime = Date.now();
		const totalDuration = currentTime - this.performanceMetrics.startTime;

		return {
			totalDuration: `${totalDuration}ms`,
			totalOperations: this.performanceMetrics.operations.length,
			totalErrors: this.performanceMetrics.errors.length,
			totalSnapshots: this.performanceMetrics.snapshots.length,
			averageOperationTime: this.calculateAverageOperationTime(),
			errorRate: this.calculateErrorRate(),
			recentOperations: this.performanceMetrics.operations.slice(-10),
			recentErrors: this.performanceMetrics.errors.slice(-5),
		};
	}

	/**
	 * Calculate average operation time
	 */
	calculateAverageOperationTime() {
		const operationsWithDuration = this.performanceMetrics.operations.filter(
			(op) => op.duration !== null && op.metadata.duration
		);

		if (operationsWithDuration.length === 0) {
			return "0ms";
		}

		const totalDuration = operationsWithDuration.reduce((sum, op) => {
			const duration = parseInt(op.metadata.duration.replace("ms", ""));
			return sum + (isNaN(duration) ? 0 : duration);
		}, 0);

		const average = totalDuration / operationsWithDuration.length;
		return `${Math.round(average)}ms`;
	}

	/**
	 * Calculate error rate
	 */
	calculateErrorRate() {
		if (this.performanceMetrics.operations.length === 0) {
			return "0%";
		}

		const errorRate =
			(this.performanceMetrics.errors.length /
				this.performanceMetrics.operations.length) *
			100;
		return `${Math.round(errorRate * 100) / 100}%`;
	}

	/**
	 * Generate comprehensive log report
	 */
	generateLogReport() {
		const report = {
			summary: {
				logLevel: this.options.logLevel,
				language: this.currentLanguage,
				logToFile: this.options.logToFile,
				logToConsole: this.options.logToConsole,
				logDirectory: this.options.logDirectory,
			},
			performance: this.getPerformanceMetrics(),
			logFiles: Array.from(this.logFiles.entries()).map(([level, path]) => ({
				level,
				path,
				exists: fs.existsSync(path),
				size: fs.existsSync(path) ? fs.statSync(path).size : 0,
			})),
			configuration: this.options,
			timestamp: new Date().toISOString(),
			environment: {
				nodeVersion: process.version,
				platform: process.platform,
				arch: process.arch,
				cwd: process.cwd(),
			},
		};

		this.info("Log report generated", {
			reportSize: JSON.stringify(report).length,
		});
		return report;
	}

	/**
	 * Save log report to file
	 */
	async saveLogReport(filePath = null) {
		const report = this.generateLogReport();
		const defaultPath = path.join(
			this.options.logDirectory,
			`percy-log-report-${Date.now()}.json`
		);
		const targetPath = filePath || defaultPath;

		try {
			this.ensureLogDirectory();
			fs.writeFileSync(targetPath, JSON.stringify(report, null, 2));

			this.info(`Log report saved to: ${targetPath}`);
			return targetPath;
		} catch (error) {
			this.error("Failed to save log report", {
				targetPath,
				error: error.message,
			});
			throw error;
		}
	}

	/**
	 * Clear performance metrics
	 */
	clearMetrics() {
		this.performanceMetrics = {
			startTime: Date.now(),
			operations: [],
			errors: [],
			snapshots: [],
		};
		this.info("Performance metrics cleared");
	}

	/**
	 * Set log level dynamically
	 */
	setLogLevel(level) {
		if (this.logLevels.hasOwnProperty(level)) {
			this.options.logLevel = level;
			this.currentLogLevel = this.logLevels[level];
			this.info(`Log level changed to: ${level}`);
		} else {
			this.warn(
				`Invalid log level: ${level}. Valid levels: ${Object.keys(
					this.logLevels
				).join(", ")}`
			);
		}
	}

	/**
	 * Set language for localized messages
	 */
	setLanguage(language) {
		if (this.messages.hasOwnProperty(language)) {
			this.currentLanguage = language;
			this.options.bahasaMelayu = language === "ms";
			this.info(`Language changed to: ${language}`);
		} else {
			this.warn(
				`Invalid language: ${language}. Valid languages: ${Object.keys(
					this.messages
				).join(", ")}`
			);
		}
	}

	/**
	 * Get log file paths
	 */
	getLogFilePaths() {
		return Array.from(this.logFiles.entries()).reduce(
			(paths, [level, path]) => {
				paths[level] = path;
				return paths;
			},
			{}
		);
	}

	/**
	 * Tail log file (get recent entries)
	 */
	tailLogFile(level, lines = 50) {
		const logFilePath = this.logFiles.get(level);

		if (!logFilePath || !fs.existsSync(logFilePath)) {
			return [];
		}

		try {
			const content = fs.readFileSync(logFilePath, "utf8");
			const allLines = content.split("\n").filter((line) => line.trim());
			return allLines.slice(-lines);
		} catch (error) {
			this.error(`Failed to tail log file: ${error.message}`);
			return [];
		}
	}
}

// Export logger class
module.exports = PercyLogger;

// CLI interface for testing logger
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const logger = new PercyLogger({
		logLevel: "debug",
		logToFile: true,
		logToConsole: true,
		bahasaMelayu: args.includes("--bahasa-melayu"),
	});

	async function main() {
		try {
			switch (command) {
				case "test-logging":
					logger.error("Test error message", { testData: "error" });
					logger.warn("Test warning message", { testData: "warning" });
					logger.info("Test info message", { testData: "info" });
					logger.debug("Test debug message", { testData: "debug" });
					break;

				case "test-percy-operations":
					logger.logPercyOperation("build-create", "success", {
						buildId: "test-123",
					});
					logger.logPercyOperation("snapshot-capture", "success", {
						snapshotName: "test-snapshot",
					});
					logger.logPercyOperation("build-finalize", "error", {
						error: "Network timeout",
					});
					break;

				case "test-timing":
					const startTime = logger.startTiming("test-operation");
					await new Promise((resolve) => setTimeout(resolve, 1000));
					logger.endTiming("test-operation", startTime, { result: "success" });
					break;

				case "performance-metrics":
					const metrics = logger.getPerformanceMetrics();
					console.log(JSON.stringify(metrics, null, 2));
					break;

				case "generate-report":
					const report = logger.generateLogReport();
					console.log(JSON.stringify(report, null, 2));
					break;

				case "save-report":
					const reportPath = await logger.saveLogReport();
					console.log(`Report saved to: ${reportPath}`);
					break;

				case "tail-logs":
					const level = args[1] || "info";
					const lines = parseInt(args[2]) || 10;
					const tailLines = logger.tailLogFile(level, lines);
					console.log(`Last ${lines} lines from ${level} log:`);
					tailLines.forEach((line) => console.log(line));
					break;

				case "set-level":
					const newLevel = args[1];
					if (newLevel) {
						logger.setLogLevel(newLevel);
					} else {
						console.log("Usage: node percy-logger.cjs set-level <level>");
					}
					break;

				case "set-language":
					const newLanguage = args[1];
					if (newLanguage) {
						logger.setLanguage(newLanguage);
					} else {
						console.log("Usage: node percy-logger.cjs set-language <language>");
					}
					break;

				default:
					console.log(`
Percy Logger for ICTServe v3.6.1

Usage:
  node percy-logger.cjs <command> [options]

Commands:
  test-logging          Test all log levels
  test-percy-operations Test Percy-specific logging
  test-timing          Test performance timing
  performance-metrics   Show performance metrics
  generate-report      Generate comprehensive log report
  save-report          Save log report to file
  tail-logs [level] [lines]  Show recent log entries
  set-level <level>    Change log level
  set-language <lang>  Change language (en/ms)

Options:
  --bahasa-melayu      Use Bahasa Melayu messages

Examples:
  node percy-logger.cjs test-logging
  node percy-logger.cjs test-percy-operations --bahasa-melayu
  node percy-logger.cjs tail-logs error 20
  node percy-logger.cjs set-level debug
                    `);
					break;
			}
		} catch (error) {
			console.error("Logger Test Failed:", error.message);
			process.exit(1);
		}
	}

	main();
}
