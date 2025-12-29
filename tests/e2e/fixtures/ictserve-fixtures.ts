/**
 * ICTServe Custom Fixtures with Percy Integration
 *
 * Provides reusable fixtures for testing authenticated flows with visual testing:
 * - authenticatedPage: Page with logged-in staff user
 * - staffDashboardPage: Staff Dashboard Page Object
 * - staffLoginPage: Staff Login Page Object
 * - Percy integration: Enhanced with visual snapshot capabilities
 *
 * Usage in tests:
 * import { test } from './fixtures/ictserve-fixtures';
 * test('example', async ({ authenticatedPage }) => { ... });
 *
 * Research findings: Playwright Fixtures Documentation v1.56.1
 * - Fixtures encapsulate setup/teardown in one place
 * - Reusable across multiple test files
 * - On-demand: only setup what each test needs
 * - Composable: can depend on other fixtures
 *
 * Percy Integration v1.0.10:
 * - Visual snapshots integrated into existing test workflows
 * - Support for ICTServe v3.6.1 True Hybrid Architecture
 * - Bahasa Melayu interface visual validation
 * - WCAG 2.2 AA compliance visual testing
 */

import { test as base, expect, type Page } from "@playwright/test";
import { StaffDashboardPage } from "../pages/staff-dashboard.page";
import { StaffLoginPage } from "../pages/staff-login.page";
import {
	takePercySnapshot,
	waitForStableContent,
	isPercyEnabled,
} from "../utils/percy-utils";

/**
 * Test credentials (must match database seeders)
 * Keep in sync with: database/seeders/StaffUserSeeder.php
 */
const TEST_CREDENTIALS = {
	STAFF_EMAIL: "userstaff@motac.gov.my",
	STAFF_PASSWORD: "password",
	ADMIN_EMAIL: "admin@motac.gov.my",
	ADMIN_PASSWORD: "password",
	GUEST_EMAIL: "guest@motac.gov.my",
	GUEST_PASSWORD: "password",
};

/**
 * Worker-scoped fixtures type definition
 * Used for per-worker data isolation in parallel execution
 */
type WorkerFixtures = {
	workerStorageState: string;
};

/**
 * Custom fixtures type definition with Percy integration
 */
type ICTServeFixtures = {
	authenticatedPage: Page;
	adminPage: Page;
	staffDashboardPage: StaffDashboardPage;
	staffLoginPage: StaffLoginPage;
	percyPage: Page; // Enhanced page with Percy utilities
};

/**
 * Authenticate as staff user and provide logged-in page
 *
 * Setup: Logs in via /login endpoint with worker-specific credentials (if available)
 * Teardown: None required (test isolation via browser context)
 *
 * Best practice: Use beforeEach for common setup while maintaining isolation
 * Research finding: Worker-scoped fixtures enable true parallel execution
 */
