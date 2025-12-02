import { test, expect } from '@playwright/test';

test.describe('Loan Module E2E', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('http://localhost:8000');
  });

  test('guest can submit loan application', async ({ page }) => {
    await page.click('text=Pinjaman Aset');
    
    await page.fill('input[name="name"]', 'Ahmad Bin Ali');
    await page.fill('input[name="email"]', 'ahmad@example.com');
    await page.fill('input[name="phone"]', '0123456789');
    await page.selectOption('select[name="division_id"]', { index: 1 });
    await page.fill('textarea[name="purpose"]', 'Untuk mesyuarat');
    
    await page.click('text=Pilih Aset');
    await page.click('[data-asset-id="1"]');
    
    await page.fill('input[name="start_date"]', '2025-12-01');
    await page.fill('input[name="end_date"]', '2025-12-05');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('text=Permohonan berjaya')).toBeVisible();
  });

  test('staff can view loan history', async ({ page }) => {
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'staff@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.click('text=Pinjaman Saya');
    
    await expect(page.locator('table')).toBeVisible();
    await expect(page.locator('td')).toContainText('PENDING');
  });

  test('approver can approve loan', async ({ page }) => {
    await page.goto('http://localhost:8000/login');
    await page.fill('input[name="email"]', 'approver@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.click('text=Kelulusan');
    
    await page.click('button:has-text("Lulus")').first();
    await page.fill('textarea[name="remarks"]', 'Diluluskan');
    await page.click('button:has-text("Sahkan")');
    
    await expect(page.locator('text=Permohonan diluluskan')).toBeVisible();
  });

  test('admin can issue asset with OTP', async ({ page }) => {
    await page.goto('http://localhost:8000/admin/login');
    await page.fill('input[name="email"]', 'admin@example.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    
    await page.goto('http://localhost:8000/admin/loan-applications');
    
    await page.click('button:has-text("Keluarkan")').first();
    
    await page.fill('input[name="otp"]', '123456');
    await page.click('button:has-text("Sahkan OTP")');
    
    await expect(page.locator('text=Aset dikeluarkan')).toBeVisible();
  });

  test('accessibility: keyboard navigation', async ({ page }) => {
    await page.goto('http://localhost:8000/loans/apply');
    
    await page.keyboard.press('Tab');
    await expect(page.locator('input[name="name"]')).toBeFocused();
    
    await page.keyboard.press('Tab');
    await expect(page.locator('input[name="email"]')).toBeFocused();
  });

  test('accessibility: screen reader labels', async ({ page }) => {
    await page.goto('http://localhost:8000/loans/apply');
    
    const nameInput = page.locator('input[name="name"]');
    await expect(nameInput).toHaveAttribute('aria-label');
    
    const submitBtn = page.locator('button[type="submit"]');
    await expect(submitBtn).toHaveAttribute('aria-label');
  });
});
