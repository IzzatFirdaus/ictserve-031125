/**
 * Percy Utilities for ICTServe v3.6.1 Visual Testing
 *
 * This utility module provides helper functions for Percy visual testing
 * with ICTServe-specific optimizations and configurations.
 */

import { Page } from "@playwright/test";
import { percySnapshot } from "@percy/playwright";
import { loadPercyEnv } from "./percy-env";

/**
 * ICTServe v3.6.1 specific Percy snapshot configuration
 */
export interface ICTServePercyOptions {
	/** Snapshot name */
	name: string;
	/** Viewport widths for responsive testing */
	widths?: number[];
	/** Minimum height for snapshot */
	minHeight?: number;
	/** Custom Percy CSS for hiding dynamic content */
	percyCSS?: string;
	/** Element selector to scope the snapshot */
	scope?: string;
	/** User type for Hybrid Architecture testing */
	userType?: "guest" | "authenticated" | "admin";
	/** Enable Bahasa Melayu interface validation */
	validateBahasaMelayu?: boolean;
	/** WCAG compliance level validation */
	wcagLevel?: "AA" | "AAA";
	/** Wait for specific selector before snapshot */
	waitForSelector?: string;
	/** Additional wait time in milliseconds */
	waitTime?: number;
}

/**
 * Default Percy configuration for ICTServe v3.6.1
 */
loadPercyEnv();

const DEFAULT_PERCY_CONFIG = {
	widths: [375, 768, 1280, 1920],
	minHeight: 800,
	percyCSS: `
    /* Hide dynamic timestamps and loading states */
    .dynamic-timestamp { display: none !important; }
    .loading-spinner { visibility: hidden !important; }
    .skeleton-loader { display: none !important; }
    
    /* Hide language switcher (v3.6.0+ Bahasa Melayu only) */
    .language-switcher { display: none !important; }
    
    /* Hide dynamic user-specific content */
    .user-avatar { visibility: hidden !important; }
    .last-login-time { display: none !important; }
    
    /* Hide real-time notifications and badges */
    .notification-badge { display: none !important; }
    .realtime-counter { display: none !important; }
    
    /* Hide dynamic form validation messages during snapshot */
    .validation-message { display: none !important; }
    
    /* Hide Livewire loading states */
    [wire\\:loading] { display: none !important; }
    .wire-loading { display: none !important; }
    
    /* Hide dynamic Filament admin content */
    .fi-loading { display: none !important; }
    .fi-notification { display: none !important; }
    
    /* Ensure consistent focus states */
    *:focus { outline: 2px solid #3b82f6 !important; }
  `,
};

/**
 * Take a Percy snapshot with ICTServe v3.6.1 optimizations
 */
export async function takeICTServeSnapshot(
	page: Page,
	options: ICTServePercyOptions
): Promise<void> {
	// Skip if Percy is disabled
	if (process.env.SKIP_PERCY === "true" || !process.env.PERCY_TOKEN) {
		console.log(`Skipping Percy snapshot: ${options.name} (Percy disabled)`);
		return;
	}

	try {
		// Wait for selector if specified
		if (options.waitForSelector) {
			await page.waitForSelector(options.waitForSelector, { timeout: 10000 });
		}

		// Additional wait time for Livewire components
		if (options.waitTime) {
			await page.waitForTimeout(options.waitTime);
		} else {
			// Default wait for Livewire components to load
			await page.waitForTimeout(1000);
		}

		// Wait for network idle to ensure all dynamic content is loaded
		await page.waitForLoadState("networkidle");

		// Build Percy CSS with user-specific and feature-specific rules
		let percyCSS = DEFAULT_PERCY_CONFIG.percyCSS;

		if (options.userType === "guest") {
			percyCSS += `
        /* Hide authenticated user elements */
        .auth-only { display: none !important; }
        .user-menu { display: none !important; }
      `;
		} else if (options.userType === "authenticated") {
			percyCSS += `
        /* Hide guest-only elements */
        .guest-only { display: none !important; }
        .login-prompt { display: none !important; }
      `;
		} else if (options.userType === "admin") {
			percyCSS += `
        /* Admin-specific hiding rules */
        .non-admin { display: none !important; }
      `;
		}

		if (options.validateBahasaMelayu) {
			percyCSS += `
        /* Ensure Bahasa Melayu content is visible */
        .lang-en { display: none !important; }
        .english-only { display: none !important; }
      `;
		}

		if (options.wcagLevel) {
			percyCSS += `
        /* WCAG compliance visual indicators */
        .wcag-violation { border: 2px solid red !important; }
        .wcag-warning { border: 2px solid orange !important; }
      `;
		}

		// Add custom Percy CSS if provided
		if (options.percyCSS) {
			percyCSS += "\n" + options.percyCSS;
		}

		// Take the Percy snapshot
		await percySnapshot(page, options.name, {
			widths: options.widths || DEFAULT_PERCY_CONFIG.widths,
			minHeight: options.minHeight || DEFAULT_PERCY_CONFIG.minHeight,
			percyCSS: percyCSS,
			scope: options.scope,
			enableJavaScript: true,
		});

		console.log(`✅ Percy snapshot captured: ${options.name}`);
	} catch (error) {
		console.error(`❌ Percy snapshot failed: ${options.name}`, error);
		// Don't throw error to avoid breaking tests when Percy fails
	}
}

