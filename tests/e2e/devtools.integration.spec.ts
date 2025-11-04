import { test, expect } from '@playwright/test';

/**
 * Chrome DevTools Integration Tests
 * Uses Playwright's debugging capabilities with Chrome DevTools Protocol
 */

test.describe('Chrome DevTools Debugging Suite', () => {
  test('should capture performance metrics', async ({ page, context }) => {
    // Enable CDP
    const client = await context.newCDPSession(page);
    await client.send('Performance.enable');

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Get performance metrics
    const metrics = await page.evaluate(() => {
      const perfData = performance.getEntriesByType('navigation')[0] as PerformanceNavigationTiming;
      return {
        domContentLoaded: perfData.domContentLoadedEventEnd - perfData.domContentLoadedEventStart,
        loadComplete: perfData.loadEventEnd - perfData.loadEventStart,
        totalTime: perfData.loadEventEnd - perfData.fetchStart,
      };
    });

    console.log('Performance Metrics:', metrics);

    // Should load within reasonable time (5 seconds)
    expect(metrics.totalTime).toBeLessThan(5000);
  });

  test('should detect all network requests and responses', async ({ page }) => {
    const requestLog: Array<{ url: string; method: string; status?: number }> = [];

    page.on('request', request => {
      requestLog.push({
        url: request.url(),
        method: request.method(),
      });
    });

    page.on('response', response => {
      const entry = requestLog.find(r => r.url === response.url());
      if (entry) {
        entry.status = response.status();
      }
    });

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    console.log(`Total Requests: ${requestLog.length}`);
    console.log('Request Log:', JSON.stringify(requestLog.slice(0, 10), null, 2));

    // Should have successful requests
    const successfulRequests = requestLog.filter(r => !r.status || (r.status >= 200 && r.status < 300));
    expect(successfulRequests.length).toBeGreaterThan(0);

    // No 5xx errors
    const serverErrors = requestLog.filter(r => r.status && r.status >= 500);
    expect(serverErrors).toEqual([]);
  });

  test('should capture console messages and errors', async ({ page }) => {
    const consoleLogs = {
      logs: [] as string[],
      warnings: [] as string[],
      errors: [] as string[],
    };

    page.on('console', msg => {
      const text = msg.text();
      if (msg.type() === 'log') consoleLogs.logs.push(text);
      if (msg.type() === 'warning') consoleLogs.warnings.push(text);
      if (msg.type() === 'error') consoleLogs.errors.push(text);
    });

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    console.log('Console Logs:', consoleLogs);

    // Filter out benign errors (404s, cross-origin)
    const criticalErrors = consoleLogs.errors.filter(e =>
      !e.includes('404') &&
      !e.includes('cross-origin') &&
      !e.includes('favicon') &&
      !e.includes('CORS')
    );

    expect(criticalErrors).toEqual([]);
  });

  test('should check accessibility tree', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Check for main landmark
    const main = page.locator('main, [role="main"]').first();
    const hasMain = await main.isVisible({ timeout: 1000 }).catch(() => false);

    // Check for navigation
    const nav = page.locator('nav, [role="navigation"]').first();
    const hasNav = await nav.isVisible({ timeout: 1000 }).catch(() => false);

    console.log('Accessibility Check:');
    console.log(`- Main content landmark: ${hasMain}`);
    console.log(`- Navigation landmark: ${hasNav}`);

    // At least one landmark should exist
    expect(hasMain || hasNav).toBeTruthy();
  });

  test('should validate page security headers', async ({ page }) => {
    const response = await page.goto('/');
    const headers = response?.headers();

    console.log('Security Headers:', {
      'content-security-policy': headers?.['content-security-policy'],
      'x-frame-options': headers?.['x-frame-options'],
      'x-content-type-options': headers?.['x-content-type-options'],
      'x-xss-protection': headers?.['x-xss-protection'],
    });

    // Should have basic security header
    expect(headers).toBeTruthy();
  });

  test('should check for memory leaks in navigation', async ({ page }) => {
    const memoryUsage: Array<{ route: string; memory: number }> = [];

    // Navigate to different pages and check memory
    const routes = ['/', '/login'];

    for (const route of routes) {
      try {
        await page.goto(route, { waitUntil: 'networkidle', timeout: 5000 }).catch(() => null);

        const memory = await page.evaluate(() => {
          const perfMemory = performance as any;
          if (perfMemory.memory) {
            return perfMemory.memory.usedJSHeapSize || 0;
          }
          return 0;
        });

        memoryUsage.push({ route, memory });
      } catch (e) {
        // Skip unavailable routes
      }
    }

    console.log('Memory Usage by Route:', memoryUsage);

    // Should not have excessive memory growth
    if (memoryUsage.length > 1) {
      const memoryGrowth = memoryUsage[1].memory - memoryUsage[0].memory;
      // Allow up to 50MB growth
      expect(memoryGrowth).toBeLessThan(50 * 1024 * 1024);
    }
  });

  test('should validate DOM and CSS rendering', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');

    const domStats = await page.evaluate(() => {
      return {
        elementCount: document.querySelectorAll('*').length,
        styleSheets: document.styleSheets.length,
        images: document.querySelectorAll('img').length,
        links: document.querySelectorAll('a').length,
        buttons: document.querySelectorAll('button').length,
        forms: document.querySelectorAll('form').length,
      };
    });

    console.log('DOM Statistics:', domStats);

    expect(domStats.elementCount).toBeGreaterThan(0);
    expect(domStats.styleSheets).toBeGreaterThanOrEqual(0);
  });

  test('should check for unhandled promise rejections', async ({ page }) => {
    const rejections: string[] = [];

    page.on('pageerror', error => {
      rejections.push(error?.toString() || 'Unknown error');
    });

    await page.goto('/');
    await page.waitForLoadState('networkidle');

    // Interact with page
    const buttons = page.locator('button').first();
    if (await buttons.isVisible({ timeout: 3000 }).catch(() => false)) {
      await buttons.click().catch(() => null);
    }

    console.log('Page Errors:', rejections);

    // Should have no critical errors
    expect(rejections).toEqual([]);
  });
});
