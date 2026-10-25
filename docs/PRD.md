# Product Requirements Document: LokerDekat

> Website info loker yang mengurutkan lowongan dari yang paling dekat ke lokasi pengguna, lengkap dengan peta dan divisi yang lowong.

## Overview

| Field | Isi |
|---|---|
| Nama produk | LokerDekat |
| Platform | web |
| Level proses | dasar |
| Tagline | Loker terdekat dari lokasimu, lengkap dengan divisinya. |
| Tim dan waktu | 1 anggota (sendirian), durasi 1 minggu |

## Problem

Anak muda yang masih lontang-lantung mencari loker sering tidak tahu harus melangkah ke mana untuk menemukan lowongan yang benar-benar bisa mereka datangi. Cara mereka mengatasinya sekarang hanya mengikuti grup lowongan: masuk grup tetapi tidak pernah ada aksi lanjutan, ditambah informasi offline dan kenalan yang belakangan ini paling sering mereka temui. Karena langkah mencari kerjanya tidak jelas, mereka akhirnya diam di tempat dan kecenderungan tidak bekerja pada anak muda terus bertambah. Belum ada satu tempat yang menampilkan lowongan terdekat dari posisi mereka lengkap dengan nama PT, alamat jalan, dan divisi yang lowong. LokerDekat menjawab kekosongan itu dengan menampilkan loker dari yang paling dekat ke lokasi pengguna.

## Goals and Non-Goals

### Goals

1. Pengguna menemukan loker terdekat dari lokasinya dalam hitungan detik lewat dasbor yang terurut dari jarak terkecil, sehingga ia tidak perlu membuka banyak sumber informasi.
2. Admin mengelola data loker (tambah, ubah, nonaktif) sendiri tanpa menyentuh kode atau database, sehingga informasi di dasbor selalu dapat diperbarui oleh pengelola.

### Non-Goals

- Tidak ada fitur lamaran atau unggah CV/berkas — website ini murni informasi lowongan, karena cakupan proyek ini adalah menampilkan loker, bukan mengelola proses rekrutmen.
- Tidak ada pembayaran, notifikasi, atau chat antar pengguna — ketiganya tidak dibutuhkan untuk menentukan loker terdekat dan menambah kerumitan di tengah waktu satu minggu.
- Tidak ada aplikasi mobile native (hanya web di browser) — platform utama sudah ditetapkan sebagai web agar bisa dibuka tanpa instalasi.

## Target Users

| Persona | Kebutuhan | Kutipan |
|---|---|---|
| Pemuda di Probolinggo yang baru mulai mencari loker dan belum tahu harus mulai dari mana | Melihat daftar lowongan terdekat dari lokasinya, lengkap dengan nama PT, alamat jalan, dan divisi yang lowong. | (ilustrasi, dirumuskan asisten) Tiap hari buka grup cuma dapat info basi, tidak tahu harus mulai dari mana. |

Pengguna kedua adalah pengelola/admin yang memasukkan data loker. Kondisi pengguna yang harus diperhatikan: antarmuka berbahasa Indonesia, perangkat spesifikasi rendah, dan pengguna dengan kebutuhan aksesibilitas.

## Core Features

### F1 - Masuk dan daftar akun (must)

Pengguna mendaftar dengan nama, email, dan password lalu masuk ke aplikasi; sesi berjalan sampai ia menekan Keluar.

Acceptance Criteria:

- Pengguna menekan Daftar lalu mengisi nama, email, dan password yang valid — akun tersimpan dan ia langsung bisa Masuk.
- Pengguna memasukkan password yang salah saat Masuk — muncul pesan galat dan ia tetap berada di halaman login.
- Setelah Masuk, nama pengguna tampil di navbar; menekan Keluar mengakhiri sesi dan mengembalikan ke halaman depan.

### F2 - Dasbor loker terurut terdekat (must)

Halaman utama setelah login menampilkan kartu loker (nama PT, alamat jalan, divisi lowong) diurutkan dari yang paling dekat ke lokasi pengguna.

Acceptance Criteria:

