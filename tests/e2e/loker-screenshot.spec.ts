import { expect, test } from '@playwright/test';

test('screenshot: dasbor dan detail loker', async ({ page }) => {
    const origin = 'http://127.0.0.1:8000';
    const context = page.context();

    // Browser memberikan posisi geografi ke aplikasi supaya dasbor memakai urutan jarak.
    await context.grantPermissions(['geolocation'], { origin });
    await context.setGeolocation({ latitude: -7.7145, longitude: 113.2146 });

    // Login via akun demo.
    await page.goto('/login');
    await page.getByLabel('Email', { exact: true }).fill('pencari@lokerdekat.test');
    await page.getByLabel('Kata sandi', { exact: true }).fill('password');
    await page.getByRole('button', { name: 'Masuk' }).click();

    await expect(page).toHaveURL(/\/dasbor/);
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Loker terdekat dari lokasimu');
    await expect(page.locator('article.job-card')).not.toHaveCount(0);

    await page.screenshot({ path: 'docs/screenshots/dashboard.png', fullPage: true });

    // Buka detail loker dari kartu pertama.
    await page.locator('article.job-card h3 a').first().click();
    await expect(page).toHaveURL(/\/lowongan\/\d+/);
    await expect(page.locator('#peta-loker')).toBeVisible();

    await page.screenshot({ path: 'docs/screenshots/detail-loker.png', fullPage: true });
});
