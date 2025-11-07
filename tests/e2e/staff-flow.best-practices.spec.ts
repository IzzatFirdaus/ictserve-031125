/**
 * Staff User Complete Flow Test - Best Practices Edition
 *
 * Refactored using:
 * - Custom fixtures (authenticatedPage, staffDashboardPage)
 * - Page Object Models (reduces duplication)
 * - Web-first assertions (auto-wait, eliminates flakiness)
 * - User-facing locators (getByRole, getByLabel instead of CSS)
 *
 * Research findings applied:
 * - Playwright Best Practices v1.56.1 (Official)
 * - Test isolation via fixtures (each test gets fresh context)
 * - Parallel-safe test structure (tests can run in any order)
 *
 * Flow: Welcome → Login → Dashboard → Helpdesk → Loan → Profile → Logout
 *
 * Run: npm run test:e2e -- tests/e2e/staff-flow.best-practices.spec.ts
 */

import { test, expect } from './fixtures/ictserve-fixtures';

/**
 * Test suite: Staff user complete workflow
 * Uses test.describe() to group related tests (best practice)
 */
test.describe('Staff User Complete Flow - Best Practices', () => {

  /**
   * Test 1: Welcome page accessible
   * Prerequisite for login flow
   */
  test('01 - Welcome page is accessible', async ({ page }) => {
    await page.goto('/');

    // Web-first assertion: auto-waits until condition true
    await expect(page).toHaveURL(/\/$/);

    // Verify heading visible (user-facing locator)
    const heading = page.getByRole('heading');
    await expect(heading).toBeVisible();
  });

  /**
   * Test 2: Navigate to staff login
   * Verifies login link is available
   */
  test('02 - Can navigate to staff login', async ({ page }) => {
    await page.goto('/');

    // Use user-facing locator: getByRole is resilient to DOM changes
    const staffLoginLink = page.getByRole('link', { name: /staff login|log masuk/i }).first();
    await expect(staffLoginLink).toBeVisible();

    await staffLoginLink.click();
    await page.waitForURL(/login/);

    // Verify login form elements
    await expect(page.getByLabel('Email')).toBeVisible();
    await expect(page.getByLabel('Password')).toBeVisible();
  });

  /**
   * Test 3: Login with valid credentials
   * Uses staffLoginPage fixture to encapsulate login logic
   */
  test('03 - Can login with valid staff credentials', async ({ staffLoginPage }) => {
    // Use Page Object method: hides implementation details
    await staffLoginPage.login('userstaff@motac.gov.my', 'password');

    // Verify dashboard loaded
    const heading = staffLoginPage.page.getByRole('heading', { name: /dashboard/i });
    await expect(heading).toBeVisible();
  });

  /**
   * Test 4: Dashboard displays after login
   * Uses authenticatedPage fixture (pre-logged-in)
   */
  test('04 - Dashboard displays main components', async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();
    await staffDashboardPage.verifyDashboardLoaded();

    // Verify key sections exist
    const statistics = authenticatedPage.getByRole('region', { name: /statistics|统计/i });
    if (statistics) {
      await expect(statistics).toBeVisible();
    }
  });

  /**
   * Test 5: Navigate to Helpdesk from dashboard
   * Tests module navigation
   */
  test('05 - Can navigate to Helpdesk module', async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();

    // Use POM method instead of direct locator (encapsulation)
    await staffDashboardPage.navigateToHelpdesk();

    // Verify Helpdesk page loaded
    const heading = authenticatedPage.getByRole('heading', { name: /helpdesk|support/i });
    await expect(heading).toBeVisible();
  });

  /**
   * Test 6: Navigate to Asset Loan module
   */
  test('06 - Can navigate to Asset Loan module', async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();
    await staffDashboardPage.navigateToAssetLoan();

    const heading = authenticatedPage.getByRole('heading', { name: /asset|loan/i });
    await expect(heading).toBeVisible();
  });

  /**
   * Test 7: Return to dashboard
   * Tests navigation flow
   */
  test('07 - Can return to dashboard from modules', async ({ authenticatedPage, staffDashboardPage }) => {
    // Navigate to helpdesk
    await staffDashboardPage.goto();
    await staffDashboardPage.navigateToHelpdesk();

    // Navigate back to dashboard
    const dashboardLink = authenticatedPage.getByRole('link', { name: /dashboard|home|الصفحة الرئيسية/i });
    await expect(dashboardLink).toBeVisible();
    await dashboardLink.click();

    await authenticatedPage.waitForURL('/dashboard');
    await staffDashboardPage.verifyDashboardLoaded();
  });

  /**
   * Test 8: Profile page accessible
   */
  test('08 - Can navigate to profile', async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();

    const profileLink = authenticatedPage.getByRole('link', { name: /profile|الملف الشخصي/i });
    if (profileLink) {
      await expect(profileLink).toBeVisible();
      await profileLink.click();
      await authenticatedPage.waitForURL(/profile/i, { timeout: 10000 });
    }
  });

  /**
   * Test 9: Logout functionality
   * Final step in user flow
   */
  test('09 - Can logout successfully', async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/dashboard');

    const logoutButton = authenticatedPage.getByRole('button', { name: /logout|خروج|sign out/i });
    if (logoutButton) {
      await logoutButton.click();
      await authenticatedPage.waitForURL('/login', { timeout: 10000 });
    }
  });

  /**
   * Integration Test: Complete staff flow
   * Tests entire workflow end-to-end
   *
   * Note: Parallel-safe because uses authenticatedPage fixture
   * Each test instance gets independent browser context
   */
  test('10 - Complete staff workflow', async ({ authenticatedPage, staffDashboardPage }) => {
    // Dashboard
    await staffDashboardPage.goto();
    await staffDashboardPage.verifyDashboardLoaded();

    // Navigate to Helpdesk
    await staffDashboardPage.navigateToHelpdesk();
    await authenticatedPage.waitForLoadState('domcontentloaded');

    // Return to dashboard
    const dashboardLink = authenticatedPage.getByRole('link', { name: /dashboard/i });
    if (dashboardLink) {
      await dashboardLink.click();
      await authenticatedPage.waitForURL('/dashboard');
    }

    // Navigate to Asset Loan
    await staffDashboardPage.navigateToAssetLoan();
    await authenticatedPage.waitForLoadState('domcontentloaded');

    // Verify logged in (session persists)
    const userInfo = authenticatedPage.getByRole('region', { name: /user|account/i });
    if (userInfo) {
      await expect(userInfo).toBeVisible();
    }
  });
});

/**
 * BEST PRACTICES APPLIED:
 *
 * ✅ Fixtures: authenticatedPage, staffDashboardPage (reusable setup)
 * ✅ Page Objects: Encapsulate selectors and common actions
 * ✅ Web-First Assertions: toBeVisible(), toHaveURL() auto-wait
 * ✅ User-Facing Locators: getByRole(), getByLabel() (resilient to DOM changes)
 * ✅ Test Isolation: Each test runs independently, no shared state
 * ✅ Parallel-Safe: Tests can run in any order (no dependencies)
 * ✅ Readable: High-level API (staffDashboardPage.navigateToHelpdesk())
 * ✅ Maintainable: Selectors centralized in POM classes
 *
 * RESEARCH REFERENCES:
 * - Playwright Best Practices v1.56.1: User-visible behavior, test isolation
 * - Playwright Fixtures: Reusable setup, on-demand, composable
 * - Playwright POM: Simplify authoring and maintenance
 * - Web-first assertions: Eliminate flakiness, auto-wait
 *
 * Run with debugging:
 * npm run test:e2e:debug -- tests/e2e/staff-flow.best-practices.spec.ts
 */
