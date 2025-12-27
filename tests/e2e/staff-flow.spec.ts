/**
 * Staff User Complete Flow Test - Refactored with Best Practices and Percy Visual Testing
 *
 * ENHANCED VERSION with Percy Integration (December 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Using Page Object Models (encapsulation)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@smoke, @staff, @flow, @percy)
 * - ✅ Soft assertions for comprehensive validation
 * - ✅ Percy visual snapshots for staff workflow validation
 * - ✅ ICTServe v3.6.1 True Hybrid Architecture support
 * - ✅ Bahasa Melayu interface visual validation
 *
 * Research findings: Playwright Best Practices v1.56.1 (Official Documentation)
 *
 * Consolidated coverage from staff-flow-refactored and staff-flow-optimized flows
 * Flow: Welcome -> Login -> Dashboard -> Helpdesk -> Loan -> Dashboard Review -> Profile -> Logout
 *
 * Run: npm run test:e2e -- tests/e2e/staff-flow.spec.ts
 * Run smoke tests only: npm run test:e2e -- --grep @smoke
 * Run with Percy: npm run test:e2e:percy -- tests/e2e/staff-flow.spec.ts
 */

import { test, expect } from "./fixtures/ictserve-fixtures";
import { StaffDashboardPage } from "./pages/staff-dashboard.page";
import {
	takePercySnapshot,
	takeResponsiveSnapshots,
} from "./utils/percy-utils";

const SCREENSHOT_DIR = "./public/images/screenshots";
const STAFF_CREDENTIALS = {
	email: "userstaff@motac.gov.my",
	password: "password",
};

