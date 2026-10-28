# Tasks: LokerDekat

## Conventions

- **ID**: `T-001`, `T-002`, ... berurutan, unik.
- **Status**: `todo`, `doing`, `review`, `done`, `blocked`.
- **Ukuran**: `S` (< 1 jam), `M` (1 sampai 3 jam), `L` (setengah hari). Task lebih besar dari `L` harus dipecah.
- Satu task = satu branch = satu PR. Hanya satu task berstatus `doing` per orang.
- Setiap task merujuk fitur (F1, F2, ...) atau "setup"/"dokumen", dan memiliki kriteria selesai yang dapat dicek.

Agen hanya mengambil task berstatus `todo` yang seluruh dependensinya sudah `done`, mengubah status menjadi `doing` saat mulai dikerjakan dan menjadi `review` saat PR dibuka. Mahasiswa mengubahnya menjadi `done` setelah merge ke `main`.

## Milestones

| Milestone | Tujuan | Fitur | Perkiraan minggu |
|---|---|---|---|
| Fondasi dan setup | Proyek terpasang, CI hijau, database dan landing page siap | setup | Minggu 1 (hari 1–2) |
| Penyempurnaan dan rilis | Seluruh fitur must berfungsi, lulus evals, dokumentasi diserahkan | F1, F2, F3, F4, F5, F6 | Minggu 1 (hari 3–7) |

## Task List

| ID | Judul | Fitur | Ukuran | Status | Branch | Bergantung pada | Kriteria selesai |
|---|---|---|---|---|---|---|---|
| T-001 | Setup repositori, tooling, dan kerangka Laravel minimal | setup | L | review | feat/T-001-setup | - | `composer create-project laravel/laravel .` dijalankan; `.env` disalin dari `.env.example` lalu `php artisan key:generate`; dibuat satu tes contoh; `composer install`, `vendor/bin/pint --test`, `php artisan test`, dan `composer install --no-dev --optimize-autoloader` lulus; CI hijau |
| T-002 | Migrasi dan seeder tiga tabel beserta model Eloquent | setup | M | review | feat/T-002-migrasi | T-001 | Tabel `users`, `jobs`, `favorites` ter-migrasi; model `User`, `Job`, `Favorite` dibuat dengan relasi; seeder menghasilkan 1 admin, 1 user, dan 24 loker Probolinggo berkoordinat; `php artisan test` lulus |
| T-003 | Halaman depan dengan panduan tiga langkah | F2, R3 | S | review | feat/T-003-landing | T-001 | Halaman depan menampilkan logo, tagline, tombol Masuk/Daftar, dan panduan "Izinkan lokasi → lihat urut terdekat → buka detail dan peta" (R3); terbaca di layar 360 px |
| T-004 | Registrasi, masuk, dan keluar | F1 | M | review | feat/T-004-auth | T-002 | Form Daftar menyimpan akun; password salah menampilkan pesan galat; nama tampil di navbar setelah masuk; Keluar mengakhiri sesi; ada tes `RegisterLoginTest` |
| T-005 | Middleware peran dan kerangka panel admin | F4 | S | review | feat/T-005-middleware-admin | T-004 | Rute `/admin` dijaga middleware `admin`; pengguna biasa yang membuka `/admin` ditolak dan dialihkan; ada tes otorisasi |
| T-006 | Service jarak Haversine beserta unit test | F2 | S | review | feat/T-006-jarak | T-002 | `DistanceService::distanceInKm` menghasilkan jarak yang benar untuk dua titik uji; rumus tertulis sesuai `docs/CODE_STYLE.md`; unit test lulus |
| T-007 | Dasbor loker terurut terdekat dengan cadangan lokasi default | F2, R1 | L | review | feat/T-007-dasbor | T-004, T-006 | Mengizinkan lokasi membuat kartu terurut menaik dengan angka km; penolakan izin memakai urutan default pusat Probolinggo plus peringatan; tanpa pencarian/filter minimal 3 loker tampil lengkap dengan nama PT, alamat, divisi (R1); ada tes `DashboardOrderTest` |
| T-008 | Halaman detail loker dengan peta Leaflet | F3 | M | review | feat/T-008-detail-peta | T-007 | Peta termuat dengan marker pada koordinat loker; alamat sama dengan database; kegagalan memuat ubin tetap menyisakan alamat teks; ada tes `JobDetailMapTest` |
| T-009 | Form tambah, ubah, nonaktif, dan hapus loker | F4, R2 | L | review | feat/T-009-crud-admin | T-005 | Form menolak penyimpanan bila nama PT, alamat, divisi, atau koordinat kosong (R2); loker tanpa koordinat tidak tayang; menonaktifkan menyembunyikan loker tanpa menghapus data; ada tes `AdminJobCrudTest` |
| T-010 | Pencarian dan filter radius | F5 | M | review | feat/T-010-pencarian | T-007 | Pencarian nama PT/divisi memakai `q` + filter radius di query dasbor; urutan jarak tetap menaik; ada tes `SearchFilterTest` |
| T-011 | Loker favorit | F6 | M | todo | feat/T-011-favorit | T-004, T-007 | Menyimpan dan melepas favorit bekerja; daftar bertahan setelah keluar lalu masuk lagi; hanya milik pengguna sendiri yang tampil; ada tes `FavoriteTest` |
| T-012 | Satu alur E2E dengan Playwright | dokumen | M | todo | test/T-012-e2e | T-008 | Skrip E2E menjalankan daftar → masuk → dasbor → detail loker dan lulus; `npx playwright test` bisa dijalankan ulang |
| T-013 | Penyempurnaan tampilan dan aksesibilitas | F2 | M | todo | feat/T-013-ui-aksesibilitas | T-008 | Warna, tipografi, dan komponen mengikuti `docs/DESIGN_SYSTEM.md`; navigasi keyboard berfungsi; target sentuh ≥ 24 px; checklist manual di `docs/TESTING.md` tercentang |
| T-014 | Rilis, README, dan refleksi | dokumen | S | todo | docs/T-014-rilis | T-009, T-010, T-011, T-012, T-013 | Seluruh gerbang `docs/EVALS.md` lulus; `README.md` berisi cara menjalankan; `docs/LESSONS.md` diisi; PR di-review sebelum merge |

## Backlog

Kosong — seluruh fitur `F1`–`F6` sudah terjadwal dalam milestone di atas.
