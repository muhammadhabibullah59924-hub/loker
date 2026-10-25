# Lessons: LokerDekat

Kesalahan berulang dan pelajaran dari proyek ini. **Dibaca agen di awal setiap sesi.** Entri baru ditambahkan oleh mahasiswa atau agen setelah kesalahan ditemukan.

## How to Use

- Tambah entri bila galat menghabiskan waktu lebih dari 30 menit, perilaku agen ternyata salah dan berulang, atau ada temuan review yang penting.
- Format entri memakai tabel di bawah: tanggal, kesalahan/gejala, akar masalah, aturan baru, dan dokumen tempat aturan itu dipasang.
- Pelajaran yang berulang dinaikkan menjadi aturan tetap dengan menambahkannya ke `docs/CODE_STYLE.md`, `docs/GUARDRAILS.md`, atau `docs/EVALS.md`.
- Tulis singkat dan tanpa menyalahkan; fokus pada perbaikan sistem, bukan pada orangnya.

## Lessons Log

| Tanggal | Kesalahan atau gejala | Akar masalah | Aturan baru | Diterapkan di |
|---|---|---|---|---|
| Awal (belum terjadi di proyek ini) | Mengarang nama paket atau API yang tidak ada saat menulis kode | Model mengingat nama dari memori tanpa memeriksa dokumentasi | Verifikasi nama paket dan fungsi ke dokumentasi resmi sebelum dipakai | `docs/CODE_STYLE.md` bagian Dependencies |
| Awal (belum terjadi di proyek ini) | Mengubah tes agar lulus alih-alih memperbaiki kode | Tekanan menyelesaikan task cepat | Tes yang gagal wajib diperbaiki di kode; perubahan tes butuh alasan di PR | `docs/EVALS.md` bagian Regression Policy |
| Awal (belum terjadi di proyek ini) | Mengerjakan lebih dari satu task dalam satu sesi sehingga diff sulit ditinjau | Task terasa saling terkait | Satu task per sesi, satu cabang satu PR | `docs/GUARDRAILS.md` bagian Ask Before |
| Awal (belum terjadi di proyek ini) | Menaruh rahasia (`APP_KEY`, `DB_PASSWORD`) di kode atau konfigurasi CI | Kebiasaan menyalin `.env` sebagai contoh | Rahasia hanya lewat variabel lingkungan; `.env` tidak pernah di-commit | `docs/SECURITY.md` bagian Secrets Management |
| Awal (belum terjadi di proyek ini) | Mengabaikan keadaan galat jaringan sehingga halaman kosong total | Hanya menguji pada keadaan berhasil | Setiap fitur punya jalur gagal (izin lokasi ditolak, peta gagal, koneksi putus) yang diuji | `docs/TESTING.md` bagian Manual Test Checklist |

## Patterns to Watch

1. **Versi API Laravel yang sudah usang diajarkan model** — deteksi: kode memakai helper atau konfigurasi yang tidak ditemukan di dokumentasi versi hasil `composer show -a laravel/framework`.
2. **Konfigurasi CI yang kadaluarsa** — deteksi: job GitHub Actions gagal padahal lokal lulus; periksa versi runtime PHP di `tech-baseline.md`.
3. **Struktur folder Laravel versi lama** — deteksi: path `app/Http/Controllers` atau `resources/views` tidak ditemukan setelah scaffold; ikuti struktur hasil perintah scaffold resmi.
4. **Kelas Bootstrap yang tidak sesuai versi CDN** — deteksi: gaya tidak muncul; periksa nama kelas di dokumentasi Bootstrap yang dipakai.
5. **Panggilan Geolocation pada origin tidak aman** — deteksi: fitur lokasi diam saja tanpa pesan; uji lewat `http://localhost`, bukan lewat berkas `file://`.

## Reflection Prompts

1. Keputusan apa yang kamu ubah selama proyek berjalan, dan mengapa perubahan itu dilakukan?
2. Bagian mana yang dikerjakan AI dengan baik, dan bagian mana yang ternyata salah?
3. Apa yang akan kamu lakukan berbeda bila proyek ini dikerjakan ulang dari awal?
4. Kendali risiko `R1`, `R2`, dan `R3` mana yang paling terasa manfaatnya bagi pengguna, dan bagaimana kamu tahu?
5. Bagian dari kode yang paling tidak kamu pahami, dan langkah apa yang akan kamu ambil untuk memahaminya?
