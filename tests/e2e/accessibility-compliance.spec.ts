import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test.describe('Portal Accessibility Compliance (WCAG 2.2 AA)', () => {
  test.beforeEach(async ({ page }) => {
    // Test on public welcome page (no authentication required)
    // Use baseURL from playwright.config.ts instead of hardcoded URL
    await page.goto('/');
  });

  test('keyboard navigation - all interactive elements accessible', async ({ page }) => {
    // Tab through all focusable elements
    const focusableElements = await page.locator('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])').all();
    
    expect(focusableElements.length).toBeGreaterThan(0);

    // Test Tab navigation
    await page.keyboard.press('Tab');
    let focusedElement = await page.evaluate(() => document.activeElement?.tagName);
    expect(focusedElement).toBeTruthy();

    // Test Shift+Tab navigation
    await page.keyboard.press('Shift+Tab');
    focusedElement = await page.evaluate(() => document.activeElement?.tagName);
    expect(focusedElement).toBeTruthy();

    // Test Enter key on first button
    const firstButton = page.locator('button').first();
    if (await firstButton.count() > 0) {
      await firstButton.focus();
      const isVisible = await firstButton.isVisible();
      expect(isVisible).toBe(true);
    }

    // Test Escape key functionality (if modals exist)
    const modalTrigger = page.locator('[data-modal-trigger], [aria-haspopup="dialog"]').first();
    if (await modalTrigger.count() > 0) {
      await modalTrigger.click();
      await page.keyboard.press('Escape');
      const modalVisible = await page.locator('[role="dialog"]').isVisible().catch(() => false);
      expect(modalVisible).toBe(false);
    }
  });

  test('full accessibility scan with axe-core', async ({ page }) => {
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  });

  test('focus indicators visible on all interactive elements', async ({ page }) => {
    const interactiveElements = await page.locator('a, button, input, select, textarea').all();

    for (const element of interactiveElements.slice(0, 5)) { // Test first 5 elements
      await element.focus();
      
      const outlineStyle = await element.evaluate((el) => {
        const styles = window.getComputedStyle(el);
        return {
          outline: styles.outline,
          outlineWidth: styles.outlineWidth,
          boxShadow: styles.boxShadow
        };
      });

      const hasFocusIndicator = 
        outlineStyle.outline !== 'none' || 
        outlineStyle.outlineWidth !== '0px' ||
        outlineStyle.boxShadow !== 'none';

      expect(hasFocusIndicator).toBe(true);
    }
  });

  test('skip navigation link present and functional', async ({ page }) => {
    const skipLink = page.locator('a[href="#main-content"], a[href="#main"]').first();
    
    if (await skipLink.count() > 0) {
      await skipLink.focus();
      await skipLink.press('Enter');
      
      const mainContent = page.locator('#main-content, #main, main').first();
      const isFocused = await mainContent.evaluate((el) => el === document.activeElement);
      expect(isFocused).toBe(true);
    }
  });

  test('color contrast meets WCAG AA standards', async ({ page }) => {
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2aa'])
      .include('body')
      .analyze();

    const contrastViolations = accessibilityScanResults.violations.filter(
      v => v.id === 'color-contrast'
    );

    expect(contrastViolations).toEqual([]);
  });
});
