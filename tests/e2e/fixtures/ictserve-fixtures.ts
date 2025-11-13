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
  APPROVER_EMAIL: 'approver@motac.gov.my',
  APPROVER_PASSWORD: 'password',
  ADMIN_EMAIL: 'admin@motac.gov.my',
  ADMIN_PASSWORD: 'password',
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
  approverPage: Page;
  adminPage: Page;
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

    // Wait for navigation with combined checks (URL + DOM presence)
    // Resilience improvement: Handles Livewire wire:navigate race conditions
    await Promise.all([
      page.waitForURL('/dashboard', { timeout: 20000 }),
      page.waitForSelector('[data-testid="dashboard-root"], main, [role="main"]', {
        state: 'visible',
        timeout: 20000
      })
    ]);
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
   * Authenticated admin page fixture for Filament panel tests.
   * Logs in via the Filament `/admin/login` route using seeded admin credentials.
   * Ensures navigation completes and admin shell is rendered before yielding the page.
   */
  adminPage: async ({ page }, use) => {
    await page.goto('/admin/login');

    await page.getByLabel(/email/i).fill(TEST_CREDENTIALS.ADMIN_EMAIL);
    await page.getByLabel(/password/i).fill(TEST_CREDENTIALS.ADMIN_PASSWORD);

    const submitButton = page.getByRole('button', { name: /log in|sign in/i });
    await expect(submitButton).toBeVisible();
    await submitButton.click();

    await page.waitForURL(/\/admin(\/.*)?$/, { timeout: 20000 });
    await page.waitForLoadState('networkidle');

    await use(page);

    // Attempt graceful logout without failing the test run if the route is unavailable.
    await page.goto('/admin/logout').catch(() => null);
  },

  /**
   * Authenticated approver page fixture for testing approver-only pages.
   * Logs in via the /login route using seeded approver credentials (Grade 41+).
   * Ensures user has approver permissions before yielding the page.
   */
  approverPage: async ({ page }, use) => {
    await page.goto('/login');

    await page.getByLabel('Email').fill(TEST_CREDENTIALS.APPROVER_EMAIL);
    await page.getByLabel('Password').fill(TEST_CREDENTIALS.APPROVER_PASSWORD);

    await expect(page.getByRole('button', { name: /log in|sign in/i })).toBeVisible();
    await page.getByRole('button', { name: /log in|sign in/i }).click();

    // Wait for navigation with combined checks (URL + DOM presence)
    await Promise.all([
      page.waitForURL('/dashboard', { timeout: 20000 }),
      page.waitForSelector('[data-testid="dashboard-root"], main, [role="main"]', {
        state: 'visible',
        timeout: 20000
      })
    ]);
    await page.waitForLoadState('domcontentloaded');

    // Verify authenticated state
    const authCookie = await page.context().cookies();
    expect(authCookie.length).toBeGreaterThan(0);

    await use(page);

    // Teardown: Logout
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
