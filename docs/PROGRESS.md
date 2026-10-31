# Progress: LokerDekat

Dokumen hidup. Diperbarui di **akhir setiap sesi** supaya sesi berikutnya (oleh agen atau manusia) dapat langsung melanjutkan.

## Current State

Tanggal: 2026-10-31

Sesudah `T-001`, kerangka Laravel 13.38.0 terpasang di folder proyek, repositori Git sudah ada (commit awal `main`), dan seluruh perintah `stack.commands` lulus. `T-002` juga selesai: basis data `db_loker_v2` (charset `utf8`, ADR-008) berisi tabel `users`, `jobs`, `favorites`, model Eloquent dengan relasi, serta seeder 1 admin, 1 pengguna, dan 24 loker Probolinggo berkoordinat. `QUEUE_CONNECTION=sync` dan penghapusan migrasi antrean bawaan tercatat di ADR-007. Tiga migrasi bawaan + dua migrasi aplikasi sudah dijalankan, `php artisan test` lulus 2/2, dan `vendor/bin/pint --test` bersih. `T-003` juga selesai: halaman depan berisi logo, tagline, tombol Masuk/Daftar, dan panduan tiga langkah (R3) di atas layout Bootstrap 5.3.8 CDN; jembatan `index.php` + `.htaccess` di root membuat `http://localhost/loker/` berfungsi lewat Apache (ADR-005) dan berkas sensitif terjawab 404. `T-004` selesai: rute `/register`, `/login`, `/logout` dengan Form Request berpesan bahasa Indonesia, navbar menampilkan nama pengguna dan tombol Keluar, limiter 5 percobaan masuk per menit (OWASP A07), dan 13 tes lulus. `T-006` selesai: `DistanceService::distanceInKm` sesuai contoh di `docs/CODE_STYLE.md` beserta 5 unit test. `T-007` selesai: rute `/dasbor` dijaga middleware `auth`, kartu diurut menaik dengan angka km, Geolocation API meminta izin saat halaman dibuka, penolakan izin jatuh ke urutan pusat Probolinggo dari `config/loker.php` (ADR-009), dan redirect setelah masuk/daftar kini menuju dasbor. `T-005` selesai: alias middleware `admin` dijaga di server untuk seluruh prefix `/admin`, pengguna biasa ditolak dan dialihkan ke dasbor dengan peringatan, dan kerangka `PanelController` + `admin.panel` berisi ringkasan jumlah loker. `T-008` selesai: rute `/lowongan/{id}` hanya menerima loker tayang (sisanya 404), alamat ditulis sebagai teks biasa sehingga tetap terbaca bila ubin gagal dimuat, peta memakai Leaflet 1.9.4 dari jsDelivr + ubin OpenStreetMap, dan kartu dasbor kini menautkan ke halaman detail. `T-009` selesai: form tambah/ubah dijaga `JobFormRequest` (otorisasi admin + aturan R2 + pesan galat Indonesia), panel memuat tabel loker dengan aksi Ubah/Nonaktifkan/Hapus berkonfirmasi, dan pemilih titik Leaflet mengisi kolom lintang/bujur. `T-010` selesai: dasbor sekarang mendukung kata kunci nama PT/divisi (`q`) dan filter radius (km), tetap menjaga urutan jarak menaik, serta menampilkan pesan kosong sesuai kondisi pencarian. `T-011` selesai: rute daftar/save/hapus favorit milik pengguna, kartu daftar favorit, tombol simpan favorit, dan tautan navbar Favorit sudah jalan. `T-013` selesai: penyesuaian DESIGN_SYSTEM (heading global 32/24/18 px, badge divisi token sekunder, alert token warna, auto-dismiss alert) dengan pint bersih, 62 tes lulus. Total tes saat ini 62, seluruh gerbang hijau, data uji sudah bersih. `T-014` juga selesai: seluruh gerbang EVALS lulus (8 tes otomatis + R3 manual), README diperbarui status tahap, LESSONS diisi; pint, phpunit 66/66, validator 21 PASS, dan E2E Playwright lulus. Yang belum: CI hijau menunggu remote GitHub (mahasiswa push sendiri; cabang `docs/T-014-rilis` belum ter-push), cek tampilan 360 px masih manual, dan status `T-001`–`T-014` masih `review` menunggu review manusia.

## Completed

