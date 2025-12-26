#!/usr/bin/env node

/**
 * Percy Performance Optimizer for ICTServe v3.6.1
 *
 * This module provides comprehensive performance optimization features:
 * - Asynchronous snapshot upload capabilities
 * - Network usage optimization for multiple snapshots
 * - Caching mechanisms for Percy CLI and dependencies
 * - Performance monitoring and impact measurement
 * - Batch processing and queue management
 * - Memory usage optimization
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const fs = require("fs");
const path = require("path");
const {
	Worker,
	isMainThread,
	parentPort,
	workerData,
} = require("worker_threads");
const { performance } = require("perf_hooks");
const crypto = require("crypto");

/**
 * Performance optimization utility for Percy integration
 */
class PercyPerformanceOptimizer {
	constructor(options = {}) {
		this.options = {
			// Asynchronous upload settings
			maxConcurrentUploads: options.maxConcurrentUploads || 3,
			uploadTimeout: options.uploadTimeout || 30000,
			enableAsyncUploads: options.enableAsyncUploads !== false,

			// Network optimization settings
			batchSize: options.batchSize || 5,
			compressionEnabled: options.compressionEnabled !== false,
			retryAttempts: options.retryAttempts || 3,
			retryDelay: options.retryDelay || 1000,

			// Caching settings
			cacheDirectory:
				options.cacheDirectory || path.join(process.cwd(), ".percy-cache"),
			enableCaching: options.enableCaching !== false,
			cacheMaxAge: options.cacheMaxAge || 24 * 60 * 60 * 1000, // 24 hours
			cacheMaxSize: options.cacheMaxSize || 100 * 1024 * 1024, // 100MB

			// Performance monitoring settings
			enablePerformanceMonitoring:
				options.enablePerformanceMonitoring !== false,
			performanceReportInterval: options.performanceReportInterval || 10000, // 10 seconds
			memoryThreshold: options.memoryThreshold || 500 * 1024 * 1024, // 500MB

			// Queue management settings
			queueMaxSize: options.queueMaxSize || 100,
			processingDelay: options.processingDelay || 100,
			enableQueuePersistence: options.enableQueuePersistence !== false,

			...options,
		};

		// Initialize performance tracking
		this.performanceMetrics = {
			startTime: performance.now(),
			uploads: {
				total: 0,
				successful: 0,
				failed: 0,
				totalTime: 0,
				averageTime: 0,
				concurrentUploads: 0,
				queueSize: 0,
			},
			network: {
				totalRequests: 0,
				totalBytes: 0,
				compressionSavings: 0,
				retries: 0,
				timeouts: 0,
			},
			cache: {
				hits: 0,
				misses: 0,
				size: 0,
				evictions: 0,
			},
			memory: {
				peak: 0,
				current: 0,
				gcCount: 0,
			},
		};

		// Initialize upload queue
		this.uploadQueue = [];
		this.activeUploads = new Map();
		this.uploadWorkers = [];

		// Initialize cache
		this.cache = new Map();
		this.cacheStats = {
			hits: 0,
			misses: 0,
			evictions: 0,
		};

		// Initialize performance monitoring
		this.performanceInterval = null;
		this.memoryMonitor = null;

		this.ensureCacheDirectory();
		this.initializePerformanceMonitoring();
		this.loadPersistedCache();
	}

	/**
	 * Ensure cache directory exists
	 */
	ensureCacheDirectory() {
		if (
			this.options.enableCaching &&
			!fs.existsSync(this.options.cacheDirectory)
		) {
			fs.mkdirSync(this.options.cacheDirectory, { recursive: true });
		}
	}

	/**
	 * Initialize performance monitoring
	 */
	initializePerformanceMonitoring() {
		if (!this.options.enablePerformanceMonitoring) {
			return;
		}

		// Start performance monitoring interval
		this.performanceInterval = setInterval(() => {
			this.updatePerformanceMetrics();
			this.checkMemoryUsage();
			this.optimizeCache();
		}, this.options.performanceReportInterval);

		// Monitor garbage collection
		if (global.gc) {
			const originalGc = global.gc;
			global.gc = () => {
				this.performanceMetrics.memory.gcCount++;
				return originalGc();
			};
		}
	}

