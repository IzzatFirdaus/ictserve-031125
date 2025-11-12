import { test, expect } from '@playwright/test';

test.describe('Branding smoke checks', () => {
  test('header, notification icon, and email asset are available', async ({ page, request }) => {
    await page.goto('/');

    const headerLogo = page.locator('img[src*="jata-negara.svg"]');
    await expect(headerLogo).toBeVisible();

    const notificationIconResponse = await request.get('/images/motac-logo-32.png');
    expect(notificationIconResponse.ok()).toBeTruthy();

    const emailLogoResponse = await request.get('/images/motac-logo.png');
    expect(emailLogoResponse.ok()).toBeTruthy();
  });
});
