import { test, expect } from '@playwright/test';

/**
 * Loan Module Integration E2E Tests
 * 
 * @trace D03-FR-016.1 (Cross-Module Integration)
 * @trace D03-FR-001.1 (Complete User Workflows)
 */

test.describe('Loan Module Integration Tests', () => {
  test('complete guest loan application workflow', async ({ page }) => {
    // Navigate to loan application
    await page.goto('/loans/apply');
    
    // Fill application form
    await page.fill('input[name="applicant_name"]', 'Ahmad bin Abdullah');
    await page.fill('input[name="applicant_email"]', 'ahmad@motac.gov.my');
    await page.fill('textarea[name="purpose"]', 'Project presentation');
    
    // Select asset
    await page.click('[data-testid="asset-selector"]');
    await page.click('[data-testid="asset-item"]:first-child');
    
    // Select dates
    await page.fill('input[name="loan_start_date"]', '2025-12-01');
    await page.fill('input[name="loan_end_date"]', '2025-12-07');
    
    // Submit
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('[role="alert"]')).toContainText('berjaya');
    
    // Verify tracking URL provided
    await expect(page.locator('[data-testid="tracking-url"]')).toBeVisible();
  });

  test('authenticated user loan workflow', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Navigate to dashboard
    await page.goto('/loans/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
    
    // Create new application
    await page.click('[data-testid="new-application"]');
    await page.fill('textarea[name="purpose"]', 'Development work');
    await page.click('[data-testid="asset-selector"]');
    await page.click('[data-testid="asset-item"]:first-child');
    await page.click('button[type="submit"]');
    
    // Verify appears in history
    await page.goto('/loans/history');
    await expect(page.locator('[data-testid="loan-item"]')).toHaveCount(1);
  });

  test('email approval workflow simulation', async ({ page }) => {
    // Simulate clicking approval link from email
    const approvalToken = 'test-token-123';
    await page.goto(`/loans/approve?token=${approvalToken}&action=approve`);
    
    // Verify approval confirmation
    await expect(page.locator('[role="alert"]')).toContainText('diluluskan');
  });

  test('loan extension request workflow', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Navigate to active loans
    await page.goto('/loans/active');
    
    // Request extension
    await page.click('[data-testid="extend-loan"]:first-child');
    await page.fill('input[name="new_return_date"]', '2025-12-15');
    await page.fill('textarea[name="justification"]', 'Project delayed');
    await page.click('button[type="submit"]');
    
    // Verify success
    await expect(page.locator('[role="alert"]')).toContainText('berjaya');
  });

  test('asset availability check integration', async ({ page }) => {
    await page.goto('/loans/apply');
    
    // Check availability
    await page.click('[data-testid="check-availability"]');
    
    // Wait for results
    await page.waitForSelector('[data-testid="available-assets"]', { state: 'visible' });
    
    // Verify assets displayed
    const assetCount = await page.locator('[data-testid="asset-item"]').count();
    expect(assetCount).toBeGreaterThan(0);
    
    // Verify status indicators
    await expect(page.locator('[data-testid="asset-status"]').first()).toBeVisible();
  });

  test('cross-module navigation', async ({ page }) => {
    // Login as admin
    await page.goto('/admin/login');
    await page.fill('input[name="email"]', 'admin@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Navigate to loans
    await page.goto('/admin/loans');
    await expect(page.locator('h1')).toContainText('Loan Applications');
    
    // Navigate to assets
    await page.goto('/admin/assets');
    await expect(page.locator('h1')).toContainText('Assets');
    
    // Navigate to helpdesk (cross-module)
    await page.goto('/admin/helpdesk');
    await expect(page.locator('h1')).toContainText('Helpdesk');
  });

  test('dashboard analytics integration', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Navigate to dashboard
    await page.goto('/loans/dashboard');
    
    // Verify statistics cards
    await expect(page.locator('[data-testid="stat-active-loans"]')).toBeVisible();
    await expect(page.locator('[data-testid="stat-pending-applications"]')).toBeVisible();
    await expect(page.locator('[data-testid="stat-overdue-items"]')).toBeVisible();
    
    // Verify charts load
    await expect(page.locator('[data-testid="loan-analytics-chart"]')).toBeVisible();
    await expect(page.locator('[data-testid="asset-utilization-chart"]')).toBeVisible();
  });

  test('notification system integration', async ({ page }) => {
    // Login
    await page.goto('/login');
    await page.fill('input[name="email"]', 'user@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    // Trigger action that sends notification
    await page.goto('/loans/apply');
    await page.fill('textarea[name="purpose"]', 'Test');
    await page.click('[data-testid="asset-selector"]');
    await page.click('[data-testid="asset-item"]:first-child');
    await page.click('button[type="submit"]');
    
    // Check for notification
    await expect(page.locator('[role="alert"]')).toBeVisible({ timeout: 5000 });
  });

  test('responsive design integration', async ({ page }) => {
    // Test mobile viewport
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/loans');
    
    // Verify mobile menu
    await expect(page.locator('[data-testid="mobile-menu-button"]')).toBeVisible();
    
    // Test tablet viewport
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/loans');
    
    // Verify layout adapts
    await expect(page.locator('[data-testid="sidebar"]')).toBeVisible();
    
    // Test desktop viewport
    await page.setViewportSize({ width: 1920, height: 1080 });
    await page.goto('/loans');
    
    // Verify full layout
    await expect(page.locator('[data-testid="main-content"]')).toBeVisible();
  });
});
