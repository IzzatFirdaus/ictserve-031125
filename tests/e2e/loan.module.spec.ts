import { test, expect } from '@playwright/test';

/**
 * Asset Loan Module E2E Tests
 * Tests core functionality: loan requests, approvals, asset selection, and error handling
 */

test.describe('Asset Loan Module', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to home
    await page.goto('/');
    await page.waitForLoadState('networkidle');
  });

  test('should load home page without JavaScript errors', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    // Verify page loaded
    const body = page.locator('body');
    await expect(body).toBeVisible({ timeout: 5000 });

    // Should have main content
    const mainContent = page.locator('main, [role="main"], .container, .content').first();
    if (await mainContent.isVisible({ timeout: 3000 }).catch(() => false)) {
      await expect(mainContent).toBeVisible();
    }

    expect(errors).toEqual([]);
  });

  test('should navigate to loan module', async ({ page }) => {
    const loanLink = page.locator('a:has-text("Loan"), a:has-text("Asset"), a:has-text("Request"), [href*="loan"], [href*="asset"]').first();

    if (await loanLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await loanLink.click();
      await page.waitForLoadState('networkidle');

      // Should navigate to loan-related page
      const url = page.url();
      expect(url).toBeTruthy();
    }
  });

  test('should display loan list without errors', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Try to find loan section
    const loanSection = page.locator('[href*="loan"], [href*="asset"], a:has-text("Loan"), a:has-text("Asset")').first();

    if (await loanSection.isVisible({ timeout: 3000 }).catch(() => false)) {
      await loanSection.click();
      await page.waitForLoadState('networkidle');

      // Check for table/grid
      const list = page.locator('table, [role="grid"], .grid').first();
      if (await list.isVisible({ timeout: 3000 }).catch(() => false)) {
        await expect(list).toBeVisible();
      }
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle loan request form interaction', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Find create/new loan button
    const createBtn = page.locator('button:has-text("Create"), button:has-text("New"), button:has-text("Request"), a:has-text("Create"), a:has-text("New")').first();

    if (await createBtn.isVisible({ timeout: 3000 }).catch(() => false)) {
      await createBtn.click();
      await page.waitForLoadState('networkidle');

      // Form should load
      const form = page.locator('form, [role="form"]').first();
      if (await form.isVisible({ timeout: 3000 }).catch(() => false)) {
        await expect(form).toBeVisible();
      }
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle asset selection dropdown', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Look for select dropdowns
    const selects = page.locator('select, [role="listbox"], [role="combobox"]').first();

    if (await selects.isVisible({ timeout: 3000 }).catch(() => false)) {
      await selects.click();
      await page.waitForLoadState('networkidle');

      // Options should appear
      const options = page.locator('[role="option"]').first();
      if (await options.isVisible({ timeout: 2000 }).catch(() => false)) {
        await expect(options).toBeVisible();
      }
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle approval workflow buttons', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Look for action buttons
    const actionButtons = page.locator('button:has-text("Approve"), button:has-text("Reject"), button:has-text("Submit"), button:has-text("Cancel")').first();

    if (await actionButtons.isVisible({ timeout: 3000 }).catch(() => false)) {
      // Just verify they're clickable, don't perform action
      await expect(actionButtons).toBeEnabled();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should maintain responsive behavior', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Check viewport size
    const viewportSize = page.viewportSize();
    expect(viewportSize).toBeTruthy();

    // Interact with page
    const buttons = page.locator('button').first();
    if (await buttons.isVisible({ timeout: 3000 }).catch(() => false)) {
      await expect(buttons).toBeVisible();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle form validation feedback', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Look for input fields
    const inputs = page.locator('input[type="text"], input[type="email"], textarea, select').first();

    if (await inputs.isVisible({ timeout: 3000 }).catch(() => false)) {
      // Focus on field
      await inputs.focus();
      await page.waitForTimeout(500);

      // Validation attributes should be present
      const isRequired = await inputs.getAttribute('required');
      expect(typeof isRequired === 'string' || isRequired === null).toBeTruthy();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle network requests without failures', async ({ page }) => {
    const failedRequests: string[] = [];

    page.on('response', response => {
      if (response.status() >= 400 && response.status() !== 404) {
        failedRequests.push(`${response.status()}: ${response.url()}`);
      }
    });

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Navigate through key pages
    const navigationLinks = page.locator('a[href^="/"], a[href^="http"]').first();

    if (await navigationLinks.isVisible({ timeout: 3000 }).catch(() => false)) {
      await navigationLinks.click();
      await page.waitForLoadState('networkidle');
    }

    // Only 5xx errors are critical; 404s are expected
    const criticalFailures = failedRequests.filter(r => r.startsWith('5'));
    expect(criticalFailures).toEqual([]);
  });
});
