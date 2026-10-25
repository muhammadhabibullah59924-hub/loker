# AGENTS.md: LokerDekat

> Website info loker yang mengurutkan lowongan dari yang paling dekat ke lokasi pengguna, lengkap dengan peta dan divisi yang lowong.

File ini dibaca agen AI di setiap sesi. Jaga tetap singkat (maksimal 150 baris); detail ada di `docs/`.

## Project

LokerDekat adalah aplikasi **web** (PHP/Laravel) berisi informasi lowongan kerja yang diurutkan dari yang terdekat ke lokasi pengguna, tanpa fitur lamaran. Penggunanya pencari kerja umum dan pengelola/admin yang memasukkan data loker. Level proses **dasar**: satu pengembang, satu minggu, gerbang dijalankan lokal sebelum PR.

## Read First

- `docs/PRD.md` — masalah, tujuan, dan acceptance criteria fitur F1–F6.
- `docs/ARCHITECTURE.md` — susunan sistem, model data, dan alur data.
- `docs/TASKS.md` — daftar task beserta statusnya; pilih satu yang `todo`.
- `docs/PROGRESS.md` — keadaan terakhir: selesai, berikutnya, hambatan.
- `docs/WORKFLOW.md` — siklus kerja, aturan cabang, dan ritual sesi.
- `docs/GUARDRAILS.md` — batas mutlak yang tidak boleh dilanggar.
- `docs/EVALS.md` — gerbang yang harus lulus sebelum menyatakan selesai.
- `docs/CODE_STYLE.md` — konvensi penamaan, commit, dan larangan pola kode.
- `docs/SECURITY.md` — ancaman, otorisasi peran, dan OWASP 2025.
- `docs/TESTING.md` — strategi tes, perintah, dan Definition of Done.
- `docs/DESIGN_SYSTEM.md` — warna, tipografi, komponen, aksesibilitas.
- `docs/DECISIONS.md` — ADR dan alasan keputusan.
- `docs/REVIEW.md` — pertanyaan pemahaman yang dijawab manusia.
- `docs/LESSONS.md` — kesalahan yang pernah terulang.

Muat dokumen saat dibutuhkan; jangan membaca semuanya jika tidak perlu, kecuali PROGRESS.md, TASKS.md, dan GUARDRAILS.md di awal sesi.

## Tech Stack

- Frontend/UI: Blade + Bootstrap 5 via CDN (latest stable; verifikasi `npm view bootstrap version`)
- Backend: Laravel + PHP (latest stable; verifikasi `composer show -a laravel/framework`)
- Database: MySQL/MariaDB XAMPP (latest stable; verifikasi `mysql --version`)
- Auth: email + password, hash bcrypt bawaan Laravel, tanpa verifikasi email
- Hosting: localhost XAMPP (Apache + MySQL)
- Testing: PHPUnit (unit) + Playwright (satu alur E2E)
- Extra: Leaflet + OpenStreetMap untuk peta lokasi; penyimpanan file lokal `public/uploads` untuk logo

Stack dipilih mahasiswa: jangan mengusulkan penggantian stack; ikuti konvensi resmi teknologi ini.

## Commands

Perintah ini berasal dari `SPEC.json` (`stack.commands`) dan identik dengan `docs/TESTING.md`, `docs/EVALS.md`, dan CI.

```bash
composer install
vendor/bin/pint --test
php artisan test
composer install --no-dev --optimize-autoloader
```

- `composer install` — memasang dependensi PHP.
- `vendor/bin/pint --test` — memeriksa gaya kode tanpa menulis ulang berkas.
- `php artisan test` — menjalankan seluruh tes PHPUnit.
- `composer install --no-dev --optimize-autoloader` — menyiapkan dependensi produksi.
- Dev: `php artisan serve` (tersedia setelah T-001).
- Typecheck: tidak dipakai.
- Ci setup: `cp .env.example .env && php artisan key:generate` (juga dipakai sekali saat setup lokal).

## Conventions

- Ikuti `docs/DESIGN_SYSTEM.md`: primer `#0D9488`, teks 16 px, jarak kelipatan 4 px, tanpa mode gelap.
- Penamaan mengikuti `docs/CODE_STYLE.md`: `StudlyCaps` untuk kelas, `camelCase` untuk fungsi, `snake_case` untuk kolom basis data.
- Bentuk kartu loker selalu memuat nama PT, alamat jalan, divisi, dan jarak km — jangan ubah urutan isinya.
- Validasi lewat Form Request; otorisasi lewat middleware, bukan lewat penyembunyian tombol.
- Semua rute admin berada di prefix `/admin` dan dijaga middleware peran `admin`.
- Logika jarak berada di `app/Services/DistanceService.php`, bukan di controller atau view.
- Pesan galat berbahasa Indonesia dan selalu menyebut langkah berikutnya.
- Koordinat lokasi pengguna tidak boleh disimpan ke basis data.

## Workflow Rules

Kerjakan **satu task** per sesi dari `docs/TASKS.md`; mulai dari branch baru (`feat/T-nnn-nama`); jalankan gerbang di `docs/EVALS.md` sebelum menyatakan selesai; perbarui `docs/PROGRESS.md` di akhir sesi; catat keputusan di `docs/DECISIONS.md` dan kesalahan yang dipelajari di `docs/LESSONS.md`; berhenti dan minta review manusia sebelum merge.

## Boundaries

### Always

- Baca `docs/PROGRESS.md`, `docs/TASKS.md`, dan `docs/GUARDRAILS.md` sebelum memulai.
- Jalankan `vendor/bin/pint --test` dan `php artisan test` sebelum menganggap pekerjaan selesai.
- Commit kecil dengan pesan Conventional Commits dan rujukan ID task.
- Ikuti ID fitur `F1`–`F6` dan kendali `R1`–`R3` persis seperti tertulis di `docs/PRD.md`.
- Perbarui `docs/PROGRESS.md` setiap akhir sesi.

### Ask First

- Menambah dependensi Composer baru atau paket CDN.
- Mengubah skema data, menulis migrasi, atau mengubah seeder.
- Mengubah arsitektur atau mengganti keputusan ADR di `docs/DECISIONS.md`.
- Mengubah `.github/workflows/ci.yml` atau melemahkan gerbang.
- Menghapus berkas/fitur atau memakai layanan berbayar.

### Never

- Commit atau push langsung ke `main`, force push, atau `git reset --hard`.
- Menaruh, mencetak, atau mengubah rahasia (`APP_KEY`, `DB_PASSWORD`, `.env`).
- Menonaktifkan, menghapus, atau melemahkan tes/lint agar lulus.
- Mengarang API, versi paket, hasil tes, atau angka statistik.
- Menjalankan perintah destruktif (`migrate:fresh`, hapus tabel/data) tanpa konfirmasi.
- Menampilkan loker nonaktif di dasbor atau menayangkan loker tanpa koordinat (R2).
- Menyimpan data pribadi pengguna di luar yang tercantum di `SPEC.json`.

## Definition of Done

1. Acceptance criteria fitur di `docs/PRD.md` terpenuhi dan dapat didemonstrasikan.
2. Tes baru ditulis dan seluruhnya lulus dengan `php artisan test`.
3. Lint bersih (`vendor/bin/pint --test`) dan build berhasil.
4. Tidak ada rahasia di diff dan `.env` tidak ikut ter-commit.
5. `docs/PROGRESS.md` diperbarui, PR dibuka, dan review manusia dilakukan.