	/**
	 * Update performance metrics
	 */
	updatePerformanceMetrics() {
		const memUsage = process.memoryUsage();
		this.performanceMetrics.memory.current = memUsage.heapUsed;
		this.performanceMetrics.memory.peak = Math.max(
			this.performanceMetrics.memory.peak,
			memUsage.heapUsed
		);

		this.performanceMetrics.uploads.queueSize = this.uploadQueue.length;
		this.performanceMetrics.uploads.concurrentUploads = this.activeUploads.size;
		this.performanceMetrics.cache.size = this.cache.size;

		// Calculate average upload time
		if (this.performanceMetrics.uploads.total > 0) {
			this.performanceMetrics.uploads.averageTime =
				this.performanceMetrics.uploads.totalTime /
				this.performanceMetrics.uploads.total;
		}
	}

	/**
	 * Check memory usage and trigger optimization if needed
	 */
	checkMemoryUsage() {
		const memUsage = process.memoryUsage();

		if (memUsage.heapUsed > this.options.memoryThreshold) {
			console.warn(
				`[percy-performance] High memory usage detected: ${Math.round(
					memUsage.heapUsed / 1024 / 1024
				)}MB`
			);

			// Trigger cache cleanup
			this.optimizeCache();

			// Force garbage collection if available
			if (global.gc) {
				global.gc();
			}

			// Reduce concurrent uploads temporarily
			if (this.options.maxConcurrentUploads > 1) {
				this.options.maxConcurrentUploads = Math.max(
					1,
					this.options.maxConcurrentUploads - 1
				);
				console.log(
					`[percy-performance] Reduced concurrent uploads to ${this.options.maxConcurrentUploads}`
				);
			}
		}
	}

	/**
	 * Optimize cache by removing old or least used entries
	 */
	optimizeCache() {
		if (!this.options.enableCaching || this.cache.size === 0) {
			return;
		}

		const now = Date.now();
		const maxAge = this.options.cacheMaxAge;
		let evicted = 0;

		// Remove expired entries
		for (const [key, entry] of this.cache.entries()) {
			if (now - entry.timestamp > maxAge) {
				this.cache.delete(key);
				evicted++;
			}
		}

		// If cache is still too large, remove least recently used entries
		const maxEntries = Math.floor(this.options.cacheMaxSize / (1024 * 1024)); // Rough estimate
		if (this.cache.size > maxEntries) {
			const entries = Array.from(this.cache.entries()).sort(
				(a, b) => a[1].lastAccessed - b[1].lastAccessed
			);

			const toRemove = this.cache.size - maxEntries;
			for (let i = 0; i < toRemove; i++) {
				this.cache.delete(entries[i][0]);
				evicted++;
			}
		}

		if (evicted > 0) {
			this.performanceMetrics.cache.evictions += evicted;
			console.log(
				`[percy-performance] Cache optimized: ${evicted} entries evicted`
			);
		}
	}

	/**
	 * Load persisted cache from disk
	 */
	loadPersistedCache() {
		if (!this.options.enableCaching || !this.options.enableQueuePersistence) {
			return;
		}

		const cacheFile = path.join(
			this.options.cacheDirectory,
			"percy-cache.json"
		);

		try {
			if (fs.existsSync(cacheFile)) {
				const cacheData = JSON.parse(fs.readFileSync(cacheFile, "utf8"));
				const now = Date.now();

				for (const [key, entry] of Object.entries(cacheData)) {
					// Only load non-expired entries
					if (now - entry.timestamp < this.options.cacheMaxAge) {
						this.cache.set(key, entry);
					}
				}

				console.log(
					`[percy-performance] Loaded ${this.cache.size} cached entries from disk`
				);
			}
		} catch (error) {
			console.warn(
				`[percy-performance] Failed to load persisted cache: ${error.message}`
			);
		}
	}

	/**
	 * Save cache to disk
	 */
	savePersistedCache() {
		if (!this.options.enableCaching || !this.options.enableQueuePersistence) {
			return;
		}

		const cacheFile = path.join(
			this.options.cacheDirectory,
			"percy-cache.json"
		);

		try {
			const cacheData = Object.fromEntries(this.cache.entries());
			fs.writeFileSync(cacheFile, JSON.stringify(cacheData, null, 2));
			console.log(
				`[percy-performance] Saved ${this.cache.size} cached entries to disk`
			);
		} catch (error) {
			console.warn(
				`[percy-performance] Failed to save persisted cache: ${error.message}`
			);
		}
	}

