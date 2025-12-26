/**
 * Percy Global Setup for ICTServe v3.6.1 Visual Testing
 *
 * This setup file runs once before all tests when Percy is enabled.
 * It handles Percy build initialization and environment validation.
 */

import { FullConfig } from "@playwright/test";
import { loadPercyEnv } from "./percy-env";

async function globalSetup(config: FullConfig) {
	console.log(
		"🎭 Percy Global Setup: Initializing visual testing environment..."
	);

	loadPercyEnv();

	// Validate Percy environment
	const percyToken = process.env.PERCY_TOKEN;
	if (!percyToken) {
		console.warn("⚠️  Percy token not found. Visual testing will be skipped.");
		return;
	}

	// Log Percy configuration
	console.log("✅ Percy token detected");
	console.log(
		`📊 Percy project: ${
			process.env.PERCY_PROJECT || "ictserve"
		}`
	);
	console.log(`🌿 Percy branch: ${process.env.PERCY_BRANCH || "develop"}`);
	console.log(
		`🏗️  Percy build: ${process.env.PERCY_BUILD_NAME || "playwright-build"}`
	);

	// ICTServe v3.6.1 specific setup
	console.log("🏢 ICTServe v3.6.1 Visual Testing Configuration:");
	console.log("   - Laravel 12.43.1 + Livewire 3.7.3 + Filament 4.3.1");
	console.log("   - True Hybrid Architecture (guest + authenticated users)");
	console.log("   - Bahasa Melayu interface validation");
	console.log("   - WCAG 2.2 AA compliance visual testing");

	// Validate base URL accessibility
	const baseURL = config.projects[0]?.use?.baseURL || "http://127.0.0.1:8000";
	try {
		const response = await fetch(`${baseURL}/health-check`, {
			method: "GET",
			timeout: 5000,
		});
		if (response.ok) {
			console.log(`✅ Laravel server accessible at ${baseURL}`);
		} else {
			console.warn(
				`⚠️  Laravel server responded with status ${response.status}`
			);
		}
	} catch (error) {
		console.warn(`⚠️  Could not verify Laravel server accessibility: ${error}`);
	}

	console.log("🚀 Percy Global Setup completed successfully");
}

export default globalSetup;
