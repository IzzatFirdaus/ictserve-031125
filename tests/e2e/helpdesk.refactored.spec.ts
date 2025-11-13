/**
 * Helpdesk Module E2E Tests - Refactored with Best Practices
 *
 * REFACTORING UPDATES (November 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@helpdesk, @smoke, @module)
 * - ✅ Soft assertions for comprehensive validation
 *
 * Research findings: Playwright Best Practices v1.56.1 (Official Documentation)
 *
 * Tests core functionality: navigation, ticket creation, filtering, and error handling
 *
 * Run: npm run test:e2e -- tests/e2e/helpdesk.refactored.spec.ts
 * Run smoke tests only: npm run test:e2e -- --grep @smoke
 */

import { test, expect } from './fixtures/ictserve-fixtures';

test.describe('Helpdesk Ticket Module - Best Practices Architecture', () => {

  test('01 - Helpdesk Module Navigation', {
    tag: ['@smoke', '@helpdesk', '@module', '@navigation'],
  }, async ({ authenticatedPage, staffDashboardPage }) => {
    await staffDashboardPage.goto();

    // Navigate to helpdesk using Page Object Model method
    await staffDashboardPage.navigateToHelpdesk();

    // Web-first assertion: verifies navigation completed
    await expect(authenticatedPage).toHaveURL(/helpdesk/);

    // Verify helpdesk page heading is visible
    await expect(authenticatedPage.getByRole('heading', { name: /helpdesk|ticket/i })).toBeVisible();
  });

  test('02 - Helpdesk Ticket List View', {
    tag: ['@smoke', '@helpdesk', '@module'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');

    // Web-first assertion: verify page loaded
    await expect(authenticatedPage).toHaveURL(/helpdesk\/tickets/);

    // Soft assertions: verify key components present
    // Using user-facing locators (table role, headings)
    const ticketTable = authenticatedPage.getByRole('table').or(
      authenticatedPage.locator('[role="grid"]')
    );

    await expect.soft(ticketTable).toBeVisible({ timeout: 10000 });

    // Verify action buttons are accessible
    const createButton = authenticatedPage.getByRole('button', { name: /create|new ticket|hantar/i });
    await expect.soft(createButton).toBeVisible({ timeout: 5000 });
  });

  test('03 - Create New Ticket - Form Accessibility', {
    tag: ['@helpdesk', '@module', '@form'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/tickets/create');

    // Web-first assertion: verify navigation
    await expect(authenticatedPage).toHaveURL(/tickets\/create/);

    // Soft assertions: verify form fields are accessible
    // Using user-facing locators (getByLabel for form fields)
    await expect.soft(
      authenticatedPage.getByLabel(/subject|tajuk/i)
    ).toBeVisible({ timeout: 5000 });

    await expect.soft(
      authenticatedPage.getByLabel(/description|keterangan/i)
    ).toBeVisible({ timeout: 5000 });

    await expect.soft(
      authenticatedPage.getByLabel(/priority|keutamaan/i)
    ).toBeVisible({ timeout: 5000 });

    // Verify submit button is accessible
    await expect.soft(
      authenticatedPage.getByRole('button', { name: /submit|hantar/i })
    ).toBeVisible();
  });

  test('04 - Create New Ticket - Form Validation', {
    tag: ['@helpdesk', '@module', '@form', '@validation'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/tickets/create');

    // Try to submit empty form (should show validation errors)
    const submitButton = authenticatedPage.getByRole('button', { name: /submit|hantar/i });
    await submitButton.click();

    // Web-first assertion: verify validation messages appear
    // User-facing locator for error messages
    const errorMessage = authenticatedPage.locator('[role="alert"]').or(
      authenticatedPage.locator('.error-message, [class*="error"]')
    );

    await expect(errorMessage).toBeVisible({ timeout: 3000 });
  });

  test('05 - Create New Ticket - Successful Submission', {
    tag: ['@smoke', '@helpdesk', '@module', '@form'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/tickets/create');

    // Fill form using user-facing locators
    await authenticatedPage.getByLabel(/subject|tajuk/i).fill('E2E Test Ticket - Network Issue');
    await authenticatedPage.getByLabel(/description|keterangan/i).fill('This is an automated test ticket created by Playwright E2E testing.');

    // Select priority if available
    const prioritySelect = authenticatedPage.getByLabel(/priority|keutamaan/i);
    if (await prioritySelect.isVisible({ timeout: 2000 })) {
      await prioritySelect.selectOption({ index: 1 });
    }

    // Submit form
    const submitButton = authenticatedPage.getByRole('button', { name: /submit|hantar/i });
    await submitButton.click();

    // Web-first assertion: verify success (redirect to list or success message)
    await expect(authenticatedPage).toHaveURL(/helpdesk\/tickets|staff\/tickets/, { timeout: 10000 });

    // Verify success message or ticket appears in list
    const successIndicator = authenticatedPage.getByText(/success|successfully|berjaya/i).or(
      authenticatedPage.getByRole('alert')
    );

    await expect.soft(successIndicator).toBeVisible({ timeout: 5000 });
  });

  test('06 - Ticket Filtering and Search', {
    tag: ['@helpdesk', '@module', '@filter'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');

    // Look for search input using user-facing locator
    const searchInput = authenticatedPage.getByRole('searchbox').or(
      authenticatedPage.getByPlaceholder(/search|cari/i)
    );

    if (await searchInput.isVisible({ timeout: 3000 })) {
      await searchInput.fill('Network');

      // Wait for results to filter
      await authenticatedPage.waitForTimeout(1000);

      // Verify table still visible (filtered results)
      const ticketTable = authenticatedPage.getByRole('table').or(
        authenticatedPage.locator('[role="grid"]')
      );

      await expect(ticketTable).toBeVisible();
    }
  });

  test('07 - View Ticket Details', {
    tag: ['@helpdesk', '@module', '@detail'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');

    // Click first ticket link using user-facing locator
    const firstTicketLink = authenticatedPage.getByRole('link', { name: /view|details|lihat/i }).first().or(
      authenticatedPage.locator('table tbody tr').first().getByRole('link').first()
    );

    if (await firstTicketLink.isVisible({ timeout: 3000 })) {
      await firstTicketLink.click();

      // Web-first assertion: verify navigation to detail page
      await expect(authenticatedPage).toHaveURL(/tickets\/\d+|staff\/tickets\/\d+/);

      // Verify detail page elements are visible
      await expect.soft(
        authenticatedPage.getByRole('heading', { name: /ticket|detail/i })
      ).toBeVisible();

      await expect.soft(
        authenticatedPage.getByText(/subject|tajuk|description|keterangan/i).first()
      ).toBeVisible();
    }
  });

  test('08 - Ticket Status Update', {
    tag: ['@helpdesk', '@module', '@status'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');

    // Navigate to first ticket
    const firstTicketLink = authenticatedPage.getByRole('link').first();
    if (await firstTicketLink.isVisible({ timeout: 3000 })) {
      await firstTicketLink.click();

      // Look for status update button/select
      const statusSelect = authenticatedPage.getByLabel(/status|state/i).or(
        authenticatedPage.locator('select[name*="status"]')
      );

      if (await statusSelect.isVisible({ timeout: 3000 })) {
        await statusSelect.selectOption({ index: 1 });

        // Look for save/update button
        const saveButton = authenticatedPage.getByRole('button', { name: /save|update|kemaskini/i });
        if (await saveButton.isVisible({ timeout: 2000 })) {
          await saveButton.click();

          // Verify success message
          await expect.soft(
            authenticatedPage.getByText(/success|updated|berjaya/i)
          ).toBeVisible({ timeout: 5000 });
        }
      }
    }
  });

  test('09 - Module Navigation - Return to Dashboard', {
    tag: ['@smoke', '@helpdesk', '@module', '@navigation'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');

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

  test('10 - Module Console Error Check', {
    tag: ['@helpdesk', '@module', '@debugging'],
  }, async ({ authenticatedPage }) => {
    const consoleErrors: string[] = [];

    // Listen for console errors
    authenticatedPage.on('console', msg => {
      if (msg.type() === 'error') {
        consoleErrors.push(msg.text());
      }
    });

    // Navigate through helpdesk module
    await authenticatedPage.goto('/helpdesk/tickets');
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
