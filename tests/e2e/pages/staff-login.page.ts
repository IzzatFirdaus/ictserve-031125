/**
 * Staff Login Page Object Model
 *
 * Encapsulates locators and common actions for /login page
 * Pattern: Page Object Model (POM) - reduces duplication, centralizes selectors
 *
 * Research findings: Playwright POM Documentation v1.56.1
 * - Simplify authoring with high-level API
 * - Simplify maintenance by capturing selectors in one place
 * - Enable reusable code to avoid duplication
 */

import { type Page, expect } from '@playwright/test';

export class StaffLoginPage {
  constructor(readonly page: Page) {}

  /**
   * Locator properties for form fields
   * Exposed for direct accessibility testing
   */
  get emailInput() {
    return this.page.getByLabel('Email');
  }

  get passwordInput() {
    return this.page.getByLabel('Password');
  }

  get loginButton() {
    return this.page.getByRole('button', { name: /log in|sign in/i });
  }

  /**
   * Navigate to login page
   * Uses baseURL from config: /login → http://localhost:8000/login
   */
  async goto() {
    await this.page.goto('/login');
    await this.page.waitForLoadState('domcontentloaded');
  }

  /**
   * Fill login form with email and password
   * Uses user-facing locators (label-based) instead of CSS selectors
   *
   * Best practice: getByLabel > getByRole > getByText > test IDs > CSS
   */
  async fillLoginForm(email: string, password: string) {
    await this.page.getByLabel('Email').fill(email);
    await this.page.getByLabel('Password').fill(password);
  }

  /**
   * Submit login form
   * Uses web-first assertion: toBeVisible() and toBeEnabled() auto-waits
   * Waits for Livewire to enable the button before clicking
   */
  async submitLogin() {
    const submitButton = this.page.getByRole('button', { name: /log in|sign in/i });
    await expect(submitButton).toBeVisible({ timeout: 10000 });
    await expect(submitButton).toBeEnabled({ timeout: 10000 });
    await submitButton.click();
  }

  /**
   * Complete login flow: goto → fill → submit → wait for dashboard
   * Reusable method reduces duplication in tests
   * Increased timeout to handle Laravel server startup delays
   */
  async login(email: string, password: string) {
    await this.goto();
    await this.fillLoginForm(email, password);
    await this.submitLogin();

    // Wait for authentication to complete and dashboard to load
    const dashboardUrlPattern = /\/(staff\/)?dashboard(?:\/?$)/;
    const adminUrlPattern = /\/admin(?:\/.*)?$/;

    await Promise.race([
      this.page.waitForURL(dashboardUrlPattern, { timeout: 90000, waitUntil: 'domcontentloaded' }),
      this.page.waitForURL(adminUrlPattern, { timeout: 90000, waitUntil: 'domcontentloaded' })
    ]);

    await this.page.waitForLoadState('domcontentloaded');

    // Additional wait for page content to render
    await this.page.waitForSelector('[data-testid="dashboard-root"], main, [role="main"], .fi-sidebar', {
      state: 'visible',
      timeout: 30000
    }).catch(() => {});
  }

  /**
   * Verify error message after failed login
   */
  async verifyErrorMessage() {
    const errorMessage = this.page.getByRole('alert');
    await expect(errorMessage).toBeVisible();
    return errorMessage.textContent();
  }

  /**
   * Check if email field is visible and empty
   */
  async isEmailFieldVisible() {
    const emailField = this.page.getByLabel('Email');
    await expect(emailField).toBeVisible();
    return emailField.inputValue();
  }
}
