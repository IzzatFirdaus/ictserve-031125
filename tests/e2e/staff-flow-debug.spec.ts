/**
 * Debug Staff Flow - Simplified Test
 * Testing steps 1-5 only to isolate browser closing issue
 */

import { test, expect } from '@playwright/test';

const SCREENSHOT_DIR = './public/images/screenshots';
const STAFF_EMAIL = 'userstaff@motac.gov.my';
const STAFF_PASSWORD = 'password';

test.describe('Staff User Debug Flow', () => {

  test('Staff journey: Steps 1-5 only', async ({ page }) => {
    console.log('\n🔧 Debug test - Steps 1-5 only\n');

    // STEP 1: Welcome Page
    console.log('📸 Step 1/5: Welcome page');
    await page.goto('/');
    await page.waitForLoadState('domcontentloaded');
    await expect(page).toHaveURL(/\//);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/debug_01_welcome.png`,
      fullPage: true
    });
    console.log('✅ Step 1 complete\n');

    // STEP 2: Navigate to Login
    console.log('📸 Step 2/5: Navigate to login');
    // Look for "Staff Login" text (can be Malay or English), use first (from header)
    const loginLink = page.getByRole('link', { name: /staff login|log masuk/i }).first();
    await expect(loginLink).toBeVisible({ timeout: 10000 });
    console.log('🔗 Found login link, clicking...');
    await loginLink.click();
    console.log('⏳ Waiting for login page to load...');
    await page.waitForURL(/login/, { timeout: 30000 });
    console.log('✅ Login page loaded');
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/debug_02_login_page.png`,
      fullPage: true
    });
    console.log('✅ Step 2 complete\n');

    // STEP 3: Fill Login
    console.log('📸 Step 3/5: Fill login credentials');
    await page.fill('input[name="email"]', STAFF_EMAIL);
    await page.fill('input[name="password"]', STAFF_PASSWORD);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/debug_03_credentials_filled.png`,
      fullPage: true
    });
    console.log('✅ Step 3 complete\n');

    // STEP 4: Submit Login
    console.log('📸 Step 4/5: Submit login');
    await page.click('button[type="submit"]');
    await page.waitForURL('/dashboard', { timeout: 15000 });
    await page.waitForLoadState('domcontentloaded');
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/debug_04_authenticated.png`,
      fullPage: true
    });
    console.log('✅ Step 4 complete - Authenticated!\n');

    // STEP 5: Dashboard View
    console.log('📸 Step 5/5: Dashboard main view');
    await expect(page).toHaveURL(/dashboard/);
    await page.screenshot({
      path: `${SCREENSHOT_DIR}/debug_05_dashboard.png`,
      fullPage: true
    });
    console.log('✅ Step 5 complete\n');

    console.log('🎉 Debug test complete - All 5 steps passed!');
  });
});
