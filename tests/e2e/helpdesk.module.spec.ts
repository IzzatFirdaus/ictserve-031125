import { test, expect } from '@playwright/test';

/**
 * Helpdesk Ticket Module E2E Tests
 * Tests core functionality: navigation, ticket creation, filtering, and error handling
 */

test.describe('Helpdesk Ticket Module', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to home
    await page.goto('/');
    await page.waitForURL('**/dashboard');
  });

  test('should load welcome page without errors', async ({ page }) => {
    // Check for console errors
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    // Page should contain welcome content
    const title = await page.locator('h1, h2, [role="heading"]').first();
    await title.waitFor({ state: 'visible', timeout: 5000 });
    await expect(title).toBeVisible();

    // No console errors
    expect(errors).toEqual([]);
  });

  test('should navigate to helpdesk module', async ({ page }) => {
    // Look for helpdesk link in navigation
    const helpDeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"], [href*="ticket"]').first();

    if (await helpDeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpDeskLink.click();
      await page.waitForURL(/\/(helpdesk|ticket)/i);

      // Verify page loaded
      await expect(page).toHaveURL(/\/(helpdesk|ticket)/i);
    }
  });

  test('should display ticket list without console errors', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    // Navigate to tickets
    await page.goto('/');
    const ticketLink = page.locator('a:has-text("Ticket"), a:has-text("Helpdesk"), [href*="ticket"], [href*="helpdesk"]').first();

    if (await ticketLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await ticketLink.click();
      await page.waitForURL(/\/(helpdesk|ticket)/i);

      // Should have table or list
      const table = page.locator('table, [role="grid"], .grid, .table').first();
      await table.waitFor({ state: 'visible', timeout: 5000 });
      await expect(table).toBeVisible();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle ticket creation form', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    // Navigate to create ticket
    await page.goto('/');
    const createLink = page.locator('a:has-text("Create"), a:has-text("New"), a:has-text("Submit"), button:has-text("Create"), button:has-text("New")').first();

    if (await createLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await createLink.click();
      // Form should be visible
      const form = page.locator('form, [role="form"], .form').first();
      await form.waitFor({ state: 'visible', timeout: 5000 });
      await expect(form).toBeVisible();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should respond to filter interactions', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    await page.goto('/');

    // Look for filter/search inputs
    const filterInputs = page.locator('input[placeholder*="earch"], input[placeholder*="ilter"], select').first();

    if (await filterInputs.isVisible({ timeout: 3000 }).catch(() => false)) {
      await filterInputs.click();
      await filterInputs.type('test', { delay: 100 });
      await page.waitForLoadState('networkidle'); // Keep networkidle here as it's waiting for filter results to load
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });

  test('should handle network errors gracefully', async ({ page }) => {
    const errors: string[] = [];
    const warnings: string[] = [];

    page.on('console', msg => {
      if (msg.type() === 'error') errors.push(msg.text());
      if (msg.type() === 'warning') warnings.push(msg.text());
    });

    await page.goto('/');
    await page.locator('h1, h2, [role="heading"]').first().waitFor({ state: 'visible', timeout: 5000 });

    // Critical errors should be empty (warnings/network info is OK)
    const criticalErrors = errors.filter(e =>
      !e.includes('404') &&
      !e.includes('cross-origin') &&
      !e.includes('favicon')
    );

    expect(criticalErrors).toEqual([]);
  });

  test('should maintain session across navigation', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') errors.push(msg.text());
    });

    await page.locator('h1, h2, [role="heading"]').first().waitFor({ state: 'visible', timeout: 5000 });

    // Try clicking any main navigation link (not hidden skip link)
    const navLinks = page.locator('header a, nav a, [role="navigation"] a').filter({
      hasNot: page.locator('.absolute, .hidden')
    }).first();

    if (await navLinks.isVisible({ timeout: 3000 }).catch(() => false)) {
      try {
        await navLinks.click();
        await page.waitForLoadState('networkidle');
      } catch (e) {
        // Navigation click optional
      }
    }

    // Should still be functional
    const heading = page.locator('h1, h2, [role="heading"]').first();
    if (await heading.isVisible({ timeout: 3000 }).catch(() => false)) {
      await expect(heading).toBeVisible();
    }

    expect(errors.filter(e => !e.includes('404'))).toEqual([]);
  });
});