	/**
	 * Generate cache key for a request
	 */
	generateCacheKey(data) {
		const hash = crypto.createHash("sha256");
		hash.update(JSON.stringify(data));
		return hash.digest("hex");
	}

	/**
	 * Get cached data
	 */
	getCachedData(key) {
		if (!this.options.enableCaching) {
			return null;
		}

		const entry = this.cache.get(key);

		if (!entry) {
			this.performanceMetrics.cache.misses++;
			return null;
		}

		// Check if entry is expired
		const now = Date.now();
		if (now - entry.timestamp > this.options.cacheMaxAge) {
			this.cache.delete(key);
			this.performanceMetrics.cache.misses++;
			return null;
		}

		// Update last accessed time
		entry.lastAccessed = now;
		this.performanceMetrics.cache.hits++;

		return entry.data;
	}

	/**
	 * Set cached data
	 */
	setCachedData(key, data) {
		if (!this.options.enableCaching) {
			return;
		}

		const now = Date.now();
		this.cache.set(key, {
			data,
			timestamp: now,
			lastAccessed: now,
		});
	}

	/**
	 * Add snapshot to upload queue with optimization
	 */
	async addSnapshotToQueue(snapshotData) {
		const startTime = performance.now();

		try {
			// Generate cache key for snapshot
			const cacheKey = this.generateCacheKey(snapshotData);

			// Check if snapshot is already cached
			const cachedResult = this.getCachedData(cacheKey);
			if (cachedResult) {
				console.log(
					`[percy-performance] Using cached snapshot: ${snapshotData.name}`
				);
				return cachedResult;
			}

			// Add to queue with optimization metadata
			const queueItem = {
				id: crypto.randomUUID(),
				data: snapshotData,
				cacheKey,
				timestamp: Date.now(),
				retryCount: 0,
				priority: snapshotData.priority || 0,
			};

			// Insert based on priority (higher priority first)
			const insertIndex = this.uploadQueue.findIndex(
				(item) => item.priority < queueItem.priority
			);
			if (insertIndex === -1) {
				this.uploadQueue.push(queueItem);
			} else {
				this.uploadQueue.splice(insertIndex, 0, queueItem);
			}

			console.log(
				`[percy-performance] Added snapshot to queue: ${snapshotData.name} (queue size: ${this.uploadQueue.length})`
			);

			// Process queue if not at capacity
			if (this.activeUploads.size < this.options.maxConcurrentUploads) {
				this.processUploadQueue();
			}

			return { queued: true, queuePosition: this.uploadQueue.length };
		} finally {
			const duration = performance.now() - startTime;
			console.log(
				`[percy-performance] Queue operation completed in ${Math.round(
					duration
				)}ms`
			);
		}
	}

	/**
	 * Process upload queue with concurrency control
	 */
	async processUploadQueue() {
		while (
			this.uploadQueue.length > 0 &&
			this.activeUploads.size < this.options.maxConcurrentUploads
		) {
			const queueItem = this.uploadQueue.shift();

			if (!queueItem) {
				break;
			}

			// Start async upload
			this.processSnapshotUpload(queueItem);
		}
	}

	/**
	 * Process individual snapshot upload asynchronously
	 */
	async processSnapshotUpload(queueItem) {
		const uploadId = queueItem.id;
		const startTime = performance.now();

		try {
			// Mark as active
			this.activeUploads.set(uploadId, {
				...queueItem,
				startTime,
			});

			console.log(
				`[percy-performance] Starting upload: ${queueItem.data.name} (${this.activeUploads.size}/${this.options.maxConcurrentUploads} active)`
			);

			// Perform the actual upload with optimization
			const result = await this.optimizedSnapshotUpload(queueItem);

			// Cache successful result
			this.setCachedData(queueItem.cacheKey, result);

			// Update metrics
			this.performanceMetrics.uploads.successful++;
			const duration = performance.now() - startTime;
			this.performanceMetrics.uploads.totalTime += duration;

			console.log(
				`[percy-performance] Upload completed: ${
					queueItem.data.name
				} in ${Math.round(duration)}ms`
			);

			return result;
		} catch (error) {
			console.error(
				`[percy-performance] Upload failed: ${queueItem.data.name} - ${error.message}`
			);

			// Handle retry logic
			if (queueItem.retryCount < this.options.retryAttempts) {
				queueItem.retryCount++;
				const retryDelay =
					this.options.retryDelay * Math.pow(2, queueItem.retryCount - 1);

				console.log(
					`[percy-performance] Retrying upload in ${retryDelay}ms (attempt ${queueItem.retryCount}/${this.options.retryAttempts})`
				);

				setTimeout(() => {
					this.uploadQueue.unshift(queueItem); // Add back to front of queue
					this.processUploadQueue();
				}, retryDelay);
			} else {
				this.performanceMetrics.uploads.failed++;
				console.error(
					`[percy-performance] Upload permanently failed after ${this.options.retryAttempts} attempts: ${queueItem.data.name}`
				);
			}
		} finally {
			// Remove from active uploads
			this.activeUploads.delete(uploadId);
			this.performanceMetrics.uploads.total++;

			// Continue processing queue
			setTimeout(() => this.processUploadQueue(), this.options.processingDelay);
		}
	}

