import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright Configuration for ICTServe E2E Testing
 * Best practices: parallelism optimization, trace debugging, comprehensive reporting
 *
 * Trace: Research findings per Playwright v1.56.1 official docs
 * - trace: 'on-first-retry' (recommended for CI - lightweight debugging)
 * - fullyParallel: true (parallel execution within files for speed)
 * - workers: adjusted per environment (CI vs local development)
 * - retries: 2 on CI (resilience to temporary failures)
 */
export default defineConfig({
  testDir: './tests/e2e',
  /* Run tests in parallel within files for faster execution */
  fullyParallel: true,
  /* Fail the build on CI if you accidentally left test.only in the source code */
  forbidOnly: process.env['CI'] === 'true',
  /* Retry on CI only to reduce infrastructure costs */
  retries: process.env['CI'] ? 2 : 0,
  /* Adjust workers: 1 per worker on CI for isolation, local can use multiple */
  workers: process.env['CI'] ? 2 : 4,
  /* Reporters: HTML (primary), JSON (CI), and list (terminal) */
  reporter: [
    ['html'],
    ['json', { outputFile: 'test-results/results.json' }],
    ['list'],
  ],
  use: {
    /* Base URL for all page.goto() calls */
    baseURL: 'http://localhost:8000',
    /* Trace viewer: captures actions, DOM snapshots, network for failed tests */
    trace: 'on-first-retry',
    /* Screenshot only on failure to save space */
    screenshot: 'only-on-failure',
    /* Video recording for visual debugging */
    video: 'retain-on-failure',
    /* Action timeout: time to perform click, fill, etc. */
    actionTimeout: 30000,
    /* Navigation timeout: time for page loads */
    navigationTimeout: 30000,
  },

  /* Global timeout for all tests (5 minutes for comprehensive flows) */
  timeout: 300000,

  /* Expect timeout: time for assertions to pass (auto-wait) */
  expect: {
    timeout: 5000,
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    /* Optional: add Firefox and WebKit for cross-browser testing */
    // {
    //   name: 'firefox',
    //   use: { ...devices['Desktop Firefox'] },
    // },
    // {
    //   name: 'webkit',
    //   use: { ...devices['Desktop Safari'] },
    // },
  ],

  /* Web server: Manual start preferred - run "php artisan serve" before tests */
  /* Uncomment to auto-start server during test runs */
  // webServer: {
  //   command: 'php artisan serve',
  //   url: 'http://localhost:8000',
  //   reuseExistingServer: !process.env['CI'],
  //   timeout: 120000,
  // },
});
