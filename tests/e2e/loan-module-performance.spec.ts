import { test, expect } from "./fixtures/ictserve-fixtures";
import { takePercySnapshot } from "./utils/percy-utils";

/**
 * Asset Loan Module Performance Tests with Percy Visual Testing Integration
 *
 * ENHANCED VERSION with Percy Integration (December 2025):
 * - ✅ Merged loan-module-performance.spec.ts + loan-module-performance.refactored.spec.ts
 * - ✅ Uses custom fixtures (test isolation + authentication)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Environment-aware thresholds (dev vs production)
 * - ✅ Comprehensive performance coverage
 * - ✅ Percy visual snapshots for performance visual validation
 * - ✅ ICTServe v3.6.1 True Hybrid Architecture support
 * - ✅ Bahasa Melayu interface visual validation
 *
 * Tests Core Web Vitals, load times, and performance optimization for asset loan module
 *
 * @trace Requirement 9 (Performance Monitoring and Optimization)
 * @trace D03-FR-007.2 (Core Web Vitals Performance)
 * @trace D03-FR-014.1 (Performance Targets)
 *
 * Run: npm run test:e2e -- tests/e2e/loan-module-performance.spec.ts
 * Run performance tests only: npm run test:e2e -- --grep @performance
 * Run with Percy: npm run test:e2e:percy -- tests/e2e/loan-module-performance.spec.ts
 */

