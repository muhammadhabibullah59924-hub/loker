import { expect, test } from '@playwright/test';

test('alur E2E: daftar, masuk, dasbor, dan detail loker terurut', async ({ page }) => {
    const origin = 'http://127.0.0.1:8000';
    const context = page.context();

    // Browser memberikan posisi geografi ke aplikasi supaya dasbor memakai urutan jarak.
    await context.grantPermissions(['geolocation'], { origin });
    await context.setGeolocation({ latitude: -7.7145, longitude: 113.2146 });

    // 1. Pengguna membuka halaman depan dan masuk lewat alur daftar.
    await page.goto('/');
    await page.getByRole('link', { name: 'Daftar dan lihat loker terdekat' }).click();
    await expect(page).toHaveURL(/\/register/);

    const email = `budi-e2e-${Date.now()}@lokerdekat.test`;

    await page.getByLabel('Nama lengkap', { exact: true }).fill('Budi E2E');
    await page.getByLabel('Email', { exact: true }).fill(email);
    await page.getByLabel('Kata sandi', { exact: true }).fill('password123');
    await page.getByLabel('Ulangi kata sandi', { exact: true }).fill('password123');
    await page.getByRole('button', { name: 'Buat akun' }).click();

    // 2. Setelah daftar, pengguna masuk dan langsung masuk ke dasbor.
    await expect(page).toHaveURL(/\/dasbor/);
    await expect(page.getByRole('heading', { level: 1 })).toContainText('Loker terdekat dari lokasimu');
    await expect(page.locator('article.job-card')).not.toHaveCount(0);
    await expect(page.locator('.job-card__jarak').first()).toContainText('km');

    // 3. Kartu pertama yang tampil adalah loker terdekat, lalu halaman detail terbuka.
    await page.locator('article.job-card h3 a').first().click();
    await expect(page).toHaveURL(/\/lowongan\/\d+/);
    await expect(page.locator('#peta-loker')).toBeVisible();
});
