import type { Page } from '@playwright/test';
import { expect, test } from './fixtures/ictserve-fixtures';

type Diagnostics = {
  consoleErrors: string[];
  pageErrors: string[];
  failedRequests: Array<{ url: string; status?: number; message?: string }>;
};

const BENIGN_CONSOLE_PATTERNS = [
  /favicon\.ico/i,
  /chrome-extension/i,
  /DevTools failed to load/i,
];

const FILAMENT_RESOURCE_ROUTES: Array<{
  name: string;
  url: string;
  heading: RegExp;
}> = [
  {
    name: 'Helpdesk Tickets',
    url: '/admin/helpdesk/helpdesk-tickets',
    heading: /helpdesk tickets/i,
  },
  {
    name: 'Loan Applications',
    url: '/admin/loans/loan-applications',
    heading: /loan applications/i,
  },
  {
    name: 'Asset Inventory',
    url: '/admin/assets',
    heading: /assets/i,
  },
];

function isBenignConsoleMessage(message: string): boolean {
  return BENIGN_CONSOLE_PATTERNS.some(pattern => pattern.test(message));
}

async function captureDiagnostics(page: Page, action: () => Promise<void>): Promise<Diagnostics> {
  const consoleErrors: string[] = [];
  const pageErrors: string[] = [];
  const failedRequests: Array<{ url: string; status?: number; message?: string }> = [];

  const consoleListener = (msg: any) => {
    if (msg.type() !== 'error') {
      return;
    }

    const text = msg.text();
    if (!isBenignConsoleMessage(text)) {
      consoleErrors.push(text);
    }
  };

  const pageErrorListener = (error: Error) => {
    pageErrors.push(error?.message ?? 'Unknown error');
  };

  const requestFailedListener = (request: any) => {
    failedRequests.push({
      url: request.url(),
      message: request.failure()?.errorText,
    });
  };

  const responseListener = (response: any) => {
    const status = response.status();

    if (status >= 500) {
      failedRequests.push({
        url: response.url(),
        status,
      });
    }
  };

  page.on('console', consoleListener);
  page.on('pageerror', pageErrorListener);
  page.on('requestfailed', requestFailedListener);
  page.on('response', responseListener);

  await action();

  page.off('console', consoleListener);
  page.off('pageerror', pageErrorListener);
  page.off('requestfailed', requestFailedListener);
  page.off('response', responseListener);

  return {
    consoleErrors,
    pageErrors,
    failedRequests,
  };
}

async function gotoWithDiagnostics(page: Page, url: string): Promise<Diagnostics> {
  return captureDiagnostics(page, async () => {
    await page.goto(url);
    await page.waitForLoadState('networkidle');
  });
}

test.describe('Filament Components Debug Suite', { tag: ['@filament', '@debug'] }, () => {
  test('Dashboard widgets render without console errors', async ({ adminPage }) => {
    const diagnostics = await gotoWithDiagnostics(adminPage, '/admin');

    expect(diagnostics.consoleErrors).toEqual([]);
    expect(diagnostics.pageErrors).toEqual([]);
    expect(diagnostics.failedRequests).toEqual([]);

    await expect(adminPage.getByRole('heading', { name: /asset availability calendar/i })).toBeVisible();
    await expect(adminPage.locator('[data-testid="asset-calendar-grid"]')).toBeVisible();

    await expect(adminPage.getByRole('heading', { name: /critical alerts/i })).toBeVisible();
    await expect(adminPage.locator('[data-testid="critical-alerts-widget"]')).toBeVisible();

    await expect(adminPage.getByRole('heading', { name: /asset status distribution/i })).toBeVisible();
    await expect(adminPage.getByRole('heading', { name: /loan applications trend/i })).toBeVisible();
  });

  for (const resource of FILAMENT_RESOURCE_ROUTES) {
    test(`${resource.name} resource loads without failures`, async ({ adminPage }) => {
      const diagnostics = await gotoWithDiagnostics(adminPage, resource.url);

      expect(diagnostics.consoleErrors).toEqual([]);
      expect(diagnostics.pageErrors).toEqual([]);
      expect(diagnostics.failedRequests).toEqual([]);

      await expect(adminPage.getByRole('heading', { name: resource.heading })).toBeVisible();
      await expect(adminPage.getByRole('table')).toBeVisible();
    });
  }

  test('Asset availability legend exposes all statuses', async ({ adminPage }) => {
    await adminPage.goto('/admin');
    await adminPage.waitForLoadState('networkidle');

    const statusMetadata = await adminPage
      .locator('[data-testid="asset-legend-item"]')
      .evaluateAll(elements =>
        elements.map(element => ({
          status: element.getAttribute('data-status'),
          label: element.textContent?.trim() ?? '',
        })),
      );

    const expectedStatuses = ['available', 'reserved', 'maintenance', 'damaged', 'retired'];
    const statusSet = new Set(statusMetadata.map(entry => entry.status));

    expectedStatuses.forEach(status => {
      expect(statusSet.has(status)).toBeTruthy();
    });

    const expectedLabels = ['Available', 'Reserved/Loaned', 'Maintenance', 'Damaged', 'Retired'];
    expectedLabels.forEach(label => {
      const match = statusMetadata.find(entry => entry.label.includes(label));
      expect(match).toBeDefined();
    });

    await expect(adminPage.locator('[data-testid="asset-calendar-grid"]')).toBeVisible();
  });

  test('Critical alerts widget surfaces empty state or alert actions', async ({ adminPage }) => {
    await adminPage.goto('/admin');
    await adminPage.waitForLoadState('networkidle');

    const widget = adminPage.locator('[data-testid="critical-alerts-widget"]');
    await expect(widget).toBeVisible();

    const emptyStateVisible = await widget
      .locator('[data-testid="critical-alerts-empty"]')
      .isVisible()
      .catch(() => false);

    const alertItems = widget.locator('[data-testid="critical-alert-item"]');
    const alertCount = await alertItems.count();

    expect(emptyStateVisible || alertCount > 0).toBeTruthy();

    if (alertCount > 0) {
      const firstAlertHref = await alertItems.first().getAttribute('href');
      expect(firstAlertHref).toMatch(/\/admin\/(helpdesk|loans)/);
    }
  });
});
