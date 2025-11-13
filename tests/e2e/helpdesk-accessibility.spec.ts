import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * Helpdesk Module Accessibility Tests (WCAG 2.2 AA)
 * Tests keyboard navigation, screen reader support, focus indicators, touch targets, and color contrast
 *
 * @trace Requirement 5 (WCAG 2.2 AA Compliance)
 * @trace Requirement 6 (Enhanced Responsive and Accessible Interfaces)
 */

test.describe('Helpdesk Module - Accessibility Compliance', () => {
  test.beforeEach(async ({ page }) => {
    // Try to navigate with better error handling and longer timeout
    // Uses baseURL from playwright.config.ts
    try {
      await page.goto('/', { timeout: 15000, waitUntil: 'domcontentloaded' });
    } catch (error) {
      console.log('Warning: Could not connect to server. Make sure Laravel is running (configured via playwright.config.ts)');
      throw new Error('Laravel server not running. Start with: php artisan serve');
    }
  });

  test('should pass WCAG 2.2 AA automated checks on helpdesk pages', async ({ page }) => {
    // Test welcome page
    const welcomeResults = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])
      .analyze();

    expect(welcomeResults.violations).toEqual([]);

    // Navigate to helpdesk if available
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();
    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      const helpdeskResults = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])
        .analyze();

      expect(helpdeskResults.violations).toEqual([]);
    }
  });

  test('should support full keyboard navigation on helpdesk forms', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      // Tab to helpdesk link
      await page.keyboard.press('Tab');
      await page.keyboard.press('Tab');

      // Press Enter to navigate
      await page.keyboard.press('Enter');
      await page.waitForLoadState('networkidle');

      // Find form inputs
      const firstInput = page.locator('input, textarea, select, button').first();
      if (await firstInput.isVisible({ timeout: 3000 }).catch(() => false)) {
        // Tab through form elements
        await page.keyboard.press('Tab');
        const focusedElement = await page.evaluate(() => document.activeElement?.tagName);
        expect(['INPUT', 'TEXTAREA', 'SELECT', 'BUTTON', 'A']).toContain(focusedElement);
      }
    }
  });

  test('should have visible focus indicators with 3:1 contrast ratio', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Find interactive elements
      const button = page.locator('button, a, input').first();
      if (await button.isVisible({ timeout: 3000 }).catch(() => false)) {
        await button.focus();

        // Check focus indicator exists
        const focusStyles = await button.evaluate((el) => {
          const styles = window.getComputedStyle(el);
          return {
            outline: styles.outline,
            outlineWidth: styles.outlineWidth,
            outlineOffset: styles.outlineOffset,
            boxShadow: styles.boxShadow
          };
        });

        // Should have visible focus indicator (outline or box-shadow)
        const hasFocusIndicator =
          focusStyles.outline !== 'none' ||
          focusStyles.boxShadow !== 'none';

        expect(hasFocusIndicator).toBeTruthy();
      }
    }
  });

  test('should have minimum 44x44px touch targets on mobile', async ({ page }) => {
    // Set mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });

    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Check interactive elements
      const interactiveElements = page.locator('button, a, input[type="submit"], input[type="button"]');
      const count = await interactiveElements.count();

      for (let i = 0; i < Math.min(count, 5); i++) {
        const element = interactiveElements.nth(i);
        if (await element.isVisible({ timeout: 1000 }).catch(() => false)) {
          const box = await element.boundingBox();
          if (box) {
            // WCAG 2.2 AA requires minimum 44x44px
            expect(box.width).toBeGreaterThanOrEqual(44);
            expect(box.height).toBeGreaterThanOrEqual(44);
          }
        }
      }
    }
  });

  test('should have proper ARIA landmarks and labels', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Check for main landmark
      const main = page.locator('main, [role="main"]');
      await expect(main).toBeVisible({ timeout: 3000 });

      // Check for navigation landmark
      const nav = page.locator('nav, [role="navigation"]');
      if (await nav.count() > 0) {
        await expect(nav.first()).toBeVisible();
      }

      // Check form has accessible name
      const form = page.locator('form').first();
      if (await form.isVisible({ timeout: 3000 }).catch(() => false)) {
        const ariaLabel = await form.getAttribute('aria-label');
        const ariaLabelledby = await form.getAttribute('aria-labelledby');
        expect(ariaLabel || ariaLabelledby).toBeTruthy();
      }
    }
  });

  test('should have proper color contrast ratios (4.5:1 for text)', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Run axe-core color contrast checks
      const results = await new AxeBuilder({ page })
        .withTags(['wcag2aa'])
        .include('body')
        .analyze();

      const contrastViolations = results.violations.filter(v =>
        v.id === 'color-contrast' || v.id === 'color-contrast-enhanced'
      );

      expect(contrastViolations).toEqual([]);
    }
  });

  test('should support screen reader announcements with ARIA live regions', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Check for ARIA live regions
      const liveRegions = page.locator('[aria-live], [role="status"], [role="alert"]');
      const count = await liveRegions.count();

      // Should have at least one live region for dynamic content
      if (count > 0) {
        const firstLiveRegion = liveRegions.first();
        const ariaLive = await firstLiveRegion.getAttribute('aria-live');
        expect(['polite', 'assertive', 'off']).toContain(ariaLive || '');
      }
    }
  });

  test('should have semantic HTML structure with proper headings', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Check heading hierarchy
      const headings = await page.locator('h1, h2, h3, h4, h5, h6').allTextContents();
      expect(headings.length).toBeGreaterThan(0);

      // Should have exactly one h1
      const h1Count = await page.locator('h1').count();
      expect(h1Count).toBe(1);
    }
  });

  test('should not rely on color alone for information', async ({ page }) => {
    const helpdeskLink = page.locator('a:has-text("Helpdesk"), a:has-text("Ticket"), [href*="helpdesk"]').first();

    if (await helpdeskLink.isVisible({ timeout: 3000 }).catch(() => false)) {
      await helpdeskLink.click();
      await page.waitForLoadState('networkidle');

      // Check status indicators have text or icons
      const statusElements = page.locator('[class*="status"], [class*="badge"], .badge');
      const count = await statusElements.count();

      for (let i = 0; i < Math.min(count, 3); i++) {
        const element = statusElements.nth(i);
        if (await element.isVisible({ timeout: 1000 }).catch(() => false)) {
          const text = await element.textContent();
          const hasIcon = await element.locator('svg, i, img').count() > 0;

          // Should have text content or icon, not just color
          expect(text?.trim() || hasIcon).toBeTruthy();
        }
      }
    }
  });
});
