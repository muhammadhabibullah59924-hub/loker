# Decisions: LokerDekat

Catatan keputusan arsitektur (Architecture Decision Records). Keputusan **tidak dibatalkan diam-diam**: untuk mengubahnya, tambahkan ADR baru yang berstatus `Supersedes ADR-nnn` dan jelaskan alasannya.

## How to Use

- Buat ADR baru saat memilih teknologi, mengubah skema, berkompromi pada keamanan, atau menambah dependensi besar.
- Setiap ADR memakai format: Status, Tanggal, Konteks, Keputusan, Konsekuensi, Alternatif yang dipertimbangkan.
- Agen wajib membaca ADR di bawah sebelum mengusulkan perubahan arsitektur.

## Decision Records

Semua ADR di bawah memakai tanggal 2026-10-25 kecuali dibuat kemudian.

### ADR-001: Platform web

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Mahasiswa memilih platform web (C-02) agar LokerDekat dibuka lewat browser tanpa instalasi, dengan fitur utama menampilkan loker terurut berdasarkan lokasi pengguna.
- **Keputusan:** Membangun aplikasi web dengan rendering di sisi server, memakai Geolocation API browser untuk lokasi dan tidak memakai platform pendukung lain (`meta.secondary_platforms` kosong).
- **Konsekuensi:** Fitur lokasi hanya bekerja pada origin aman (localhost/HTTPS); admin berada di aplikasi yang sama sehingga tidak perlu proyek terpisah.
- **Alternatif yang dipertimbangkan:** Mobile (butuh build dan distribusi APK), Desktop (terlalu berat untuk berbagi lowongan), IoT (tidak relevan dengan kebutuhan).

### ADR-002: Frontend dan UI

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Stack dipilih mahasiswa, dengan alasan yang dikutip persis: **"Sudah dipelajari di kelas."** Frontend ditetapkan sebagai Blade + Bootstrap 5 via CDN tanpa langkah build aset.
- **Keputusan:** Memakai template Laravel Blade yang dirapikan Bootstrap 5 dari CDN, warna primer `#0D9488`, tanpa mode gelap, tanpa bundler.
- **Konsekuensi:** Tidak ada `npm install`/`npm run build` sehingga deployment sederhana; penyesuaian dilakukan lewat kelas CSS tambahan; risiko yang disampaikan saat wawancara: kebutuhan bergantung pada ketersediaan CDN — disiapkan cadangan berkas lokal di `public/`.
- **Alternatif yang dipertimbangkan:** Rekomendasi kit untuk frontend web adalah Next.js dengan Tailwind CSS + shadcn/ui; dipakai sebagai pembanding saja, tanpa usulan perpindahan karena pilihan mahasiswa menang.

### ADR-003: Backend dan data

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Mahasiswa memilih Laravel dan MySQL/MariaDB bawaan XAMPP; alasan stack: "Sudah dipelajari di kelas." Model data pokok: `users`, `jobs`, dan `favorites`.
- **Keputusan:** Laravel menangani routing, validasi (Form Request), autentikasi, dan logika pengurutan jarak melalui `DistanceService`; MySQL menyimpan tiga tabel dengan `jobs.latitude`/`jobs.longitude` sebagai kunci fitur urutan terdekat.
- **Konsekuensi:** Pengurutan jarak dihitung di server sehingga konsisten; seluruh loker aktif dibaca tiap permintaan, masih wajar untuk ukuran data tugas; perlu verifikasi kompatibilitas MariaDB 10.1 XAMPP.
- **Alternatif yang dipertimbangkan:** PostgreSQL (rekomendasi kit, tidak dipakai), SQLite (terlalu terbatas untuk banyak penulis), API terpisah (menambah kompleksitas tanpa kebutuhan).

### ADR-004: Autentikasi dan keamanan

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Mahasiswa memilih login email + password tanpa verifikasi email (W-04) untuk dua peran: `user` dan `admin`.
- **Keputusan:** Memakai autentikasi bawaan Laravel dengan hash bcrypt, sesi cookie bertanda tangan, CSRF token pada semua form, middleware peran untuk seluruh rute `/admin`, dan larangan menyimpan koordinat lokasi pengguna ke basis data.
- **Konsekuensi:** Registrasi langsung aktif sehingga email bisa dipakai orang lain — risiko disadari dan dicatat; otorisasi diperiksa di server, bukan lewat penyembunyian tombol.
- **Alternatif yang dipertimbangkan:** Verifikasi email (butuh layanan email transaksional), login sosial Google (butuh kredensial OAuth), tanpa login (tidak memungkinkan favorit dan panel admin).

### ADR-005: Hosting dan deployment

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Mahasiswa memilih localhost XAMPP (W-06) untuk keperluan tugas; tidak ada kebutuhan akses publik.
- **Keputusan:** Aplikasi berjalan di Apache + MySQL XAMPP pada komputer pengembang, dibuka lewat `http://localhost/loker`; CI GitHub Actions tetap disiapkan agar setiap PR teruji.
- **Konsekuensi:** Tidak ada domain, TLS, atau cadangan otomatis; deployment produksi ditunda dan bila ada nanti perlu HTTPS karena Geolocation API menuntut origin aman.
- **Alternatif yang dipertimbangkan:** Vercel/Cloudflare (cocok untuk framework JavaScript), Render/Railway, VPS + Docker (butuh pemeliharaan), hosting kampus.

