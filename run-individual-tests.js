const { exec } = require("child_process");
const fs = require("fs");

const testFiles = [
	"dashboard.spec.ts",
	"staff-flow.spec.ts",
	"helpdesk.spec.ts",
	"loan.spec.ts",
	"branding-smoke.spec.ts",
];

let allResults = [];

function runTest(testFile) {
	return new Promise((resolve) => {
		console.log(`\n========================================`);
		console.log(`Running: ${testFile}`);
		console.log(`========================================\n`);

		const proc = exec(`npx playwright test ${testFile} --reporter=list`, {
			cwd: process.cwd(),
			maxBuffer: 1024 * 1024 * 5,
		});

		let output = "";
		let errorOutput = "";

		proc.stdout.on("data", (data) => {
			const str = data.toString();
			output += str;
			process.stdout.write(str);
		});

		proc.stderr.on("data", (data) => {
			const str = data.toString();
			errorOutput += str;
			process.stderr.write(str);
		});

		proc.on("close", (code) => {
			allResults.push({
				file: testFile,
				exitCode: code,
				output: output,
				error: errorOutput,
			});
			resolve();
		});
	});
}

async function main() {
	for (const testFile of testFiles) {
		await runTest(testFile);
	}

	console.log(`\n\n========================================`);
	console.log(`SUMMARY`);
	console.log(`========================================\n`);

	allResults.forEach((result) => {
		console.log(
			`${result.file}: ${
				result.exitCode === 0 ? "PASSED" : "FAILED"
			} (exit code: ${result.exitCode})`
		);
	});

	// Save results
	fs.writeFileSync(
		"test-run-summary.json",
		JSON.stringify(allResults, null, 2)
	);
	console.log(`\nResults saved to test-run-summary.json`);
}

main();
