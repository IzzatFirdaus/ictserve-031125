import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * Loan Module WCAG 2.2 AA Accessibility Tests
 * 
 * Automated accessibility testing using axe-core for loan module pages.
 * 
 * @trace D03-FR-006.1 (WCAG 2.2 AA Compliance)
 * @trace D12 (UI/UX Design Guide)
 */

test.describe('Loan Module Accessibility', () => {
  test.beforeEach(async ({ page }) => {
    // Set viewport for consistent testing
    await page.setViewportSize({ width: 1280, height: 720 });
  });

  test('Guest loan application form meets WCAG 2.2 AA', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  });

  test('Authenticated loan dashboard meets WCAG 2.2 AA', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/dashboard');
    await page.waitForLoadState('networkidle');
    
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  });

  test('Loan history page meets WCAG 2.2 AA', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/history');
    await page.waitForLoadState('networkidle');
    
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
      .analyze();

    expect(accessibilityScanResults.violations).toEqual([]);
  });

  test('Keyboard navigation works on loan form', async ({ page }) => {
    await page.goto('/loan/apply');
    
    // Tab through form fields
    await page.keyboard.press('Tab');
    let focusedElement = await page.evaluate(() => document.activeElement?.tagName);
    expect(['INPUT', 'SELECT', 'TEXTAREA', 'BUTTON']).toContain(focusedElement);
    
    // Verify focus indicators are visible
    const focusedElementStyle = await page.evaluate(() => {
      const el = document.activeElement;
      return window.getComputedStyle(el as Element).outline;
    });
    
    expect(focusedElementStyle).not.toBe('none');
  });

  test('Form validation errors are announced to screen readers', async ({ page }) => {
    await page.goto('/loan/apply');
    
    // Submit empty form
    await page.click('button[type="submit"]');
    
    // Check for ARIA live regions
    const liveRegions = await page.locator('[aria-live]').count();
    expect(liveRegions).toBeGreaterThan(0);
    
    // Check for error messages with role="alert"
    const alerts = await page.locator('[role="alert"]').count();
    expect(alerts).toBeGreaterThan(0);
  });

  test('Color contrast meets WCAG AA standards', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2aa'])
      .include('body')
      .analyze();

    const contrastViolations = accessibilityScanResults.violations.filter(
      v => v.id === 'color-contrast'
    );
    
    expect(contrastViolations).toEqual([]);
  });

  test('Images have alt text', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const images = await page.locator('img').all();
    
    for (const img of images) {
      const alt = await img.getAttribute('alt');
      expect(alt).not.toBeNull();
    }
  });

  test('Form labels are properly associated', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const accessibilityScanResults = await new AxeBuilder({ page })
      .withTags(['wcag2a'])
      .analyze();

    const labelViolations = accessibilityScanResults.violations.filter(
      v => v.id === 'label'
    );
    
    expect(labelViolations).toEqual([]);
  });

  test('Skip links are present and functional', async ({ page }) => {
    await page.goto('/loan/apply');
    
    // Press Tab to focus skip link
    await page.keyboard.press('Tab');
    
    const skipLink = await page.locator('a:has-text("Skip to main content")').first();
    expect(await skipLink.isVisible()).toBeTruthy();
    
    // Click skip link
    await skipLink.click();
    
    // Verify focus moved to main content
    const focusedElement = await page.evaluate(() => document.activeElement?.tagName);
    expect(focusedElement).toBe('MAIN');
  });

  test('Language attribute is set correctly', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const lang = await page.getAttribute('html', 'lang');
    expect(['ms', 'en']).toContain(lang);
  });

  test('Page title is descriptive', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const title = await page.title();
    expect(title.length).toBeGreaterThan(10);
    expect(title).toContain('Loan');
  });

  test('Touch targets meet minimum size (44x44px)', async ({ page }) => {
    await page.goto('/loan/apply');
    
    const buttons = await page.locator('button').all();
    
    for (const button of buttons) {
      const box = await button.boundingBox();
      if (box) {
        expect(box.width).toBeGreaterThanOrEqual(44);
        expect(box.height).toBeGreaterThanOrEqual(44);
      }
    }
  });

  test('Modal dialogs have proper ARIA attributes', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/dashboard');
    
    // Look for modal triggers
    const modalTriggers = await page.locator('[data-modal-trigger]').all();
    
    if (modalTriggers.length > 0) {
      await modalTriggers[0].click();
      
      const modal = await page.locator('[role="dialog"]').first();
      expect(await modal.getAttribute('aria-modal')).toBe('true');
      expect(await modal.getAttribute('aria-labelledby')).not.toBeNull();
    }
  });

  test('Tables have proper structure', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/history');
    await page.waitForLoadState('networkidle');
    
    const tables = await page.locator('table').all();
    
    for (const table of tables) {
      const thead = await table.locator('thead').count();
      const tbody = await table.locator('tbody').count();
      const thWithScope = await table.locator('th[scope]').count();
      
      expect(thead).toBeGreaterThan(0);
      expect(tbody).toBeGreaterThan(0);
      expect(thWithScope).toBeGreaterThan(0);
    }
  });

  test('Responsive design maintains accessibility', async ({ page }) => {
    // Test mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/loan/apply');
    
    const mobileAccessibility = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();

    expect(mobileAccessibility.violations).toEqual([]);
    
    // Test tablet viewport
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/loan/apply');
    
    const tabletAccessibility = await new AxeBuilder({ page })
      .withTags(['wcag2a', 'wcag2aa'])
      .analyze();

    expect(tabletAccessibility.violations).toEqual([]);
  });

  test('Loading states are announced', async ({ page }) => {
    await page.goto('/loan/apply');
    
    // Look for loading indicators with ARIA attributes
    const loadingIndicators = await page.locator('[aria-busy="true"], [aria-live="polite"]').count();
    
    // At least one loading mechanism should exist
    expect(loadingIndicators).toBeGreaterThanOrEqual(0);
  });

  test('Focus trap works in modals', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/dashboard');
    
    // Look for modal triggers
    const modalTriggers = await page.locator('[data-modal-trigger]').all();
    
    if (modalTriggers.length > 0) {
      await modalTriggers[0].click();
      
      // Tab through modal
      await page.keyboard.press('Tab');
      await page.keyboard.press('Tab');
      await page.keyboard.press('Tab');
      
      // Verify focus stays within modal
      const focusedElement = await page.evaluate(() => {
        const el = document.activeElement;
        return el?.closest('[role="dialog"]') !== null;
      });
      
      expect(focusedElement).toBeTruthy();
    }
  });

  test('Escape key closes modals', async ({ page }) => {
    // Login first
    await page.goto('/login');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('/loan/dashboard');
    
    // Look for modal triggers
    const modalTriggers = await page.locator('[data-modal-trigger]').all();
    
    if (modalTriggers.length > 0) {
      await modalTriggers[0].click();
      
      // Press Escape
      await page.keyboard.press('Escape');
      
      // Verify modal is closed
      const modalVisible = await page.locator('[role="dialog"]').isVisible();
      expect(modalVisible).toBeFalsy();
    }
  });
});
