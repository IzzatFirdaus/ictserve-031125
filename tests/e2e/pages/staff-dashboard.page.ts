/**
 * Staff Dashboard Page Object Model
 *
 * Encapsulates locators and actions for staff dashboard
 * Provides high-level API for dashboard interactions
 *
 * Assumes: User is already authenticated (use authenticatedPage fixture)
 */

import { type Page, expect } from '@playwright/test';

export class StaffDashboardPage {
  constructor(readonly page: Page) {}

  /**
   * Locator properties for dashboard sections
   * Exposed for direct accessibility testing
   */
  get quickActionsSection() {
    return this.page.locator('[data-testid="quick-actions"], .quick-actions, section').filter({ hasText: /quick actions|tindakan pantas/i }).first();
  }

  get recentActivitySection() {
    return this.page.locator('[data-testid="recent-activity"], .recent-activity, section').filter({ hasText: /recent activity|aktiviti terkini/i }).first();
  }

  /**
   * Navigate to dashboard
   */
  async goto() {
    await this.page.goto('/dashboard');
    await this.page.waitForLoadState('domcontentloaded');
  }

  /**
   * Verify dashboard is loaded
   * Uses web-first assertion: auto-waits until content visible
   */
  async verifyDashboardLoaded() {
    const heading = this.page.getByRole('heading', { name: /dashboard|مقابض البيانات/i });
    await expect(heading).toBeVisible();
  }

  /**
   * Navigate to Helpdesk module
   * Uses user-facing link text instead of hardcoded selectors
   */
  async navigateToHelpdesk() {
    const helpdeskLink = this.page.getByRole('link', { name: /helpdesk|مكتب المساعدة/i });
    await expect(helpdeskLink).toBeVisible();
    await helpdeskLink.click();
    await this.page.waitForURL(/helpdesk|tickets/i, { timeout: 10000 });
  }

  /**
   * Navigate to Asset Loan module
   */
  async navigateToAssetLoan() {
    const assetLoanLink = this.page.getByRole('link', { name: /asset loan|loan|pinjaman|تمويل الأصول|استعارة الأصول/i });
    await expect(assetLoanLink).toBeVisible();
    await assetLoanLink.click();
    await this.page.waitForURL(/loan|asset/i, { timeout: 10000 });
  }

  /**
   * Alias for navigateToAssetLoan (compatibility)
   */
  async navigateToLoan() {
    return this.navigateToAssetLoan();
  }

  /**
   * Navigate to user profile
   */
  async navigateToProfile() {
    // Open user menu (typically avatar or dropdown)
    const profileLink = this.page.getByRole('link', { name: /profile|الملف الشخصي/i });
    if (profileLink) {
      await profileLink.click();
      await this.page.waitForURL(/profile/i, { timeout: 10000 });
    }
  }

  /**
   * Logout from dashboard
   */
  async logout() {
    const logoutButton = this.page.getByRole('button', { name: /logout|خروج|log out|تسجيل الخروج/i });
    if (logoutButton) {
      await logoutButton.click();
      await this.page.waitForURL('/login', { timeout: 10000 });
    }
  }

  /**
   * Click quick action by name
   * Example: "Create Ticket", "Request Asset", etc.
   */
  async clickQuickAction(actionName: string) {
    const action = this.page.getByRole('button', { name: new RegExp(actionName, 'i') });
    await expect(action).toBeVisible();
    await action.click();
  }

  /**
   * Verify quick actions section exists
   */
  async verifyQuickActionsExist() {
    const quickActions = this.page.getByRole('region', { name: /quick actions|actions rapides/i });
    if (quickActions) {
      await expect(quickActions).toBeVisible();
    }
  }

  /**
   * Get dashboard statistics card by label
   * Example: "Total Tickets", "Pending Loans", etc.
   */
  async getStatCard(label: string) {
    return this.page.getByRole('heading', { name: new RegExp(label, 'i') }).locator('..');
  }

  /**
   * Take full-page screenshot (useful for visual regression)
   */
  async screenshot(filename: string) {
    await this.page.screenshot({
      path: `./public/images/screenshots/${filename}`,
      fullPage: true,
    });
  }
}
