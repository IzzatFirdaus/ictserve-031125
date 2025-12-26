#!/usr/bin/env node

/**
 * Package Configuration Validator for Percy Visual Testing Integration
 *
 * This script validates that package.json and npm configuration are properly
 * set up for Percy visual testing in the ICTServe v3.6.1 environment.
 *
 * Usage: node scripts/percy/validate-package-config.cjs
 */

const fs = require("fs");
const path = require("path");

class PackageConfigValidator {
	constructor() {
		this.errors = [];
		this.warnings = [];
		this.info = [];
	}

	/**
	 * Main validation function
	 */
	async validate() {
		console.log(
			"🔍 Validating Percy package configuration for ICTServe v3.6.1...\n"
		);

		try {
			await this.validatePackageJson();
			await this.validateNpmConfig();
			await this.validatePercyConfig();
			await this.validateEnvironmentVariables();
			await this.validateScripts();

			this.printResults();
			return this.errors.length === 0;
		} catch (error) {
			console.error("❌ Validation failed:", error.message);
			return false;
		}
	}

	/**
	 * Validate package.json configuration
	 */
	async validatePackageJson() {
		const packagePath = path.join(process.cwd(), "package.json");

		if (!fs.existsSync(packagePath)) {
			this.errors.push("package.json not found");
			return;
		}

		const packageJson = JSON.parse(fs.readFileSync(packagePath, "utf8"));

		// Check required Percy dependencies
		const requiredDeps = {
			"@percy/cli": "^1.31.6",
			"@percy/playwright": "^1.0.10",
			"@playwright/test": "^1.57.0",
		};

		for (const [dep, version] of Object.entries(requiredDeps)) {
			if (!packageJson.devDependencies?.[dep]) {
				this.errors.push(`Missing required dependency: ${dep}`);
			} else {
				this.info.push(
					`✓ Found dependency: ${dep} ${packageJson.devDependencies[dep]}`
				);
			}
		}

		// Check for Percy-related scripts
		const requiredScripts = [
			"test:e2e:percy",
			"percy:exec",
			"percy:build",
			"ci:percy",
		];

		for (const script of requiredScripts) {
			if (!packageJson.scripts?.[script]) {
				this.errors.push(`Missing required script: ${script}`);
			} else {
				this.info.push(`✓ Found script: ${script}`);
			}
		}

		// Validate script patterns
		this.validateScriptPatterns(packageJson.scripts);
	}

	/**
	 * Validate npm configuration
	 */
	async validateNpmConfig() {
		const npmrcPath = path.join(process.cwd(), ".npmrc");
		const npmrcCiPath = path.join(process.cwd(), ".npmrc.ci");

		if (fs.existsSync(npmrcPath)) {
			this.info.push("✓ Found .npmrc configuration");
			const npmrcContent = fs.readFileSync(npmrcPath, "utf8");

			if (npmrcContent.includes("percy-cli-cache")) {
				this.info.push("✓ Percy CLI cache optimization enabled");
			} else {
				this.warnings.push("Percy CLI cache optimization not configured");
			}
		} else {
			this.warnings.push(
				".npmrc file not found - consider adding for optimization"
			);
		}

		if (fs.existsSync(npmrcCiPath)) {
			this.info.push("✓ Found .npmrc.ci for CI/CD optimization");
		} else {
			this.warnings.push(
				".npmrc.ci file not found - consider adding for CI/CD"
			);
		}
	}

	/**
	 * Validate Percy configuration
	 */
	async validatePercyConfig() {
		const percyConfigPath = path.join(process.cwd(), "percy.config.js");

		if (!fs.existsSync(percyConfigPath)) {
			this.errors.push("percy.config.js not found");
			return;
		}

		this.info.push("✓ Found percy.config.js");

		try {
			// Read the config file content to validate structure
			const configContent = fs.readFileSync(percyConfigPath, "utf8");

			// Basic validation of config structure
			if (configContent.includes("version:")) {
				this.info.push("✓ Percy config version specified");
			} else {
				this.errors.push("Percy config missing version");
			}

			if (configContent.includes("widths:")) {
				this.info.push("✓ Percy responsive widths configured");
			} else {
				this.warnings.push("Percy config missing responsive widths");
			}

			if (configContent.includes("percyCSS:")) {
				this.info.push("✓ Percy CSS customizations configured");
			} else {
				this.warnings.push("Percy config missing CSS customizations");
			}

			// Check ICTServe v3.6.1 specific configurations
			if (configContent.includes("ictserve-v3.6.1")) {
				this.info.push("✓ ICTServe v3.6.1 project name configured");
			} else {
				this.warnings.push(
					"Consider updating project name to include ICTServe v3.6.1"
				);
			}

			// Check for Bahasa Melayu and Hybrid Architecture support
			if (
				configContent.includes("language-switcher") ||
				configContent.includes("bahasa")
			) {
				this.info.push("✓ Bahasa Melayu interface support configured");
			}

			if (
				configContent.includes("hybrid") ||
				configContent.includes("guest") ||
				configContent.includes("authenticated")
			) {
				this.info.push("✓ True Hybrid Architecture support configured");
			}
		} catch (error) {
			this.errors.push(`Error reading percy.config.js: ${error.message}`);
		}
	}

