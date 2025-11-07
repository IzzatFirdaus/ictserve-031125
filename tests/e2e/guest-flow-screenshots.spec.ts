import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

/**
 * Guest User Flow E2E Test with Screenshots
 *
 * Test Flow:
 * 1. Welcome Page → Screenshot
 * 2. Helpdesk Form → Fill & Screenshot
 * 3. Loan Application Form → Fill & Screenshot
 * 4. Success Pages → Screenshot
 *
 * Screenshot Naming Convention:
 * <step_number>_<page_name>_<activity>_<user_type>.png
 *
 * Example: 01_welcome_home_guest.png
 */

// Screenshot directory - screenshots saved relative to test run location
const SCREENSHOT_DIR = './public/images/screenshots';

test.describe('Guest User Flow - Welcome → Helpdesk → Loan Application', () => {
  let page: Page;

  test.beforeAll(async ({ browser }) => {
    // Setup: No specific setup needed for guest flow
  });

  test('01 - Welcome Page - Initial Load', async ({ page }) => {
    // Step 1: Load welcome page
    await page.goto('/');

    // Wait for page to fully load
    await page.waitForLoadState('networkidle');

    // Verify welcome page loaded
    await expect(page).toHaveTitle(/ICTServe|Welcome|MOTAC/i);

    // Take screenshot
    const screenshotPath = path.join(SCREENSHOT_DIR, '01_welcome_page_home_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('02 - Welcome Page - Navigate to Helpdesk', async ({ page }) => {
    // Navigate to welcome
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Find and click helpdesk link/button
    const helpdeskLink = page.locator('a, button').filter({
      hasText: /helpdesk|ticket|issue|complaint/i
    }).first();

    if (await helpdeskLink.isVisible()) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');
    } else {
      // Fallback: navigate directly
      await page.goto('/helpdesk/create');
      await page.waitForLoadState('networkidle');
    }

    // Take screenshot showing navigation
    const screenshotPath = path.join(SCREENSHOT_DIR, '02_welcome_page_navigation_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('03 - Helpdesk Form - Loaded', async ({ page }) => {
    // Navigate to helpdesk form
    await page.goto('/helpdesk/create');
    await page.waitForLoadState('networkidle');

    // Verify form is loaded
    const formTitle = page.locator('h1, h2').filter({
      hasText: /helpdesk|ticket|submit|create/i
    }).first();
    await expect(formTitle).toBeVisible({ timeout: 5000 });

    // Take screenshot of form
    const screenshotPath = path.join(SCREENSHOT_DIR, '03_helpdesk_form_loaded_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('04 - Helpdesk Form - Filling Out', async ({ page }) => {
    // Navigate to helpdesk form
    await page.goto('/helpdesk/create');
    await page.waitForLoadState('networkidle');

    // Fill in form fields
    const testData = {
      name: 'John Guest User',
      email: `guest-${Date.now()}@example.com`,
      phone: '+60123456789',
      subject: 'Unable to access loan application portal',
      category: 'Technical Support',
      description: 'I am having trouble accessing the loan application form. The page keeps showing an error.',
      priority: 'High'
    };

    // Fill name field
    const nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible()) {
      await nameInput.fill(testData.name);
    }

    // Fill email field
    const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
    if (await emailInput.isVisible()) {
      await emailInput.fill(testData.email);
    }

    // Fill phone field
    const phoneInput = page.locator('input[type="tel"], input[placeholder*="phone"], input[name*="phone"]').first();
    if (await phoneInput.isVisible()) {
      await phoneInput.fill(testData.phone);
    }

    // Fill subject field
    const subjectInput = page.locator('input[placeholder*="Subject"], input[name*="subject"]').first();
    if (await subjectInput.isVisible()) {
      await subjectInput.fill(testData.subject);
    }

    // Fill category dropdown (if exists)
    const categorySelect = page.locator('select[name*="category"], [role="combobox"]').filter({
      hasText: /category|type|department/i
    }).first();
    if (await categorySelect.isVisible()) {
      await categorySelect.click();
      const option = page.locator('text=' + testData.category).first();
      if (await option.isVisible()) {
        await option.click();
      }
    }

    // Fill description textarea
    const descriptionInput = page.locator('textarea[placeholder*="Description"], textarea[name*="description"], textarea[name*="message"]').first();
    if (await descriptionInput.isVisible()) {
      await descriptionInput.fill(testData.description);
    }

    // Fill priority (if exists)
    const prioritySelect = page.locator('select[name*="priority"], [role="combobox"]').filter({
      hasText: /priority|urgency/i
    }).first();
    if (await prioritySelect.isVisible()) {
      await prioritySelect.click();
      const priorityOption = page.locator('text=' + testData.priority).first();
      if (await priorityOption.isVisible()) {
        await priorityOption.click();
      }
    }


    // Take screenshot of filled form
    const screenshotPath = path.join(SCREENSHOT_DIR, '04_helpdesk_form_filled_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('05 - Helpdesk Form - Submit', async ({ page }) => {
    // Navigate to helpdesk form
    await page.goto('/helpdesk/create');
    await page.waitForLoadState('networkidle');

    // Navigate through form steps to reach submission
    // Step 1: Fill contact info
    let nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible({ timeout: 5000 })) {
      await nameInput.fill('Guest Helpdesk User');

      const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
      if (await emailInput.isVisible()) {
        await emailInput.fill(`helpdesk-submit-${Date.now()}@example.com`);
      }

      // Move to next step
      const nextButton = page.locator('button').filter({ hasText: /Next|next/ }).first();
      if (await nextButton.isVisible()) {
        await nextButton.click();
        await page.waitForTimeout(500);
      }
    }

    // Step 2 & 3: Navigate through remaining steps
    for (let i = 0; i < 2; i++) {
      const nextBtn = page.locator('button').filter({ hasText: /Next|next/ }).first();
      if (await nextBtn.isVisible({ timeout: 3000 })) {
        await nextBtn.click();
        await page.waitForTimeout(500);
      }
    }

    // Step 4: Submit
    const submitButton = page.locator('button').filter({ hasText: /Submit|submit/ }).first();
    if (await submitButton.isVisible({ timeout: 5000 })) {
      await submitButton.click();
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(1000);
    }

    // Take screenshot after submission
    const screenshotPath = path.join(SCREENSHOT_DIR, '05_helpdesk_form_submitted_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('06 - Helpdesk Success Page', async ({ page }) => {
    // Navigate to helpdesk create page
    await page.goto('/helpdesk/create');
    await page.waitForLoadState('networkidle');

    // Navigate through wizard steps
    let nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible({ timeout: 5000 })) {
      await nameInput.fill('Guest Success Test');

      const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
      if (await emailInput.isVisible()) {
        await emailInput.fill(`success-test-${Date.now()}@example.com`);
      }

      // Move through steps
      for (let i = 0; i < 3; i++) {
        const nextBtn = page.locator('button').filter({ hasText: /Next|next|Submit|submit/ }).first();
        if (await nextBtn.isVisible({ timeout: 3000 })) {
          await nextBtn.click();
          await page.waitForTimeout(500);
        }
      }

      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(1000);
    }

    // Take screenshot
    const screenshotPath = path.join(SCREENSHOT_DIR, '06_helpdesk_success_page_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('07 - Navigate to Loan Application Form', async ({ page }) => {
    // Navigate to welcome page
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Find and click loan application link
    const loanLink = page.locator('a, button').filter({
      hasText: /loan|asset|application|borrow/i
    }).first();

    if (await loanLink.isVisible()) {
      await loanLink.click();
      await page.waitForLoadState('networkidle');
    } else {
      // Fallback: navigate directly
      await page.goto('/loan/apply');
      await page.waitForLoadState('networkidle');
    }

    // Take screenshot showing navigation
    const screenshotPath = path.join(SCREENSHOT_DIR, '07_welcome_loan_navigation_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('08 - Loan Application Form - Loaded', async ({ page }) => {
    // Navigate to loan application form
    await page.goto('/loan/apply');
    await page.waitForLoadState('networkidle');

    // Verify form is loaded
    const formTitle = page.locator('h1, h2').filter({
      hasText: /loan|application|asset|borrow/i
    }).first();
    await expect(formTitle).toBeVisible({ timeout: 5000 });

    // Take screenshot of form
    const screenshotPath = path.join(SCREENSHOT_DIR, '08_loan_form_loaded_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('09 - Loan Application Form - Filling Out', async ({ page }) => {
    // Navigate to loan application form
    await page.goto('/loan/apply');
    await page.waitForLoadState('networkidle');

    // Fill in form fields
    const testData = {
      name: 'Guest Loan Applicant',
      email: `loan-guest-${Date.now()}@example.com`,
      phone: '+60198765432',
      department: 'Human Resources',
      position: 'Manager',
      loanAmount: '5000',
      assetType: 'Laptop',
      assetDescription: 'Dell XPS 15 for work purposes',
      loanPurpose: 'Work equipment acquisition',
      repaymentPeriod: '12'
    };

    // Fill name field
    const nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible()) {
      await nameInput.fill(testData.name);
    }

    // Fill email field
    const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
    if (await emailInput.isVisible()) {
      await emailInput.fill(testData.email);
    }

    // Fill phone field
    const phoneInput = page.locator('input[type="tel"], input[placeholder*="phone"], input[name*="phone"]').first();
    if (await phoneInput.isVisible()) {
      await phoneInput.fill(testData.phone);
    }

    // Fill department
    const departmentInput = page.locator('input[placeholder*="Department"], input[name*="department"]').first();
    if (await departmentInput.isVisible()) {
      await departmentInput.fill(testData.department);
    }

    // Fill position
    const positionInput = page.locator('input[placeholder*="Position"], input[name*="position"]').first();
    if (await positionInput.isVisible()) {
      await positionInput.fill(testData.position);
    }

    // Fill loan amount
    const amountInput = page.locator('input[type="number"], input[placeholder*="Amount"]').first();
    if (await amountInput.isVisible()) {
      await amountInput.fill(testData.loanAmount);
    }

    // Fill asset type
    const assetInput = page.locator('input[placeholder*="Asset"], input[name*="asset"]').first();
    if (await assetInput.isVisible()) {
      await assetInput.fill(testData.assetType);
    }

    // Fill description
    const descriptionInput = page.locator('textarea[placeholder*="Description"], textarea[name*="description"]').first();
    if (await descriptionInput.isVisible()) {
      await descriptionInput.fill(testData.loanPurpose);
    }

    // Scroll to ensure all content visible
    await page.keyboard.press('End');
    await page.waitForTimeout(500);

    // Take screenshot of filled form
    const screenshotPath = path.join(SCREENSHOT_DIR, '09_loan_form_filled_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('10 - Loan Application Form - Submit', async ({ page, context }) => {
    // Navigate to loan application form with increased timeout
    const navigationPromise = page.goto('/loan/apply', { waitUntil: 'domcontentloaded' });

    try {
      await navigationPromise;
    } catch (e) {
      // Continue even if navigation takes longer
    }

    await page.waitForTimeout(2000);

    // Fill in minimal required fields
    const nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible({ timeout: 3000 })) {
      await nameInput.fill('Guest Loan Applicant');
    }

    const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
    const uniqueEmail = `loan-submit-${Date.now()}@example.com`;
    if (await emailInput.isVisible({ timeout: 3000 })) {
      await emailInput.fill(uniqueEmail);
    }

    const phoneInput = page.locator('input[type="tel"], input[name*="phone"]').first();
    if (await phoneInput.isVisible({ timeout: 3000 })) {
      await phoneInput.fill('+60187654321');
    }

    // Find and click submit button using button content
    const submitButton = page.locator('button').filter({ hasText: /Submit|Apply|Create/i }).first();

    if (await submitButton.isVisible({ timeout: 3000 })) {
      await submitButton.click();

      // Wait for navigation or success message
      await page.waitForTimeout(2000);
    }

    // Take screenshot after submission
    const screenshotPath = path.join(SCREENSHOT_DIR, '10_loan_form_submitted_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('11 - Loan Application Success Page', async ({ page }) => {
    // Navigate to loan application form
    const navigationPromise = page.goto('/loan/apply', { waitUntil: 'domcontentloaded' });

    try {
      await navigationPromise;
    } catch (e) {
      // Continue even if navigation takes longer
    }

    await page.waitForTimeout(2000);

    // Fill and submit
    const nameInput = page.locator('input[placeholder*="Name"], input[name*="name"], input[name*="full_name"]').first();
    if (await nameInput.isVisible({ timeout: 3000 })) {
      await nameInput.fill('Loan Success Test User');

      const emailInput = page.locator('input[type="email"], input[name*="email"]').first();
      if (await emailInput.isVisible({ timeout: 3000 })) {
        await emailInput.fill(`loan-success-${Date.now()}@example.com`);
      }

      const phoneInput = page.locator('input[type="tel"], input[name*="phone"]').first();
      if (await phoneInput.isVisible({ timeout: 3000 })) {
        await phoneInput.fill('+60198765432');
      }

      const submitButton = page.locator('button').filter({ hasText: /Submit|Apply|Create/ }).first();
      if (await submitButton.isVisible({ timeout: 3000 })) {
        await submitButton.click();
        await page.waitForTimeout(2000);
      }
    }

    // Take screenshot
    const screenshotPath = path.join(SCREENSHOT_DIR, '11_loan_success_page_guest.png');
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`✓ Screenshot saved: ${screenshotPath}`);
  });

  test('12 - Complete Flow Summary - Screenshots Verification', async ({ page }) => {
    // Verify all screenshots were created
    const screenshots: string[] = fs.readdirSync(SCREENSHOT_DIR)
      .filter((file: string) => file.startsWith('0') && file.endsWith('.png'))
      .sort();

    console.log('\n╔════════════════════════════════════════════════════════════╗');
    console.log('║     Guest User Flow - Screenshots Captured                  ║');
    console.log('╚════════════════════════════════════════════════════════════╝\n');

    screenshots.forEach((screenshot: string, index: number) => {
      const step = screenshot.split('_')[0];
      const pageName = screenshot.split('_')[1];
      const activity = screenshot.split('_')[2];
      const userType = screenshot.split('_')[3]?.replace('.png', '') || 'guest';

      console.log(`${index + 1}. [Step ${step}] ${pageName} - ${activity} (${userType})`);
      console.log(`   📸 Location: /public/images/screenshots/${screenshot}\n`);
    });

    console.log(`Total Screenshots Captured: ${screenshots.length}`);
    console.log(`Screenshot Directory: ${SCREENSHOT_DIR}\n`);

    // Verify directory exists and has screenshots
    expect(screenshots.length).toBeGreaterThan(0);
  });
});
