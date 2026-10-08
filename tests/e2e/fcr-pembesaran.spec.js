import { test, expect } from '@playwright/test';

test.describe('Testing FCR & Analisis Pembesaran', () => {

  test('Harus dapat login, membuka halaman Pembesaran, dan memvalidasi nilai FCR pada Modal Detail', async ({ page }) => {
    // 1. Login sebagai Manajer
    await page.goto('/test-login/manajer');
    await expect(page).toHaveURL(/.*dashboard/);

    // 2. Navigasi ke halaman Pembesaran
    await page.goto('/pembesaran');
    await expect(page).toHaveURL(/.*pembesaran/);

    // Pastikan halaman dan judul termuat
    await expect(page.locator('h1')).toContainText('Pembesaran');

    // 3. Cari kartu batch / baris tabel batch aktif
    const detailButtons = page.locator('button:has-text("Detail"), [data-batch-id], button:has-text("#PB-")');
    const count = await detailButtons.count();
    console.log(`Menemukan ${count} elemen interaktif batch pembesaran.`);

    // Klik tombol / kartu batch pertama untuk membuka modal detail
    const firstCard = page.locator('div:has-text("#PB-")').first();
    await expect(firstCard).toBeVisible();
    await firstCard.click();

    // 4. Tunggu modal detail muncul
    const modal = page.locator('div:has-text("Analisis Efisiensi Pakan (4 Tipe FCR)")').first();
    await expect(modal).toBeVisible({ timeout: 5000 });

    // 5. Ekstrak dan verifikasi teks FCR
    const fcrSection = page.locator('div:has-text("Analisis Efisiensi Pakan (4 Tipe FCR)")').first();
    const modalText = await fcrSection.innerText();
    console.log('=== HASIL ANALISIS FCR PADA MODAL ===');
    console.log(modalText);

    // Pastikan 4 komponen FCR ada
    await expect(modal).toContainText('1. FCR KUMULATIF');
    await expect(modal).toContainText('2. FCR KOMERSIAL');
    await expect(modal).toContainText('3. FCR BIOLOGIS');
    await expect(modal).toContainText('4. STANDAR SOP');

    // 6. Pastikan Analisis Finansial juga tampil
    const financialSection = page.locator('div:has-text("Analisis Finansial & Profitabilitas Kolam")').first();
    await expect(financialSection).toBeVisible();
    await expect(financialSection).toContainText('BIAYA PAKAN');
    await expect(financialSection).toContainText('BIAYA BIBIT');
    await expect(financialSection).toContainText('ESTIMASI OMSET');
    await expect(financialSection).toContainText('LABA / RUGI');

    // Ambil screenshot sebagai bukti pengujian
    await page.screenshot({ path: 'tests/e2e/screenshots/pembesaran-fcr-modal.png', fullPage: true });
    console.log('Screenshot berhasil disimpan di tests/e2e/screenshots/pembesaran-fcr-modal.png');
  });

});
