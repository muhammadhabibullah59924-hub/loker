# Deploy LokerDekat ke Hosting Publik (InfinityFree)

Panduan ini untuk membuat website bisa dibuka dari laptop lain atau HP lewat internet.
Semua langkah memakai **panel website hosting** (bukan XAMPP). Anda tidak perlu menginstal
ap pun di laptop — cukup browser.

> **Hosting yang dipilih:** [InfinityFree](https://infinityfree.net/) — gratis selamanya,
> tanpa kartu kredit, PHP 8.4, MySQL 8.0/MariaDB 11.4, dan SSL gratis.
> Verifikasi dari situs resminya: https://infinityfree.net/
>
> **Alamat tujuan:** `https://lokerdekat.rf.gd/loker/`
> (kalau nama `lokerdekat` sudah dipakai orang lain, pilih nama lain yang tersedia dan
> sesuaikan `APP_URL` di `.env` seperti di langkah 6).

## File yang sudah disiapkan

| File | Lokasi di laptop | Fungsi |
|---|---|---|
| `lokerdekat.zip` | `storage/app/deploy/lokerdekat.zip` | Seluruh aplikasi + `vendor/` produksi, siap di-upload |
| `lokerdekat.sql` | `storage/app/deploy/lokerdekat.sql` | Isi database: 8 tabel, 260 loker, akun demo |
| `.env` | `storage/app/deploy/.env` | Konfigurasi produksi, `APP_KEY` sudah terisi |

**Jangan** meng-upload `.env` dari folder utama proyek (itu untuk XAMPP/localhost),
dan **jangan** membagikan password database kepada siapa pun.

---

## Langkah 1 — Buat akun hosting

1. Buka https://infinityfree.net/ lalu klik **Create Free Account**.
2. Isi alamat email Anda, lalu verifikasi email (klik tautan yang dikirim).
3. Setelah masuk ke panel, buat/add website baru. Saat ditanya subdomain:
   - pilih nama **`lokerdekat`**
   - pilih ekstensi yang tersedia, misalnya **`.rf.gd`**
   - hasil alamat: `https://lokerdekat.rf.gd`
4. Tunggu sampai akun/hosting aktif (biasanya beberapa menit).

## Langkah 2 — Aktifkan SSL gratis (wajib)

Tanpa HTTPS, browser menolak izin lokasi di HP, sehingga fitur "loker terdekat"
tidak bekerja.

1. Di panel hosting, cari menu **SSL Certificates** / **Free SSL**.
2. Pilih domain `lokerdekat.rf.gd`, lalu aktifkan / request certificate.
3. Tunggu sampai status aktif. Coba buka `https://lokerdekat.rf.gd` — harus tersaji (bukan peringatan certificate).

## Langkah 3 — Buat database MySQL

1. Di panel hosting, buka **MySQL Databases** lalu buat database baru.
2. Buat juga **user database** dan passwordnya, lalu hubungkan user itu ke database.
3. Catat empat nilai ini (untuk langkah 6):
   - **DB_HOST** (misalnya `sql###.infinityfree.com` atau sesuai yang tertera di panel)
   - **DB_DATABASE**
   - **DB_USERNAME**
   - **DB_PASSWORD**

Keempat nilai ini rahasia — isi sendiri di panel, jangan diketik di chat atau repo.

## Langkah 4 — Upload aplikasi

1. Buka **File Manager** di panel hosting, masuk ke folder **`htdocs`**.
2. Buat folder baru bernama **`loker`** (huruf kecil semua).
   > Nama folder ini harus persis `loker`, karena aturan di `.htaccess` sudah diset untuk folder itu.
3. Masuk ke folder `loker`, lalu upload `storage/app/deploy/lokerdekat.zip` dari laptop Anda.
4. Extract zip tersebut **di dalam folder `loker`** (klik kanan → Extract / Unzip).
5. Pastikan di dalam `htdocs/loker/` terlihat file `artisan`, `.htaccess`, dan folder `app`, `public`, `vendor`, `storage`.

## Langkah 5 — Pasang file `.env`

1. Di laptop, buka file `storage/app/deploy/.env` dengan text editor.
   Isinya sudah berisi `APP_KEY` dan `APP_URL=https://lokerdekat.rf.gd/loker`.
2. **Ganti hanya 4 baris ini** dengan nilai dari langkah 3:

   ```dotenv
   DB_HOST=...
   DB_DATABASE=...
   DB_USERNAME=...
   DB_PASSWORD=...
   ```

   Jangan mengubah `APP_KEY` dan `APP_URL`.
3. Di File Manager hosting, buat file baru bernama **`.env`** di dalam `htdocs/loker/`,
   lalu salin seluruh isi file tersebut ke dalamnya dan simpan.
   (Alternatif: upload langsung file `.env` dari laptop, lalu klik Edit untuk mengisi 4 baris tadi.)

## Langkah 6 — Import database

1. Buka **phpMyAdmin** dari panel hosting, lalu pilih database yang dibuat di langkah 3.
2. Klik tab **Import** → pilih file `storage/app/deploy/lokerdekat.sql` dari laptop → klik **Go**.
3. Setelah selesai, harus terlihat 8 tabel: `cache`, `cache_locks`, `favorites`, `jobs`,
   `migrations`, `password_reset_tokens`, `sessions`, `users`.

## Langkah 7 — Periksa izin folder

Di File Manager, klik kanan folder berikut lalu atur permission agar writable (biasanya 755 / 777):

- `storage/`
- `storage/framework/`
- `storage/framework/cache/`
- `storage/framework/sessions/`
- `storage/framework/views/`
- `storage/logs/`
- `bootstrap/cache/`
- `public/uploads/`

## Langkah 8 — Uji website

1. Buka **`https://lokerdekat.rf.gd/loker/`** → harus tampil halaman depan LokerDekat.
2. Klik **Masuk**, pakai akun demo:
   - Admin: `admin@lokerdekat.test` / `password`
   - Pencari kerja: `pencari@lokerdekat.test` / `password`
3. **Segera ganti password** lewat menu **Profil** → isi kata sandi baru.
   Password demo diketahui umum, jadi jangan dibiarkan di website publik.
4. Buka **Dasbor**, izinkan lokasi, lalu cek loker terurut dari yang terdekat.
5. Coba dari **HP memakai data seluler** (bukan WiFi yang sama dengan laptop) untuk memastikan benar-benar publik.

## Kalau ada masalah

| Gejala | Penyebab umum | Solusi |
|---|---|---|
| Halaman 500 | `DB_*` di `.env` salah, atau folder `storage/` belum writable | Cek `storage/logs/laravel.log` lewat File Manager; perbaiki `.env` / permission |
| Halaman putih kosong | `APP_KEY` kosong atau `.env` tidak terbaca | Pastikan file `.env` ada di `htdocs/loker/` dan baris `APP_KEY=base64:...` terisi |
| Semua halaman 404 | File `.htaccess` tidak ter-upload, atau folder bukan `loker` | Upload ulang `.htaccess`, pastikan struktur `htdocs/loker/public/index.php` |
| Link masih mengarah `localhost` | `APP_URL` belum diganti | Set `APP_URL=https://lokerdekat.rf.gd/loker` di `.env` |
| Izin lokasi ditolak di HP | SSL belum aktif / masih http:// | Aktifkan SSL, buka dengan `https://` |
| Dasbor kosong | SQL belum ter-import | Import ulang `lokerdekat.sql` lewat phpMyAdmin |

## Catatan pembaruan berikutnya

Kalau kode aplikasi berubah di masa depan: buat ulang zip, upload, lalu extract ulang
di `htdocs/loker/`. File `.env` dan database **tidak perlu** diganti kecuali ada perubahan
konfigurasi atau skema.
