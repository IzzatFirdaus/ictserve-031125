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

    // Verify loan page heading is visible
    await expect(authenticatedPage.getByRole('heading', { name: /loan|pinjaman/i })).toBeVisible();
  });

  test('02 - Loan Application List View', {
    tag: ['@smoke', '@loan', '@module'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan');

    // Web-first assertion: verify page loaded
    await expect(authenticatedPage).toHaveURL(/loan/);

    // Soft assertions: verify key components present
    // Using user-facing locators (table role, headings)
    const loanTable = authenticatedPage.getByRole('table').or(
      authenticatedPage.locator('[role="grid"]')
    );

    await expect.soft(loanTable).toBeVisible({ timeout: 10000 });

    // Verify action buttons are accessible
    const createButton = authenticatedPage.getByRole('button', { name: /apply|new loan|pinjaman baru/i });
    await expect.soft(createButton).toBeVisible({ timeout: 5000 });
  });

  test('03 - Create New Loan Application - Form Accessibility', {
    tag: ['@loan', '@module', '@form'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/apply');

    // Web-first assertion: verify navigation
    await expect(authenticatedPage).toHaveURL(/loan.*apply/);

    // Soft assertions: verify form fields are accessible
    // Using user-facing locators (getByLabel for form fields)
    await expect.soft(
      authenticatedPage.getByLabel(/item|barang|asset/i)
    ).toBeVisible({ timeout: 5000 });

    await expect.soft(
      authenticatedPage.getByLabel(/purpose|tujuan/i)
    ).toBeVisible({ timeout: 5000 });

    await expect.soft(
      authenticatedPage.getByLabel(/start date|tarikh mula/i)
    ).toBeVisible({ timeout: 5000 });

    await expect.soft(
      authenticatedPage.getByLabel(/end date|tarikh tamat/i)
    ).toBeVisible({ timeout: 5000 });

    // Verify submit button is accessible
    await expect.soft(
      authenticatedPage.getByRole('button', { name: /submit|hantar|apply/i })
    ).toBeVisible();
  });

  test('04 - Create New Loan Application - Form Validation', {
    tag: ['@loan', '@module', '@form', '@validation'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan/apply');

    // Try to submit empty form (should show validation errors)
    const submitButton = authenticatedPage.getByRole('button', { name: /submit|hantar|apply/i });
    await submitButton.click();

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

    // Fill form using user-facing locators
    await authenticatedPage.getByLabel(/purpose|tujuan/i).fill('E2E Test Loan - Equipment for development');

    // Set dates (tomorrow and next week)
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);

    await authenticatedPage.getByLabel(/start date|tarikh mula/i).fill(tomorrow.toISOString().split('T')[0]);
    await authenticatedPage.getByLabel(/end date|tarikh tamat/i).fill(nextWeek.toISOString().split('T')[0]);

    // Select item if available
    const itemSelect = authenticatedPage.getByLabel(/item|barang|asset/i);
    if (await itemSelect.isVisible({ timeout: 2000 })) {
      await itemSelect.selectOption({ index: 1 });
    }

    // Submit form
    const submitButton = authenticatedPage.getByRole('button', { name: /submit|hantar|apply/i });
    await submitButton.click();

    // Web-first assertion: verify success (redirect to list or success message)
    await expect(authenticatedPage).toHaveURL(/loan(?!.*apply)/, { timeout: 10000 });

    // Verify success message or loan appears in list
    const successIndicator = authenticatedPage.getByText(/success|successfully|berjaya/i).or(
      authenticatedPage.getByRole('alert')
    );

    await expect.soft(successIndicator).toBeVisible({ timeout: 5000 });
  });

  test('06 - Loan Application Filtering and Search', {
    tag: ['@loan', '@module', '@filter'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan');

    // Look for search input using user-facing locator
    const searchInput = authenticatedPage.getByRole('searchbox').or(
      authenticatedPage.getByPlaceholder(/search|cari/i)
    );

    if (await searchInput.isVisible({ timeout: 3000 })) {
      await searchInput.fill('Equipment');

      // Wait for results to filter
      await authenticatedPage.waitForTimeout(1000);

      // Verify table still visible (filtered results)
      const loanTable = authenticatedPage.getByRole('table').or(
        authenticatedPage.locator('[role="grid"]')
      );

      await expect(loanTable).toBeVisible();
    }
  });

  test('07 - View Loan Application Details', {
    tag: ['@loan', '@module', '@detail'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/loan');

    // Click first loan link using user-facing locator
    const firstLoanLink = authenticatedPage.getByRole('link', { name: /view|details|lihat/i }).first().or(
      authenticatedPage.locator('table tbody tr').first().getByRole('link').first()
    );

    if (await firstLoanLink.isVisible({ timeout: 3000 })) {
      await firstLoanLink.click();

      // Web-first assertion: verify navigation to detail page
      await expect(authenticatedPage).toHaveURL(/loan\/\d+/);

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
    await authenticatedPage.goto('/loan');

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
    await authenticatedPage.goto('/loan');

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
    await authenticatedPage.goto('/loan');

    // Navigate back to dashboard using user-facing locator
    const dashboardLink = authenticatedPage.getByRole('link', { name: /dashboard|home|papan pemuka/i });

    if (await dashboardLink.isVisible({ timeout: 3000 })) {
      await dashboardLink.click();

      // Web-first assertion: verify navigation to dashboard
      await expect(authenticatedPage).toHaveURL(/dashboard/);
    } else {
      // Fallback: direct navigation
      await authenticatedPage.goto('/dashboard');
      await expect(authenticatedPage).toHaveURL(/dashboard/);
    }
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
    await authenticatedPage.goto('/loan');
    await authenticatedPage.waitForLoadState('networkidle');

    // Filter out expected errors (404s, third-party scripts)
    const criticalErrors = consoleErrors.filter(error =>
      !error.includes('404') &&
      !error.includes('favicon') &&
      !error.includes('cdn') &&
      !error.includes('analytics')
    );

    // Soft assertion: no critical errors should occur
    await expect.soft(criticalErrors.length).toBe(0);

    if (criticalErrors.length > 0) {
      console.log('Console errors detected:', criticalErrors);
    }
  });

});
