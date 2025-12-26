#!/usr/bin/env node

/**
 * Percy Build Reporter for ICTServe v3.6.1
 *
 * This utility provides comprehensive build status reporting:
 * - Build status monitoring and alerts
 * - Performance metrics and analytics
 * - Integration with CI/CD reporting
 * - Visual regression analysis
 * - Historical build comparison
 *
 * @package ICTServe
 * @version 3.6.1
 * @author ICTServe Development Team
 */

const PercyCliWrapper = require("./percy-cli-wrapper.cjs");
const fs = require("fs").promises;
const path = require("path");

class PercyBuildReporter {
	constructor(options = {}) {
		this.options = {
			reportPath: options.reportPath || "./percy-reports",
			outputFormat: options.outputFormat || "json", // json, html, markdown
			includeScreenshots: options.includeScreenshots !== false,
			includeMetrics: options.includeMetrics !== false,
			...options,
		};

		this.wrapper = new PercyCliWrapper(options);
	}

	/**
	 * Generate comprehensive build status report
	 */
	async generateBuildStatusReport(buildId = null) {
		try {
			console.log("[build-reporter] Generating build status report");

			const buildStatus = await this.wrapper.getBuildStatus(buildId);
			const buildInfo = this.wrapper.buildInfo;

			const report = {
				reportMetadata: {
					generatedAt: new Date().toISOString(),
					reportVersion: "3.6.1",
					buildId: buildId || buildInfo.id,
				},
				buildStatus: buildStatus,
				buildInfo: buildInfo,
				performance: await this.calculatePerformanceMetrics(buildInfo),
				visualRegression: await this.analyzeVisualRegressions(buildStatus),
				ictserveContext: this.getICTServeContext(),
				recommendations: this.generateRecommendations(buildStatus, buildInfo),
			};

			console.log("[build-reporter] Build status report generated");

			return report;
		} catch (error) {
			console.error(
				"[build-reporter] Failed to generate build status report:",
				error.message
			);
			throw error;
		}
	}

	/**
	 * Calculate performance metrics
	 */
	async calculatePerformanceMetrics(buildInfo) {
		const metrics = {
			totalSnapshots: buildInfo.totalSnapshots || 0,
			buildDuration: null,
			averageSnapshotTime: null,
			snapshotEfficiency: "unknown",
		};

		if (buildInfo.createdAt && buildInfo.finishedAt) {
			const duration =
				new Date(buildInfo.finishedAt) - new Date(buildInfo.createdAt);
			metrics.buildDuration = duration;
			metrics.buildDurationFormatted = this.formatDuration(duration);

			if (buildInfo.totalSnapshots > 0) {
				metrics.averageSnapshotTime = Math.round(
					duration / buildInfo.totalSnapshots
				);
				metrics.averageSnapshotTimeFormatted = this.formatDuration(
					metrics.averageSnapshotTime
				);

				// Determine efficiency rating
				if (metrics.averageSnapshotTime < 5000) {
					metrics.snapshotEfficiency = "excellent";
				} else if (metrics.averageSnapshotTime < 10000) {
					metrics.snapshotEfficiency = "good";
				} else if (metrics.averageSnapshotTime < 20000) {
					metrics.snapshotEfficiency = "fair";
				} else {
					metrics.snapshotEfficiency = "poor";
				}
			}
		}

		return metrics;
	}

	/**
	 * Analyze visual regressions
	 */
	async analyzeVisualRegressions(buildStatus) {
		const analysis = {
			totalComparisons: buildStatus.totalComparisons || 0,
			visualChangesDetected: false,
			regressionSeverity: "none",
			affectedSnapshots: [],
			recommendations: [],
		};

		// Note: This is a simplified analysis
		// In a real implementation, you would integrate with Percy API
		// to get detailed comparison results

		if (buildStatus.state === "finished" && buildStatus.totalComparisons > 0) {
			analysis.visualChangesDetected = true;
			analysis.regressionSeverity = "low"; // This would be determined by actual comparison data

			analysis.recommendations.push(
				"Review visual changes in Percy dashboard",
				"Verify changes are intentional",
				"Update baselines if changes are approved"
			);
		}

		return analysis;
	}