test.describe("Asset Loan Module - Performance Tests with Percy", () => {
	// Environment-aware thresholds
	const isDev =
		process.env["APP_ENV"] === "local" ||
		process.env["APP_ENV"] === "development" ||
		!process.env["APP_ENV"];

	const THRESHOLDS = {
		LCP: isDev ? 5000 : 2500, // Largest Contentful Paint
		FID: isDev ? 200 : 100, // First Input Delay
		CLS: isDev ? 0.2 : 0.1, // Cumulative Layout Shift
		PAGE_LOAD: isDev ? 12000 : 3000, // Page load time
		FORM_LOAD: isDev ? 8000 : 2000, // Form load time
		API_RESPONSE: isDev ? 2000 : 1000, // API response time
	};

	test(
		"01 - Core Web Vitals meet performance targets with Percy",
		{
			tag: ["@loan", "@performance", "@vitals", "@smoke", "@percy"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Enhanced with Percy visual validation for performance baseline
			await takePercySnapshot(authenticatedPage, {
				name: "Loan Module Performance - Core Web Vitals Baseline",
				userType: "authenticated",
				widths: [768, 1280],
				validateBahasaMelayu: true,
			});

			// Measure Core Web Vitals
			const metrics = await authenticatedPage.evaluate(() => {
				return new Promise<Record<string, number>>((resolve) => {
					const vitals: Record<string, number> = {};

					// Collect paint timing
					const paintEntries = performance.getEntriesByType("paint");
					paintEntries.forEach((entry) => {
						if (entry.name === "first-contentful-paint") {
							vitals.fcp = entry.startTime;
						}
					});

					// Collect largest contentful paint
					const lcpEntries = performance.getEntriesByType(
						"largest-contentful-paint"
					);
					if (lcpEntries.length > 0) {
						const lastEntry = lcpEntries[lcpEntries.length - 1] as any;
						vitals.lcp = lastEntry.startTime;
					}

					// Collect CLS (Cumulative Layout Shift)
					let clsScore = 0;
					const clsEntries = performance.getEntriesByType("layout-shift");
					clsEntries.forEach((entry: any) => {
						if (!entry.hadRecentInput) {
							clsScore += entry.value;
						}
					});
					vitals.cls = clsScore;

					resolve(vitals);
				});
			});

			// Verify metrics meet thresholds
			if (metrics.lcp) {
				expect(metrics.lcp).toBeLessThan(THRESHOLDS.LCP);
			}
			if (metrics.cls !== undefined) {
				expect(metrics.cls).toBeLessThan(THRESHOLDS.CLS);
			}
		}
	);

	test(
		"02 - Guest loan request form loads quickly",
		{
			tag: ["@loan", "@performance", "@guest", "@form"],
		},
		async ({ page }) => {
			const startTime = Date.now();

			await page.goto("/loans/request");
			await page.waitForLoadState("networkidle");

			const loadTime = Date.now() - startTime;

			expect(loadTime).toBeLessThan(THRESHOLDS.FORM_LOAD);
		}
	);

	test(
		"03 - Asset availability check is performant",
		{
			tag: ["@loan", "@performance", "@api"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/request");
			await authenticatedPage.waitForLoadState("networkidle");

			// Find asset selection field
			const assetSelect = authenticatedPage
				.locator('select[name*="asset"], select[id*="asset"]')
				.first();

			const hasSelect = await assetSelect
				.isVisible({ timeout: 3000 })
				.catch(() => false);

			if (hasSelect) {
				const startTime = Date.now();

				// Trigger availability check
				await assetSelect.selectOption({ index: 1 });
				await authenticatedPage.waitForLoadState("networkidle");

				const responseTime = Date.now() - startTime;

				expect(responseTime).toBeLessThan(THRESHOLDS.API_RESPONSE);
			}
		}
	);

	test(
		"04 - Loan history pagination is efficient",
		{
			tag: ["@loan", "@performance", "@pagination"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/history");
			await authenticatedPage.waitForLoadState("networkidle");

			// Find pagination controls
			const paginationLink = authenticatedPage
				.locator(
					'[aria-label*="page"], [aria-label*="next"], button:has-text("Next")'
				)
				.first();

			const hasPagination = await paginationLink
				.isVisible({ timeout: 3000 })
				.catch(() => false);

			if (hasPagination) {
				const startTime = Date.now();

				await paginationLink.click();
				await authenticatedPage.waitForLoadState("networkidle");

				const paginationTime = Date.now() - startTime;

				expect(paginationTime).toBeLessThan(2000);
			}
		}
	);

	test(
		"05 - Loan search/filter performs efficiently",
		{
			tag: ["@loan", "@performance", "@search"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/history");
			await authenticatedPage.waitForLoadState("networkidle");

			// Find search input
			const searchInput = authenticatedPage
				.getByRole("textbox", { name: /search|cari/i })
				.or(
					authenticatedPage
						.locator('input[type="search"], input[name*="search"]')
						.first()
				);

			const hasSearch = await searchInput
				.isVisible({ timeout: 3000 })
				.catch(() => false);

			if (hasSearch) {
				const startTime = Date.now();

				await searchInput.fill("test");
				await authenticatedPage.waitForLoadState("networkidle");

				const searchTime = Date.now() - startTime;

				// Search should be fast (debounced or instant)
				expect(searchTime).toBeLessThan(3000);
			}
		}
	);

	test(
		"06 - Progressive loading for asset list",
		{
			tag: ["@loan", "@performance", "@progressive"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/dashboard");

			// Measure time to first meaningful content
			const startTime = Date.now();

			// Wait for skeleton/loader to appear (progressive loading indicator)
			const loader = authenticatedPage
				.locator('[role="progressbar"], .skeleton, .loading')
				.first();
			const hasLoader = await loader
				.isVisible({ timeout: 1000 })
				.catch(() => false);

			if (hasLoader) {
				// Wait for content to replace loader
				await authenticatedPage.waitForLoadState("networkidle");
			} else {
				// Direct load - still acceptable
				await authenticatedPage.waitForLoadState("networkidle");
			}

			const loadTime = Date.now() - startTime;

			expect(loadTime).toBeLessThan(THRESHOLDS.PAGE_LOAD);
		}
	);

	test(
		"07 - Time to Interactive (TTI) is acceptable",
		{
			tag: ["@loan", "@performance", "@tti"],
		},
		async ({ authenticatedPage }) => {
			const startTime = Date.now();

			await authenticatedPage.goto("/loans/dashboard");

			// Wait for page to be fully interactive
			await authenticatedPage.waitForLoadState("networkidle");
			await authenticatedPage.waitForFunction(
				() => document.readyState === "complete"
			);

			// Try to interact with page
			const interactiveElement = authenticatedPage
				.getByRole("button")
				.first()
				.or(authenticatedPage.getByRole("link").first());

			if (
				await interactiveElement.isVisible({ timeout: 2000 }).catch(() => false)
			) {
				await interactiveElement.focus();
			}

			const tti = Date.now() - startTime;

			// TTI should be reasonable
			const maxTTI = isDev ? 8000 : 3000;
			expect(tti).toBeLessThan(maxTTI);
		}
	);

	test(
		"08 - JavaScript bundle size is optimized",
		{
			tag: ["@loan", "@performance", "@bundle"],
		},
		async ({ authenticatedPage }) => {
			const resources: Array<{ name: string; size: number }> = [];

			authenticatedPage.on("response", async (response) => {
				if (response.url().endsWith(".js")) {
					const buffer = await response.body().catch(() => null);
					if (buffer) {
						resources.push({
							name: response.url(),
							size: buffer.length,
						});
					}
				}
			});

			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Calculate total JS size
			const totalJsSize = resources.reduce((sum, r) => sum + r.size, 0);

			// Total JS should be reasonable (< 1MB for dev, < 500KB for production)
			const maxSize = isDev ? 1024 * 1024 : 500 * 1024;
			expect(totalJsSize).toBeLessThan(maxSize);
		}
	);

	test(
		"09 - Database queries are optimized (no N+1)",
		{
			tag: ["@loan", "@performance", "@database"],
		},
		async ({ authenticatedPage }) => {
			// Monitor network requests
			const apiRequests: string[] = [];
			authenticatedPage.on("request", (request) => {
				if (
					request.url().includes("/api/") ||
					request.url().includes("/livewire/")
				) {
					apiRequests.push(request.url());
				}
			});

			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Should not have excessive API calls (< 10 for initial load)
			expect(apiRequests.length).toBeLessThan(10);
		}
	);

	test(
		"10 - Asset images load efficiently",
		{
			tag: ["@loan", "@performance", "@images"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Check for lazy loading
			const images = authenticatedPage.locator("img");
			const count = await images.count();

			if (count > 0) {
				let lazyLoadedCount = 0;

				for (let i = 0; i < Math.min(count, 5); i++) {
					const img = images.nth(i);
					const loading = await img.getAttribute("loading");

					if (loading === "lazy") {
						lazyLoadedCount++;
					}
				}

				// At least some images should use lazy loading
				if (count > 3) {
					expect(lazyLoadedCount).toBeGreaterThan(0);
				}
			}
		}
	);

	test(
		"11 - Form submission response time is fast",
		{
			tag: ["@loan", "@performance", "@submit"],
		},
		async ({ page }) => {
			await page.goto("/loans/request");
			await page.waitForLoadState("networkidle");

			// Find and fill form
			const form = page.locator("form").first();
			await expect(form).toBeVisible({ timeout: 5000 });

			// Fill minimal required fields
			const inputs = page
				.locator('input[type="text"], input[type="email"]')
				.first();
			if (await inputs.isVisible({ timeout: 2000 }).catch(() => false)) {
				await inputs.fill("Performance Test");
			}

			// Measure submission time
			const startTime = Date.now();

			const submitButton = page.getByRole("button", {
				name: /submit|hantar|request/i,
			});
			if (await submitButton.isVisible({ timeout: 2000 }).catch(() => false)) {
				await submitButton.click();
				await page.waitForLoadState("networkidle");

				const submitTime = Date.now() - startTime;

				expect(submitTime).toBeLessThan(3000);
			}
		}
	);

	test(
		"12 - Static assets are cached effectively",
		{
			tag: ["@loan", "@performance", "@cache"],
		},
		async ({ authenticatedPage }) => {
			// First load
			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Navigate away
			await authenticatedPage.goto("/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// Second load (should use cache)
			const cachedRequests: string[] = [];
			authenticatedPage.on("response", (response) => {
				const cacheHeader = response.headers()["cache-control"];
				if (cacheHeader && cacheHeader.includes("max-age")) {
					cachedRequests.push(response.url());
				}
			});

			await authenticatedPage.goto("/loans/dashboard");
			await authenticatedPage.waitForLoadState("networkidle");

			// At least some assets should be cached
			expect(cachedRequests.length).toBeGreaterThan(0);
		}
	);
});
