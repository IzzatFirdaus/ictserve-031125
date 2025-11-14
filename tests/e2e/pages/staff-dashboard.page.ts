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
   * Note: Dashboard uses "Recent Tickets" and "Recent Loans" cards, not generic "Quick Actions"
   */
  get quickActionsSection() {
    // Dashboard doesn't have a dedicated "Quick Actions" section
    // Instead, it has action links within cards (e.g., "View All" links)
    return this.page.locator('[class*="bg-slate-900"]').filter({ hasText: /view all|lihat semua/i }).first();
  }

  get recentActivitySection() {
    // Dashboard has two activity cards: "Recent Tickets" and "Recent Loans"
    return this.page.locator('[class*="bg-slate-900"]').filter({ hasText: /recent tickets|recent loans|tiket terkini|pinjaman terkini/i }).first();
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
   * Handles strict mode by selecting navigation link (not quick action button)
   */
  async navigateToHelpdesk() {
    // Select navigation link specifically (excludes quick action buttons)
    const helpdeskLink = this.page.getByRole('navigation').getByRole('link', { name: /helpdesk|مكتب المساعدة/i }).first();
    await expect(helpdeskLink).toBeVisible({ timeout: 10000 });

    // Livewire wire:navigate requires waiting for actual navigation event
    // Use Promise.race to handle both successful navigation and errors
    await Promise.race([
      helpdeskLink.click().then(() => this.page.waitForURL(/helpdesk|tickets/i, { timeout: 90000 })),
      this.page.waitForNavigation({ timeout: 90000 }).catch(() => {
        // If navigation fails, try waiting for URL change directly
        return this.page.waitForURL(/helpdesk|tickets/i, { timeout: 90000 });
      })
    ]);
  }

  /**
   * Navigate to Asset Loan module
   * Handles strict mode by selecting navigation link (not quick action button)
   */
  async navigateToAssetLoan() {
    // Select navigation link specifically (excludes quick action buttons)
    // Navigation text is "Loans" (staff.nav.loans) or "Pinjaman" (Malay)
    const assetLoanLink = this.page.getByRole('navigation').getByRole('link', { name: /loan/i }).first();
    await expect(assetLoanLink).toBeVisible({ timeout: 10000 });

    // Livewire wire:navigate requires waiting for actual navigation event
    await Promise.race([
      assetLoanLink.click().then(() => this.page.waitForURL(/loan|asset/i, { timeout: 90000 })),
      this.page.waitForNavigation({ timeout: 90000 }).catch(() => {
        return this.page.waitForURL(/loan|asset/i, { timeout: 90000 });
      })
    ]);
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