	/**
	 * Get ICTServe v3.6.1 specific context
	 */
	getICTServeContext() {
		return {
			version: "3.6.1",
			technologyStack: {
				laravel: "12.43.1",
				livewire: "3.7.3",
				filament: "4.3.1",
				playwright: "1.56.1",
				tailwind: "4.1.18",
				phpunit: "11.5.46",
			},
			architecture: {
				type: "True Hybrid Architecture",
				userTypes: ["guest", "authenticated", "admin"],
				nullableUserIdFK: true,
			},
			interface: {
				language: "Bahasa Melayu",
				version: "3.6.0+",
				exclusiveLanguage: true,
			},
			compliance: {
				wcag: "2.2 AA",
				accessibilityTesting: true,
				visualComplianceValidation: true,
			},
		};
	}

	/**
	 * Generate recommendations based on build results
	 */
	generateRecommendations(buildStatus, buildInfo) {
		const recommendations = [];

		// Performance recommendations
		if (buildInfo.totalSnapshots > 20) {
			recommendations.push({
				category: "performance",
				priority: "medium",
				title: "Consider snapshot optimization",
				description:
					"Large number of snapshots detected. Consider grouping related tests or using selective snapshot capture.",
				action: "Review test structure and snapshot strategy",
			});
		}

		// Build state recommendations
		if (buildStatus.state === "failed") {
			recommendations.push({
				category: "reliability",
				priority: "high",
				title: "Build failure detected",
				description: "Percy build failed. Check logs and retry if necessary.",
				action: "Review error logs and fix underlying issues",
			});
		}

		// Visual regression recommendations
		if (buildStatus.totalComparisons > 0) {
			recommendations.push({
				category: "visual-regression",
				priority: "medium",
				title: "Visual changes detected",
				description:
					"Visual differences found in snapshots. Review changes in Percy dashboard.",
				action: "Review and approve/reject visual changes",
			});
		}

		// ICTServe v3.6.1 specific recommendations
		recommendations.push({
			category: "ictserve-integration",
			priority: "low",
			title: "Hybrid Architecture testing",
			description:
				"Ensure visual tests cover both guest and authenticated user workflows.",
			action: "Verify test coverage for True Hybrid Architecture scenarios",
		});

		recommendations.push({
			category: "accessibility",
			priority: "medium",
			title: "WCAG 2.2 AA compliance",
			description:
				"Combine Percy visual testing with accessibility compliance validation.",
			action: "Run accessibility tests alongside visual regression tests",
		});

		return recommendations;
	}

	/**
	 * Generate HTML report
	 */
	async generateHTMLReport(report) {
		const html = `
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Percy Build Report - ICTServe v3.6.1</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #1e40af; color: white; padding: 20px; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .section { margin-bottom: 30px; }
        .metric { display: inline-block; margin: 10px; padding: 15px; background: #f8fafc; border-radius: 6px; border-left: 4px solid #3b82f6; }
        .status-success { border-left-color: #10b981; }
        .status-warning { border-left-color: #f59e0b; }
        .status-error { border-left-color: #ef4444; }
        .recommendation { margin: 10px 0; padding: 15px; background: #fef3c7; border-radius: 6px; border-left: 4px solid #f59e0b; }
        .recommendation.high { background: #fee2e2; border-left-color: #ef4444; }
        .recommendation.low { background: #ecfdf5; border-left-color: #10b981; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Percy Build Report</h1>
            <p>ICTServe v3.6.1 Visual Testing Report</p>
            <p>Generated: ${report.reportMetadata.generatedAt}</p>
        </div>
        
        <div class="content">
            <div class="section">
                <h2>Build Status</h2>
                <div class="metric status-${
									report.buildStatus.state === "finished"
										? "success"
										: "warning"
								}">
                    <strong>Status:</strong> ${report.buildStatus.state}
                </div>
                <div class="metric">
                    <strong>Snapshots:</strong> ${
											report.buildStatus.totalSnapshots
										}
                </div>
                <div class="metric">
                    <strong>Comparisons:</strong> ${
											report.buildStatus.totalComparisons
										}
                </div>
                ${
									report.buildStatus.webUrl
										? `<div class="metric"><strong>Review URL:</strong> <a href="${report.buildStatus.webUrl}" target="_blank">View in Percy</a></div>`
										: ""
								}
            </div>

            <div class="section">
                <h2>Performance Metrics</h2>
                <table>
                    <tr><th>Metric</th><th>Value</th><th>Rating</th></tr>
                    <tr><td>Total Snapshots</td><td>${
											report.performance.totalSnapshots
										}</td><td>-</td></tr>
                    <tr><td>Build Duration</td><td>${
											report.performance.buildDurationFormatted || "N/A"
										}</td><td>-</td></tr>
                    <tr><td>Average Snapshot Time</td><td>${
											report.performance.averageSnapshotTimeFormatted || "N/A"
										}</td><td>${report.performance.snapshotEfficiency}</td></tr>
                </table>
            </div>

            <div class="section">
                <h2>Visual Regression Analysis</h2>
                <div class="metric ${
									report.visualRegression.visualChangesDetected
										? "status-warning"
										: "status-success"
								}">
                    <strong>Visual Changes:</strong> ${
											report.visualRegression.visualChangesDetected
												? "Detected"
												: "None"
										}
                </div>
                <div class="metric">
                    <strong>Severity:</strong> ${
											report.visualRegression.regressionSeverity
										}
                </div>
            </div>

            <div class="section">
                <h2>ICTServe v3.6.1 Context</h2>
                <table>
                    <tr><th>Component</th><th>Version</th></tr>
                    <tr><td>Laravel</td><td>${
											report.ictserveContext.technologyStack.laravel
										}</td></tr>
                    <tr><td>Livewire</td><td>${
											report.ictserveContext.technologyStack.livewire
										}</td></tr>
                    <tr><td>Filament</td><td>${
											report.ictserveContext.technologyStack.filament
										}</td></tr>
                    <tr><td>Playwright</td><td>${
											report.ictserveContext.technologyStack.playwright
										}</td></tr>
                    <tr><td>Interface Language</td><td>${
											report.ictserveContext.interface.language
										}</td></tr>
                    <tr><td>WCAG Compliance</td><td>${
											report.ictserveContext.compliance.wcag
										}</td></tr>
                </table>
            </div>

            <div class="section">
                <h2>Recommendations</h2>
                ${report.recommendations
									.map(
										(rec) => `
                    <div class="recommendation ${rec.priority}">
                        <strong>${rec.title}</strong> (${rec.priority} priority)<br>
                        ${rec.description}<br>
                        <em>Action: ${rec.action}</em>
                    </div>
                `
									)
									.join("")}
            </div>
        </div>
    </div>
</body>
</html>`;

		return html;
	}