- Pengguna mengizinkan lokasi — kartu loker tampil urut menaik sesuai jarak dan setiap kartu menampilkan angka jarak dalam km.
- Pengguna menolak izin lokasi — daftar tetap tampil memakai urutan default pusat Probolinggo dan muncul peringatan untuk mengizinkan lokasi.
- Saat tidak ada pencarian atau filter aktif, dasbor menampilkan minimal 3 loker terdekat lengkap dengan nama PT, alamat, dan divisi (R1).
- Saat filter radius dipilih, hanya loker dalam radius itu yang tampil; tanpa radius semua loker aktif tampil.

### F3 - Peta dan lokasi loker (must)

Setiap loker mempunyai titik koordinat yang ditampilkan sebagai peta Leaflet/OpenStreetMap di halaman detail.

Acceptance Criteria:

- Pengguna membuka detail satu loker — peta termuat dan menampilkan marker di titik latitude/longitude loker tersebut.
- Alamat yang tertulis di halaman detail sama persis dengan nilai field alamat di database.

### F4 - Kelola data loker (admin) (must)

Admin menambah, mengubah, menonaktifkan, dan menghapus loker lewat panel admin tanpa menyentuh database.

Acceptance Criteria:

- Admin mengisi form tambah loker (nama PT, alamat, divisi, kota, titik peta) lalu menyimpan — loker langsung muncul di dasbor.
- Form menolak disimpan bila nama PT, alamat, divisi, atau titik koordinat kosong — muncul pesan validasi dan loker tidak tersimpan (R2).
- Loker tanpa titik koordinat tidak pernah ditayangkan di dasbor (R2).
- Pengguna biasa membuka halaman admin — ia ditolak dan dialihkan keluar dari halaman tersebut.
- Admin menonaktifkan satu loker — loker hilang dari dasbor tetapi datanya masih ada di database.

### F5 - Pencarian dan filter loker (should)

Pengguna mencari loker berdasarkan nama PT atau divisi, dan menyaring menurut radius jarak.

Acceptance Criteria:

- Pengguna mengetik nama PT atau nama divisi — hanya loker yang cocok yang tampil.
- Kata kunci yang tidak cocok dengan loker mana pun — muncul pesan bahwa tidak ada hasil.
- Pencarian dan filter tetap mempertahankan urutan jarak dari yang terdekat.

### F6 - Simpan loker favorit (could)

Pengguna menandai loker yang diminati dan menemukannya kembali di halaman Favorit.

Acceptance Criteria:

- Pengguna menekan tombol simpan pada sebuah loker — loker masuk ke halaman Favorit miliknya.
- Pengguna menekan tombol lepas pada loker tersimpan — loker hilang dari daftar Favorit.
- Daftar Favorit tetap berisi loker yang sama setelah pengguna keluar lalu masuk kembali.

## User Flows

1. **Alur pencari kerja menemukan loker terdekat (F1 → F2 → F3).** Pengguna membuka LokerDekat, menekan Daftar dan mengisi data dirinya, lalu Masuk. Dasbor meminta izin lokasi; setelah izin diberikan kartu loker tampil urut dari jarak terkecil dengan angka jarak tiap kartu. Pengguna memilih satu kartu dan halaman detail menampilkan peta titik lokasi PT. Keadaan galat: izin lokasi ditolak, maka daftar tetap tampil dengan urutan default pusat Probolinggo dan peringatan ditampilkan; koneksi putus saat halaman dimuat, maka pesan galat meminta pengguna mencoba lagi.
2. **Alur pencarian menyaring loker (F2 → F5 → F6).** Pengguna mengetik nama PT atau divisi pada kotak pencarian di dasbor; hanya loker yang cocok yang tampil sambil urutan jarak dipertahankan. Bila kata kunci tidak cocok dengan loker mana pun, pesan "tidak ada hasil" muncul dan pengguna mengosongkan pencarian. Pengguna menekan tombol simpan pada loker yang diminati, lalu membuka halaman Favorit untuk memastikan loker tersimpan di sana.
3. **Alur admin mengelola loker (F1 → F4 → F2).** Admin Masuk memakai akun berperan admin, membuka panel admin, dan menekan Tambah loker. Bila nama PT, alamat, divisi, atau titik koordinat kosong, form menolak disimpan dan menampilkan pesan validasi. Setelah tersimpan, loker muncul di dasbor sesuai urutan jaraknya; saat dinonaktifkan, loker hilang dari dasbor tetapi datanya tetap ada. Keadaan galat utama: pengguna biasa mencoba membuka halaman admin, maka ia ditolak dan dialihkan.

## AI Features

