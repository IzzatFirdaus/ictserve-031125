/**
 * Percy Snapshot Validation Tests
 * ICTServe v3.6.1 Visual Testing Examples
 * 
 * This test suite provides comprehensive examples of Percy snapshot usage
 * for visual regression testing in ICTServe v3.6.1.
 * 
 * Examples Include:
 * - Basic snapshot capture
 * - Responsive breakpoint testing
 * - Component state variations
 * - Dynamic content handling
 * - Livewire component snapshots
 * - Filament admin panel snapshots
 * - Accessibility state snapshots
 * - Error state snapshots
 * - Loading state snapshots
 * 
 * @see docs/Percy-Playwright-Integration.md
 * @see percy.config.js
 */

import { test, expect } from '@playwright/test';
import percySnapshot from '@percy/playwright';

/**
 * Configuration
 */
const PERCY_ENABLED = process.env.PERCY_ENABLED === 'true';

/**
 * Helper: Wait for Livewire
 */
async function waitForLivewire(page) {
  await page.waitForSelector('[wire\\:id]', { timeout: 10000 });
  await page.waitForTimeout(500);
}

/**
 * Helper: Capture snapshot if Percy is enabled
 */
async function snapshot(page, name, options = {}) {
  if (!PERCY_ENABLED) {
    console.log(`Percy disabled - skipping: ${name}`);
    return;
  }
  await percySnapshot(page, name, options);
}

test.describe('Percy Snapshot Validation - Basic Examples', () => {
  
  test('Example 1: Simple page snapshot', async ({ page }) => {
    // Navigate to page
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    
    // Capture snapshot with default settings
    await snapshot(page, 'Example - Simple Page Snapshot');
  });

  test('Example 2: Multiple viewport widths', async ({ page }) => {
    await page.goto('/');
    await page.waitForLoadState('networkidle');
    
    // Capture across multiple breakpoints
    await snapshot(page, 'Example - Multiple Viewports', {
      widths: [375, 768, 1024, 1280, 1920],
    });
  });

  test('Example 3: Custom viewport height', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Capture with custom minimum height
    await snapshot(page, 'Example - Custom Height', {
      widths: [1280],
      minHeight: 2000, // Ensure full form is visible
    });
  });

  test('Example 4: Specific element snapshot', async ({ page }) => {
    await page.goto('/');
    
    // Capture only a specific element
    const header = page.locator('header');
    await snapshot(page, 'Example - Header Only', {
      scope: header,
      widths: [375, 768, 1280],
    });
  });
});

test.describe('Percy Snapshot Validation - Component States', () => {
  
  test('Component States: Empty form', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    await snapshot(page, 'Component State - Helpdesk Form Empty');
  });

  test('Component States: Partially filled form', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Fill some fields
    await page.fill('input[name="title"]', 'Test Issue');
    await page.waitForTimeout(300);
    
    await snapshot(page, 'Component State - Helpdesk Form Partial');
  });

  test('Component States: Fully filled form', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Fill all required fields
    await page.fill('input[name="title"]', 'Network Connectivity Issue');
    await page.fill('textarea[name="description"]', 'Unable to connect to Wi-Fi in meeting room');
    await page.selectOption('select[name="category"]', { index: 1 });
    await page.fill('input[name="requester_name"]', 'Ahmad bin Ali');
    await page.fill('input[name="requester_email"]', 'ahmad@motac.gov.my');
    await page.waitForTimeout(500);
    
    await snapshot(page, 'Component State - Helpdesk Form Filled');
  });

  test('Component States: Form with validation errors', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Trigger validation
    await page.fill('input[name="title"]', 'Te'); // Too short
    await page.blur('input[name="title"]');
    await page.waitForTimeout(500);
    
    await snapshot(page, 'Component State - Helpdesk Form Validation Error');
  });

  test('Component States: Form with success message', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Fill and submit
    await page.fill('input[name="title"]', 'Test Issue');
    await page.fill('textarea[name="description"]', 'Test description');
    await page.selectOption('select[name="category"]', { index: 1 });
    await page.fill('input[name="requester_name"]', 'Test User');
    await page.fill('input[name="requester_email"]', 'test@motac.gov.my');
    await page.click('button[type="submit"]');
    
    // Wait for success page
    await page.waitForURL(/\/helpdesk\/status\/.+/);
    await page.waitForSelector('.success-message, [role="alert"]');
    
    await snapshot(page, 'Component State - Helpdesk Form Success');
  });
});

