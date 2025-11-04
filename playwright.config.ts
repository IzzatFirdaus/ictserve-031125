import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright Configuration for ICTServe E2E Testing
 * Tests both Helpdesk Ticket and Asset Loan modules
 */
export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  forbidOnly: process.env['CI'] === 'true',
  retries: process.env['CI'] ? 2 : 0,
  workers: process.env['CI'] ? 1 : 1,
  reporter: [
    ['html'],
    ['json', { outputFile: 'test-results/results.json' }],
    ['list'],
  ],
  use: {
    baseURL: 'http://localhost:8000',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
  },
  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
        launchArgs: ['--disable-dev-shm-usage'],
      },
    },
  ],
  webServer: {
    command: 'php artisan serve',
    url: 'http://localhost:8000',
    reuseExistingServer: !process.env['CI'],
    timeout: 120000,
  },
});