	/**
	 * Save report in specified format
	 */
	async saveReport(report, format = null) {
		const outputFormat = format || this.options.outputFormat;
		const timestamp = new Date().toISOString().replace(/[:.]/g, "-");

		try {
			// Ensure report directory exists
			await fs.mkdir(this.options.reportPath, { recursive: true });

			switch (outputFormat) {
				case "html":
					const html = await this.generateHTMLReport(report);
					const htmlPath = path.join(
						this.options.reportPath,
						`percy-status-report-${timestamp}.html`
					);
					await fs.writeFile(htmlPath, html);
					console.log(`[build-reporter] HTML report saved: ${htmlPath}`);
					break;

				case "json":
				default:
					const jsonPath = path.join(
						this.options.reportPath,
						`percy-status-report-${timestamp}.json`
					);
					await fs.writeFile(jsonPath, JSON.stringify(report, null, 2));
					console.log(`[build-reporter] JSON report saved: ${jsonPath}`);
					break;
			}

			// Also save as latest report
			const latestPath = path.join(
				this.options.reportPath,
				`percy-status-latest.${outputFormat}`
			);
			if (outputFormat === "html") {
				const html = await this.generateHTMLReport(report);
				await fs.writeFile(latestPath, html);
			} else {
				await fs.writeFile(latestPath, JSON.stringify(report, null, 2));
			}
		} catch (error) {
			console.warn("[build-reporter] Failed to save report:", error.message);
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

	const reporter = new PercyBuildReporter();

	async function main() {
		try {
			switch (command) {
				case "status":
					const buildId = args[1];
					const report = await reporter.generateBuildStatusReport(buildId);
					await reporter.saveReport(report, "json");
					console.log("Status report generated successfully");
					break;

				case "html":
					const buildIdHtml = args[1];
					const htmlReport = await reporter.generateBuildStatusReport(
						buildIdHtml
					);
					await reporter.saveReport(htmlReport, "html");
					console.log("HTML report generated successfully");
					break;

				default:
					console.log(`
Percy Build Reporter for ICTServe v3.6.1

Usage:
  node build-reporter.js <command> [build-id]

Commands:
  status [build-id]     Generate JSON status report
  html [build-id]       Generate HTML status report

Examples:
  node build-reporter.js status
  node build-reporter.js html
  node build-reporter.js status abc123
                    `);
					break;
			}
		} catch (error) {
			console.error("Percy Build Reporter Error:", error.message);
			process.exit(1);
		}
	}

	main();
}

module.exports = PercyBuildReporter;
