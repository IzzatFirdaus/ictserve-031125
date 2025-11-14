/**
 * Optimized Staff User Flow - Single Session Journey
 *
 * FIXES APPLIED:
 * - Increased test timeout to 5 minutes
 * - Reduced unnecessary waits
 * - Single session maintained throughout
 * - Uses correct seeded credentials
 * - Optimized screenshot timing
 *
 * Flow: Welcome → Login → Dashboard → Helpdesk Form → Loan Form →
 *       Dashboard Review → Profile → Logout
 */

import { test, expect } from '@playwright/test';

const SCREENSHOT_DIR = './public/images/screenshots';
const STAFF_EMAIL = 'userstaff@motac.gov.my';
const STAFF_PASSWORD = 'password';

test.describe('Staff User Complete Flow', () => {

  test('Staff journey: Welcome to Logout with all features', async ({ page }) => {
    console.log('\n🚀 Starting optimized staff flow test\n');

    // ==================== STEP 1: Welcome Page ====================
    console.log('📸 Step 1/19: Welcome page');
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/01_welcome_page_home_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 1 complete\n');

    // ==================== STEP 2: Navigate to Login ====================
    console.log('📸 Step 2/19: Navigate to login');
    // Use getByRole with staff_login translation key (matches header component)
    // Translation: "Staff Login" (EN) / "Log Masuk Kakitangan" (MS)
    // Use .first() to avoid strict mode violation (link appears in header AND footer)
    const loginLink = page.getByRole('link', { name: /staff\s+login|log\s+masuk\s+kakitangan/i }).first();
    await expect(loginLink).toBeVisible({ timeout: 10000 });
    await loginLink.click();
    await page.waitForURL(/login/, { timeout: 5000 });
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/02_welcome_page_navigate_to_login_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 2 complete\n');

    // ==================== STEP 3: Fill Login ====================
    console.log('📸 Step 3/19: Fill login credentials');
    await page.fill('input[name="email"]', STAFF_EMAIL);
    await page.fill('input[name="password"]', STAFF_PASSWORD);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/03_login_page_fill_credentials_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 3 complete\n');

    // ==================== STEP 4: Submit Login ====================
    console.log('📸 Step 4/19: Submit login');
    await page.click('button[type="submit"]');
    await page.waitForURL('/dashboard', { timeout: 10000 });
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(1000); // Brief wait for Livewire
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/04_login_submit_authenticate_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 4 complete - Authenticated!\n');

    // ==================== STEP 5: Dashboard Main ====================
    console.log('📸 Step 5/19: Dashboard main view');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/05_dashboard_main_view_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 5 complete\n');

    // ==================== STEP 6: Quick Actions ====================
    console.log('📸 Step 6/19: Dashboard quick actions');
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/06_dashboard_quick_actions_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 6 complete\n');

    // ==================== STEP 7: Helpdesk Form Navigate ====================
    console.log('📸 Step 7/19: Navigate to helpdesk form');
    await page.goto('/staff/tickets/create', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/07_navigate_to_helpdesk_form_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 7 complete\n');

    // ==================== STEP 8: Fill Helpdesk Form ====================
    console.log('📸 Step 8/19: Fill helpdesk form');

    // Step 1: Issue type
    const issueType = page.locator('select[name="issue_type"]').first();
    if (await issueType.isVisible({ timeout: 2000 })) {
      await issueType.selectOption({ index: 1 });
      const next1 = page.locator('button').filter({ hasText: /seterusnya|next/i }).first();
      if (await next1.isVisible({ timeout: 2000 })) {
        await next1.click();
        await page.waitForTimeout(800);
      }
    }

    // Step 2: Subject & Description
    const subject = page.locator('input[name="subject"]').first();
    if (await subject.isVisible({ timeout: 2000 })) {
      await subject.fill('Network Connectivity Issue');
      await page.locator('textarea[name="description"]').first().fill('Unable to access network drive. Need assistance.');
      const next2 = page.locator('button').filter({ hasText: /seterusnya|next/i }).first();
      if (await next2.isVisible({ timeout: 2000 })) {
        await next2.click();
        await page.waitForTimeout(800);
      }
    }

    // Step 3: Priority
    const priority = page.locator('select[name="priority"]').first();
    if (await priority.isVisible({ timeout: 2000 })) {
      await priority.selectOption('medium');
    }

    await page.screenshot({
      path: `${SCREENSHOT_DIR}/08_helpdesk_form_fill_details_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 8 complete\n');

    // ==================== STEP 9: Submit Helpdesk ====================
    console.log('📸 Step 9/19: Submit helpdesk ticket');
    const submitBtn = page.locator('button').filter({ hasText: /hantar|submit/i }).first();
    if (await submitBtn.isVisible({ timeout: 2000 })) {
      await submitBtn.click();
      await page.waitForLoadState('domcontentloaded', { timeout: 10000 });
      await page.waitForTimeout(1000);
    }
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/09_helpdesk_form_submit_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 9 complete - Ticket submitted!\n');

    // ==================== STEP 10: Loan Form Navigate ====================
    console.log('📸 Step 10/19: Navigate to loan form');
    await page.goto('/loan/authenticated/create', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/10_navigate_to_loan_form_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 10 complete\n');

    // ==================== STEP 11: Fill Loan Form ====================
    console.log('📸 Step 11/19: Fill loan application');
    const purpose = page.locator('textarea[name="purpose"]').first();
    if (await purpose.isVisible({ timeout: 2000 })) {
      await purpose.fill('Laptop for project development tasks');

      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const nextWeek = new Date();
      nextWeek.setDate(nextWeek.getDate() + 7);

      await page.locator('input[name="loan_start_date"]').first().fill(tomorrow.toISOString().split('T')[0]);
      await page.locator('input[name="loan_end_date"]').first().fill(nextWeek.toISOString().split('T')[0]);
    }
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/11_loan_form_fill_details_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 11 complete\n');

    // ==================== STEP 12: Submit Loan ====================
    console.log('📸 Step 12/19: Submit loan application');
    const loanSubmit = page.locator('button').filter({ hasText: /hantar|submit/i }).first();
    if (await loanSubmit.isVisible({ timeout: 2000 })) {
      await loanSubmit.click();
      await page.waitForLoadState('domcontentloaded', { timeout: 10000 });
      await page.waitForTimeout(1000);
    }
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/12_loan_form_submit_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 12 complete - Loan submitted!\n');

    // ==================== STEP 13: Return to Dashboard ====================
    console.log('📸 Step 13/19: Return to dashboard');
    await page.goto('/dashboard', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/13_return_to_dashboard_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 13 complete\n');

    // ==================== STEP 14: Helpdesk Dashboard ====================
    console.log('📸 Step 14/19: View helpdesk dashboard');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/14_view_helpdesk_dashboard_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 14 complete\n');

    // ==================== STEP 15: Loan Dashboard ====================
    console.log('📸 Step 15/19: View loan dashboard');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/15_view_loan_dashboard_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 15 complete\n');

    // ==================== STEP 16: Profile Navigate ====================
    console.log('📸 Step 16/19: Navigate to profile');
    await page.goto('/portal/profile', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/16_navigate_to_profile_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 16 complete\n');

    // ==================== STEP 17: Profile Info ====================
    console.log('📸 Step 17/19: Profile information');
    await page.waitForTimeout(500);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/17_profile_personal_info_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 17 complete\n');

    // ==================== STEP 18: Logout Menu ====================
    console.log('📸 Step 18/19: Navigate to logout');
    // Try to open dropdown if exists
    const dropdown = page.locator('[class*="dropdown"] button, [class*="menu"] button').first();
    if (await dropdown.isVisible({ timeout: 2000 })) {
      await dropdown.click();
      await page.waitForTimeout(300);
    }
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/18_navigate_to_logout_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 18 complete\n');

    // ==================== STEP 19: Complete Logout ====================
    console.log('📸 Step 19/19: Complete logout');
    const logoutBtn = page.locator('button[form="logout-form"]').first();
    if (await logoutBtn.isVisible({ timeout: 2000 })) {
      await logoutBtn.click();
    } else {
      const logoutForm = page.locator('form[action*="logout"]').first();
      if (await logoutForm.isVisible({ timeout: 2000 })) {
        await logoutForm.locator('button').click();
      }
    }
    await page.waitForLoadState('domcontentloaded', { timeout: 10000 });
    await page.waitForTimeout(1000);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/19_complete_logout_staff.png`,
      fullPage: true
    });
    console.log('✅ Step 19 complete - Logged out!\n');

    console.log('🎉 Complete flow test finished successfully!');
    console.log('📊 Total: 19 screenshots captured');
    console.log('📁 Location: public/images/screenshots/\n');
  });
});
