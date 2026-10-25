# Design System: LokerDekat

## Principles

1. **Jarak lebih dulu daripada segalanya.** Informasi pertama yang dilihat pengguna adalah seberapa dekat loker daripada posisinya, diterapkan dengan menempatkan angka jarak di sudut kanan atas setiap kartu loker.
2. **Bahasa seperti teman, bukan seperti birokrat.** Nada ramah untuk pelamar muda, diterapkan dengan memakai kalimat "Loker terdekat dari lokasimu" alih-alih "Rekomendasi berdasarkan koordinat geospasial".
3. **Satu layar cukup untuk memutuskan.** Nama PT, alamat jalan, dan divisi harus terbaca tanpa membuka halaman lain, diterapkan dengan menampilkan ketiganya langsung di dalam kartu pada dasbor.
4. **Ringan untuk perangkat seadanya.** Tampilan harus tetap jelas di perangkat spesifikasi rendah dengan koneksi lambat, diterapkan dengan memakai satu berkas CSS CDN dan tanpa animasi berat.
5. **Tidak ada jalan buntu.** Setiap keadaan galat menyebut langkah berikutnya, diterapkan dengan pesan seperti "Izinkan lokasi agar loker diurutkan dari yang terdekat" beserta tombol tindak lanjut.

## Color Tokens

| Token | Nilai | Penggunaan |
|---|---|---|
| `--color-primary` | `#0D9488` | tombol utama, tautan, aksen jarak |
| `--color-primary-dark` | `#0F766E` | keadaan hover/fokus tombol utama |
| `--color-secondary` | `#0EA5E9` | badge kota dan penanda divisi |
| `--color-bg` | `#F8FAFC` | latar halaman |
| `--color-surface` | `#FFFFFF` | kartu, form, modal |
| `--color-text` | `#0F172A` | teks isi |
| `--color-text-muted` | `#475569` | keterangan dan label sekunder |
| `--color-border` | `#CBD5E1` | garis kartu, batas input |
| `--color-success` | `#15803D` | pesan tersimpan |
| `--color-warning` | `#B45309` | peringatan izin lokasi ditolak |
| `--color-danger` | `#B91C1C` | pesan galat validasi |
| `--color-focus` | `#0D9488` | cincin fokus keyboard 2 px |

Warna primer berasal dari `spec.design.primary_color` (`#0D9488`) dan seluruh turunan dipakai di light mode; `dark_mode` berarti false sehingga tidak ada token gelap. Rasio kontras teks `--color-text` di atas `--color-surface` ditujukan minimal 4,5:1 dan batas kartu minimal 3:1 sesuai WCAG 2.2 AA — **rasio persisnya belum dihitung**, perlu diperiksa dengan penghitung kontras sebelum diserahkan.

## Typography

| Gaya | Ukuran | Bobot | Tinggi baris | Penggunaan |
|---|---|---|---|---|
| Judul halaman (h1) | 32 px | 700 | 1,2 | judul dasbor dan halaman detail |
| Subjudul (h2) | 24 px | 600 | 1,3 | judul bagian |
| Judul kartu (h3) | 18 px | 600 | 1,4 | nama PT di kartu loker |
| Isi | 16 px | 400 | 1,5 | paragraf, alamat, deskripsi |
| Keterangan | 14 px | 400 | 1,4 | jarak, kota, waktu tayang |
| Kode/angka jarak | 14 px | 600 | 1,4 | kolom jarak `1,2 km` |

Font memakai **font bawaan Bootstrap 5 (system-ui)** sesuai `spec.design.font`, sehingga tidak menambah permintaan unduh berkas font dan tampil langsung pada perangkat rendah. Ukuran isi minimal 16 px sesuai WCAG 2.2 AA.

## Spacing and Layout

Skala jarak memakai kelipatan 4 px: `4, 8, 12, 16, 24, 32, 48, 64`. Layout web memakai grid Bootstrap dengan breakpoint `576 / 768 / 992 / 1200 / 1400 px`, lebar konten maksimum 1140 px, padding sisi 16 px di layar kecil dan 24 px di layar lebar. Ukuran jendela minimum yang didukung adalah 320 px. Kartu loker tersusun satu kolom di bawah 768 px dan tiga kolom di atas 992 px. Area navigasi utama berada di atas agar dapat dijangkau dengan satu tangan pada layar ponsel.

## Components

Komponen berikut memakai kelas bawaan **Bootstrap 5** sehingga tidak dibuat ulang dari nol:

