# LokerDekat

> Website info loker yang mengurutkan lowongan dari yang paling dekat ke lokasi pengguna, lengkap dengan peta dan divisi yang lowong.

## About

Anak muda di Probolinggo sering hanya ikut grup lowongan tanpa tahu harus melangkah ke mana mencari loker yang benar-benar bisa mereka datangi. LokerDekat menjawabnya dengan menampilkan lowongan **diurutkan dari yang paling dekat** ke lokasi pengguna, lengkap dengan nama PT, alamat jalan, dan divisi yang lowong. Pengguna umum cukup mendaftar, mengizinkan lokasi, lalu memilih loker; admin mengelola isi katalog lewat panel tersendiri. Website ini murni informasi lowongan — tidak ada fitur lamaran atau unggah CV.

## Features

- `F1` — Masuk dan daftar akun (must) — **direncanakan**
- `F2` — Dasbor loker terurut terdekat (must) — **direncanakan**
- `F3` — Peta dan lokasi loker (must) — **direncanakan**
- `F4` — Kelola data loker (admin) (must) — **direncanakan**
- `F5` — Pencarian dan filter loker (should) — **direncanakan**
- `F6` — Simpan loker favorit (could) — **direncanakan**

## Tech Stack

- **Laravel** — framework backend PHP
- **PHP** — bahasa pemrograman utama
- **MySQL** — basis data (bawaan XAMPP)
- **Bootstrap 5** — gaya antarmuka lewat CDN
- **Blade** — template server-side
- **Leaflet** — peta lokasi PT dengan ubin OpenStreetMap

Versi tidak ditulis di sini karena belum diverifikasi; periksa dengan `composer show -a laravel/framework` dan `mysql --version`.

## Getting Started

1. Pastikan terpasang: PHP 8.3 (atau versi yang didukung Laravel terbaru), Composer, dan XAMPP (Apache + MySQL).
2. Clone repositori ini ke folder web, misalnya `C:\xampp\htdocs\loker`.
3. Jalankan instalasi dependensi: `composer install`.
4. Salin `.env.example` menjadi `.env` lalu isi nilainya (`DB_DATABASE=db_loker_v2`, `DB_USERNAME`, `DB_PASSWORD`).
5. Jalankan persiapan lingkungan: `cp .env.example .env && php artisan key:generate` (lewati langkah salin bila `.env` sudah ada).
6. Nyalakan Apache dan MySQL lewat XAMPP Control Panel, lalu buat basis data `db_loker_v2` dengan charset `utf8` (`CREATE DATABASE db_loker_v2 CHARACTER SET utf8 COLLATE utf8_general_ci;`) — lihat ADR-008.
7. Jalankan migrasi dan seeder: `php artisan migrate --seed`, kemudian mode pengembangan: `php artisan serve` (tersedia setelah `T-001`).
8. Buka `http://localhost/loker` di browser. Berkas `index.php` dan `.htaccess` pada root proyek menjadi jembatan menuju `public/` (ADR-005) sehingga tidak perlu mengubah konfigurasi XAMPP. Bila memakai `php artisan serve`, buka `http://127.0.0.1:8000`.
9. Akun demo hasil seeder: `admin@lokerdekat.test` (admin) dan `pencari@lokerdekat.test` (pengguna), kata sandi `password`.

## Scripts and Commands

| Perintah | Fungsi |
|---|---|
| `composer install` | Memasang dependensi PHP |
| `php artisan serve` | Menjalankan server pengembangan |
| `vendor/bin/pint --test` | Memeriksa gaya kode |
| `php artisan test` | Menjalankan seluruh tes |
| `composer install --no-dev --optimize-autoloader` | Menyiapkan dependensi produksi |
| `cp .env.example .env && php artisan key:generate` | Persiapan lingkungan (sekali saat setup) |
| `npx playwright test` | Menjalankan satu alur E2E |

## Project Structure

```text
loker/
├── AGENTS.md          # pintu masuk agen AI
├── SPEC.json          # sumber kebenaran spesifikasi
├── docs/              # PRD, ARCHITECTURE, TASKS, dan dokumen lain
├── kit/               # pembangkit paket dokumen
├── app/               # controller, model, service, middleware Laravel
├── resources/views/   # template Blade
├── database/          # migrasi dan seeder
├── routes/            # definisi rute
├── public/            # berkas statis dan unggahan
└── tests/             # unit test dan alur E2E
```

## Documentation

| Dokumen | Fungsi |
|---|---|
| [AGENTS.md](AGENTS.md) | Ringkasan singkat untuk agen AI |
| [docs/PRD.md](docs/PRD.md) | Masalah, tujuan, dan acceptance criteria |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Susunan sistem dan model data |
| [docs/DESIGN_SYSTEM.md](docs/DESIGN_SYSTEM.md) | Warna, tipografi, komponen |
| [docs/SECURITY.md](docs/SECURITY.md) | Ancaman dan OWASP 2025 |
| [docs/CODE_STYLE.md](docs/CODE_STYLE.md) | Konvensi penamaan dan commit |
| [docs/TESTING.md](docs/TESTING.md) | Strategi tes dan checklist manual |
| [docs/EVALS.md](docs/EVALS.md) | Gerbang kelayakan lulus |
| [docs/WORKFLOW.md](docs/WORKFLOW.md) | Siklus kerja dan aturan cabang |
| [docs/GUARDRAILS.md](docs/GUARDRAILS.md) | Batas mutlak |
| [docs/TASKS.md](docs/TASKS.md) | Daftar task dan milestone |
| [docs/PROGRESS.md](docs/PROGRESS.md) | Keadaan terakhir proyek |
| [docs/DECISIONS.md](docs/DECISIONS.md) | ADR dan alasan keputusan |
| [docs/REVIEW.md](docs/REVIEW.md) | Checklist review dan template PR |
| [docs/LESSONS.md](docs/LESSONS.md) | Kesalahan berulang dan pelajaran |

## Contributing

Ikuti `docs/WORKFLOW.md`: satu task per cabang, PR kecil, CI hijau, dan review manusia sebelum merge. Pesan commit mengikuti `docs/CODE_STYLE.md` (Conventional Commits). Agen AI bekerja mengikuti `AGENTS.md` dan `docs/GUARDRAILS.md`.

## AI Usage Disclosure

Proyek ini disusun dengan bantuan alat AI, yaitu **GitHub Copilot**, termasuk pembuatan paket dokumen awal yang digenerate dari `SPEC.json`. Seluruh perubahan ditinjau dan dipahami oleh penulis sebelum diterima, dan dokumen dihasilkan dari satu sumber kebenaran berupa `SPEC.json` yang dikonfirmasi saat wawancara.

## Status

Implementasi inti LokerDekat sudah berjalan menyusul F1–F6, dan seluruh gerbang `docs/EVALS.md` lulus secara lokal; tahap terakhir menuai review manusia sebelum PR ke `main`. Lihat [docs/PROGRESS.md](docs/PROGRESS.md) dan [docs/EVALS.md](docs/EVALS.md).