Tidak ada fitur AI di dalam aplikasi; pengurutan jarak dan pencarian memakai aturan tetap, bukan model.

## Non-Functional Requirements

| Kategori | Persyaratan |
|---|---|
| Performa | Dasbor menampilkan daftar loker terurut tanpa meminta pengguna memuat ulang halaman; halaman berjalan normal pada perangkat spesifikasi rendah. |
| Aksesibilitas | Memenuhi WCAG 2.2 AA: kontras teks, fokus keyboard terlihat, label formulir, dan target sentuh minimal 24×24 CSS px. |
| Kondisi pengguna | Antarmuka berbahasa Indonesia, ringan untuk perangkat spesifikasi rendah, dan dapat dinavigasi lewat keyboard untuk pengguna dengan kebutuhan aksesibilitas. |
| Bahasa antarmuka | Bahasa Indonesia pada seluruh label, pesan, dan isi halaman. |
| Privasi data | Data pribadi berupa nama, email, dan password (hash); koordinat lokasi hanya dikirim saat sesi untuk mengurutkan jarak dan tidak disimpan. |
| Ketersediaan | Dapat diakses selama XAMPP (Apache dan MySQL) berjalan di lingkungan pengembangan; tidak ada klaim ketersediaan produksi. |

## Success Metrics

| Metrik | Target | Cara Ukur |
|---|---|---|
| Urutan dasbor sesuai jarak | Semua loker di dasbor tampil menaik mulai dari jarak terkecil | Membandingkan angka jarak pada tiap kartu di dasbor dengan perhitungan jarak di database. |
| Data loker hasil input admin tampil lengkap | 10 loker yang ditambahkan admin muncul semua di dasbor pada posisi sesuai jaraknya | Menambah 10 loker lewat panel admin lalu menghitung kartu yang muncul di dasbor beserta urutannya. |

## Risks and Open Questions

### Risiko menurut mahasiswa

> resiko terbesar proyek ini menurutku adalah kecenderungan tidak kerja bagi anak muda karena masih tidak tahu untuk melangkah kmn untuk mencari loker

### Kendali risiko yang disetujui mahasiswa

- **R1:** R1: Dasbor langsung menampilkan loker terdekat + jarak + divisi begitu lokasi diizinkan (atau kota dipilih). Hasil teramati: dalam satu layar terlihat minimal 3 opsi loker lengkap dengan nama PT, alamat, divisi, dan jarak. Terdampak pada F2.
- **R2:** R2: Loker tidak bisa disimpan tanpa nama PT, alamat, divisi, dan titik koordinat. Hasil teramati: 100% kartu di dasbor menampilkan keempat informasi itu. Terdampak pada F4 dan F2.
- **R3:** R3: Tampil panduan 3 langkah di halaman depan atau dasbor: Izinkan lokasi - lihat urut terdekat - buka detail dan peta. Hasil teramati: pengguna baru melihat urutan langkah tanpa perlu bertanya ke siapa pun. Terdampak pada F2 dan F3.

### Risiko teknis dan proyek

- Titik koordinat loker bisa salah sehingga urutan jarak di dasbor menjadi menyesatkan — mitigasi: admin wajib mengisi titik lewat klik peta dan mengecek pratinjau alamat sebelum menyimpan (R2).
- XAMPHP memakai MariaDB 10.1 yang mungkin lebih lama dari versi yang didukung Laravel terbaru — mitigasi: verifikasi dengan `composer show -a laravel/framework` saat instalasi dan catat di DECISIONS.
- Fitur lokasi GPS hanya berjalan di origin aman (localhost atau HTTPS) — mitigasi: siapkan urutan default pusat Probolinggo sebagai jalur cadangan dan uji keduanya.
- Waktu 1 minggu dengan 1 pengembang sangat ketat untuk 6 fitur sekaligus — mitigasi: kerjakan F1–F4 lebih dulu sebagai must, F5 dan F6 menyusul bila waktu tersisa.

### Pertanyaan terbuka

- Versi pasti Laravel dan kompatibilitasnya dengan MariaDB 10.1 XAMPP belum dipastikan (perlu `composer show -a laravel/framework`).
- Belum diputuskan apakah koordinat pusat Probolinggo akan ditulis sebagai konfigurasi tetap atau data seed.
- Layanan hosting produksi belum ditentukan; untuk tugas ini berjalan di localhost XAMPP.
