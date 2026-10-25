# Testing: LokerDekat

## Strategy

LokerDekat memakai piramida pengujian yang condong ke unit test: logika murni seperti perhitungan jarak Haversine, validasi Form Request, dan otorisasi peran diuji banyak dengan PHPUnit, sedangkan satu alur E2E utama dengan Playwright menutup jalur paling penting yaitu daftar → masuk → dasbor terurut → detail loker. Tingkat **dasar** dan platform **web** membuat kombinasi ini cukup: proyek hanya berdurasi 1 minggu dengan 1 pengembang, sehingga tes yang terlalu tebal justru menghabiskan waktu yang dibutuhkan untuk menyelesaikan F1–F4. UI yang bersifat tampilan cukup diuji lewat daftar periksa manual.

## Test Types

| Jenis | Alat | Lokasi berkas | Kapan dijalankan |
|---|---|---|---|
| Unit | PHPUnit | `tests/Unit/` | Setiap perubahan kode; `php artisan test` atau `php artisan test --filter=DistanceServiceTest` |
| Fitur/HTTP | PHPUnit | `tests/Feature/` | Setiap perubahan rute atau controller; `php artisan test --filter=JobControllerTest` |
| E2E | Playwright | `tests/e2e/` | Sebelum menyatakan task selesai; `npx playwright test` |
| Lint | Laravel Pint | seluruh berkas PHP | Sebelum commit; `vendor/bin/pint --test` |

## What Must Be Tested

| ID | Perilaku kunci | Jenis tes |
|---|---|---|
| F1 | Registrasi menyimpan akun dan login berhasil; password salah menampilkan galat; logout mengakhiri sesi | unit/feature |
| F2 | Urutan dasbor menaik sesuai jarak; izin lokasi ditolak memakai urutan default pusat Probolinggo; tanpa filter seluruh loker aktif tampil | unit (logika jarak) + feature (rute dasbor) |
| F3 | Halaman detail menampilkan marker pada koordinat yang tepat dan alamat sama dengan database | feature |
| F4 | Form admin menolak kolom kosong; loker tanpa koordinat tidak tayang; pengguna biasa ditolak dari `/admin`; menonaktifkan menyembunyikan loker tanpa menghapusnya | feature |
| F5 | Pencarian mengembalikan hanya loker yang cocok dengan urutan jarak tetap terjaga; kata kunci tanpa hasil menampilkan pesan kosong | feature |
| F6 | Menyimpan dan melepas favorit bekerja serta bertahan setelah login ulang | feature |

Wajib uji keamanan: otorisasi per peran (user biasa tidak boleh menyentuh `/admin`), validasi input pada form loker dan registrasi, perlindungan CSRF pada seluruh form, serta perilaku saat galat (izin lokasi ditolak, peta gagal dimuat, jaringan putus) yang harus tetap menampilkan daftar loker.

## Commands

Perintah utama (dari `stack.commands`, identik dengan AGENTS.md, EVALS.md, dan CI):

| Tujuan | Perintah |
|---|---|
| Instal | `composer install` |
| Lint | `vendor/bin/pint --test` |
| Semua tes | `php artisan test` |
| Build | `composer install --no-dev --optimize-autoloader` |

Perintah tambahan yang lazim:

- Menjalankan satu berkas tes: `php artisan test --filter=DistanceServiceTest`
- Mode tonton: `php artisan test --watch` bila tersedia pada versi yang terpasang
- Perintah dev: `php artisan serve` (tersedia setelah T-001)
- Pemeriksaan tipe: tidak dipakai (`stack.commands.typecheck` kosong)
- Persiapan lingkungan sekali saat setup lokal dan di CI: `cp .env.example .env && php artisan key:generate`
- E2E: `npx playwright test` setelah skrip E2E dibuat oleh task setup

Bila skrip tes tambahan belum ada, ia dibuat oleh task setup (T-001 atau T-002), bukan ditambahkan diam-diam oleh agen.

## Coverage and Quality Bar

Target cakupan realistis: logika bisnis (perhitungan jarak, urutan, validasi, otorisasi) diusahakan teruji penuh, sedangkan tampilan Blade cukup tercakup lewat tes fitur dan daftar periksa manual — angka 100 persen tidak dipaksa. Tes tidak boleh dihapus atau dilemahkan agar lulus. Setiap bug yang diperbaiki wajib mendapat tes baru yang gagal sebelum perbaikan dan lulus sesudahnya.

## Test Data

Data uji dibuat dari seeder berupa loker dummy di sekitar Probolinggo dan akun `user`/`admin` buatan; tidak ada data pribadi asli dalam tes apa pun, sesuai `personal_data = location_contact`. Basis data uji memakai basis data terpisah yang direset sebelum suite berjalan (mis. SQLite uji atau `db_loker_test`) sehingga pengujian tidak menyentuh data kerja. Tidak ada layanan eksternal berbayar dalam pengujian; ubin peta dipalsukan pada tes E2E agar tidak bergantung jaringan.

## Manual Test Checklist

- [ ] Membuka dasbor di Chrome, Firefox, dan Edge terbaru — kartu loker tampil urut.
- [ ] Membuka di layar 360 px lebar — navbar, kartu, dan tombol tetap terbaca tanpa geser.
- [ ] Navigasi hanya dengan keyboard: tab sampai ke tombol Masuk, tombol simpan, dan form admin.
- [ ] Menolak izin lokasi — peringatan muncul dan urutan default pusat Probolinggo dipakai.
- [ ] Mematikan GPS lalu memuat ulang dasbor — aplikasi tetap menampilkan daftar loker.
- [ ] Login dengan password salah — muncul pesan galat yang ramah.
- [ ] Pengguna biasa mengetik `/admin` langsung di address bar — ditolak.
- [ ] Admin mengosongkan kolom nama PT lalu menyimpan — form menolak dengan pesan validasi (R2).
- [ ] Admin menonaktifkan satu loker — loker hilang dari dasbor, datanya masih ada.
- [ ] Peta di halaman detail termuat dan marker berada di titik yang benar.
- [ ] Menyimpan loker favorit, logout, login lagi — daftar Favorit tetap sama.
- [ ] Mematikan jaringan lalu memuat halaman — pesan galat muncul tanpa halaman kosong total.
- [ ] Mengaktifkan *reduced motion* di sistem — transisi mati.

## Definition of Done

1. Kriteria acceptance fitur terkait di `docs/PRD.md` terpenuhi dan dapat didemonstrasikan.
2. Tes baru ditulis untuk perubahan tersebut dan seluruhnya lulus dengan `php artisan test`.
3. Lint bersih (`vendor/bin/pint --test` tanpa galat) dan tidak ada sisa debug.
4. Build berhasil dijalankan (`composer install --no-dev --optimize-autoloader`).
5. Tidak ada rahasia di diff; `.env` tidak ikut ter-commit.
6. `docs/PROGRESS.md` diperbarui dan keputusan baru tercatat di `docs/DECISIONS.md`.
7. PR ditinjau sebelum merge ke `main`.