	/**
	 * Validate environment variables
	 */
	async validateEnvironmentVariables() {
		const requiredEnvVars = ["PERCY_TOKEN"];
		const optionalEnvVars = [
			"PERCY_BRANCH",
			"PERCY_TARGET_BRANCH",
			"PERCY_PARALLEL_NONCE",
			"PERCY_PARALLEL_TOTAL",
			"PERCY_PROJECT",
		];

		for (const envVar of requiredEnvVars) {
			if (!process.env[envVar]) {
				this.warnings.push(
					`Environment variable ${envVar} not set (required for Percy)`
				);
			} else {
				this.info.push(`✓ Environment variable ${envVar} is set`);
			}
		}

		for (const envVar of optionalEnvVars) {
			if (process.env[envVar]) {
				this.info.push(`✓ Optional environment variable ${envVar} is set`);
			}
		}
	}

	/**
	 * Validate script patterns
	 */
	validateScriptPatterns(scripts) {
		if (!scripts) return;

		// Check for CI-specific scripts
		const ciScripts = Object.keys(scripts).filter((key) =>
			key.startsWith("ci:")
		);
		if (ciScripts.length > 0) {
			this.info.push(`✓ Found ${ciScripts.length} CI-specific scripts`);
		} else {
			this.warnings.push("No CI-specific scripts found");
		}

		// Check for development scripts
		const devScripts = Object.keys(scripts).filter((key) =>
			key.startsWith("dev:percy")
		);
		if (devScripts.length > 0) {
			this.info.push(`✓ Found ${devScripts.length} development Percy scripts`);
		} else {
			this.warnings.push("No development Percy scripts found");
		}

		// Check for environment-specific scripts
		const envScripts = Object.keys(scripts).filter(
			(key) => key.includes("production:") || key.includes("staging:")
		);
		if (envScripts.length > 0) {
			this.info.push(
				`✓ Found ${envScripts.length} environment-specific scripts`
			);
		} else {
			this.warnings.push("No environment-specific scripts found");
		}
	}

	/**
	 * Validate that required scripts exist and are properly configured
	 */
	async validateScripts() {
		const scriptsDir = path.join(process.cwd(), "scripts", "percy");

		if (!fs.existsSync(scriptsDir)) {
			this.errors.push("Percy scripts directory not found");
			return;
		}

		const requiredScripts = [
			"percy-cli-wrapper.cjs",
			"build-manager.cjs",
			"simple-build-manager.cjs",
			"percy-degradation-manager.cjs",
		];

		for (const script of requiredScripts) {
			const scriptPath = path.join(scriptsDir, script);
			if (fs.existsSync(scriptPath)) {
				this.info.push(`✓ Found Percy script: ${script}`);
			} else {
				this.errors.push(`Missing Percy script: ${script}`);
			}
		}
	}

	/**
	 * Print validation results
	 */
	printResults() {
		console.log("\n📊 Validation Results:\n");

		if (this.info.length > 0) {
			console.log("✅ Information:");
			this.info.forEach((msg) => console.log(`   ${msg}`));
			console.log();
		}

		if (this.warnings.length > 0) {
			console.log("⚠️  Warnings:");
			this.warnings.forEach((msg) => console.log(`   ${msg}`));
			console.log();
		}

		if (this.errors.length > 0) {
			console.log("❌ Errors:");
			this.errors.forEach((msg) => console.log(`   ${msg}`));
			console.log();
		}

		// Summary
		const total = this.info.length + this.warnings.length + this.errors.length;
		console.log(`📈 Summary: ${total} checks completed`);
		console.log(`   ✅ ${this.info.length} passed`);
		console.log(`   ⚠️  ${this.warnings.length} warnings`);
		console.log(`   ❌ ${this.errors.length} errors`);

		if (this.errors.length === 0) {
			console.log("\n🎉 Package configuration is valid for Percy integration!");
		} else {
			console.log(
				"\n🔧 Please fix the errors above before proceeding with Percy integration."
			);
		}
	}
}

// Run validation if called directly
if (require.main === module) {
	const validator = new PackageConfigValidator();
	validator
		.validate()
		.then((isValid) => {
			process.exit(isValid ? 0 : 1);
		})
		.catch((error) => {
			console.error("Validation failed:", error);
			process.exit(1);
		});
}

module.exports = PackageConfigValidator;
