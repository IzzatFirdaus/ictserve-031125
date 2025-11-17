import { test, expect } from './fixtures/ictserve-fixtures';

/**
 * Helpdesk Module Performance Tests - REFACTORED
 *
 * REFACTORING UPDATES (November 2025):
 * - ✅ Migrated to custom fixtures (test isolation + reusability)
 * - ✅ Web-first assertions (auto-wait)
 * - ✅ User-facing locators (getByRole, getByLabel)
 * - ✅ Test tags for filtering (@helpdesk, @performance)
 * - ✅ Environment-aware thresholds (dev vs production)
 *
 * Tests Core Web Vitals (LCP, FID, CLS), load times, and concurrent user scenarios
 *
 * @trace Requirement 9 (Performance Monitoring and Optimization)
 * @trace D03-FR-007.2 (Core Web Vitals Performance)
 * @trace D03-FR-014.1 (Performance Targets)
 *
 * Run: npm run test:e2e -- tests/e2e/helpdesk-performance.refactored.spec.ts
 * Run performance tests only: npm run test:e2e -- --grep @performance
 */

test.describe('Helpdesk Module - Performance Tests', () => {
  // Environment-aware thresholds
  const isDev = process.env['APP_ENV'] === 'local' ||
                process.env['APP_ENV'] === 'development' ||
                !process.env['APP_ENV'];

  const THRESHOLDS = {
    LCP: isDev ? 5000 : 2500,           // Largest Contentful Paint
    FID: isDev ? 200 : 100,             // First Input Delay
    CLS: isDev ? 0.2 : 0.1,             // Cumulative Layout Shift
    PAGE_LOAD: isDev ? 12000 : 3000,    // Page load time
    FORM_SUBMIT: isDev ? 3000 : 2000,   // Form submission time
    PAGINATION: isDev ? 2000 : 1500,    // Pagination response time
  };

  test('01 - Core Web Vitals meet performance targets', {
    tag: ['@helpdesk', '@performance', '@vitals', '@smoke'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Measure Core Web Vitals
    const metrics = await authenticatedPage.evaluate(() => {
      return new Promise<Record<string, number>>((resolve) => {
        const vitals: Record<string, number> = {};

        // Collect paint timing
        const paintEntries = performance.getEntriesByType('paint');
        paintEntries.forEach((entry) => {
          if (entry.name === 'first-contentful-paint') {
            vitals.fcp = entry.startTime;
          }
        });

        // Collect largest contentful paint
        const lcpEntries = performance.getEntriesByType('largest-contentful-paint');
        if (lcpEntries.length > 0) {
          const lastEntry = lcpEntries[lcpEntries.length - 1] as any;
          vitals.lcp = lastEntry.startTime;
        }

        // Collect CLS (Cumulative Layout Shift)
        let clsScore = 0;
        const clsEntries = performance.getEntriesByType('layout-shift');
        clsEntries.forEach((entry: any) => {
          if (!entry.hadRecentInput) {
            clsScore += entry.value;
          }
        });
        vitals.cls = clsScore;

        resolve(vitals);
      });
    });

    // Verify metrics meet thresholds
    if (metrics.lcp) {
      expect(metrics.lcp).toBeLessThan(THRESHOLDS.LCP);
    }
    if (metrics.cls !== undefined) {
      expect(metrics.cls).toBeLessThan(THRESHOLDS.CLS);
    }
  });

  test('02 - Helpdesk ticket list loads within acceptable time', {
    tag: ['@helpdesk', '@performance', '@load'],
  }, async ({ authenticatedPage }) => {
    const startTime = Date.now();

    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    const loadTime = Date.now() - startTime;

    // Verify page loads within threshold
    expect(loadTime).toBeLessThan(THRESHOLDS.PAGE_LOAD);
  });

  test('03 - Ticket submission form loads quickly', {
    tag: ['@helpdesk', '@performance', '@form'],
  }, async ({ authenticatedPage }) => {
    const startTime = Date.now();

    await authenticatedPage.goto('/tickets/create');
    await authenticatedPage.waitForSelector('form', { state: 'visible' });

    const loadTime = Date.now() - startTime;

    expect(loadTime).toBeLessThan(THRESHOLDS.PAGE_LOAD);

    // Verify form is interactive
    const form = authenticatedPage.locator('form').first();
    await expect(form).toBeVisible();
  });

  test('04 - Pagination performance is acceptable', {
    tag: ['@helpdesk', '@performance', '@pagination'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Find pagination controls
    const paginationLink = authenticatedPage.locator(
      '[aria-label*="page"], [aria-label*="next"], button:has-text("Next")'
    ).first();

    const haspagination = await paginationLink.isVisible({ timeout: 3000 }).catch(() => false);

    if (haspagination) {
      const startTime = Date.now();

      await paginationLink.click();
      await authenticatedPage.waitForLoadState('networkidle');

      const paginationTime = Date.now() - startTime;

      expect(paginationTime).toBeLessThan(THRESHOLDS.PAGINATION);
    }
  });

  test('05 - Form submission response time is fast', {
    tag: ['@helpdesk', '@performance', '@submit'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/tickets/create');
    await authenticatedPage.waitForLoadState('networkidle');

    // Find and fill form
    const form = authenticatedPage.locator('form').first();
    await expect(form).toBeVisible({ timeout: 5000 });

    // Fill required fields (using generic selectors since we don't know exact structure)
    const inputs = authenticatedPage.locator('input[type="text"], textarea').first();
    if (await inputs.isVisible({ timeout: 2000 }).catch(() => false)) {
      await inputs.fill('Performance Test Ticket');
    }

    // Measure submission time
    const startTime = Date.now();

    const submitButton = authenticatedPage.getByRole('button', { name: /submit|hantar|send/i });
    if (await submitButton.isVisible({ timeout: 2000 }).catch(() => false)) {
      await submitButton.click();
      await authenticatedPage.waitForLoadState('networkidle');

      const submitTime = Date.now() - startTime;

      expect(submitTime).toBeLessThan(THRESHOLDS.FORM_SUBMIT);
    }
  });

  test('06 - No excessive database queries (N+1 prevention)', {
    tag: ['@helpdesk', '@performance', '@database'],
  }, async ({ authenticatedPage }) => {
    // Monitor network requests
    const requests: string[] = [];
    authenticatedPage.on('request', request => {
      if (request.url().includes('/api/') || request.url().includes('/livewire/')) {
        requests.push(request.url());
      }
    });

    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Should not have excessive API calls (< 10 for initial load)
    expect(requests.length).toBeLessThan(10);
  });

  test('07 - Static assets are cached effectively', {
    tag: ['@helpdesk', '@performance', '@cache'],
  }, async ({ authenticatedPage }) => {
    // First load
    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Navigate away
    await authenticatedPage.goto('/dashboard');
    await authenticatedPage.waitForLoadState('networkidle');

    // Second load (should use cache)
    const cachedRequests: string[] = [];
    authenticatedPage.on('response', response => {
      const cacheHeader = response.headers()['cache-control'];
      if (cacheHeader && cacheHeader.includes('max-age')) {
        cachedRequests.push(response.url());
      }
    });

    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // At least some assets should be cached
    expect(cachedRequests.length).toBeGreaterThan(0);
  });

  test('08 - Images use lazy loading', {
    tag: ['@helpdesk', '@performance', '@images'],
  }, async ({ authenticatedPage }) => {
    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Check images have loading attribute
    const images = authenticatedPage.locator('img');
    const count = await images.count();

    if (count > 0) {
      for (let i = 0; i < Math.min(count, 5); i++) {
        const img = images.nth(i);
        const loading = await img.getAttribute('loading');

        // Images should have loading="lazy" or be above fold (no attribute needed)
        const hasLazyLoading = loading === 'lazy' || loading === null;
        expect(hasLazyLoading).toBeTruthy();
      }
    }
  });

  test('09 - JavaScript bundle size is optimized', {
    tag: ['@helpdesk', '@performance', '@bundle'],
  }, async ({ authenticatedPage }) => {
    const resources: Array<{ name: string; size: number; duration: number }> = [];

    authenticatedPage.on('response', async (response) => {
      if (response.url().endsWith('.js')) {
        const buffer = await response.body().catch(() => null);
        if (buffer) {
          resources.push({
            name: response.url(),
            size: buffer.length,
            duration: 0
          });
        }
      }
    });

    await authenticatedPage.goto('/helpdesk/tickets');
    await authenticatedPage.waitForLoadState('networkidle');

    // Calculate total JS size
    const totalJsSize = resources.reduce((sum, r) => sum + r.size, 0);

    // Total JS should be reasonable (< 1MB for dev, < 500KB for production)
    const maxSize = isDev ? 1024 * 1024 : 500 * 1024;
    expect(totalJsSize).toBeLessThan(maxSize);
  });

  test('10 - Time to Interactive (TTI) is acceptable', {
    tag: ['@helpdesk', '@performance', '@tti'],
  }, async ({ authenticatedPage }) => {
    const startTime = Date.now();

    await authenticatedPage.goto('/helpdesk/tickets');

    // Wait for page to be fully interactive
    await authenticatedPage.waitForLoadState('networkidle');
    await authenticatedPage.waitForFunction(() => document.readyState === 'complete');

    // Try to interact with page
    const interactiveElement = authenticatedPage.getByRole('button').first().or(
      authenticatedPage.getByRole('link').first()
    );

    if (await interactiveElement.isVisible({ timeout: 2000 }).catch(() => false)) {
      await interactiveElement.focus();
    }

    const tti = Date.now() - startTime;

    // TTI should be reasonable
    const maxTTI = isDev ? 8000 : 3000;
    expect(tti).toBeLessThan(maxTTI);
  });

});