### ADR-006: Strategi pengujian dan level proses

- **Status:** Accepted
- **Tanggal:** 2026-10-25
- **Konteks:** Mahasiswa memilih level **dasar** (C-10), ukuran tim 1 orang (C-13a), dan waktu 1 minggu (C-13b); pengujian memakai PHPUnit untuk unit dan Playwright untuk satu alur E2E (W-09).
- **Keputusan:** Piramida condong ke unit test untuk logika jarak, validasi, dan otorisasi; satu alur E2E untuk jalur utama; gerbang dijalankan lokal sebelum PR lalu diulang CI; level dasar menetapkan pemeriksaan manusia setiap task.
- **Konsekuensi:** Cakupan UI tidak dipaksa 100 persen; cek manual lewat checklist di `docs/TESTING.md`; jumlah task dibatasi 14 sesuai panduan 1 minggu.
- **Alternatif yang dipertimbangkan:** Level menengah (CI wajib dan review rutin, cocok bila durasi 3–6 minggu), level loop (menambah `docs/LOOP.md`, di luar kebutuhan saat ini).

## Delegated Choices

Semua pilihan dibuat langsung oleh mahasiswa.

## Assistant Proposals

| Bidang SPEC | Nilai yang diusulkan asisten | Alasan singkat | Disetujui mahasiswa |
|---|---|---|---|
| `meta.one_liner` | Kalimat ringkas ≤ 140 karakter | Tidak ada pertanyaan tersendiri untuk tagline | ya |
| `features.acceptance` | Butir aksi–hasil teramati tiap fitur | C-04b dijawab lewat draf yang disetujui | ya |
| `features.priority` | must: F1, F2, F3, F4; should: F5; could: F6 | C-05 memakai usulan asisten | ya |
| `goals` | Dua tujuan hasil utama | C-20 dijawab lewat draf yang disetujui | ya |
| `metrics` | Dua metrik beserta cara ukur | C-20 dijawab lewat draf yang disetujui | ya |
| `non_goals` | Tanpa lamaran/CV, tanpa pembayaran/notifikasi/chat, tanpa aplikasi mobile | C-20 dijawab lewat draf yang disetujui | ya |
| `users` | Persona pencari kerja dengan kutipan berlabel ilustrasi | C-03b tidak terjawab sehingga dirumuskan asisten | ya |
| `design` | Nada ramah, primer `#0D9488`, font sistem, tanpa mode gelap | W-05 memakai usulan asisten | ya |
| `data.entities` | Tiga tabel `users`, `jobs`, `favorites` beserta field | W-03 memakai draf asisten | ya |
| `risks` | Empat risiko teknis/proyek (koordinat, MariaDB 10.1, GPS origin, durasi) | Risiko teknis diisi asisten | ya |
| `stack.custom_layers` | backend, frontend, database, auth, hosting, testing | Lapisan diuraikan dari pilihan mahasiswa | ya |

**Konflik antar kriteria yang diselesaikan saat penyusunan SPEC:**

- K1: R1 (minimal 3 loker tampil) bentrok dengan F5 (kata kunci tanpa hasil) → R1 hanya berlaku saat tidak ada pencarian atau filter aktif; ditulis ke acceptance F2.
- K2: R2 (koordinat wajib) bentrok dengan F4 (admin menambah loker) → form menolak penyimpanan bila kolom wajib kosong; loker tanpa koordinat tidak ditayangkan.
- K3: Metrics "10 loker muncul semua" bentrok dengan filter radius → dasbor tanpa radius menampilkan seluruh loker aktif; radius hanya menyaring bila dipilih.
- K4: Lokasi GPS dipakai pengurutan bentrok dengan minimisasi data → koordinat hanya dikirim saat sesi dan tidak disimpan.

## Open Questions

| Hal yang belum pasti | Perintah verifikasi | Pemilik | Kapan diputuskan |
|---|---|---|---|
| ~~Versi Laravel terbaru~~ **Terverifikasi 2026-10-25:** `laravel/framework v13.38.0`, PHP 8.3.35, MariaDB 10.1.32 | `composer show laravel/framework`, `php -v`, `mysql --version` | mahasiswa | selesai saat `T-001` |
| Kompatibilitas MariaDB 10.1 dengan Laravel 13 saat menjalankan migrasi | `php artisan migrate` pada `db_loker` | mahasiswa | saat `T-002` |
| Batas paket gratis bila kelak butuh hosting publik | cek paket terbaru di situs penyedia | mahasiswa | setelah tugas selesai |
| Penentuan koordinat pusat Probolinggo sebagai konfigurasi atau seeder | pilih salah satu lalu catat di ADR baru | mahasiswa | saat `T-007` |
| Kebutuhan verifikasi email di masa depan | putuskan bersama dosen | dosen | setelah penilaian |