test.describe('Percy Snapshot Validation - Dynamic Content', () => {
  
  test('Dynamic Content: Hide timestamps', async ({ page }) => {
    await page.goto('/');
    
    // Use Percy CSS to hide dynamic content
    await snapshot(page, 'Dynamic Content - Timestamps Hidden', {
      percyCSS: `
        .dynamic-timestamp { display: none !important; }
        .last-updated { display: none !important; }
      `,
    });
  });

  test('Dynamic Content: Hide loading states', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Hide Livewire loading indicators
    await snapshot(page, 'Dynamic Content - Loading Hidden', {
      percyCSS: `
        [wire\\:loading] { display: none !important; }
        .loading-spinner { display: none !important; }
      `,
    });
  });

  test('Dynamic Content: Hide user-specific data', async ({ page }) => {
    await page.goto('/');
    
    // Hide user avatars and personal info
    await snapshot(page, 'Dynamic Content - User Data Hidden', {
      percyCSS: `
        .user-avatar { visibility: hidden !important; }
        .user-name { visibility: hidden !important; }
      `,
    });
  });
});

test.describe('Percy Snapshot Validation - Livewire Components', () => {
  
  test('Livewire: Real-time validation feedback', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Capture initial state
    await snapshot(page, 'Livewire - Initial State');
    
    // Type and trigger validation
    await page.fill('input[name="title"]', 'Te');
    await page.blur('input[name="title"]');
    await page.waitForTimeout(500);
    
    // Capture error state
    await snapshot(page, 'Livewire - Validation Error State');
    
    // Fix error
    await page.fill('input[name="title"]', 'Test Issue Title');
    await page.blur('input[name="title"]');
    await page.waitForTimeout(500);
    
    // Capture valid state
    await snapshot(page, 'Livewire - Validation Success State');
  });

  test('Livewire: Select dropdown interaction', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Capture before selection
    await snapshot(page, 'Livewire - Before Category Selection');
    
    // Select category
    await page.selectOption('select[name="category"]', { index: 1 });
    await page.waitForTimeout(500);
    
    // Capture after selection
    await snapshot(page, 'Livewire - After Category Selection');
  });

  test('Livewire: Form reset', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Fill form
    await page.fill('input[name="title"]', 'Test');
    await page.fill('textarea[name="description"]', 'Description');
    await page.waitForTimeout(300);
    
    await snapshot(page, 'Livewire - Before Reset');
    
    // Reset form (if reset button exists)
    const resetButton = page.locator('button:has-text("Reset"), button[type="reset"]');
    if (await resetButton.count() > 0) {
      await resetButton.click();
      await page.waitForTimeout(500);
      await snapshot(page, 'Livewire - After Reset');
    }
  });
});

test.describe('Percy Snapshot Validation - Filament Admin', () => {
  
  test.beforeEach(async ({ page }) => {
    // Login as admin
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForURL('/admin');
  });

  test('Filament: Dashboard overview', async ({ page }) => {
    await page.waitForSelector('.fi-sidebar');
    
    await snapshot(page, 'Filament - Dashboard Overview', {
      widths: [1280, 1920],
    });
  });

  test('Filament: Table with data', async ({ page }) => {
    await page.click('a[href*="/admin/submissions"]');
    await page.waitForSelector('.fi-ta-table');
    
    await snapshot(page, 'Filament - Submissions Table');
  });

  test('Filament: Empty table state', async ({ page }) => {
    // Navigate to a potentially empty table
    await page.click('a[href*="/admin/assets"]');
    await page.waitForSelector('.fi-ta-table, .fi-ta-empty-state');
    
    await snapshot(page, 'Filament - Empty Table State');
  });

  test('Filament: Form create page', async ({ page }) => {
    await page.click('a[href*="/admin/submissions/create"]');
    await page.waitForSelector('.fi-fo-field-wrp');
    
    await snapshot(page, 'Filament - Create Form');
  });

  test('Filament: Table filters open', async ({ page }) => {
    await page.click('a[href*="/admin/submissions"]');
    await page.waitForSelector('.fi-ta-table');
    
    // Open filters
    const filterButton = page.locator('button:has-text("Filter")');
    if (await filterButton.count() > 0) {
      await filterButton.click();
      await page.waitForTimeout(500);
      await snapshot(page, 'Filament - Table Filters Open');
    }
  });

  test('Filament: Sidebar navigation', async ({ page }) => {
    await page.waitForSelector('.fi-sidebar');
    
    // Capture collapsed sidebar
    const collapseButton = page.locator('[aria-label="Collapse sidebar"]');
    if (await collapseButton.count() > 0) {
      await collapseButton.click();
      await page.waitForTimeout(300);
      await snapshot(page, 'Filament - Sidebar Collapsed');
    }
  });
});

test.describe('Percy Snapshot Validation - Accessibility States', () => {
  
  test('Accessibility: Focus visible on inputs', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Focus first input
    await page.focus('input[name="title"]');
    await snapshot(page, 'Accessibility - Input Focus Visible');
  });

  test('Accessibility: Focus visible on buttons', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Focus submit button
    await page.focus('button[type="submit"]');
    await snapshot(page, 'Accessibility - Button Focus Visible');
  });

  test('Accessibility: Keyboard navigation sequence', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Tab through elements
    await page.keyboard.press('Tab');
    await snapshot(page, 'Accessibility - Tab Navigation 1');
    
    await page.keyboard.press('Tab');
    await snapshot(page, 'Accessibility - Tab Navigation 2');
    
    await page.keyboard.press('Tab');
    await snapshot(page, 'Accessibility - Tab Navigation 3');
  });

  test('Accessibility: Error announcement region', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Trigger validation error
    await page.click('button[type="submit"]');
    await page.waitForTimeout(500);
    
    // Capture error announcement
    await snapshot(page, 'Accessibility - Error Announcement', {
      percyCSS: '', // Don't hide error messages
    });
  });
});