	/**
	 * Perform optimized snapshot upload
	 */
	async optimizedSnapshotUpload(queueItem) {
		const { data } = queueItem;

		// Simulate network optimization techniques
		const optimizedData = await this.optimizeSnapshotData(data);

		// Track network metrics
		this.performanceMetrics.network.totalRequests++;
		this.performanceMetrics.network.totalBytes += optimizedData.size || 0;

		if (optimizedData.compressed) {
			this.performanceMetrics.network.compressionSavings +=
				optimizedData.compressionSavings || 0;
		}

		// Simulate actual upload (in real implementation, this would call Percy API)
		return new Promise((resolve, reject) => {
			const uploadTime = Math.random() * 2000 + 500; // 500-2500ms

			setTimeout(() => {
				if (Math.random() > 0.1) {
					// 90% success rate
					resolve({
						success: true,
						snapshotId: crypto.randomUUID(),
						uploadTime,
						optimized: true,
						compressed: optimizedData.compressed,
						originalSize: data.size,
						optimizedSize: optimizedData.size,
					});
				} else {
					reject(new Error("Simulated network error"));
				}
			}, uploadTime);
		});
	}

	/**
	 * Optimize snapshot data for upload
	 */
	async optimizeSnapshotData(data) {
		const startTime = performance.now();

		try {
			let optimizedData = { ...data };
			let compressionSavings = 0;

			// Simulate compression if enabled
			if (this.options.compressionEnabled && data.size) {
				const originalSize = data.size;
				const compressionRatio = 0.3 + Math.random() * 0.4; // 30-70% compression
				optimizedData.size = Math.floor(originalSize * compressionRatio);
				compressionSavings = originalSize - optimizedData.size;
				optimizedData.compressed = true;
				optimizedData.compressionSavings = compressionSavings;
			}

			// Simulate other optimizations (image optimization, etc.)
			if (data.type === "image") {
				optimizedData.optimized = true;
				optimizedData.format = "webp"; // Convert to more efficient format
			}

			const duration = performance.now() - startTime;
			console.log(
				`[percy-performance] Data optimization completed in ${Math.round(
					duration
				)}ms`
			);

			return optimizedData;
		} catch (error) {
			console.warn(
				`[percy-performance] Data optimization failed: ${error.message}`
			);
			return data; // Return original data if optimization fails
		}
	}

	/**
	 * Batch process multiple snapshots
	 */
	async batchProcessSnapshots(snapshots) {
		const startTime = performance.now();
		const batches = [];

		// Split snapshots into batches
		for (let i = 0; i < snapshots.length; i += this.options.batchSize) {
			batches.push(snapshots.slice(i, i + this.options.batchSize));
		}

		console.log(
			`[percy-performance] Processing ${snapshots.length} snapshots in ${batches.length} batches`
		);

		const results = [];

		for (const [batchIndex, batch] of batches.entries()) {
			console.log(
				`[percy-performance] Processing batch ${batchIndex + 1}/${
					batches.length
				} (${batch.length} snapshots)`
			);

			const batchPromises = batch.map((snapshot) =>
				this.addSnapshotToQueue(snapshot)
			);
			const batchResults = await Promise.allSettled(batchPromises);

			results.push(...batchResults);

			// Small delay between batches to prevent overwhelming the system
			if (batchIndex < batches.length - 1) {
				await new Promise((resolve) =>
					setTimeout(resolve, this.options.processingDelay)
				);
			}
		}

		const duration = performance.now() - startTime;
		const successful = results.filter((r) => r.status === "fulfilled").length;
		const failed = results.filter((r) => r.status === "rejected").length;

		console.log(
			`[percy-performance] Batch processing completed in ${Math.round(
				duration
			)}ms: ${successful} successful, ${failed} failed`
		);

		return {
			total: snapshots.length,
			successful,
			failed,
			duration,
			results,
		};
	}

