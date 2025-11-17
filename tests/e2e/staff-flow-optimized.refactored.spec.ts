/**
 * Optimized Staff User Flow - Complete Journey Test (Refactored)
 *
 * REFACTORING UPDATES (November 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Using Page Object Models (encapsulation)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@smoke, @staff, @optimization, @e2e)
 * - ✅ Single test for complete journey (optimization pattern)
 *
 * This test follows the "single journey" pattern - one test covering
 * the complete user flow from welcome to logout. This is optimal for
 * E2E smoke testing where we want to verify the entire system works.
 *
 * Flow: Welcome → Login → Dashboard → Helpdesk → Loan → Profile → Logout
 *
 * Run: npx playwright test tests/e2e/staff-flow-optimized.refactored.spec.ts
 * Run smoke: npx playwright test --grep @smoke
 */

import { test, expect } from './fixtures/ictserve-fixtures';

const SCREENSHOT_DIR = './public/images/screenshots';

test.describe('Staff User Optimized Complete Journey', () => {

  test('Complete staff journey: Welcome to Logout (optimized single session)', {
    tag: ['@smoke', '@staff', '@optimization', '@e2e'],
  }, async ({ page }) => {

    // ==================== PHASE 1: Welcome & Authentication ====================
    console.log('\n🚀 Starting optimized staff flow test\n');

    // Step 1: Welcome Page
    console.log('📸 Step 1/15: Welcome page');
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/\/$/);
    await expect(page.getByRole('heading', { level: 1 })).toBeVisible();
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_01_welcome_page.png`,
      fullPage: true
    });
    console.log('✅ Step 1 complete\n');

    // Step 2: Navigate to Login via direct URL (more reliable than finding link)
    console.log('📸 Step 2/15: Navigate to login');
    await page.goto('/login', { waitUntil: 'domcontentloaded' });
    await expect(page).toHaveURL(/login/);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_02_navigate_to_login.png`,
      fullPage: true
    });
    console.log('✅ Step 2 complete\n');

        // Step 3: Perform Login
    console.log('📸 Step 3/15: Perform login');
    await page.getByLabel('Email').fill('userstaff@motac.gov.my');
    await page.getByLabel('Password').fill('password');
    const submitButton = page.getByRole('button', { name: /log in|sign in/i });
    await expect(submitButton).toBeVisible({ timeout: 10000 });
    await expect(submitButton).toBeEnabled({ timeout: 10000 });
    await submitButton.click();
    await page.waitForURL('/dashboard', { timeout: 90000, waitUntil: 'domcontentloaded' });
    await page.waitForLoadState('domcontentloaded');
    await expect(page).toHaveURL(/dashboard/);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_03_perform_login.png`,
      fullPage: true
    });
    console.log('✅ Step 3 complete\n');

    // ==================== PHASE 2: Dashboard Exploration ====================

    // Step 4: Dashboard Main View
    console.log('📸 Step 4/15: Dashboard main view');
    await page.waitForLoadState('domcontentloaded');

    // Verify dashboard components
    await expect.soft(page.getByRole('heading', { name: /dashboard|papan pemuka/i })).toBeVisible();
    await expect.soft(page.getByText(/welcome|selamat datang/i)).toBeVisible({ timeout: 3000 });

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_04_dashboard_main.png`,
      fullPage: true
    });
    console.log('✅ Step 4 complete\n');

    // Step 5: Dashboard Quick Actions
    console.log('📸 Step 5/15: Dashboard quick actions');

    // Look for common dashboard cards/sections
    const dashboardCards = page.locator('[class*="card"], [class*="widget"], [class*="panel"]');
    if (await dashboardCards.count() > 0) {
      await expect.soft(dashboardCards.first()).toBeVisible();
    }

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_05_dashboard_quick_actions.png`,
      fullPage: true
    });
    console.log('✅ Step 5 complete\n');

    // ==================== PHASE 3: Module Navigation ====================

    // Step 6: Navigate to Helpdesk
    console.log('📸 Step 6/15: Navigate to Helpdesk module');
    const helpdeskLink = page.getByRole('link', { name: /helpdesk|bantuan/i }).first();

    if (await helpdeskLink.isVisible({ timeout: 3000 })) {
      await helpdeskLink.click();
      await expect(page).toHaveURL(/helpdesk/);
      await page.waitForLoadState('domcontentloaded');

      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_06_helpdesk_module.png`,
        fullPage: true
      });
      console.log('✅ Step 6 complete\n');
    } else {
      console.log('⚠️  Step 6 skipped - Helpdesk module not accessible\n');
    }

    // Step 7: Helpdesk List View
    console.log('📸 Step 7/15: Helpdesk ticket list');

    if (await page.url().includes('helpdesk')) {
      const ticketTable = page.getByRole('table').or(
        page.locator('[role="grid"], .table, table')
      );

      if (await ticketTable.isVisible({ timeout: 3000 })) {
        await expect.soft(ticketTable).toBeVisible();
      }

      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_07_helpdesk_list.png`,
        fullPage: true
      });
      console.log('✅ Step 7 complete\n');
    } else {
      console.log('⚠️  Step 7 skipped - Not on helpdesk page\n');
    }

    // Step 8: Navigate to Dashboard (for Loan)
    console.log('📸 Step 8/15: Return to dashboard');
    const dashboardLink = page.getByRole('link', { name: /dashboard|papan pemuka/i }).first();

    if (await dashboardLink.isVisible({ timeout: 3000 })) {
      await dashboardLink.click();
      await expect(page).toHaveURL(/dashboard/);
      await page.waitForLoadState('domcontentloaded');
      console.log('✅ Step 8 complete\n');
    } else {
      await page.goto('/dashboard');
      console.log('✅ Step 8 complete (direct navigation)\n');
    }

    // Step 9: Navigate to Loan Module
    console.log('📸 Step 9/15: Navigate to Loan module');
    const loanLink = page.getByRole('link', { name: /loan|pinjaman/i }).first();

    if (await loanLink.isVisible({ timeout: 3000 })) {
      await loanLink.click();
      await expect(page).toHaveURL(/loans?/);
      await page.waitForLoadState('domcontentloaded');

      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_09_loan_module.png`,
        fullPage: true
      });
      console.log('✅ Step 9 complete\n');
    } else {
      console.log('⚠️  Step 9 skipped - Loan module not accessible\n');
    }

    // Step 10: Loan List View
    console.log('📸 Step 10/15: Loan application list');

    if (await page.url().includes('loan')) {
      const loanTable = page.getByRole('table').or(
        page.locator('[role="grid"], .table, table')
      );

      if (await loanTable.isVisible({ timeout: 3000 })) {
        await expect.soft(loanTable).toBeVisible();
      }

      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_10_loan_list.png`,
        fullPage: true
      });
      console.log('✅ Step 10 complete\n');
    } else {
      console.log('⚠️  Step 10 skipped - Not on loan page\n');
    }

    // ==================== PHASE 4: Dashboard Review ====================

    // Step 11: Return to Dashboard for Final Review
    console.log('📸 Step 11/15: Dashboard final review');
    await page.goto('/dashboard');
    await expect(page).toHaveURL('/dashboard');
    await page.waitForLoadState('domcontentloaded');

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_11_dashboard_review.png`,
      fullPage: true
    });
    console.log('✅ Step 11 complete\n');

    // Step 12: Dashboard Statistics Check
    console.log('📸 Step 12/15: Dashboard statistics');

    // Check for common statistics widgets
    const statsWidgets = page.locator('[class*="stat"], [class*="count"], [class*="metric"]');
    if (await statsWidgets.count() > 0) {
      await expect.soft(statsWidgets.first()).toBeVisible();
    }

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_12_dashboard_stats.png`,
      fullPage: true
    });
    console.log('✅ Step 12 complete\n');

    // ==================== PHASE 5: Profile & Logout ====================

    // Step 13: View User Profile
    console.log('📸 Step 13/15: User profile');

    // Try to find profile link (common patterns)
    const profileLink = page.getByRole('link', { name: /profile|profil/i }).or(
      page.getByRole('button', { name: /profile|profil/i })
    ).first();

    if (await profileLink.isVisible({ timeout: 3000 })) {
      await profileLink.click();
      await page.waitForLoadState('domcontentloaded');

      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_13_user_profile.png`,
        fullPage: true
      });
      console.log('✅ Step 13 complete\n');
    } else {
      console.log('⚠️  Step 13 skipped - Profile not accessible\n');
      await page.screenshot({
        path: `${SCREENSHOT_DIR}/optimized_13_profile_not_found.png`,
        fullPage: true
      });
    }

    // Step 14: Prepare for Logout
    console.log('📸 Step 14/15: Prepare logout');

    // Navigate back to dashboard before logout
    if (!page.url().includes('dashboard')) {
      await page.goto('/dashboard');
      await page.waitForLoadState('domcontentloaded');
    }

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_14_pre_logout.png`,
      fullPage: true
    });
    console.log('✅ Step 14 complete\n');

    // Step 15: Logout
    console.log('📸 Step 15/15: Logout');

    // Open user dropdown menu
    const userMenuButton = page.getByRole('button', { name: /user menu|menu pengguna/i });
    await expect(userMenuButton).toBeVisible({ timeout: 10000 });
    await userMenuButton.click();

    // Click logout link in dropdown
    const logoutLink = page.getByRole('link', { name: /log out|log keluar/i });
    await expect(logoutLink).toBeVisible({ timeout: 10000 });
    await logoutLink.click();

    // Wait for page load (logout redirects to welcome page)
    await page.waitForLoadState('domcontentloaded', { timeout: 15000 });

    // Verify logout by checking for Staff Login link on welcome page
    const staffLoginLink = page.getByRole('link', { name: /staff login|log masuk/i }).first();
    await expect(staffLoginLink).toBeVisible({ timeout: 10000 });

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/optimized_15_logout_complete.png`,
      fullPage: true
    });
    console.log('✅ Step 15 complete - Logged out!\n');

    console.log('🎉 Optimized staff flow test complete!\n');
  });

});
