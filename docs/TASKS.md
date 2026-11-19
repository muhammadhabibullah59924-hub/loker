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
| T-001 | Setup repositori, tooling, dan kerangka Laravel minimal | setup | L | done | feat/T-001-setup | - | `composer create-project laravel/laravel .` dijalankan; `.env` disalin dari `.env.example` lalu `php artisan key:generate`; dibuat satu tes contoh; `composer install`, `vendor/bin/pint --test`, `php artisan test`, dan `composer install --no-dev --optimize-autoloader` lulus; CI hijau |
| T-002 | Migrasi dan seeder tiga tabel beserta model Eloquent | setup | M | done | feat/T-002-migrasi | T-001 | Tabel `users`, `jobs`, `favorites` ter-migrasi; model `User`, `Job`, `Favorite` dibuat dengan relasi; seeder menghasilkan 1 admin, 1 user, dan 24 loker Probolinggo berkoordinat; `php artisan test` lulus |
| T-003 | Halaman depan dengan panduan tiga langkah | F2, R3 | S | done | feat/T-003-landing | T-001 | Halaman depan menampilkan logo, tagline, tombol Masuk/Daftar, dan panduan "Izinkan lokasi → lihat urut terdekat → buka detail dan peta" (R3); terbaca di layar 360 px |
| T-004 | Registrasi, masuk, dan keluar | F1 | M | done | feat/T-004-auth | T-002 | Form Daftar menyimpan akun; password salah menampilkan pesan galat; nama tampil di navbar setelah masuk; Keluar mengakhiri sesi; ada tes `RegisterLoginTest` |
| T-005 | Middleware peran dan kerangka panel admin | F4 | S | done | feat/T-005-middleware-admin | T-004 | Rute `/admin` dijaga middleware `admin`; pengguna biasa yang membuka `/admin` ditolak dan dialihkan; ada tes otorisasi |
| T-006 | Service jarak Haversine beserta unit test | F2 | S | done | feat/T-006-jarak | T-002 | `DistanceService::distanceInKm` menghasilkan jarak yang benar untuk dua titik uji; rumus tertulis sesuai `docs/CODE_STYLE.md`; unit test lulus |
| T-007 | Dasbor loker terurut terdekat dengan cadangan lokasi default | F2, R1 | L | done | feat/T-007-dasbor | T-004, T-006 | Mengizinkan lokasi membuat kartu terurut menaik dengan angka km; penolakan izin memakai urutan default pusat Probolinggo plus peringatan; tanpa pencarian/filter minimal 3 loker tampil lengkap dengan nama PT, alamat, divisi (R1); ada tes `DashboardOrderTest` |
| T-008 | Halaman detail loker dengan peta Leaflet | F3 | M | done | feat/T-008-detail-peta | T-007 | Peta termuat dengan marker pada koordinat loker; alamat sama dengan database; kegagalan memuat ubin tetap menyisakan alamat teks; ada tes `JobDetailMapTest` |
| T-009 | Form tambah, ubah, nonaktif, dan hapus loker | F4, R2 | L | done | feat/T-009-crud-admin | T-005 | Form menolak penyimpanan bila nama PT, alamat, divisi, atau koordinat kosong (R2); loker tanpa koordinat tidak tayang; menonaktifkan menyembunyikan loker tanpa menghapus data; ada tes `AdminJobCrudTest` |
| T-010 | Pencarian dan filter radius | F5 | M | done | feat/T-010-pencarian | T-007 | Pencarian nama PT/divisi memakai `q` + filter radius di query dasbor; urutan jarak tetap menaik; ada tes `SearchFilterTest` |
| T-011 | Loker favorit | F6 | M | done | feat/T-011-favorit | T-004, T-007 | Menyimpan dan melepas favorit bekerja; daftar bertahan setelah keluar lalu masuk lagi; hanya milik pengguna sendiri yang tampil; ada tes `FavoriteTest` |
| T-012 | Satu alur E2E dengan Playwright | dokumen | M | done | test/T-012-e2e | T-008 | Skrip E2E menjalankan daftar → masuk → dasbor → detail loker dan lulus; `npx playwright test` bisa dijalankan ulang |
| T-013 | Penyempurnaan tampilan dan aksesibilitas | F2 | M | done | feat/T-013-ui-aksesibilitas | T-008 | Warna, tipografi, dan komponen mengikuti `docs/DESIGN_SYSTEM.md`; navigasi keyboard berfungsi; target sentuh ≥ 24 px; checklist manual di `docs/TESTING.md` tercentang |
| T-014 | Rilis, README, dan refleksi | dokumen | S | done | docs/T-014-rilis | T-009, T-010, T-011, T-012, T-013 | Seluruh gerbang `docs/EVALS.md` lulus; `README.md` berisi cara menjalankan; `docs/LESSONS.md` diisi; PR di-review sebelum merge |
| T-015 | Loker kadaluarsa melebihi batas waktu tidak tampil di dasbor | F2, R2 | M | done | feat/T-015-loker-kadaluarsa | T-007, T-014 | Loker dengan `deadline` sudah lewat tidak tampil di dasbor; loker tanpa deadline tetap tampil; ada tes `DashboardOrderTest` |
| T-016 | Layanan lokal tidak aktif sehingga website tidak bisa dibuka | setup | S | done | fix/T-016-akses-website | - | Apache + MySQL aktif; `GET /loker/` mengembalikan 200; alur login → dasbor teruji; gerbang EVALS lulus |
| T-017 | Menambah loker baru agar dasbor lebih banyak untuk pengguna | F2 | S | done | feat/T-017-tambah-loker-baru | T-015 | Seeder ditambah loker baru dengan `deadline` masa depan; dasbor menampilkan lebih banyak loker; gerbang EVALS lulus |
| T-018 | Menambah tanggal buka-tutup loker | F4 | M | done | feat/T-018-tambah-tanggal-buka-tutup | T-017 | Loker punya rentang buka-tutup via `opens_at`/`closes_at`; form admin menyimpan; dasbor menampilkan rentang; gerbang EVALS lulus |
| T-019 | Perbaikan tampilan GitHub agar terlihat profesional | dokumen | S | done | docs/T-019-tampilan-github | - | Berkas `LICENSE` (MIT) terpasang; README berisi badge dan seksi lisensi; gerbang EVALS lulus |
| T-020 | Dasbor dan detail loker menampilkan loker populer serta info lengongan lengkap | F2, F3 | M | done | feat/T-020-loker-populer | T-007 | Section "Loker Populer" pada dasbor tampil dari loker butuh terbanyak; kartu loker menampilkan tipe dan jumlah lowongan; `LokerPopularTest` lulus, pint, phpunit 72/72, E2E lulus |
| T-021 | Perbaikan error fatal `Maximum execution time` saat login setelah lama tidak aktif | F1 | S | done | fix/T-021-perbaikan-error-login | T-004 | Batas PHP dinaikkan lewat `.htaccess` (30 -> 60 detik); error `Fatal error: Maximum execution time of 30 seconds exceeded` tidak lagi memicu fatal saat login; login kembali tanpa error; siap PR sebelum deployment |
| T-022 | Dasbor menambahkan statistik jumlah loker aktif tersedia | F2 | S | done | feat/T-022-statistik-dasbor | T-020 | Statistik dihitung dari jumlah loker aktif dan tampil di dasbor; tes `LokerPopularTest`, pint, phpunit 73/73, E2E 2/2 lulus |
| T-023 | Dasbor menambahkan banner CTA loker populer | F2 | S | done | feat/T-023-banner-cta-popular | T-022 | Banner CTA "Lihat loker populer" tampil di atas daftar popular; tes `LokerPopularTest` lulus |
| T-024 | Dasbor dan detail loker menampilkan badge Premium Employer | F2, F3 | M | done | feat/T-024-badge-premium-employer | T-023 | Loker populer bernilai `is_premium` menampilkan badge Premium Employer; tes `LokerPopularTest` lulus |
| T-025 | Dasbor menambahkan statistik jumlah loker populer | F2 | S | done | feat/T-025-statistik-loker-popular | T-024 | Statistik jumlah loker populer ditambahkan ke dasbor; tes `LokerPopularTest` lulus |
| T-026 | Desain hero section dasbor ala Glints | F2 | S | done | feat/T-026-hero-dasbor | T-025 | Hero dasbor tampil dengan eyebrow "Rekomendasi terdekat", judul, dan subjudul; tes `DashboardHeroTest`, pint, phpunit 78/78, E2E 2/2 lulus |
| T-027 | Desain kartu loker dasbor ala Glints | F2 | S | done | feat/T-027-job-card-desain | T-026 | Kartu populer badge premium + info tipe/kebutuhan dibungkus satu blok; hover card ditambah; tes `LokerPopularTest`, pint, phpunit, E2E lulus |
| T-028 | Desain halaman detail loker ala Glints | F3 | S | done | feat/T-028-detail-loker-desain | T-027 | Blok info detail dibungkus kartu; tes `LokerPopularTest`, pint, phpunit, E2E lulus |
| T-029 | Desain halaman landing ala Glints | F2 | S | done | feat/T-029-landing-desain | T-028 | Hero landing menampilkan eyebrow "Rekomendasi terdekat" sebelum judul; tes `LandingTest`, pint, phpunit, E2E lulus |
| T-030 | Filter tipe loker di dasbor | F5 | S | done | feat/T-030-filter-tipe-loker | T-029 | Parameter `tipe` menyaring loker Full-Time/Part-Time/Contract/Internship; tes `SearchFilterTest`, pint, phpunit, E2E lulus |
| T-031 | Urutkan loker di dasbor | F5 | S | done | feat/T-031-urutkan-loker | T-030 | Parameter `urut` mendukung Terdekat/Terbaru/Butuh terbanyak; tes `SearchFilterTest`, pint, phpunit, E2E lulus |
| T-032 | Chip filter divisi di dasbor | F5 | S | done | feat/T-032-chip-divisi | T-031 | Chip divisi aktif menyaring loker per divisi; tes `SearchFilterTest`, pint, phpunit, E2E lulus |
| T-033 | Desain dan lengkapi kartu favorit | F6 | S | done | feat/T-033-kartu-favorit | T-032 | Kartu favorit menampilkan tipe, kebutuhan, dan badge premium; header eyebrow "Loker tersimpan"; tes `FavoriteTest`, pint, phpunit, E2E lulus |
| T-034 | Tanggal berakhir dan pembeda tipe loker di kartu dasbor | F1, F5 | S | done | feat/T-034-kartu-deadline-tipe | T-033 | Kartu dasbor menampilkan "Batas lamaran: 30 November 2026"; Full-Time/Part-Time dibedakan lewat badge dan garis tepi kartu; chip cepat tipe; tes `LokerPopularTest`, pint, phpunit, E2E lulus |
| T-035 | Desain dan filter panel admin | F4 | M | done | feat/T-035-panel-admin | T-034 | Panel admin memakai kartu statistik (tayang, nonaktif, premium, butuh banyak orang), form filter kata kunci/status/tipe, tabel dengan tipe dan batas lamaran, tombol Lihat, serta empty state filter; tes `AdminPanelTest`, pint, phpunit, E2E lulus |
| T-036 | Redesain navbar dan halaman landing | F2 | M | done | feat/T-036-navbar-landing | T-035 | Navbar sticky dengan aksen gradien teal, logo berpanggung, status menu aktif; landing hero dua kolom dengan contoh kartu loker, chip keunggulan, dan CTA daftar di bawah; tes `LandingTest` dan `DashboardHeroTest`, pint, phpunit, E2E lulus |
| T-037 | Filter otomatis di dasbor | F5 | S | done | feat/T-037-filter-otomatis | T-036 | Pilihan tipe/radius/urut langsung diterapkan saat diubah dan kata kunci dikirim 600 ms setelah berhenti mengetik, dengan status "Menerapkan filter…"; tombol "Cari loker" tetap ada sebagai cadangan tanpa JavaScript; tes `DashboardHeroTest` dan E2E baru, pint, phpunit, E2E lulus |
| T-038 | Redesain halaman detail loker | F3 | M | done | feat/T-038-detail-loker-redesain | T-037 | Detail loker dua kolom: identitas, deskripsi, dan syarat dalam kartu terpisah; sidebar lengket berisi ringkasan loker, peta, dan aksi simpan favorit; tombol berubah "Lihat di favorit" bila sudah tersimpan; tes `JobDetailMapTest`, pint, phpunit, E2E lulus |
| T-039 | Hapus CTA ganda di halaman depan | F2 | S | done | feat/T-039-hapus-cta-landing | T-038 | Bagian "Siap cari loker di sekitarmu?" beserta tombol Daftar sekarang/Masuk di bawah panduan dihapus; tombol Daftar/Masuk di hero tetap ada; tes `LandingTest` disesuaikan, pint, phpunit, E2E lulus |
| T-040 | Tampilan halaman depan lebih profesional | F2 | M | done | feat/T-040-landing-profesional | T-039 | Hero dengan grid halus, lingkaran dekoratif, judul lebih besar bergaris teal, chip centang, kartu bertumpuk, dan ilustrasi radar CSS; pemisah antar bagian; bagian "Kenapa LokerDekat" 3 kartu; panduan dengan nomor beroutline, garis penghubung, dan hover; tes `LandingTest`, pint, phpunit, E2E lulus |
| T-041 | Redesain form tambah/ubah loker admin | F4 | M | done | feat/T-041-form-admin | T-040 | Form loker dikelompokkan jadi 3 kartu bernomor (Informasi loker, Jadwal loker, Lokasi & peta) + panel panduan "Sebelum menyimpan" di samping; field, validasi, dan peta tidak diubah; tes `AdminFormDesignTest`, pint, phpunit, E2E lulus |
| T-042 | Banner akses cepat dari HP di dasbor | F2 | S | done | feat/T-042-banner-hp | T-041 | Banner teal di atas statistik: "Buka LokerDekat di HP" + langkah izinkan lokasi dan tambahkan ke layar utama, ilustrasi telepon CSS, tombol Tutup yang mengingat pilihan di `localStorage`; tanpa tautan toko aplikasi; tes `DashboardHeroTest`, pint, phpunit, E2E lulus |
| T-043 | Halaman profil pengguna | F1 | M | done | feat/T-043-profil-pengguna | T-042 | Rute `/profil` (get/put) dengan `ProfileController` dan `ProfileRequest`: ubah nama & email tanpa duplikasi, ganti kata sandi dengan verifikasi kata sandi lama dan konfirmasi; menu Profil di navbar; halaman memakai gaya kartu form admin; tes `ProfileTest`, pint, phpunit, E2E lulus |
| T-044 | Loker serupa di halaman detail | F3 | S | done | feat/T-044-loker-serupa | T-043 | Detail menampilkan maksimal 3 loker aktif dengan divisi atau kota sama (belum kadaluarsa, punya koordinat), lengkap dengan jarak km, badge tipe, dan batas lamaran; tes `JobDetailMapTest`, pint, phpunit, E2E lulus |
| T-045 | FAQ statis di halaman depan | F2 | S | done | feat/T-045-faq-landing | T-043 | Bagian "Pertanyaan yang sering ditanyakan" dengan 3 accordion (berbayar, penyimpanan lokasi, lamaran langsung), murni teks statis tanpa database; tes `LandingTest`, pint, phpunit, E2E lulus |
| T-046 | Isian tipe, kebutuhan, dan premium di form admin | F4 | M | done | feat/T-046-form-tipe-loker | T-044 | Form loker admin bisa mengisi tipe loker, jumlah orang dibutuhkan, dan checkbox Premium Employer; `JobFormRequest` memvalidasi nilai; controller menyimpan dan bisa mematikan premium saat checkbox dikosongkan; tes `AdminFormDesignTest`, pint, phpunit, E2E lulus |

## Backlog

Kosong — seluruh fitur `F1`–`F6` sudah terjadwal dalam milestone di atas.