/**
 * Take responsive Percy snapshots across multiple viewport sizes
 */
export async function takeResponsiveSnapshots(
	page: Page,
	baseName: string,
	options: Omit<ICTServePercyOptions, "name" | "widths"> = {}
): Promise<void> {
	const viewports = [
		{ name: "Mobile", width: 375, height: 667 },
		{ name: "Tablet", width: 768, height: 1024 },
		{ name: "Desktop", width: 1280, height: 720 },
		{ name: "Wide Desktop", width: 1920, height: 1080 },
	];

	for (const viewport of viewports) {
		await page.setViewportSize({
			width: viewport.width,
			height: viewport.height,
		});
		await takeICTServeSnapshot(page, {
			...options,
			name: `${baseName} - ${viewport.name} (${viewport.width}x${viewport.height})`,
			widths: [viewport.width],
			minHeight: viewport.height,
		});
	}
}

/**
 * Take Percy snapshot for Hybrid Architecture testing
 */
export async function takeHybridArchitectureSnapshot(
	page: Page,
	baseName: string,
	userType: "guest" | "authenticated" | "admin",
	options: Omit<ICTServePercyOptions, "name" | "userType"> = {}
): Promise<void> {
	await takeICTServeSnapshot(page, {
		...options,
		name: `${baseName} - ${
			userType.charAt(0).toUpperCase() + userType.slice(1)
		} User`,
		userType,
	});
}

/**
 * Take Percy snapshot with Bahasa Melayu validation
 */
export async function takeBahasaMelayuSnapshot(
	page: Page,
	baseName: string,
	options: Omit<ICTServePercyOptions, "name" | "validateBahasaMelayu"> = {}
): Promise<void> {
	await takeICTServeSnapshot(page, {
		...options,
		name: `${baseName} - Bahasa Melayu Interface`,
		validateBahasaMelayu: true,
	});
}

/**
 * Take Percy snapshot with WCAG compliance validation
 */
export async function takeWCAGComplianceSnapshot(
	page: Page,
	baseName: string,
	wcagLevel: "AA" | "AAA" = "AA",
	options: Omit<ICTServePercyOptions, "name" | "wcagLevel"> = {}
): Promise<void> {
	await takeICTServeSnapshot(page, {
		...options,
		name: `${baseName} - WCAG ${wcagLevel} Compliance`,
		wcagLevel,
	});
}

/**
 * Check if Percy is enabled and configured
 */
export function isPercyEnabled(): boolean {
	return !!(process.env.PERCY_TOKEN && process.env.SKIP_PERCY !== "true");
}

/**
 * Get Percy environment information
 */
export function getPercyEnvironment(): {
	enabled: boolean;
	token: boolean;
	project: string;
	branch: string;
	build: string;
} {
	return {
		enabled: isPercyEnabled(),
		token: !!process.env.PERCY_TOKEN,
		project: process.env.PERCY_PROJECT || "ictserve",
		branch: process.env.PERCY_BRANCH || "develop",
		build: process.env.PERCY_BUILD_NAME || "playwright-build",
	};
}
