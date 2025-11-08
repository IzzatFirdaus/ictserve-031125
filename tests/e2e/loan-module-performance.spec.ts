import { test, expect } from '@playwright/test';

/**
 * Loan Module Performance E2E Tests
 * 
 * @trace D03-FR-007.2 (Core Web Vitals Performance)
 * @trace D03-FR-014.1 (Performance Targets)
 */

test.describe('Loan Module Performance Tests', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/loans');
  });

  test('measures Core Web Vitals for loan dashboard', async ({ page }) => {
    const metrics = await page.evaluate(() => {
      return new Promise((resolve) => {
        new PerformanceObserver((list) => {
          const entries = list.getEntries();
          const vitals: Record<string, number> = {};
          
          entries.forEach((entry: any) => {
            if (entry.name === 'first-contentful-paint') {
              vitals.FCP = entry.startTime;
            }
            if (entry.entryType === 'largest-contentful-paint') {
              vitals.LCP = entry.startTime;
            }
            if (entry.name === 'first-input') {
              vitals.FID = entry.processingStart - entry.startTime;
            }
          });
          
          resolve(vitals);
        }).observe({ entryTypes: ['paint', 'largest-contentful-paint', 'first-input'] });
        
        setTimeout(() => resolve({}), 5000);
      });
    });

    // Core Web Vitals targets
    if (metrics.LCP) {
      expect(metrics.LCP).toBeLessThan(2500); // LCP < 2.5s
    }
    if (metrics.FCP) {
      expect(metrics.FCP).toBeLessThan(1500); // FCP < 1.5s
    }
    if (metrics.FID) {
      expect(metrics.FID).toBeLessThan(100); // FID < 100ms
    }
  });

  test('loan application form loads quickly', async ({ page }) => {
    const startTime = Date.now();
    
    await page.goto('/loans/apply');
    await page.waitForSelector('form', { state: 'visible' });
    
    const loadTime = Date.now() - startTime;
    
    expect(loadTime).toBeLessThan(2000); // < 2 seconds
  });

  test('asset availability check is responsive', async ({ page }) => {
    await page.goto('/loans/apply');
    
    const startTime = Date.now();
    
    await page.click('[data-testid="check-availability"]');
    await page.waitForSelector('[data-testid="available-assets"]', { state: 'visible' });
    
    const responseTime = Date.now() - startTime;
    
    expect(responseTime).toBeLessThan(1000); // < 1 second
  });

  test('loan history pagination is smooth', async ({ page }) => {
    await page.goto('/loans/history');
    
    const startTime = Date.now();
    
    await page.click('[aria-label="Next page"]');
    await page.waitForLoadState('networkidle');
    
    const paginationTime = Date.now() - startTime;
    
    expect(paginationTime).toBeLessThan(1500); // < 1.5 seconds
  });

  test('search functionality is fast', async ({ page }) => {
    await page.goto('/loans');
    
    const startTime = Date.now();
    
    await page.fill('input[name="search"]', 'laptop');
    await page.waitForTimeout(500); // Debounce
    await page.waitForLoadState('networkidle');
    
    const searchTime = Date.now() - startTime;
    
    expect(searchTime).toBeLessThan(2000); // < 2 seconds
  });

  test('dashboard widgets load progressively', async ({ page }) => {
    await page.goto('/loans/dashboard');
    
    // Check that skeleton loaders appear first
    const skeletons = await page.locator('[data-testid="skeleton-loader"]').count();
    expect(skeletons).toBeGreaterThan(0);
    
    // Wait for actual content
    await page.waitForSelector('[data-testid="dashboard-stats"]', { state: 'visible', timeout: 3000 });
    
    // Verify no skeletons remain
    const remainingSkeletons = await page.locator('[data-testid="skeleton-loader"]').count();
    expect(remainingSkeletons).toBe(0);
  });

  test('measures Time to Interactive (TTI)', async ({ page }) => {
    const startTime = Date.now();
    
    await page.goto('/loans/apply');
    
    // Wait for page to be fully interactive
    await page.waitForLoadState('networkidle');
    await page.waitForFunction(() => document.readyState === 'complete');
    
    // Try to interact with form
    await page.fill('input[name="applicant_name"]', 'Test');
    
    const tti = Date.now() - startTime;
    
    expect(tti).toBeLessThan(3000); // TTI < 3 seconds
  });

  test('checks bundle size impact', async ({ page }) => {
    const response = await page.goto('/loans');
    
    const resources = await page.evaluate(() => {
      return performance.getEntriesByType('resource').map((r: any) => ({
        name: r.name,
        size: r.transferSize,
        duration: r.duration
      }));
    });
    
    const jsResources = resources.filter(r => r.name.endsWith('.js'));
    const totalJsSize = jsResources.reduce((sum, r) => sum + r.size, 0);
    
    // Total JS should be under 500KB
    expect(totalJsSize).toBeLessThan(500 * 1024);
  });
});