| Tanggal | Task | PR/Commit | Catatan |
|---|---|---|---|
| 2026-10-25 | T-001 Setup repositori, tooling, kerangka Laravel | commit awal `main` | Laravel 13.38.0 + PHP 8.3.35; pint, phpunit (2 tes), dan build lulus |
| 2026-10-25 | T-002 Migrasi dan seeder tiga tabel + model Eloquent | branch `feat/T-002-migrasi` | `db_loker_v2` (utf8), 2 user + 24 loker terseeder; relasi teruji; pint + tes lulus |
| 2026-10-25 | T-003 Halaman depan dengan panduan tiga langkah | branch `feat/T-003-landing` | Landing + layout Bootstrap 5.3.8 CDN, jembatan `index.php`/`.htaccess` agar `/loker` jalan; 5 tes lulus |
| 2026-10-25 | T-004 Registrasi, masuk, dan keluar | branch `feat/T-004-auth` | FormRequest, AuthController, limiter 5×/menit; 13 tes lulus; alur login→navbar→logout diuji lewat Apache |
| 2026-10-26 | T-006 Service jarak Haversine + unit test | branch `feat/T-006-jarak` | `DistanceService::distanceInKm` (acos, radius 6371 km, bulat 1 desimal); 5 unit test; total 18 tes lulus |
| 2026-10-26 | T-007 Dasbor loker terurut + cadangan lokasi default | branch `feat/T-007-dasbor` | Rute `/dasbor` dijaga `auth`, GPS via Geolocation API, fallback `config/loker.php` (ADR-009), 24 kartu terurut; 24 tes lulus |
| 2026-10-26 | T-005 Middleware peran dan kerangka panel admin | branch `feat/T-005-middleware-admin` | Alias `admin` + `EnsureUserIsAdmin`, rute `/admin` + `PanelController`, peringatan tampil di layout; 28 tes lulus |
| 2026-10-26 | T-008 Halaman detail loker dengan peta Leaflet | branch `feat/T-008-detail-peta` | Rute `/lowongan/{id}` (auth), `JobDetailController` hanya loker tayang, Leaflet 1.9.4 CDN + ubin OSM; 36 tes lulus |
| 2026-10-26 | T-009 Form tambah, ubah, nonaktif, dan hapus loker | branch `feat/T-009-crud-admin` | `JobFormRequest` (R2 + pesan Indonesia), `Admin\JobController`, panel berisi tabel + aksi, form dengan pemilih titik Leaflet; 47 tes lulus |
| 2026-10-26 | T-010 Pencarian dan filter radius | branch `feat/T-010-pencarian` | Pencarian `q` nama PT/divisi + radius di dasbor; urutan jarak tetap terjaga; 55 tes lulus |
| 2026-10-26 | T-011 Loker favorit | branch `feat/T-011-favorit` | Rute daftar/save/hapus favorit, kartu daftar favorit, tombol simpan favorit, tes FavoriteTest; 62 tes lulus |
| 2026-10-26 | T-012 Alur E2E Playwright | branch `test/T-012-e2e` | `npx playwright test` lulus (daftar → masuk → dasbor → detail); @playwright/test + chromium terpasang |
| 2026-10-26 | T-013 Penyempurnaan tampilan dan aksesibilitas | branch `feat/T-013-ui-aksesibilitas` | Heading global 32/24/18 px, badge divisi token sekunder, alert token warna, auto-dismiss alert; pint bersih, 62 tes lulus |
| 2026-10-26 | T-014 Rilis, README, dan refleksi | branch `docs/T-014-rilis` | Seluruh gerbang EVALS lulus (8 tes otomatis + R3 manual), README diperbarui status, LESSONS diisi; pint, phpunit 66/66, validator 21 PASS, E2E Playwright lulus |

## In Progress

Tidak ada

## Next Up

Tidak ada task aktif. Tahap berikutnya adalah review manusia sebelum PR ke `main` (lihat `docs/WORKFLOW.md`).

## Blockers

Tidak ada. Format entri nanti: `tanggal | task | hambatan | yang sudah dicoba | siapa yang bisa membantu`.

## Session Log

Format entri: `tanggal | siapa (manusia atau nama alat) | task | hasil | catatan untuk sesi berikutnya`.

`2026-10-25 | asisten penyusun dokumen | - | 16 dokumen proyek dan pendukung git selesai dihasilkan dari SPEC.json | mulai dari T-001, jangan lupa buat repo Git dan database db_loker sebelum menjalankan migrasi`