test.describe('Percy Snapshot Validation - Responsive Design', () => {
  
  test('Responsive: Mobile portrait (375px)', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Mobile Portrait', {
      widths: [375],
    });
  });

  test('Responsive: Mobile landscape (667px)', async ({ page }) => {
    await page.setViewportSize({ width: 667, height: 375 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Mobile Landscape', {
      widths: [667],
    });
  });

  test('Responsive: Tablet portrait (768px)', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Tablet Portrait', {
      widths: [768],
    });
  });

  test('Responsive: Tablet landscape (1024px)', async ({ page }) => {
    await page.setViewportSize({ width: 1024, height: 768 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Tablet Landscape', {
      widths: [1024],
    });
  });

  test('Responsive: Desktop (1280px)', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 1024 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Desktop', {
      widths: [1280],
    });
  });

  test('Responsive: Wide desktop (1920px)', async ({ page }) => {
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto('/');
    
    await snapshot(page, 'Responsive - Wide Desktop', {
      widths: [1920],
    });
  });

  test('Responsive: All breakpoints comparison', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Capture across all ICTServe breakpoints
    await snapshot(page, 'Responsive - All Breakpoints', {
      widths: [375, 768, 1024, 1280, 1920],
    });
  });
});

test.describe('Percy Snapshot Validation - Error States', () => {
  
  test('Error State: 404 Not Found', async ({ page }) => {
    await page.goto('/nonexistent-page');
    
    await snapshot(page, 'Error State - 404 Not Found');
  });

  test('Error State: Form validation (all fields)', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Submit empty form
    await page.click('button[type="submit"]');
    await page.waitForTimeout(500);
    
    await snapshot(page, 'Error State - Multiple Validation Errors');
  });

  test('Error State: Form validation (single field)', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Invalid email format
    await page.fill('input[name="requester_email"]', 'invalid-email');
    await page.blur('input[name="requester_email"]');
    await page.waitForTimeout(500);
    
    await snapshot(page, 'Error State - Invalid Email Format');
  });
});

test.describe('Percy Snapshot Validation - Loading States', () => {
  
  test('Loading State: Initial page load', async ({ page }) => {
    // Start navigation but don't wait for completion
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    
    // Check if loading indicator is visible
    const loading = page.locator('.loading-spinner, [wire\\:loading]');
    if (await loading.isVisible()) {
      await snapshot(page, 'Loading State - Page Load');
    }
    
    // Wait for completion
    await page.waitForLoadState('networkidle');
  });

  test('Loading State: Livewire form submission', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Fill form
    await page.fill('input[name="title"]', 'Test');
    await page.fill('textarea[name="description"]', 'Description');
    await page.selectOption('select[name="category"]', { index: 1 });
    await page.fill('input[name="requester_name"]', 'Test User');
    await page.fill('input[name="requester_email"]', 'test@motac.gov.my');
    
    // Submit and capture loading state (if visible)
    await page.click('button[type="submit"]');
    
    const loading = page.locator('[wire\\:loading], .loading');
    if (await loading.isVisible()) {
      await snapshot(page, 'Loading State - Form Submission');
    }
  });
});

test.describe('Percy Snapshot Validation - Best Practices', () => {
  
  test('Best Practice: Descriptive naming', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Good: Descriptive name that explains what's being tested
    await snapshot(page, 'Helpdesk Form - Empty State - Desktop 1280px');
  });

  test('Best Practice: Consistent naming pattern', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForLivewire(page);
    
    // Use consistent naming: [Component] - [State] - [Context]
    await snapshot(page, 'Helpdesk Form - Filled State - With Validation');
  });

  test('Best Practice: Wait for stability', async ({ page }) => {
    await page.goto('/helpdesk/create');
    
    // Wait for Livewire initialization
    await waitForLivewire(page);
    
    // Wait for network idle
    await page.waitForLoadState('networkidle');
    
    // Additional wait for animations
    await page.waitForTimeout(300);
    
    // Now capture stable state
    await snapshot(page, 'Helpdesk Form - Stable State');
  });

  test('Best Practice: Hide dynamic content appropriately', async ({ page }) => {
    await page.goto('/');
    
    // Use percy.config.js settings + additional hiding
    await snapshot(page, 'Landing Page - Dynamic Content Hidden', {
      percyCSS: `
        .dynamic-timestamp { display: none !important; }
        .user-avatar { visibility: hidden !important; }
        [wire\\:loading] { display: none !important; }
      `,
    });
  });
});
