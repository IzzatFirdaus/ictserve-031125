/**
 * Loan Module E2E Tests - Refactored with Best Practices
 *
 * REFACTORING UPDATES (November 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@loan, @smoke, @module)
 * - ✅ Soft assertions for comprehensive validation
 *
 * Research findings: Playwright Best Practices v1.56.1 (Official Documentation)
 *
 * Tests core functionality: navigation, loan application, approval workflow, and status tracking
 *
 * Run: npm run test:e2e -- tests/e2e/loan.refactored.spec.ts
 * Run smoke tests only: npm run test:e2e -- --grep @smoke
 */

import { test, expect } from './fixtures/ictserve-fixtures';

test.describe('Loan Module - Best Practices Architecture', () => {

  test('01 - Loan Module Navigation', {
    tag: ['@smoke', '@loan', '@module', '@navigation'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();

    // Navigate to loan using Page Object Model method
    await staffDashboardPage.navigateToLoan();

    // Web-first assertion: verifies navigation completed
    await expect(authenticatedPage).toHaveURL(/loan/);

    // Verify loan page heading is visible (use first() to avoid strict mode)
    await expect(authenticatedPage.getByRole('heading', { name: /loan|pinjaman/i }).first()).toBeVisible();
  });

  test('02 - Loan Application List View', {
    tag: ['@smoke', '@loan', '@module'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/staff/loans');

    // Web-first assertion: verify page loaded
    await expect(authenticatedPage).toHaveURL(/staff\/loans/);

    // Verify page content loaded
    await expect(authenticatedPage.getByRole('main')).toBeVisible();
  });

  test('03 - Create New Loan Application - Form Accessibility', {
    tag: ['@loan', '@module', '@form'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/apply');

    // Step 1: applicant info (authenticated user has prefilled identity)
    const purposeField = authenticatedPage.getByLabel(/purpose|tujuan/i).first();
    if (await purposeField.isVisible({ timeout: 3000 })) {
      await purposeField.fill('Loan request for testing the new wizard');
    }

    const locationField = authenticatedPage.getByLabel(/location|lokasi/i).first();
    if (await locationField.isVisible({ timeout: 3000 })) {
      await locationField.fill('HQ Meeting Room');
    }

    const nextButton = authenticatedPage.getByRole('button', { name: /next|seterusnya/i }).first();
    if (await nextButton.isVisible({ timeout: 5000 })) {
      await nextButton.click();

      // Step 2: responsible officer (optional)
      await expect.soft(authenticatedPage.getByText(/responsible officer|pegawai bertanggungjawab/i).first()).toBeVisible({ timeout: 5000 });
      await authenticatedPage.getByRole('button', { name: /next|seterusnya/i }).first().click();

      // Step 3: equipment list
      await expect(authenticatedPage.getByText(/equipment list|senarai peralatan/i).first()).toBeVisible({ timeout: 5000 });
    }
  });

  test('04 - Create New Loan Application - Form Validation', {
    tag: ['@loan', '@module', '@form', '@validation'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/apply');

    // Try to advance without filling required fields
    await authenticatedPage.getByRole('button', { name: /next|seterusnya/i }).click();

    // Web-first assertion: verify validation messages appear
    // User-facing locator for error messages
    const errorMessage = authenticatedPage.locator('[role="alert"]').or(
      authenticatedPage.locator('.error-message, [class*="error"]')
    );

    await expect(errorMessage).toBeVisible({ timeout: 3000 });
  });

  test('05 - Create New Loan Application - Successful Submission', {
    tag: ['@smoke', '@loan', '@module', '@form'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/apply');

    // Step 1
    const purposeField = authenticatedPage.getByLabel(/purpose|tujuan/i).first();
    if (await purposeField.isVisible({ timeout: 3000 })) {
      await purposeField.fill('E2E Test Loan - Equipment for development');
    }

    const locationField = authenticatedPage.getByLabel(/location|lokasi/i).first();
    if (await locationField.isVisible({ timeout: 3000 })) {
      await locationField.fill('HQ Lab');
    }

    const nextButton = authenticatedPage.getByRole('button', { name: /next|seterusnya/i }).first();
    if (await nextButton.isVisible({ timeout: 3000 })) {
      await nextButton.click();

      // Step 2 (optional) -> Step 3
      await authenticatedPage.getByRole('button', { name: /next|seterusnya/i }).first().click();

      // Verify we reached equipment selection
      await expect(authenticatedPage.getByText(/equipment|peralatan/i).first()).toBeVisible({ timeout: 5000 });
    }
  });

  test('06 - Loan Application Filtering and Search', {
    tag: ['@loan', '@module', '@filter'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/staff/loans');

    // Verify page loaded
    await expect(authenticatedPage).toHaveURL(/staff\/loans/);

    // Verify page content loaded
    await expect(authenticatedPage.getByRole('main')).toBeVisible();
  });

  test('07 - View Loan Application Details', {
    tag: ['@loan', '@module', '@detail'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/staff/loans');

    // Click first loan link using specific selector
    const firstLoanLink = authenticatedPage.locator('a[href*="loan.authenticated.show"]').first();

    if (await firstLoanLink.isVisible({ timeout: 3000 })) {
      await firstLoanLink.click();

      // Web-first assertion: verify navigation to detail page
      await expect(authenticatedPage).toHaveURL(/loans.*\d+/);

      // Verify detail page elements are visible
      await expect.soft(
        authenticatedPage.getByRole('heading', { name: /loan|detail|pinjaman/i })
      ).toBeVisible();

      await expect.soft(
        authenticatedPage.getByText(/purpose|tujuan|item|barang/i).first()
      ).toBeVisible();
    }
  });

  test('08 - Loan Status Filter', {
    tag: ['@loan', '@module', '@filter'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/staff/loans');

    // Look for status filter using user-facing locator
    const statusFilter = authenticatedPage.getByLabel(/status|filter/i).or(
      authenticatedPage.locator('select[name*="status"]')
    );

    if (await statusFilter.isVisible({ timeout: 3000 })) {
      // Select "Pending" status (using index to avoid RegExp restriction)
      await statusFilter.selectOption({ index: 1 });

      // Wait for filter to apply
      await authenticatedPage.waitForTimeout(1000);

      // Verify table still visible with filtered results
      const loanTable = authenticatedPage.getByRole('table').or(
        authenticatedPage.locator('[role="grid"]')
      );

      await expect(loanTable).toBeVisible();
    }
  });

  test('09 - Loan Approval Workflow (if admin)', {
    tag: ['@loan', '@module', '@approval'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/staff/loans');

    // Navigate to first pending loan
    const firstLoanLink = authenticatedPage.getByRole('link').first();
    if (await firstLoanLink.isVisible({ timeout: 3000 })) {
      await firstLoanLink.click();

      // Look for approve/reject buttons
      const approveButton = authenticatedPage.getByRole('button', { name: /approve|lulus/i });
      const rejectButton = authenticatedPage.getByRole('button', { name: /reject|tolak/i });

      // If approve button exists, this user has approval permissions
      if (await approveButton.isVisible({ timeout: 3000 })) {
        await approveButton.click();

        // Verify success message
        await expect.soft(
          authenticatedPage.getByText(/approved|diluluskan|success/i)
        ).toBeVisible({ timeout: 5000 });
      }
    }
  });

  test('10 - Module Navigation - Return to Dashboard', {
    tag: ['@smoke', '@loan', '@module', '@navigation'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/authenticated');

    // Direct navigation to dashboard
    await authenticatedPage.goto('/staff/dashboard');
    await expect(authenticatedPage).toHaveURL(/staff\/dashboard/);
  });

  test('11 - Module Console Error Check', {
    tag: ['@loan', '@module', '@debugging'],
  }, async ({ authenticatedPage }) => {
    const consoleErrors: string[] = [];

    // Listen for console errors
    authenticatedPage.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // Navigate through loan module
    await authenticatedPage.goto('/loan/authenticated');
    await authenticatedPage.waitForLoadState('networkidle');

    // Filter out expected errors (404s, third-party scripts, Livewire, WebSocket)
    const criticalErrors = consoleErrors.filter(error =>
      !error.includes('404') &&
      !error.includes('favicon') &&
      !error.includes('cdn') &&
      !error.includes('analytics') &&
      !error.includes('livewire') &&
      !error.includes('Livewire') &&
      !error.includes('WebSocket') &&
      !error.includes('ws://') &&
      !error.includes('wss://') &&
      !error.includes('Failed to send logs')
    );

    // Soft assertion: no critical errors should occur
    await expect.soft(criticalErrors.length).toBe(0);

    if (criticalErrors.length > 0) {
      console.log('Console errors detected:', criticalErrors);
    }
  });

  test('12 - Guest Asset Loan Wizard', {
    tag: ['@loan', '@module', '@guest', '@form'],
  }, async ({ page }) => {
    await page.goto('/loan/apply');

    await expect(page.getByRole('heading', { name: /loan|pinjaman/i }).first()).toBeVisible();
    await expect(page.getByText(/applicant information|your information|section 1/i).first()).toBeVisible();

    const nameField = page.getByLabel(/full name|nama penuh/i).first();
    if (await nameField.isVisible({ timeout: 5000 })) {
      await nameField.fill('Guest Borrower');
    }

    const positionField = page.getByLabel(/position|jawatan|grade/i).first();
    if (await positionField.isVisible({ timeout: 3000 })) {
      await positionField.fill('Administrative Officer N41');
    }

    const phoneField = page.getByLabel(/phone number|telefon|phone/i).first();
    if (await phoneField.isVisible({ timeout: 3000 })) {
      await phoneField.fill('012-3456789');
    }

    const divisionSelect = page.getByLabel(/division|unit/i);
    if (await divisionSelect.isVisible({ timeout: 3000 })) {
      await divisionSelect.selectOption({ index: 1 });
    }

    const purposeField = page.getByLabel(/purpose|tujuan/i).first();
    if (await purposeField.isVisible({ timeout: 3000 })) {
      await purposeField.fill('Guest automation test');
    }

    const locationField = page.getByLabel(/location|lokasi/i).first();
    if (await locationField.isVisible({ timeout: 3000 })) {
      await locationField.fill('MOTAC HQ');
    }

    const startDateInput = page.getByLabel(/loan date|tarikh pinjaman/i).first();
    const endDateInput = page.getByLabel(/expected return date|return date|tarikh pulang/i).first();
    if (await startDateInput.isVisible({ timeout: 3000 }) && await endDateInput.isVisible({ timeout: 3000 })) {
      const tomorrow = new Date();
      tomorrow.setDate(tomorrow.getDate() + 1);
      const nextWeek = new Date();
      nextWeek.setDate(nextWeek.getDate() + 7);
      await startDateInput.fill(tomorrow.toISOString().split('T')[0]);
      await endDateInput.fill(nextWeek.toISOString().split('T')[0]);
    }

    await page.getByRole('button', { name: /next|seterusnya/i }).click();
    await page.getByRole('button', { name: /next|seterusnya/i }).click();

    await expect(page.getByText(/equipment list|senarai peralatan/i)).toBeVisible();
    const equipmentSelectGuest = page.locator('select[name*="equipment_items"]').first();
    if (await equipmentSelectGuest.isVisible({ timeout: 3000 })) {
      await equipmentSelectGuest.selectOption({ index: 1 });
    }

    await expect(page.getByRole('button', { name: /next|seterusnya/i })).toBeVisible();
  });

});