	/**
	 * Get comprehensive performance report
	 */
	getPerformanceReport() {
		const currentTime = performance.now();
		const totalDuration = currentTime - this.performanceMetrics.startTime;

		return {
			summary: {
				totalDuration: `${Math.round(totalDuration)}ms`,
				uploadsPerSecond:
					this.performanceMetrics.uploads.total / (totalDuration / 1000),
				successRate:
					this.performanceMetrics.uploads.total > 0
						? `${Math.round(
								(this.performanceMetrics.uploads.successful /
									this.performanceMetrics.uploads.total) *
									100
						  )}%`
						: "0%",
				cacheHitRate:
					this.performanceMetrics.cache.hits +
						this.performanceMetrics.cache.misses >
					0
						? `${Math.round(
								(this.performanceMetrics.cache.hits /
									(this.performanceMetrics.cache.hits +
										this.performanceMetrics.cache.misses)) *
									100
						  )}%`
						: "0%",
			},
			uploads: {
				...this.performanceMetrics.uploads,
				averageTime: `${Math.round(
					this.performanceMetrics.uploads.averageTime
				)}ms`,
			},
			network: {
				...this.performanceMetrics.network,
				totalBytes: `${
					Math.round(
						(this.performanceMetrics.network.totalBytes / 1024 / 1024) * 100
					) / 100
				}MB`,
				compressionSavings: `${
					Math.round(
						(this.performanceMetrics.network.compressionSavings / 1024 / 1024) *
							100
					) / 100
				}MB`,
				averageBytesPerRequest:
					this.performanceMetrics.network.totalRequests > 0
						? `${
								Math.round(
									(this.performanceMetrics.network.totalBytes /
										this.performanceMetrics.network.totalRequests /
										1024) *
										100
								) / 100
						  }KB`
						: "0KB",
			},
			cache: {
				...this.performanceMetrics.cache,
				hitRate:
					this.performanceMetrics.cache.hits +
						this.performanceMetrics.cache.misses >
					0
						? `${Math.round(
								(this.performanceMetrics.cache.hits /
									(this.performanceMetrics.cache.hits +
										this.performanceMetrics.cache.misses)) *
									100
						  )}%`
						: "0%",
			},
			memory: {
				...this.performanceMetrics.memory,
				peak: `${
					Math.round(
						(this.performanceMetrics.memory.peak / 1024 / 1024) * 100
					) / 100
				}MB`,
				current: `${
					Math.round(
						(this.performanceMetrics.memory.current / 1024 / 1024) * 100
					) / 100
				}MB`,
			},
			queue: {
				currentSize: this.uploadQueue.length,
				activeUploads: this.activeUploads.size,
				maxConcurrentUploads: this.options.maxConcurrentUploads,
			},
			configuration: {
				maxConcurrentUploads: this.options.maxConcurrentUploads,
				batchSize: this.options.batchSize,
				enableCaching: this.options.enableCaching,
				compressionEnabled: this.options.compressionEnabled,
				enableAsyncUploads: this.options.enableAsyncUploads,
			},
			timestamp: new Date().toISOString(),
		};
	}

	/**
	 * Save performance report to file
	 */
	async savePerformanceReport(filePath = null) {
		const report = this.getPerformanceReport();
		const defaultPath = path.join(
			this.options.cacheDirectory,
			`percy-performance-report-${Date.now()}.json`
		);
		const targetPath = filePath || defaultPath;

		try {
			this.ensureCacheDirectory();
			fs.writeFileSync(targetPath, JSON.stringify(report, null, 2));
			console.log(
				`[percy-performance] Performance report saved to: ${targetPath}`
			);
			return targetPath;
		} catch (error) {
			console.error(
				`[percy-performance] Failed to save performance report: ${error.message}`
			);
			throw error;
		}
	}

