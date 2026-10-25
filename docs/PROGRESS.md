# Progress: LokerDekat

Dokumen hidup. Diperbarui di **akhir setiap sesi** supaya sesi berikutnya (oleh agen atau manusia) dapat langsung melanjutkan.

## Current State

Tanggal: 2026-10-25

Sesudah `T-001`, kerangka Laravel 13.38.0 terpasang di folder proyek, repositori Git sudah ada (commit awal `main`), dan seluruh perintah `stack.commands` lulus. `T-002` juga selesai: basis data `db_loker_v2` (charset `utf8`, ADR-008) berisi tabel `users`, `jobs`, `favorites`, model Eloquent dengan relasi, serta seeder 1 admin, 1 pengguna, dan 24 loker Probolinggo berkoordinat. `QUEUE_CONNECTION=sync` dan penghapusan migrasi antrean bawaan tercatat di ADR-007. Tiga migrasi bawaan + dua migrasi aplikasi sudah dijalankan, `php artisan test` lulus 2/2, dan `vendor/bin/pint --test` bersih. `T-003` juga selesai: halaman depan berisi logo, tagline, tombol Masuk/Daftar, dan panduan tiga langkah (R3) di atas layout Bootstrap 5.3.8 CDN; jembatan `index.php` + `.htaccess` di root membuat `http://localhost/loker/` berfungsi lewat Apache (ADR-005) dan berkas sensitif terjawab 404. `T-004` selesai: rute `/register`, `/login`, `/logout` dengan Form Request berpesan bahasa Indonesia, navbar menampilkan nama pengguna dan tombol Keluar, limiter 5 percobaan masuk per menit (OWASP A07), dan 13 tes lulus. Yang belum: CI hijau menunggu remote GitHub (mahasiswa push sendiri), cek tampilan 360 px masih manual, dan status `T-001`–`T-004` masih `review` menunggu review manusia.

## Completed

| Tanggal | Task | PR/Commit | Catatan |
|---|---|---|---|
| 2026-10-25 | T-001 Setup repositori, tooling, kerangka Laravel | commit awal `main` | Laravel 13.38.0 + PHP 8.3.35; pint, phpunit (2 tes), dan build lulus |
| 2026-10-25 | T-002 Migrasi dan seeder tiga tabel + model Eloquent | branch `feat/T-002-migrasi` | `db_loker_v2` (utf8), 2 user + 24 loker terseeder; relasi teruji; pint + tes lulus |
| 2026-10-25 | T-003 Halaman depan dengan panduan tiga langkah | branch `feat/T-003-landing` | Landing + layout Bootstrap 5.3.8 CDN, jembatan `index.php`/`.htaccess` agar `/loker` jalan; 5 tes lulus |
| 2026-10-25 | T-004 Registrasi, masuk, dan keluar | branch `feat/T-004-auth` | FormRequest, AuthController, limiter 5×/menit; 13 tes lulus; alur login→navbar→logout diuji lewat Apache |

## In Progress

Tidak ada

## Next Up

1. `T-005` — Middleware peran dan kerangka panel admin.
2. `T-006` — Service jarak Haversine beserta unit test.
3. `T-007` — Dasbor loker terurut terdekat dengan cadangan lokasi default.

## Blockers

Tidak ada. Format entri nanti: `tanggal | task | hambatan | yang sudah dicoba | siapa yang bisa membantu`.

## Session Log

Format entri: `tanggal | siapa (manusia atau nama alat) | task | hasil | catatan untuk sesi berikutnya`.

`2026-10-25 | asisten penyusun dokumen | - | 16 dokumen proyek dan pendukung git selesai dihasilkan dari SPEC.json | mulai dari T-001, jangan lupa buat repo Git dan database db_loker sebelum menjalankan migrasi`

`2026-10-25 | agen coding (T-001) | T-001 | Laravel 13.38.0 terpasang, ci_setup dijalankan, pint lulus, 2 tes lulus, build lulus; repositori Git diinisialisasi | status T-001 = review: CI hijau belum diverifikasi karena belum ada remote GitHub`

`2026-10-25 | agen coding (T-002) | T-002 | Migrasi gagal pertama karena batas indeks 767 byte MariaDB 10.1, lalu dibuat db_loker_v2 charset utf8 (ADR-008); antrean bawaan dihapus dan QUEUE_CONNECTION=sync (ADR-007); 5 migrasi jalan, seeder 2 user + 24 loker sukses, relasi teruji, pint dan 2 tes lulus | status T-002 = review; mahasiswa memilih push GitHub sendiri`

`2026-10-25 | agen coding (T-003) | T-003 | Landing page + layout Blade dengan Bootstrap 5.3.8 CDN, logo SVG, panduan 3 langkah R3; jembatan index.php + .htaccess dibuat karena Apache menyajikan daftar folder di /loker; diuji lewat Apache: halaman 200, aset 200, .env/vendor/docs 404; 5 tes lulus dan pint bersih | status T-003 = review; cek tampilan 360 px masih manual (checklist TESTING); lanjut T-004 (registrasi, masuk, keluar)`

`2026-10-25 | agen coding (T-004) | T-004 | RegisterRequest + LoginRequest (pesan galat Indonesia), AuthController daftar/masuk/keluar, navbar menampilkan nama + Keluar, limiter login 5 percobaan per menit (A07) di AppServiceProvider; 13 tes lulus, pint bersih, validasi kit 21 PASS; diuji lewat Apache dengan akun seeder: login 302, navbar tampil, logout kembali tamu, GET /logout = 405 | status T-004 = review; catatan: render halaman galat 405 lambat (~5 dtk) dan pernah sekali kena timeout 30 dtk saat kompilasi view pertama, belum merusak fungsi; lanjut T-005 (middleware admin) atau T-006 (DistanceService)`
