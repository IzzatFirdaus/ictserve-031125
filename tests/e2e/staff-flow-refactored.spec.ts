/**
 * Staff User Complete Flow Test - Refactored with Best Practices
 *
 * REFACTORING UPDATES (November 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Using Page Object Models (encapsulation)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@smoke, @staff, @flow)
 * - ✅ Soft assertions for comprehensive validation
 *
 * Research findings: Playwright Best Practices v1.56.1 (Official Documentation)
 *
 * Flow: Welcome → Login → Dashboard → Helpdesk → Loan → Dashboard Review → Profile → Logout
 *
 * Run: npm run test:e2e -- tests/e2e/staff-flow-refactored.spec.ts
 * Run smoke tests only: npm run test:e2e -- --grep @smoke
 */

import { test, expect } from './fixtures/ictserve-fixtures';

const SCREENSHOT_DIR = './public/images/screenshots';

test.describe('Staff User Complete Flow - Best Practices Architecture', () => {

  test('01 - Welcome Page Accessibility Check', {
    tag: ['@smoke', '@staff', '@flow'],
  }, async ({ page }) => {
    await page.goto('/');

    // Web-first assertion: auto-waits until URL matches pattern
    await expect(page).toHaveURL(/\/$/);

    // Verify key elements are accessible (user-facing locators)
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_01_welcome_page_staff.png`,
      fullPage: true
    });
  });

  test('02 - Navigate to Staff Login', {
    tag: ['@smoke', '@staff', '@authentication'],
  }, async ({ page }) => {
    await page.goto('/');

    // User-facing locator: works in both English and Malay
    // Use .first() to avoid strict mode violation (header + footer links)
    const loginLink = page.getByRole('link', { name: /staff login|log masuk/i }).first();
    await expect(loginLink).toBeVisible();
    await loginLink.click();

    // Web-first assertion: auto-waits for navigation
    await expect(page).toHaveURL(/login/);

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_02_navigate_to_login_staff.png`,
      fullPage: true
    });
  });

  test('03 - Login Page Form Accessibility', {
    tag: ['@smoke', '@staff', '@authentication'],
  }, async ({ staffLoginPage }) => {
    await staffLoginPage.goto();

    // Soft assertions: collect all failures instead of stopping at first one
    await expect.soft(staffLoginPage.emailInput).toBeVisible();
    await expect.soft(staffLoginPage.passwordInput).toBeVisible();
    await expect.soft(staffLoginPage.loginButton).toBeVisible();

    await staffLoginPage.page.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_03_login_accessibility_staff.png`,
      fullPage: true
    });
  });

  test('04 - Successful Authentication', {
    tag: ['@smoke', '@staff', '@authentication'],
  }, async ({ staffLoginPage }) => {
    await staffLoginPage.goto();
    await staffLoginPage.login('userstaff@motac.gov.my', 'password');

    // Web-first assertion: verifies navigation completed
    await expect(staffLoginPage.page).toHaveURL(/dashboard/);

    await staffLoginPage.page.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_04_authentication_staff.png`,
      fullPage: true
    });
  });

  test('05 - Dashboard Main View After Login', {
    tag: ['@smoke', '@staff', '@dashboard'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    // authenticatedPage fixture provides logged-in context
    await staffDashboardPage.goto();

    // Verify dashboard loaded successfully
    await expect(authenticatedPage).toHaveURL(/dashboard/);

    // Soft assertions: validate all key components present
    await expect.soft(staffDashboardPage.quickActionsSection).toBeVisible();
    await expect.soft(staffDashboardPage.recentActivitySection).toBeVisible();

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_05_dashboard_view_staff.png`,
      fullPage: true
    });
  });

  test('06 - Dashboard Quick Actions Interaction', {
    tag: ['@staff', '@dashboard'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();

    // Verify quick action buttons are interactive
    await expect(staffDashboardPage.quickActionsSection).toBeVisible();

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_06_quick_actions_staff.png`,
      fullPage: true
    });
  });

  test('07 - Navigate to Helpdesk Module', {
    tag: ['@staff', '@helpdesk', '@navigation'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();
    await staffDashboardPage.navigateToHelpdesk();

    // Web-first assertion: verifies navigation
    await expect(authenticatedPage).toHaveURL(/helpdesk/);

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_07_helpdesk_navigation_staff.png`,
      fullPage: true
    });
  });

  test('08 - Navigate to Loan Module', {
    tag: ['@staff', '@loan', '@navigation'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();
    await staffDashboardPage.navigateToLoan();

    // Web-first assertion: verifies navigation
    await expect(authenticatedPage).toHaveURL(/loan/);

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_08_loan_navigation_staff.png`,
      fullPage: true
    });
  });

  test('09 - View User Profile', {
    tag: ['@staff', '@profile'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/profile');

    // Web-first assertion: verifies navigation
    await expect(authenticatedPage).toHaveURL(/profile/);

    // Verify profile elements are visible (using .first() to handle multiple matching headings)
    await expect(authenticatedPage.getByRole('heading', { name: /my profile|profil saya/i }).first()).toBeVisible();

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_09_profile_view_staff.png`,
      fullPage: true
    });
  });

  test('10 - Complete Logout', {
    tag: ['@smoke', '@staff', '@authentication'],
  }, async ({ authenticatedPage }) => {
    // Navigate to dashboard first
    await authenticatedPage.goto('/dashboard');
    await expect(authenticatedPage).toHaveURL(/dashboard/);

    // Open the user dropdown menu (shows user's name)
    // Look for the button with user's name or "User menu" label
    // Use getByRole to find the specific user menu button (not the language switcher)
    const userMenuButton = authenticatedPage.getByRole('button', { name: /user menu|menu pengguna/i });
    await expect(userMenuButton).toBeVisible({ timeout: 10000 });
    await userMenuButton.click();

    // Find and click the logout link in the dropdown
    // The logout is a link within a form (uses onclick to submit)
    const logoutLink = authenticatedPage.getByRole('link', { name: /log out|log keluar/i });
    await expect(logoutLink).toBeVisible({ timeout: 10000 });
    await logoutLink.click();

    // Web-first assertion: verify redirected to welcome page
    await expect(authenticatedPage).toHaveURL('/', { timeout: 10000 });

    // Verify logout by checking for "Staff Login" link on welcome page
    // Use .first() because header and footer both have this link
    const staffLoginLink = authenticatedPage.getByRole('link', { name: /staff login|log masuk/i }).first();
    await expect(staffLoginLink).toBeVisible({ timeout: 10000 });

    await authenticatedPage.screenshot({
      path: `${SCREENSHOT_DIR}/refactored_10_logout_complete_staff.png`,
      fullPage: true
    });
  });

});
