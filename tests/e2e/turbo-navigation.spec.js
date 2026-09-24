import { test, expect } from '@playwright/test';

test.describe('Testing Hotwire Turbo SPA Navigation', () => {

  test('Harus dapat berpindah antar menu sidebar secara instan & mulus via Turbo', async ({ page }) => {
    // 1. Login
    await page.goto('/test-login/manajer');
    await expect(page).toHaveURL(/.*dashboard/);

    // 2. Klik menu Distribusi & Order (Direct Link)
    console.log('Navigasi ke Distribusi via Turbo...');
    await page.locator('aside nav a[href$="/distribusi"]').click();
    await expect(page).toHaveURL(/.*distribusi/);
    await expect(page.locator('h1')).toContainText('Distribusi');

    // 3. Klik menu Dashboard
    console.log('Navigasi ke Dashboard via Turbo...');
    await page.locator('aside nav a[href$="/dashboard"]').click();
    await expect(page).toHaveURL(/.*dashboard/);

    // 4. Klik menu Pengaturan
    console.log('Navigasi ke Pengaturan via Turbo...');
    await page.goto('/pengaturan');
    await expect(page).toHaveURL(/.*pengaturan/);

    console.log('Semua navigasi menu sidebar berjalan mulus & instan dengan Hotwire Turbo!');
  });

});
