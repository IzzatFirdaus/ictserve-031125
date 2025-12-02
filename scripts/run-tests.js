const { exec } = require("child_process");

console.log("Running Playwright tests...");

const testProcess = exec("npx playwright test --reporter=list", {
	cwd: process.cwd(),
	maxBuffer: 1024 * 1024 * 10, // 10MB buffer
});

testProcess.stdout.on("data", (data) => {
	process.stdout.write(data);
});

testProcess.stderr.on("data", (data) => {
	process.stderr.write(data);
});

testProcess.on("close", (code) => {
	console.log(`\nTest process exited with code ${code}`);
	process.exit(code);
});