export const test = base.extend<ICTServeFixtures, WorkerFixtures>({
	// Worker-scoped fixture: provides unique credentials per worker
	// This enables parallel execution without data conflicts
	workerStorageState: [
		async ({}, use, workerInfo) => {
			// Use per-worker credentials if in parallel mode (CI or local with workers > 1)
			// Workers 0-3 use unique emails, fallback to default for worker 4+
			const workerEmails = [
				"userstaff@motac.gov.my", // Worker 0 (default)
				"userstaff+w1@motac.gov.my", // Worker 1
				"userstaff+w2@motac.gov.my", // Worker 2
				"userstaff+w3@motac.gov.my", // Worker 3
			];
			const workerEmail =
				workerEmails[workerInfo.workerIndex] || TEST_CREDENTIALS.STAFF_EMAIL;
			console.log(
				`[Worker ${workerInfo.workerIndex}] Using email: ${workerEmail}`
			);
			await use(workerEmail);
		},
		{ scope: "worker" },
	],

	authenticatedPage: async ({ page, workerStorageState }, use) => {
		// Setup: Navigate to login with enhanced retry logic
		let loginAttempts = 0;
		const maxAttempts = 8; // Increased from 5 to 8 attempts for slow Laravel servers
		let loginSuccessful = false;

		// Add console error handler to suppress expected errors
		const consoleErrors: string[] = [];
		page.on("console", (msg) => {
			const expectedErrors = [
				"Pusher",
				"WebSocket",
				"connection refused",
				"Livewire component not mounted",
				"ERR_CONNECTION_REFUSED",
			];
			const text = msg.text();
			const isExpected = expectedErrors.some((err) => text.includes(err));
			if (!isExpected && msg.type() === "error") {
				consoleErrors.push(text);
			}
		});

		while (loginAttempts < maxAttempts && !loginSuccessful) {
			try {
				loginAttempts++;
				console.log(
					`[Auth Fixture] Login attempt ${loginAttempts}/${maxAttempts}`
				);

				// Navigate with increased timeout and wait for server response
				await page.goto("/login", {
					waitUntil: "domcontentloaded",
					timeout: 60000, // Increased from 30s to 60s
				});

				// Wait for network to settle
				await page
					.waitForLoadState("networkidle", { timeout: 20000 })
					.catch(() => {
						console.log(
							"[Auth Fixture] Network idle timeout - continuing anyway"
						);
					});

				// Fill credentials (using ID selectors since labels are empty)
				await page.locator("#email").fill(workerStorageState);
				await page.locator("#password").fill(TEST_CREDENTIALS.STAFF_PASSWORD);

				// Wait for Livewire to initialize and enable the submit button
				// More robust button matcher: accepts 'Login', 'Log in', and 'Sign in', plus fallback to submit button
				const submitButton = page.getByRole("button", {
					name: /log ?in|sign in|login/i,
				});
				// Fallback: use the first submit button or form button element if role search fails
				let effectiveSubmitButton = submitButton;
				if (!(await submitButton.isVisible().catch(() => false))) {
					const fallback = page
						.locator('button[type="submit"], form button')
						.first();
					if (await fallback.isVisible().catch(() => false)) {
						console.log("[Auth Fixture] Using fallback submit button selector");
						effectiveSubmitButton = fallback;
					}
				}
				await expect(effectiveSubmitButton).toBeVisible({ timeout: 15000 }); // Increased from 10s
				await expect(effectiveSubmitButton).toBeEnabled({ timeout: 15000 }); // Increased from 10s

				// Submit login
				console.log("[Auth Fixture] Submitting login form");
				await effectiveSubmitButton.click();

				// Wait for navigation with combined checks (URL + DOM presence)
				// Resilience improvement: Handles Livewire wire:navigate race conditions
				console.log("[Auth Fixture] Waiting for dashboard redirect...");
				await Promise.race([
					page.waitForURL("/dashboard", {
						timeout: 120000,
						waitUntil: "domcontentloaded",
					}), // Increased from 90s to 120s
					page.waitForURL("/staff/dashboard", {
						timeout: 120000,
						waitUntil: "domcontentloaded",
					}),
					page.waitForURL("/admin", {
						timeout: 120000,
						waitUntil: "domcontentloaded",
					}),
				]);

				console.log(`[Auth Fixture] Redirected to: ${page.url()}`);

				// Additional wait for dashboard to fully render
				await page
					.waitForSelector(
						'[data-testid="dashboard-root"], main, [role="main"], .fi-sidebar, h1, h2',
						{
							state: "visible",
							timeout: 45000, // Increased from 30s to 45s
						}
					)
					.catch(() => {
						console.log(
							"[Auth Fixture] Dashboard selector timeout - checking auth cookies"
						);
					});

				await page.waitForLoadState("domcontentloaded");

				// Verify authenticated state
				const authCookie = await page.context().cookies();
				expect(authCookie.length).toBeGreaterThan(0);

				console.log("[Auth Fixture] Login successful!");
				loginSuccessful = true;
			} catch (error) {
				console.log(
					`[Auth Fixture] Attempt ${loginAttempts} failed: ${
						error instanceof Error ? error.message : "Unknown error"
					}`
				);

				if (loginAttempts >= maxAttempts) {
					console.error(
						`[Auth Fixture] All ${maxAttempts} login attempts failed`
					);
					throw new Error(
						`Login failed after ${maxAttempts} attempts: ${error}`
					);
				}

				// Wait longer between retries (exponential backoff with longer delays)
				const waitTime = 3000 * loginAttempts; // 3s, 6s, 9s, 12s, 15s, 18s, 21s, 24s
				console.log(`[Auth Fixture] Waiting ${waitTime}ms before retry...`);
				await page.waitForTimeout(waitTime);
			}
		}

		// Provide logged-in page to test
		await use(page);

		// Teardown: Logout (optional - test isolation via context reset)
		// Note: /logout requires POST method, not GET
		try {
			// Submit logout form if available, or just rely on context cleanup
			const logoutForm = page.locator('form[action="/logout"]').first();
			if (await logoutForm.isVisible().catch(() => false)) {
				await logoutForm.evaluate((form: HTMLFormElement) => form.submit());
				await page.waitForURL("/login", { timeout: 5000 }).catch(() => {});
			}
			// If no form, context cleanup handles session termination
		} catch (e) {
			// Logout may fail if page navigated elsewhere; context cleanup handles it
		}
	},

	/**
	 * Authenticated admin page fixture for Filament panel tests.
	 * Logs in via the Filament `/admin/login` route using seeded admin credentials.
	 * Ensures navigation completes and admin shell is rendered before yielding the page.
	 */
	adminPage: async ({ page }, use) => {
		await page.goto("/admin/login");
		await page.waitForLoadState("networkidle");

		await page.locator("#form\\.email").fill(TEST_CREDENTIALS.ADMIN_EMAIL);
		await page
			.locator("#form\\.password")
			.fill(TEST_CREDENTIALS.ADMIN_PASSWORD);

		// Use the same approach as the working login test
		const loginForm = page.locator("form[wire\\:submit='authenticate']");
		const submitButton = loginForm.locator("button[type='submit']");
		await expect(submitButton).toBeVisible();
		await submitButton.click();

		await page.waitForURL(/\/admin(\/.*)?$/, { timeout: 30000 });
		await page.waitForLoadState("networkidle");

		await use(page);

		// Attempt graceful logout without failing the test run if the route is unavailable.
		// Note: Admin logout also requires POST method
		const adminLogoutForm = page.locator('form[action*="/logout"]').first();
		if (await adminLogoutForm.isVisible().catch(() => false)) {
			await adminLogoutForm
				.evaluate((form: HTMLFormElement) => form.submit())
				.catch(() => null);
		}
	},

	/**
	 * Staff Dashboard Page Object fixture
	 *
	 * Provides reusable methods for dashboard interactions
	 * Pattern: Page Object Model (POM) - encapsulates locators + actions
	 */
	staffDashboardPage: async (
		{ authenticatedPage }: { authenticatedPage: Page },
		use: (value: StaffDashboardPage) => Promise<void>
	) => {
		const dashboardPage = new StaffDashboardPage(authenticatedPage);
		await use(dashboardPage);
	},

	/**
	 * Staff Login Page Object fixture
	 */
	staffLoginPage: async (
		{ page }: { page: Page },
		use: (value: StaffLoginPage) => Promise<void>
	) => {
		const loginPage = new StaffLoginPage(page);
		await use(loginPage);
	},

	/**
	 * Percy-enhanced page fixture
	 *
	 * Provides a page with Percy visual testing utilities integrated.
	 * Automatically waits for content to stabilize before yielding the page.
	 */
	percyPage: async ({ page }, use) => {
		// Add Percy-specific page methods
		const percyEnhancedPage = Object.assign(page, {
			/**
			 * Take a Percy snapshot with ICTServe v3.6.1 specific configuration
			 */
			takePercySnapshot: async (name: string, options: any = {}) => {
				await waitForStableContent(page);
				await takePercySnapshot(page, { name, ...options });
			},

			/**
			 * Check if Percy is enabled for this test run
			 */
			isPercyEnabled: () => isPercyEnabled(),

			/**
			 * Wait for content to stabilize before taking snapshots
			 */
			waitForStableContent: () => waitForStableContent(page),
		});

		// Wait for initial page stability
		if (isPercyEnabled()) {
			console.log("[Percy] Enhanced page fixture initialized");
		}

		await use(percyEnhancedPage);
	},
});

export { expect };