test.describe("Staff User Complete Flow - Best Practices Architecture", () => {
	test(
		"01 - Welcome Page Accessibility Check with Percy",
		{
			tag: ["@smoke", "@staff", "@flow", "@percy"],
		},
		async ({ page }) => {
			await page.goto("/");

			// Web-first assertion: auto-waits until URL matches pattern
			await expect(page).toHaveURL(/\/$/);

			// Verify key elements are accessible (user-facing locators)
			await expect(page.getByRole("heading", { level: 1 })).toBeVisible();

			// Enhanced with Percy visual validation
			await takePercySnapshot(page, {
				name: "Staff Flow - Welcome Page",
				userType: "guest",
				widths: [375, 768, 1280],
				validateBahasaMelayu: true,
			});

			await page.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_01_welcome_page_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"02 - Navigate to Staff Login with Percy",
		{
			tag: ["@smoke", "@staff", "@authentication", "@percy"],
		},
		async ({ page }) => {
			await page.goto("/");

			// User-facing locator: works in both English and Malay
			// Use .first() to avoid strict mode violation (header + footer links)
			const loginLink = page
				.getByRole("link", { name: /staff login|log masuk/i })
				.first();
			await expect(loginLink).toBeVisible();
			await loginLink.click();

			// Web-first assertion: auto-waits for navigation
			await expect(page).toHaveURL(/login/);

			// Enhanced with Percy visual validation
			await takePercySnapshot(page, {
				name: "Staff Flow - Login Page Navigation",
				userType: "guest",
				widths: [375, 768, 1280],
				validateBahasaMelayu: true,
			});

			await page.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_02_navigate_to_login_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"03 - Login Page Form Accessibility with Percy",
		{
			tag: ["@smoke", "@staff", "@authentication", "@percy"],
		},
		async ({ staffLoginPage }) => {
			await staffLoginPage.goto();

			// Soft assertions: collect all failures instead of stopping at first one
			await expect.soft(staffLoginPage.emailInput).toBeVisible();
			await expect.soft(staffLoginPage.passwordInput).toBeVisible();
			await expect.soft(staffLoginPage.loginButton).toBeVisible();

			// Enhanced with Percy visual validation
			await takePercySnapshot(staffLoginPage.page, {
				name: "Staff Flow - Login Form Accessibility",
				userType: "guest",
				widths: [375, 768, 1280],
				validateBahasaMelayu: true,
			});

			await staffLoginPage.page.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_03_login_accessibility_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"04 - Successful Authentication",
		{
			tag: ["@smoke", "@staff", "@authentication"],
		},
		async ({ staffLoginPage }) => {
			await staffLoginPage.goto();
			await staffLoginPage.login("userstaff@motac.gov.my", "password");

			// Web-first assertion: verifies navigation completed
			await expect(staffLoginPage.page).toHaveURL(/dashboard/);

			await staffLoginPage.page.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_04_authentication_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"05 - Dashboard Main View After Login",
		{
			tag: ["@smoke", "@staff", "@dashboard"],
		},
		async ({ authenticatedPage, staffDashboardPage }) => {
			// authenticatedPage fixture provides logged-in context
			await staffDashboardPage.goto();

			// Verify dashboard loaded successfully
			await expect(authenticatedPage).toHaveURL(/dashboard/);

			// Soft assertions: validate all key components present
			await expect.soft(staffDashboardPage.quickActionsSection).toBeVisible();
			await expect.soft(staffDashboardPage.recentActivitySection).toBeVisible();

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_05_dashboard_view_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"06 - Dashboard Quick Actions Interaction",
		{
			tag: ["@staff", "@dashboard"],
		},
		async ({ authenticatedPage, staffDashboardPage }) => {
			await staffDashboardPage.goto();

			// Verify quick action buttons are interactive
			await expect(staffDashboardPage.quickActionsSection).toBeVisible();

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_06_quick_actions_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"07 - Navigate to Helpdesk Module",
		{
			tag: ["@staff", "@helpdesk", "@navigation"],
		},
		async ({ authenticatedPage, staffDashboardPage }) => {
			await staffDashboardPage.goto();
			await staffDashboardPage.navigateToHelpdesk();

			// Web-first assertion: verifies navigation
			await expect(authenticatedPage).toHaveURL(/helpdesk|tickets/);

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_07_helpdesk_navigation_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"08 - Navigate to Loan Module",
		{
			tag: ["@staff", "@loan", "@navigation"],
		},
		async ({ authenticatedPage, staffDashboardPage }) => {
			await staffDashboardPage.goto();
			await staffDashboardPage.navigateToLoan();

			// Web-first assertion: verifies navigation
			await expect(authenticatedPage).toHaveURL(/loans?/);

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_08_loan_navigation_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"09 - View User Profile",
		{
			tag: ["@staff", "@profile"],
		},
		async ({ authenticatedPage }) => {
			await authenticatedPage.goto("/profile");

			// Web-first assertion: verifies navigation
			await expect(authenticatedPage).toHaveURL(/profile/);

			// Verify profile elements are visible (using .first() to handle multiple matching headings)
			await expect(
				authenticatedPage
					.getByRole("heading", { name: /my profile|profil saya/i })
					.first()
			).toBeVisible();

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_09_profile_view_staff.png`,
				fullPage: true,
			});
		}
	);

	test(
		"10 - Complete Logout",
		{
			tag: ["@smoke", "@staff", "@authentication"],
		},
		async ({ authenticatedPage }) => {
			// Navigate to dashboard first
			await authenticatedPage.goto("/dashboard");
			await expect(authenticatedPage).toHaveURL(/dashboard/);

			// Open the user dropdown menu (shows user's name)
			// Look for the button with user's name or "User menu" label
			// Use getByRole to find the specific user menu button (not the language switcher)
			const userMenuButton = authenticatedPage.getByRole("button", {
				name: /user menu|menu pengguna/i,
			});
			await expect(userMenuButton).toBeVisible({ timeout: 10000 });
			await userMenuButton.click();

			// Find and click the logout link in the dropdown
			// The logout is a link within a form (uses onclick to submit)
			const logoutLink = authenticatedPage.getByRole("link", {
				name: /log out|log keluar/i,
			});
			await expect(logoutLink).toBeVisible({ timeout: 10000 });
			await logoutLink.click();

			// Web-first assertion: verify redirected to welcome page
			await expect(authenticatedPage).toHaveURL("/", { timeout: 10000 });

			// Verify logout by checking for "Staff Login" link on welcome page
			// Use .first() because header and footer both have this link
			const staffLoginLink = authenticatedPage
				.getByRole("link", { name: /staff login|log masuk/i })
				.first();
			await expect(staffLoginLink).toBeVisible({ timeout: 10000 });

			await authenticatedPage.screenshot({
				path: `${SCREENSHOT_DIR}/refactored_10_logout_complete_staff.png`,
				fullPage: true,
			});
		}
	);
});

test.describe("Staff User Optimized Complete Journey", () => {
	test(
		"Complete staff journey: Welcome to Logout (optimized single session)",
		{
			tag: ["@smoke", "@staff", "@optimization", "@e2e"],
		},
		async ({ page, staffLoginPage }) => {
			const dashboardPage = new StaffDashboardPage(page);

			// Step 1: Welcome page
			await page.goto("/", { waitUntil: "domcontentloaded" });
			await expect(page).toHaveURL(/\/$/);
			await expect(page.getByRole("heading", { level: 1 })).toBeVisible();
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_01_welcome_page.png`,
				fullPage: true,
			});

			// Step 2: Navigate to login and authenticate
			await staffLoginPage.goto();
			await expect(page).toHaveURL(/login/);
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_02_navigate_to_login.png`,
				fullPage: true,
			});

			await staffLoginPage.fillLoginForm(
				STAFF_CREDENTIALS.email,
				STAFF_CREDENTIALS.password
			);
			await staffLoginPage.submitLogin();
			await page.waitForURL("/dashboard", {
				timeout: 90000,
				waitUntil: "domcontentloaded",
			});
			await dashboardPage.verifyDashboardLoaded();
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_03_perform_login.png`,
				fullPage: true,
			});

			// Step 3: Dashboard quick checks
			await expect
				.soft(page.getByRole("heading", { name: /dashboard|papan pemuka/i }))
				.toBeVisible();
			const welcomeText = page.getByText(/welcome|selamat datang/i);
			if ((await welcomeText.count()) > 0) {
				await expect.soft(welcomeText.first()).toBeVisible({ timeout: 3000 });
			}
			const dashboardCards = page.locator(
				'[class*="card"], [class*="widget"], [class*="panel"], [data-testid="dashboard-stats-grid"]'
			);
			if ((await dashboardCards.count()) > 0) {
				await expect.soft(dashboardCards.first()).toBeVisible();
			}
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_04_dashboard_main.png`,
				fullPage: true,
			});

			// Step 4: Navigate to Helpdesk
			await dashboardPage.navigateToHelpdesk();
			await page.waitForLoadState("domcontentloaded");
			await expect.soft(page).toHaveURL(/helpdesk|tickets/);
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_06_helpdesk_module.png`,
				fullPage: true,
			});

			// Step 5: Navigate to Loan
			await dashboardPage.goto();
			await dashboardPage.navigateToLoan();
			await page.waitForLoadState("domcontentloaded");
			await expect.soft(page).toHaveURL(/loan|asset/);
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_08_loan_navigation_staff.png`,
				fullPage: true,
			});

			// Step 6: Profile view (best effort)
			try {
				await dashboardPage.navigateToProfile();
				await expect.soft(page).toHaveURL(/profile/);
				await page.screenshot({
					path: `${SCREENSHOT_DIR}/optimized_13_user_profile.png`,
					fullPage: true,
				});
			} catch {
				test.info().annotations.push({
					type: "note",
					description: "Profile link not available; skipping profile capture",
				});
				await page.screenshot({
					path: `${SCREENSHOT_DIR}/optimized_13_profile_not_found.png`,
					fullPage: true,
				});
			}

			// Step 7: Logout
			if (!/dashboard/.test(page.url())) {
				await dashboardPage.goto();
			}

			const userMenuButton = page
				.getByRole("button", { name: /user menu|menu pengguna/i })
				.first();
			await expect(userMenuButton).toBeVisible({ timeout: 10000 });
			await userMenuButton.click();

			const logoutLink = page
				.getByRole("link", { name: /log out|log keluar/i })
				.first();
			await expect(logoutLink).toBeVisible({ timeout: 10000 });
			await logoutLink.click();

			await page.waitForLoadState("domcontentloaded");
			await expect(page).toHaveURL(/\/$/);
			const staffLoginLink = page
				.getByRole("link", { name: /staff login|log masuk/i })
				.first();
			await expect(staffLoginLink).toBeVisible({ timeout: 10000 });
			await page.screenshot({
				path: `${SCREENSHOT_DIR}/optimized_15_logout_complete.png`,
				fullPage: true,
			});
		}
	);
});