`2026-10-25 | agen coding (T-001) | T-001 | Laravel 13.38.0 terpasang, ci_setup dijalankan, pint lulus, 2 tes lulus, build lulus; repositori Git diinisialisasi | status T-001 = review: CI hijau belum diverifikasi karena belum ada remote GitHub`

`2026-10-25 | agen coding (T-002) | T-002 | Migrasi gagal pertama karena batas indeks 767 byte MariaDB 10.1, lalu dibuat db_loker_v2 charset utf8 (ADR-008); antrean bawaan dihapus dan QUEUE_CONNECTION=sync (ADR-007); 5 migrasi jalan, seeder 2 user + 24 loker sukses, relasi teruji, pint dan 2 tes lulus | status T-002 = review; mahasiswa memilih push GitHub sendiri`

`2026-10-25 | agen coding (T-003) | T-003 | Landing page + layout Blade dengan Bootstrap 5.3.8 CDN, logo SVG, panduan 3 langkah R3; jembatan index.php + .htaccess dibuat karena Apache menyajikan daftar folder di /loker; diuji lewat Apache: halaman 200, aset 200, .env/vendor/docs 404; 5 tes lulus dan pint bersih | status T-003 = review; cek tampilan 360 px masih manual (checklist TESTING); lanjut T-004 (registrasi, masuk, keluar)`

`2026-10-25 | agen coding (T-004) | T-004 | RegisterRequest + LoginRequest (pesan galat Indonesia), AuthController daftar/masuk/keluar, navbar menampilkan nama + Keluar, limiter login 5 percobaan per menit (A07) di AppServiceProvider; 13 tes lulus, pint bersih, validasi kit 21 PASS; diuji lewat Apache dengan akun seeder: login 302, navbar tampil, logout kembali tamu, GET /logout = 405 | status T-004 = review; catatan: render halaman galat 405 lambat (~5 dtk) dan pernah sekali kena timeout 30 dtk saat kompilasi view pertama, belum merusak fungsi; lanjut T-005 (middleware admin) atau T-006 (DistanceService)`

`2026-10-26 | agen coding (T-006) | T-006 | DistanceService::distanceInKm dibuat persis contoh CODE_STYLE (acos, radius 6371 km, bulat 1 desimal) plus pembatasan argumen acos ke [-1,1] agar tidak NAN saat titik berimpit; 5 unit test (London-Paris 343,5 km, Probolinggo-Surabaya 72 km, simetris, nol, pembulatan); total 18 tes lulus dan pint bersih | status T-006 = review; T-003 dan T-004 ternyata belum ter-push (hanya T-001 dan T-002 yang ada di remote); lanjut T-005 (middleware admin) lalu T-007 (dasbor urut jarak)`

`2026-10-26 | agen coding (T-007) | T-007 | Route /dasbor (auth) + DashboardController + DashboardRequest (koordinat tidak valid jatuh ke default, bukan halaman galat), kartu sesuai urutan wajib nama PT-alamat-divisi-jarak dengan angka km di sudut kanan atas, Geolocation API minta izin saat dibuka dan redirect bila disetujui, peringatan bila ditolak; pusat Probolinggo disimpan di config/loker.php (ADR-009); redirect sesi setelah masuk/daftar kini ke /dasbor | status T-007 = review; teruji lewat Apache: 24 kartu urut 0,2-0,5-0,8 km, tamu dialihkan ke /login; 24 tes lulus, pint bersih, validator 21 PASS; lanjut T-005 (middleware admin)`

`2026-10-26 | agen coding (T-005) | T-005 | Middleware EnsureUserIsAdmin didaftarkan sebagai alias admin di bootstrap/app.php; grup rute prefix /admin dibungkus ['auth','admin'] dengan PanelController + view admin.panel (ringkasan jumlah loker aktif/nonaktif); pengguna biasa diarahkan ke /dasbor dengan session('peringatan') yang dirender layout, tautan Panel Admin hanya muncul untuk peran admin | status T-005 = review; teruji lewat Apache: tamu→/login, pencari kerja→/dasbor + peringatan, admin→200 "Panel admin" 24 tayang; 28 tes lulus (90 asersi), pint bersih; lanjut T-008 (detail + peta Leaflet)`

