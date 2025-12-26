/**
 * Test Validation Script
 * Validates Playwright test files individually with timeout handling
 */

const { spawn } = require("child_process");
const fs = require("fs");
const path = require("path");

const testFiles = [
	"dashboard.spec.ts",
	"helpdesk.spec.ts",
	"loan-module.spec.ts",
	"loan.spec.ts",
	"guest-flow-screenshots.spec.ts",
	"accessibility.comprehensive.spec.ts",
	"accessibility.interactions.spec.ts",
	"guest-landing-accessibility.spec.ts",
	"cross-browser.spec.ts",
	"staff-flow.spec.ts",
	"branding-smoke.spec.ts",
	"ollama-accessibility.spec.ts",
	"devtools.integration.spec.ts",
	"filament.components.debug.spec.ts",
	"helpdesk-performance.spec.ts",
	"loan-module-performance.spec.ts",
];

const results = [];

async function validateTest(testFile) {
	return new Promise((resolve) => {
		console.log(`\n🧪 Testing: ${testFile}`);

		const testPath = `tests/e2e/${testFile}`;
		const startTime = Date.now();

		// First check if file exists
		if (!fs.existsSync(testPath)) {
			resolve({
				file: testFile,
				status: "FILE_NOT_FOUND",
				error: "Test file does not exist",
				duration: 0,
			});
			return;
		}

		// Check syntax with --list
		const listProcess = spawn(
			"npx",
			["playwright", "test", "--list", testPath],
			{
				stdio: ["pipe", "pipe", "pipe"],
				shell: true,
			}
		);

		let listOutput = "";
		let listError = "";

		listProcess.stdout.on("data", (data) => {
			listOutput += data.toString();
		});

		listProcess.stderr.on("data", (data) => {
			listError += data.toString();
		});

		const listTimeout = setTimeout(() => {
			listProcess.kill("SIGTERM");
			resolve({
				file: testFile,
				status: "SYNTAX_CHECK_TIMEOUT",
				error: "Syntax check timed out after 30 seconds",
				duration: Date.now() - startTime,
			});
		}, 30000);

		listProcess.on("close", (code) => {
			clearTimeout(listTimeout);

			if (code === 0) {
				// Syntax is OK, try to run one test
				console.log(`✅ Syntax OK for ${testFile}`);

				const runProcess = spawn(
					"npx",
					[
						"playwright",
						"test",
						testPath,
						"--config=playwright.test.config.ts",
						"--max-failures=1",
						"--timeout=30000",
					],
					{
						stdio: ["pipe", "pipe", "pipe"],
						shell: true,
					}
				);

				let runOutput = "";
				let runError = "";

				runProcess.stdout.on("data", (data) => {
					runOutput += data.toString();
				});

				runProcess.stderr.on("data", (data) => {
					runError += data.toString();
				});

				const runTimeout = setTimeout(() => {
					runProcess.kill("SIGTERM");
					resolve({
						file: testFile,
						status: "EXECUTION_TIMEOUT",
						error: "Test execution timed out after 60 seconds",
						output: runOutput,
						duration: Date.now() - startTime,
					});
				}, 60000);

				runProcess.on("close", (runCode) => {
					clearTimeout(runTimeout);

					resolve({
						file: testFile,
						status: runCode === 0 ? "PASSED" : "FAILED",
						error: runCode !== 0 ? runError : null,
						output: runOutput,
						duration: Date.now() - startTime,
					});
				});
			} else {
				resolve({
					file: testFile,
					status: "SYNTAX_ERROR",
					error: listError,
					duration: Date.now() - startTime,
				});
			}
		});
	});
}

async function main() {
	console.log("🚀 Starting Playwright Test Validation");
	console.log(`📋 Validating ${testFiles.length} test files`);

	for (const testFile of testFiles) {
		const result = await validateTest(testFile);
		results.push(result);

		console.log(`📊 ${testFile}: ${result.status} (${result.duration}ms)`);
		if (result.error) {
			console.log(`❌ Error: ${result.error.substring(0, 200)}...`);
		}
	}

	// Generate report
	console.log("\n📈 VALIDATION SUMMARY");
	console.log("=".repeat(50));

	const summary = {
		total: results.length,
		passed: results.filter((r) => r.status === "PASSED").length,
		failed: results.filter((r) => r.status === "FAILED").length,
		syntaxErrors: results.filter((r) => r.status === "SYNTAX_ERROR").length,
		timeouts: results.filter((r) => r.status.includes("TIMEOUT")).length,
		notFound: results.filter((r) => r.status === "FILE_NOT_FOUND").length,
	};

	console.log(`Total Files: ${summary.total}`);
	console.log(`✅ Passed: ${summary.passed}`);
	console.log(`❌ Failed: ${summary.failed}`);
	console.log(`🔧 Syntax Errors: ${summary.syntaxErrors}`);
	console.log(`⏰ Timeouts: ${summary.timeouts}`);
	console.log(`📁 Not Found: ${summary.notFound}`);

	// Write detailed report
	const reportPath = "test-validation-results.json";
	fs.writeFileSync(
		reportPath,
		JSON.stringify(
			{
				timestamp: new Date().toISOString(),
				summary,
				results,
			},
			null,
			2
		)
	);

	console.log(`\n📄 Detailed report saved to: ${reportPath}`);
}

main().catch(console.error);