| Komponen | Varian | Status | Aturan pemakaian |
|---|---|---|---|
| Tombol utama | `btn-primary`, `btn-outline-secondary`, `btn-sm` | default, hover/fokus, nonaktif, memuat (`disabled` + teks "Menyimpan…") | satu tombol utama per halaman |
| Input & label | `form-control`, `form-select` | default, fokus, galat (`is-invalid` + teks merah), terisi | setiap input punya `<label>` eksplisit |
| Kartu loker | kartu dasbor, kartu favorit | default, hover, tanpa foto | menampilkan nama PT, alamat, divisi, jarak; dipakai F2, F5, F6 |
| Badge | kota, divisi, jarak | aktif/nonaktif | dipakai untuk pemindaian cepat di dasbor |
| Navbar | sesi login, sesi tamu | default, terbuka di layar kecil | memuat nama pengguna dan tombol Keluar setelah F1 berhasil |
| Tabel admin | tabel data loker | kosong, memuat, ada baris | hanya di panel admin F4 |
| Dialog konfirmasi | hapus/nonaktif loker | terbuka, batal, konfirmasi | wajib untuk tindakan merusak |
| Peta | halaman detail F3 | memuat, gagal dimuat | cadangan teks alamat selalu ikut ditampilkan |
| Notifikasi (alert) | sukses, peringatan, galat | muncul sekali lalu hilang | mengikuti aturan di *Motion and Feedback* |

## Accessibility

- Kontras teks minimum 4,5:1 dan elemen UI minimum 3:1; warna tidak dipakai sebagai satu-satunya penanda status.
- Seluruh fungsi dapat dijalankan lewat keyboard dengan urutan tab logis dan cincin fokus terlihat 2 px.
- Setiap form memakai `<label>` yang terhubung, pesan galat dekat dengan input bermasalah, dan `aria-describedby` untuk penjelasan.
- Gambar dan peta mempunyai teks alternatif atau judul yang menjelaskan isinya.
- Target sentuh minimal 24×24 CSS px; pada tombol utama direkomendasikan 44–48 px.
- Menghormati `prefers-reduced-motion` sehingga animasi dimatikan bila sistem memintanya.
- Mendukung perangkat spesifikasi rendah: tanpa efek bayangan berat dan tanpa unduhan font tambahan.
- Mendukung pengguna dengan kebutuhan aksesibilitas: struktur heading berurutan dan tautan yang dapat dibedakan pembaca layar.
- Antarmuka berbahasa Indonesia dengan istilah baku agar pembaca layar membacanya secara konsisten.

## Motion and Feedback

Durasi transisi 150 ms untuk hover dan 200 ms untuk alert, easing `ease-out`; seluruh animasi dimatikan bila `prefers-reduced-motion: reduce`. Keadaan **memuat**: tombol menjadi nonaktif dengan teks "Menyimpan…" dan kartu memakai kerangka abu-abu. Keadaan **kosong**: "Belum ada loker yang cocok. Coba ubah kata kunci pencarianmu." Keadaan **galat**: pesan merah langsung di bawah input. Keadaan **sukses**: alert hijau yang hilang otomatis.

Tiga contoh pesan galat nyata:

1. "Email atau password yang kamu masukkan belum cocok. Coba periksa lagi." (F1)
2. "Nama PT, alamat, divisi, dan titik lokasi wajib diisi supaya loker bisa tampil di dasbor." (F4)
3. "Browser menolak akses lokasi. Izinkan lokasi lewat ikon gembok di address bar agar loker diurutkan dari yang terdekat." (F2)

## Content and Voice

Nada: ramah, langsung, dan menenangkan pelamar muda; kalimat pendek maksimal 15 kata, tanpa jargon teknis, dan tanpa menakut-nakuti. Glosarium istilah baku: **loker** (lowongan kerja), **divisi** (bagian/perusahaan yang membuka lowongan), **PT** (nama perusahaan), **jarak** (selisih jarak dari lokasi pengguna), **favorit** (loker yang disimpan pengguna), **dasbor** (halaman utama setelah masuk), **admin** (pengelola data loker), **izin lokasi** (izin browser memakai GPS), **tayang** (loker aktif ditampilkan).

Format tanggal memakai gaya Indonesia: `25 Oktober 2026`. Angka memakai koma sebagai pemisah desimal (`1,2 km`) dan titik untuk ribuan. Mata uang tidak ditampilkan karena aplikasi tidak memuat informasi gaji maupun transaksi.