`2026-10-26 | agen coding (T-008) | T-008 | Route GET /lowongan/{id} (auth, whereNumber) + JobDetailController yang hanya mengembalikan loker is_active (id asing/nonaktif → 404); view lowongan.blade.php: alamat sebagai teks biasa + noscript, peta Leaflet 1.9.4 dari jsDelivr (diverifikasi npm view leaflet version) dengan ubin OpenStreetMap dan marker yang memakai document.createTextNode; layout diberi yield styles; judul kartu dasbor jadi tautan ke detail | status T-008 = review; CDN Leaflet dan ubin OSM terjangkau (HTTP 200); teruji lewat Apache: detail 200 dengan data koordinat, /lowongan/99999 dan /lowongan/bukan-angka → 404, tamu → /login; 36 tes lulus (108 asersi), pint bersih; lanjut T-009 (CRUD admin)`

`2026-10-26 | agen coding (T-009) | T-009 | JobFormRequest (authorize admin, aturan wajib nama PT/alamat/divisi/kota/koordinat sesuai R2, messages + attributes berbahasa Indonesia); Admin\JobController: store/edit/update/toggle/destroy dengan pesan status berbahasa Indonesia; rute CRUD di grup /admin dengan whereNumber dan deklarasi loker/create sebelum pemetaan {loker}; panel berisi ringkasan + tabel + tombol aksi (konfirmasi hapus lewat atribut data, bukan inline confirm); form memakai pemilih titik Leaflet yang mengisi kolom lintang/bujur | status T-009 = review; teruji lewat Apache: POST valid → 25 loker, POST tanpa koordinat → ditolak dengan pesan "Titik lintang wajib diisi agar loker tampil di dasbor.", nonaktif → detail 404 dan hilang dari dasbor, hapus → kembali 24, pencari → /dasbor, tamu → /login; data uji dibersihkan; 47 tes lulus (149 asersi), pint bersih; lanjut T-010 (pencarian)`

`2026-10-26 | agen coding (T-010) | T-010 | Parameter pencarian `q` (nama PT/divisi) dan filter `radius` ditambahkan pada DashboardRequest (q dibuang bila kosong, radius bila tidak valid); controller memfilter berdasarkan kata kunci lalu membatasi radius memakai jarak yang sudah dihitung, urutan tetap menaik; view menambahkan form GET cari + radius + hidden lat/lng bila ada lokasi, serta pesan kosong berbeda bila hasil kosong | status T-010 = review; teruji lewat Apache: q=Probowangi → 1 hasil, q=alpha → nilai input tersimpan, q=zzzx → pesan kosong, radius=5 → info radius tampil; 55 tes lulus (177 asersi), pint bersih; lanjut T-011 (favorit)`

`2026-10-26 | agen coding (T-011) | T-011 | Route GET/POST/DELETE /favorit (auth, whereNumber) + FavoriteController (index daftar milik user, store dengan cek is_active + idempoten, destroy); relasi Favorite memakai job() (tidak loker); view favorit dengan kartu + aksi batal favorit; tombol "Simpan favorit" di kartu dasbor; navbar menampilkan tautan Favorit; tes FavoriteTest (simpan/melepas, idempoten, bertahan setelah logout/login, hanya milik sendiri, loker nonaktif, aksi favorit di kartu) | status T-011 = review; teruji lewat Apache: simpan → 302 /favorit, daftar → tampil job, hapus → 302, daftar → empty, tamu → /login; 62 tes lulus (191 asersi), pint bersih; lanjut T-012 (E2E Playwright)`

`2026-10-26 | agen coding (T-012) | T-012 | `npx playwright test` lulus untuk satu skrip E2E (daftar → masuk → dasbor → detail loker); `playwright.config.ts` memakai webServer `php artisan serve` dan baseURL 127.0.0.1:8000; `@playwright/test` + chromium terpasang via npm; geolocation diberi lewat context | status T-012 = review; lanjut T-013 (penyempurnaan tampilan/aksesibilitas)`

`2026-10-26 | agen coding (T-013) | T-013 | Penyesuaian DESIGN_SYSTEM: heading global h1 32/h2 24/h3 18 px, heading bagian mengganti h4/h5 ke h2/h3, badge divisi token sekunder, alert token warna, auto-dismiss alert 5 dtk | status T-013 = review; lanjut T-014 (rilis)`

`2026-10-26 | agen coding (T-014) | T-014 | Membuat tes eval yang hilang (`DashboardNearestVisibleTest` R1, `JobRequiredFieldsTest` R2) agar seluruh perintah di `docs/EVALS.md` bisa lulus; membarui README status tahap, `docs/LESSONS.md`, `docs/TASKS.md`, `docs/PROGRESS.md` | status T-014 = review; pint, phpunit 66/66, validator 21 PASS, E2E Playwright lulus; lanjut review manusia sebelum PR ke main`
