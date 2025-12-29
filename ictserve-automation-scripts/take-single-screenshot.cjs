/**
 * Single Screenshot Capture Script
 * Takes a screenshot of a specific URL using Playwright
 */

const { chromium } = require("playwright");
const path = require("path");
const fs = require("fs");

async function takeScreenshot() {
	const args = process.argv.slice(2);
	const url = args[0] || "http://127.0.0.1:8000";
	const outputPath = args[1] || "screenshot.png";
	const fullPage = args[2] !== "false";

	let browser = null;

	try {
		console.log("📸 Taking screenshot of: " + url);
		console.log("💾 Output path: " + outputPath);
		console.log("📄 Full page: " + fullPage);

		browser = await chromium.launch({
			headless: true,
			args: [
				"--no-sandbox",
				"--disable-setuid-sandbox",
				"--disable-dev-shm-usage",
				"--disable-accelerated-2d-canvas",
				"--no-first-run",
				"--no-zygote",
				"--disable-gpu",
			],
		});

		const page = await browser.newPage();
		await page.setViewportSize({ width: 1920, height: 1080 });

		// Navigate to URL
		console.log("🌐 Navigating to URL...");
		await page.goto(url, { waitUntil: "networkidle", timeout: 30000 });

		// Wait for page to be fully loaded
		console.log("⏳ Waiting for page to stabilize...");
		await page.waitForTimeout(2000);

		// Ensure output directory exists
		const outputDir = path.dirname(outputPath);
		if (!fs.existsSync(outputDir)) {
			console.log("📁 Creating directory: " + outputDir);
			fs.mkdirSync(outputDir, { recursive: true });
		}

		// Take screenshot
		console.log("📷 Capturing screenshot...");
		await page.screenshot({
			path: outputPath,
			fullPage: fullPage,
			animations: "disabled",
			type: "png",
		});

		// Verify file was created
		if (fs.existsSync(outputPath)) {
			const stats = fs.statSync(outputPath);
			console.log("✅ Screenshot saved successfully!");
			console.log("📊 File size: " + (stats.size / 1024).toFixed(2) + " KB");
		} else {
			throw new Error("Screenshot file was not created");
		}
	} catch (error) {
		console.error("❌ Screenshot failed: " + error.message);
		process.exit(1);
	} finally {
		if (browser) {
			console.log("🔒 Closing browser...");
			await browser.close();
		}
	}
}

takeScreenshot();
