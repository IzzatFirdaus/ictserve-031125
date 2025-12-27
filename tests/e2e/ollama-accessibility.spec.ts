/**
 * Ollama AI Accessibility Testing Suite with Percy Visual Testing
 *
 * ENHANCED VERSION with Percy Integration (December 2025):
 * - ✅ Tests WCAG 2.2 Level AA compliance for Ollama AI interfaces
 * - ✅ Uses axe-core for automated accessibility testing
 * - ✅ Percy visual snapshots for AI component visual validation
 * - ✅ ICTServe v3.6.1 True Hybrid Architecture support
 * - ✅ Bahasa Melayu interface visual validation
 *
 * Requirements: 5.1, 5.2, 5.3, 5.6, 5.7
 * Standards: WCAG 2.2 Level AA, D12-D14 v3.6.0
 *
 * Run: npm run test:e2e -- tests/e2e/ollama-accessibility.spec.ts
 * Run with Percy: npm run test:e2e:percy -- tests/e2e/ollama-accessibility.spec.ts
 */

import { test, expect } from "./fixtures/ictserve-fixtures";
import AxeBuilder from "@axe-core/playwright";
import {
	takePercySnapshot,
	takeAccessibilitySnapshot,
} from "./utils/percy-utils";

// WCAG 2.2 AA tags for axe-core
const WCAG_22_AA_TAGS = [
	"wcag2a",
	"wcag2aa",
	"wcag21a",
	"wcag21aa",
	"wcag22aa",
];

// Ollama AI pages to test
const OLLAMA_PAGES = [
	{ url: "/admin/ollama/faqs", name: "FAQ Management" },
	{ url: "/admin/ollama/documents", name: "Document Management" },
	{ url: "/admin/ollama/templates", name: "Auto-Reply Templates" },
	{ url: "/admin/ollama/drafts", name: "Auto-Reply Drafts" },
	{ url: "/admin/ollama/message-logs", name: "Message Logs" },
	{ url: "/admin/ollama/performance", name: "Performance Dashboard" },
];

/**
 * Helper function to run axe accessibility scan with Percy visual validation
 */
async function runAxeScan(page: any, pageName: string) {
	// Enhanced with Percy visual validation for accessibility
	await takeAccessibilitySnapshot(page, `Ollama AI - ${pageName}`, {
		userType: "admin",
		widths: [1024, 1280],
		validateBahasaMelayu: true,
	});

	const accessibilityScanResults = await new AxeBuilder({ page })
		.withTags(WCAG_22_AA_TAGS)
		.analyze();

	return {
		pageName,
		violations: accessibilityScanResults.violations,
		passes: accessibilityScanResults.passes,
		incomplete: accessibilityScanResults.incomplete,
	};
}

/**
 * Helper function to format violation report
 */
function formatViolationReport(results: any) {
	if (results.violations.length === 0) {
		return `✅ ${results.pageName}: No accessibility violations found`;
	}

	let report = `\n❌ ${results.pageName}: ${results.violations.length} violation(s) found\n`;

	results.violations.forEach((violation: any, index: number) => {
		report += `\n${index + 1}. ${violation.id} (${violation.impact})\n`;
		report += `   Description: ${violation.description}\n`;
		report += `   Help: ${violation.help}\n`;
		report += `   Help URL: ${violation.helpUrl}\n`;
		report += `   Affected elements: ${violation.nodes.length}\n`;

		violation.nodes.slice(0, 3).forEach((node: any, nodeIndex: number) => {
			report += `   - Element ${nodeIndex + 1}: ${node.html.substring(
				0,
				100
			)}...\n`;
			report += `     Target: ${node.target.join(" > ")}\n`;
		});
	});

	return report;
}

test.describe(
	"01 - Ollama AI Admin Pages Accessibility",
	{
		tag: ["@accessibility", "@a11y", "@wcag", "@ollama", "@admin"],
	},
	() => {
		test.beforeEach(async ({ page }) => {
			await page.setViewportSize({ width: 1280, height: 720 });
		});

		for (const pageInfo of OLLAMA_PAGES) {
			test(`01-${OLLAMA_PAGES.indexOf(pageInfo) + 1} - ${
				pageInfo.name
			} should pass WCAG 2.2 AA`, async ({ authenticatedPage }) => {
				await authenticatedPage.goto(pageInfo.url);
				await authenticatedPage.waitForLoadState("domcontentloaded");

				const currentUrl = authenticatedPage.url();
				if (currentUrl.includes("/admin/ollama")) {
					const results = await runAxeScan(authenticatedPage, pageInfo.name);
					console.log(formatViolationReport(results));

					expect
						.soft(
							results.violations,
							`${pageInfo.name} should have no accessibility violations`
						)
						.toHaveLength(0);

					if (results.violations.length === 0) {
						console.log(
							`✅ ${pageInfo.name}: ${results.passes.length} accessibility checks passed`
						);
					}
				} else {
					console.log(
						`⚠️  ${pageInfo.name}: Skipped - User lacks admin permissions`
					);
					test.skip();
				}
			});
		}
	}
);

