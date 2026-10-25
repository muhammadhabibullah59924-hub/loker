# Evals: LokerDekat

Evals adalah kriteria lulus yang **dapat dicek mesin**. Agen dan manusia memakai dokumen ini untuk memutuskan apakah suatu pekerjaan selesai.

## Purpose

Evals dipakai agar penilaian "sudah selesai" tidak bergantung pada perasaan: setiap task hanya dinyatakan lulus bila perintah gerbangnya benar-benar dijalankan dan hasilnya sesuai kriteria. Pada level **dasar**, gerbang dijalankan secara manual dan lokal oleh pengembang sebelum PR dibuat, lalu diulang oleh CI sebagai pemeriksaan kedua. Karena itu evals menjadi satu-satunya daftar yang menentukan apakah sebuah perubahan boleh masuk ke `main`.

## Quality Gates

Semua gerbang di tabel harus lulus sebelum merge ke `main`.

| Gerbang | Perintah | Kriteria lulus | Dijalankan di |
|---|---|---|---|
| Lint | `vendor/bin/pint --test` | Tanpa galat | Lokal dan CI |
| Tes | `php artisan test` | Semua tes lulus | Lokal dan CI |
| Build | `composer install --no-dev --optimize-autoloader` | Build berhasil | Lokal dan CI |

Gerbang tambahan:

- **Persiapan lingkungan:** `cp .env.example .env && php artisan key:generate` dijalankan sekali setelah instal dan sebelum lint/tes.
- **Pemeriksaan tipe:** tidak dipakai (`stack.commands.typecheck` kosong).
- **E2E:** `npx playwright test` untuk satu alur utama (daftar → masuk → dasbor → detail) dijalankan lokal sebelum PR dibuat.
- **Audit dependensi:** `composer audit` untuk ekosistem `php`; temuan kritis harus diperbaiki sebelum merge.
- **Secret scanning** GitHub (push protection) aktif; adanya rahasia di diff membuat PR gagal.
- **Daftar periksa manual:** checklist pada `docs/TESTING.md` minimal untuk pengujian di layar kecil, keyboard, dan izin lokasi ditolak.

## Feature Acceptance Evals

| ID | Eval | Cara dijalankan | Kriteria lulus |
|---|---|---|---|
| F1 | `RegisterLoginTest` | Otomatis: `php artisan test --filter=RegisterLoginTest` | Registrasi menyimpan akun; password salah menghasilkan pesan galat; setelah masuk nama tampil; logout mengakhiri sesi |
| F2 | `DashboardOrderTest` | Otomatis: `php artisan test --filter=DashboardOrderTest` | Kartu loker terurut menaik sesuai jarak; izin ditolak memakai urutan default pusat Probolinggo beserta peringatan; tanpa filter semua loker aktif tampil |
| F3 | `JobDetailMapTest` | Otomatis: `php artisan test --filter=JobDetailMapTest` | Halaman detail memuat koordinat loker dan alamat sama persis dengan nilai di database |
| F4 | `AdminJobCrudTest` | Otomatis: `php artisan test --filter=AdminJobCrudTest` | Form menolak kolom kosong; loker tanpa koordinat tidak tayang; pengguna biasa ditolak dari `/admin`; menonaktifkan menyembunyikan loker tanpa menghapusnya |
| F5 | `SearchFilterTest` | Otomatis: `php artisan test --filter=SearchFilterTest` | Hanya loker cocok yang tampil; kata kunci tanpa hasil menghasilkan pesan kosong; urutan jarak tetap terjaga |
| F6 | `FavoriteTest` | Otomatis: `php artisan test --filter=FavoriteTest` | Menyimpan dan melepas favorit bekerja dan daftar bertahan setelah login ulang |
| R1 | `DashboardNearestVisibleTest` | Otomatis: `php artisan test --filter=DashboardNearestVisibleTest` | Tanpa pencarian/filter, dasbor menampilkan minimal 3 loker terdekat lengkap dengan nama PT, alamat, divisi, dan jarak |
| R2 | `JobRequiredFieldsTest` | Otomatis: `php artisan test --filter=JobRequiredFieldsTest` | Penyimpanan loker gagal bila nama PT, alamat, divisi, atau koordinat kosong; 100% kartu di dasbor menampilkan keempat informasi itu |
| R3 | `GuideStepsVisibleTest` | Manual: buka halaman depan dan dasbor lalu cocokkan langkah | Panduan 3 langkah (Izinkan lokasi → lihat urut terdekat → buka detail dan peta) tampil dan terbaca tanpa bantuan orang lain |

## CI Configuration

`.github/workflows/ci.yml` berjalan pada **pull request** dan **push ke `main`**. Urutan langkah: checkout → setup runtime PHP sesuai versi yang dipakai proyek → cache dependensi `composer` → `composer install` → `cp .env.example .env && php artisan key:generate` → `vendor/bin/pint --test` → `php artisan test` → `composer install --no-dev --optimize-autoloader`. Versi runtime mengikuti `tech-baseline.md`, dan versi GitHub Actions diperiksa ulang — untuk produksi dipin ke SHA agar tidak berubah diam-diam. Cabang `main` dilindungi sehingga PR hanya dapat di-merge bila seluruh job CI hijau.

## Regression Policy

Setiap bug yang diperbaiki mendapat tes yang **gagal sebelum perbaikan** dan **lulus sesudahnya**. Tes yang gagal tidak boleh dinonaktifkan, di-skip, atau dihapus agar lulus. Bila sebuah tes memang harus berubah, alasannya dijelaskan di PR dan dicatat di `docs/DECISIONS.md`, bukan diubah diam-diam.

## Failure Handling

Saat gerbang gagal: baca log → perbaiki penyebab → jalankan ulang perintah yang sama. Agen dibatasi maksimal **3 kali percobaan per masalah**; bila tetap gagal, ia berhenti dan meminta manusia agar tidak membuang waktu atau menutupi masalah dengan mengubah tes. Kesalahan yang berulang dan pelajarannya dicatat di `docs/LESSONS.md` ketika masalah akhirnya selesai, lengkap dengan gejala, penyebab, dan pencegahannya.