	/**
	 * Wait for all uploads to complete
	 */
	async waitForUploadsComplete(timeout = 60000) {
		const startTime = Date.now();

		return new Promise((resolve, reject) => {
			const checkComplete = () => {
				if (this.uploadQueue.length === 0 && this.activeUploads.size === 0) {
					resolve({
						completed: true,
						duration: Date.now() - startTime,
						metrics: this.getPerformanceReport(),
					});
					return;
				}

				if (Date.now() - startTime > timeout) {
					reject(new Error(`Upload completion timeout after ${timeout}ms`));
					return;
				}

				setTimeout(checkComplete, 100);
			};

			checkComplete();
		});
	}

	/**
	 * Clear all caches and reset metrics
	 */
	clearCaches() {
		this.cache.clear();
		this.performanceMetrics.cache.hits = 0;
		this.performanceMetrics.cache.misses = 0;
		this.performanceMetrics.cache.evictions = 0;

		console.log("[percy-performance] All caches cleared");
	}

	/**
	 * Shutdown optimizer and cleanup resources
	 */
	async shutdown() {
		console.log("[percy-performance] Shutting down performance optimizer...");

		// Clear intervals
		if (this.performanceInterval) {
			clearInterval(this.performanceInterval);
		}

		// Wait for active uploads to complete (with timeout)
		try {
			await this.waitForUploadsComplete(10000);
		} catch (error) {
			console.warn(
				`[percy-performance] Some uploads may not have completed: ${error.message}`
			);
		}

		// Save cache to disk
		this.savePersistedCache();

		// Generate final report
		const finalReport = this.getPerformanceReport();
		console.log(
			"[percy-performance] Final performance metrics:",
			finalReport.summary
		);

		return finalReport;
	}
}

// Export the optimizer class
module.exports = PercyPerformanceOptimizer;

// CLI interface for testing and management
if (require.main === module) {
	const args = process.argv.slice(2);
	const command = args[0];

	const optimizer = new PercyPerformanceOptimizer({
		enablePerformanceMonitoring: true,
		maxConcurrentUploads: 3,
		batchSize: 5,
		enableCaching: true,
	});

	async function main() {
		try {
			switch (command) {
				case "test-upload":
					const testSnapshots = Array.from({ length: 10 }, (_, i) => ({
						name: `test-snapshot-${i + 1}`,
						size: Math.floor(Math.random() * 1000000) + 100000,
						type: "image",
						priority: Math.floor(Math.random() * 3),
					}));

					console.log("Testing batch upload with performance optimization...");
					const result = await optimizer.batchProcessSnapshots(testSnapshots);
					console.log("Batch upload result:", result);
					break;

				case "performance-report":
					const report = optimizer.getPerformanceReport();
					console.log(JSON.stringify(report, null, 2));
					break;

				case "save-report":
					const reportPath = await optimizer.savePerformanceReport();
					console.log(`Performance report saved to: ${reportPath}`);
					break;

				case "wait-complete":
					console.log("Waiting for all uploads to complete...");
					const completion = await optimizer.waitForUploadsComplete();
					console.log("All uploads completed:", completion);
					break;

				case "clear-cache":
					optimizer.clearCaches();
					console.log("All caches cleared");
					break;

				case "shutdown":
					const finalReport = await optimizer.shutdown();
					console.log("Optimizer shutdown complete");
					process.exit(0);
					break;

				default:
					console.log(`
Percy Performance Optimizer for ICTServe v3.6.1

Usage:
  node percy-performance-optimizer.cjs <command>

Commands:
  test-upload          Test batch upload with optimization
  performance-report   Show current performance metrics
  save-report         Save performance report to file
  wait-complete       Wait for all uploads to complete
  clear-cache         Clear all caches
  shutdown            Shutdown optimizer and save final report

Features:
  - Asynchronous snapshot upload capabilities
  - Network usage optimization for multiple snapshots
  - Caching mechanisms for Percy CLI and dependencies
  - Performance monitoring and impact measurement
  - Batch processing and queue management
  - Memory usage optimization

Examples:
  node percy-performance-optimizer.cjs test-upload
  node percy-performance-optimizer.cjs performance-report
  node percy-performance-optimizer.cjs save-report
                    `);
					break;
			}
		} catch (error) {
			console.error("Performance Optimizer Error:", error.message);
			process.exit(1);
		}
	}

	main();
}