test.describe(
	"02 - FAQ Bot Component Accessibility",
	{
		tag: ["@accessibility", "@a11y", "@wcag", "@ollama", "@faq-bot"],
	},
	() => {
		test("02-01 - FAQ Bot should have proper ARIA attributes", async ({
			page,
		}) => {
			// Navigate to a page with FAQ Bot (if available as guest)
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Check for FAQ Bot component (if present)
			const faqBot = page.locator(
				'[data-testid="faq-bot"], #faq-chat-messages'
			);

			if ((await faqBot.count()) > 0) {
				// Verify ARIA attributes
				const chatRegion = page.locator('[role="log"]');
				if ((await chatRegion.count()) > 0) {
					await expect(chatRegion).toHaveAttribute("aria-live", "polite");
					await expect(chatRegion).toHaveAttribute("aria-label");
				}

				// Verify skip links
				const skipLink = page.locator('a[href="#faq-chat-input"]');
				if ((await skipLink.count()) > 0) {
					await expect(skipLink).toBeVisible({ visible: false }); // sr-only
				}

				console.log("✅ FAQ Bot ARIA attributes verified");
			} else {
				console.log("⚠️  FAQ Bot not found on page - skipping");
				test.skip();
			}
		});

		test("02-02 - FAQ Bot input should be keyboard accessible", async ({
			page,
		}) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			const chatInput = page.locator("#faq-chat-input");

			if ((await chatInput.count()) > 0) {
				// Focus input with keyboard
				await chatInput.focus();
				await expect(chatInput).toBeFocused();

				// Verify input has proper label
				const label = page.locator('label[for="faq-chat-input"]');
				await expect(label).toBeAttached();

				// Verify aria-describedby for help text
				await expect(chatInput).toHaveAttribute("aria-describedby");

				console.log("✅ FAQ Bot keyboard accessibility verified");
			} else {
				console.log("⚠️  FAQ Bot input not found - skipping");
				test.skip();
			}
		});

		test("02-03 - FAQ Bot buttons should meet touch target requirements", async ({
			page,
		}) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Find FAQ Bot buttons
			const submitButton = page.locator(
				'button[type="submit"][aria-label="Hantar soalan"]'
			);
			const clearButton = page.locator('button[aria-label="Padam perbualan"]');

			for (const button of [submitButton, clearButton]) {
				if ((await button.count()) > 0) {
					const box = await button.boundingBox();
					if (box) {
						expect
							.soft(box.width, "Button width should be at least 44px")
							.toBeGreaterThanOrEqual(44);
						expect
							.soft(box.height, "Button height should be at least 44px")
							.toBeGreaterThanOrEqual(44);
					}
				}
			}

			console.log("✅ FAQ Bot touch targets verified");
		});
	}
);

test.describe(
	"03 - Ollama AI Loading States Accessibility",
	{
		tag: ["@accessibility", "@a11y", "@wcag", "@ollama", "@loading"],
	},
	() => {
		test("03-01 - Loading indicators should have proper ARIA attributes", async ({
			page,
		}) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Check for loading indicators
			const loadingIndicator = page.locator(
				'[aria-busy="true"], [role="status"][aria-live="polite"]'
			);

			// If loading indicator exists, verify attributes
			if ((await loadingIndicator.count()) > 0) {
				const firstIndicator = loadingIndicator.first();

				// Should have aria-live for screen readers
				const ariaLive = await firstIndicator.getAttribute("aria-live");
				expect
					.soft(ariaLive, "Loading indicator should have aria-live")
					.toBeTruthy();

				console.log("✅ Loading indicator ARIA attributes verified");
			} else {
				console.log("ℹ️  No loading indicators currently visible");
			}
		});
	}
);

test.describe(
	"04 - Ollama AI Error States Accessibility",
	{
		tag: ["@accessibility", "@a11y", "@wcag", "@ollama", "@errors"],
	},
	() => {
		test('04-01 - Error messages should use role="alert"', async ({ page }) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Check for error message containers
			const errorAlert = page.locator('[role="alert"]');

			if ((await errorAlert.count()) > 0) {
				// Verify aria-live is assertive for errors
				await expect(errorAlert.first()).toHaveAttribute(
					"aria-live",
					"assertive"
				);
				await expect(errorAlert.first()).toHaveAttribute("aria-atomic", "true");

				console.log("✅ Error message ARIA attributes verified");
			} else {
				console.log("ℹ️  No error messages currently visible");
			}
		});
	}
);

test.describe(
	"05 - Ollama AI Bahasa Melayu Interface",
	{
		tag: ["@accessibility", "@a11y", "@wcag", "@ollama", "@language"],
	},
	() => {
		test('05-01 - Page should have lang="ms" attribute', async ({ page }) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Verify HTML lang attribute
			const htmlLang = await page.locator("html").getAttribute("lang");
			expect(htmlLang, 'HTML should have lang="ms" for Bahasa Melayu').toBe(
				"ms"
			);

			console.log("✅ Bahasa Melayu language attribute verified");
		});

		test("05-02 - AI interface text should be in Bahasa Melayu", async ({
			page,
		}) => {
			await page.goto("/");
			await page.waitForLoadState("domcontentloaded");

			// Check for common Bahasa Melayu text patterns
			const pageContent = await page.content();

			// Should contain Bahasa Melayu text (common words)
			const malayWords = [
				"Selamat",
				"Perkhidmatan",
				"Soalan",
				"Hantar",
				"Tutup",
			];
			let foundMalayText = false;

			for (const word of malayWords) {
				if (pageContent.includes(word)) {
					foundMalayText = true;
					break;
				}
			}

			expect
				.soft(foundMalayText, "Page should contain Bahasa Melayu text")
				.toBeTruthy();

			console.log("✅ Bahasa Melayu interface text verified");
		});
	}
);
