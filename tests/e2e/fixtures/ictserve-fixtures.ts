/**
 * ICTServe Custom Fixtures
 *
 * Provides reusable fixtures for testing authenticated flows:
 * - authenticatedPage: Page with logged-in staff user
 * - staffDashboardPage: Staff Dashboard Page Object
 * - staffLoginPage: Staff Login Page Object
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
 */

import { test as base, expect, type Page } from '@playwright/test';
import { StaffDashboardPage } from '../pages/staff-dashboard.page';
import { StaffLoginPage } from '../pages/staff-login.page';

/**
 * Test credentials (must match database seeders)
 * Keep in sync with: database/seeders/StaffUserSeeder.php
 */
const TEST_CREDENTIALS = {
  STAFF_EMAIL: 'userstaff@motac.gov.my',
  STAFF_PASSWORD: 'password',
  GUEST_EMAIL: 'guest@motac.gov.my',
  GUEST_PASSWORD: 'password',
};

/**
 * Worker-scoped fixtures type definition
 * Used for per-worker data isolation in parallel execution
 */
type WorkerFixtures = {
  workerStorageState: string;
};

/**
 * Custom fixtures type definition
 */
type ICTServeFixtures = {
  authenticatedPage: Page;
  staffDashboardPage: StaffDashboardPage;
  staffLoginPage: StaffLoginPage;
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
  workerStorageState: [async ({}, use, workerInfo) => {
    // For now, use same credentials (future: per-worker database users)
    // TODO: Implement per-worker user seeding for true data isolation
    // const uniqueEmail = `staff.worker${workerInfo.workerIndex}@motac.gov.my`;
    await use(TEST_CREDENTIALS.STAFF_EMAIL);
  }, { scope: 'worker' }],

  authenticatedPage: async ({ page, workerStorageState }, use) => {
    // Setup: Navigate to login
    await page.goto('/login');

    // Fill credentials (using user-facing locators)
    await page.getByLabel('Email').fill(workerStorageState);
    await page.getByLabel('Password').fill(TEST_CREDENTIALS.STAFF_PASSWORD);

    // Submit login (use web-first assertion: auto-waits until visible + enabled)
    await expect(page.getByRole('button', { name: /log in|sign in/i })).toBeVisible();
    await page.getByRole('button', { name: /log in|sign in/i }).click();

    // Wait for navigation (auto-wait with timeout)
    await page.waitForURL('/dashboard', { timeout: 15000 });
    await page.waitForLoadState('domcontentloaded');

    // Verify authenticated state
    const authCookie = await page.context().cookies();
    expect(authCookie.length).toBeGreaterThan(0);

    // Provide logged-in page to test
    await use(page);

    // Teardown: Logout (optional - test isolation via context reset)
    try {
      await page.goto('/logout');
    } catch (e) {
      // Logout may fail if page navigated elsewhere; context cleanup handles it
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
});

export { expect };
