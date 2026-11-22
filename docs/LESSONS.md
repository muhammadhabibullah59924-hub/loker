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
| 2026-10-26 | Menyatakan task fitur lulus berdasarkan tes lama, padahal eval menyebut nama tes lain yang belum ada (`DashboardNearestVisibleTest`, `JobRequiredFieldsTest`) | Eval `docs/EVALS.md` menjadi source of truth perintah gerbang, tetapi daftar eval belum dikonsultasi dengan daftar file tes | Sebel menjalankan perintah eval, cocokkan setiap eval dengan nama kelas tes yang benar-benar ada sebelum menulis laporan kelayakan | `docs/EVALS.md` bagian Feature Acceptance Evals |
| 2026-10-26 | Menulis ekspektasi urutan pada tes dasbor sebelum memastikan urutan data uji | Koordinat uji dan jarak antar titik harus dihitung sebelum assertion urutan dipakai | Hitung ulang jarak dan susun urutan ekspektasi sebelum menulis `assertSeeInOrder` pada tes dasbor | `docs/TESTING.md` bagian Tes |
| 2026-11-08 | Website tidak bisa dibuka padahal kode, migrasi, dan seeder sudah benar; galat saat membuka tampak seperti aplikasi | Layanan lokal (Apache + MySQL) XAMPP belum aktif sehingga permintaan tidak diterima; error sempat muncul pada permintaan pertama setelah layanan aktif | Sebel menelusuri kode saat gejala "tidak bisa dibuka", cek status layanan dan log akses Apache/MySQL; catat di manual test checklist | `docs/TESTING.md` bagian Manual Test Checklist |
2026-11-18 | Error `Fatal error: Maximum execution time of 30 seconds exceeded` berulang saat login setelah idle, dengan fase berbeda di Connector / DatabaseSessionHandler / ClassLoader | Batas PHP `max_execution_time` default 30 detik ditabrak oleh operasi database/sesi yang melekat setelah idle; fatal saat error handling ikut memperumur fase | Naikkan batas eksekusi PHP lewat `.htaccess` dan cek status layanan lokal sebelum menyerahkan fitur baru | docs/TESTING.md bagian Manual Test Checklist, docs/EVALS.md Quality Gates |
| 2026-11-18 | Statistik jumlah loker aktif tidak tampil dan tes dasbor berhenti dengan galat "Undefined variable $jumlahLokerAktif" | Key data yang dilempar ke view Laravel harus sama persis dengan nama variabel Blade; key snake_case `jumlah_loker_aktif` justru membentuk variabel `$jumlah_loku_aktif`, bukan `$jumlahLokerAktif` | Sebel menulis key view, tentukan nama variabel PHP (camelCase) lalu pasangkan key view dengan nama persis itu | app/Http/Controllers/DashboardController.php, resources/views/dashboard.blade.php |
| 2026-11-22 | E2E filter tipe gagal setelah data diperbanyak: masih ada badge Part-Time saat filter Full-Time aktif | Bagian "Loker Populer" memakai query terpisah yang tidak mewarisi filter dasbor; dengan 30 loker kasus ini tidak terlihat | Setiap bagian yang merender kartu loker harus mematuhi filter aktif yang sama, dan uji regresi memakai data yang cukup besar untuk menyingkapnya | `app/Http/Controllers/DashboardController.php`, `tests/Feature/SearchFilterTest.php` |
| 2026-11-22 | `db:seed` gagal dengan `Unknown column 'is_premium'` pada database lokal yang belum menjalankan migrasi T-024 | Seeder menulis semua field loker tanpa memeriksa kolom yang benar-benar ada; DB lokal sengaja tertinggal satu migrasi | Sebelum menulis data dari seeder, periksa daftar kolom aktif dan lewati kolom yang belum ada; jangan mengubah skema hanya agar seeder jalan | `database/seeders/LokerSeeder.php` metode `kolomJobs()` |
| 2026-11-22 | Favorit tersimpan di akun `mailmullet343@gmail.com` tetapi pemilik proyek mengira akun admin ikut memilikinya | Favorit adalah data per akun: `FavoriteController::index` memfilter `user_id` dari sesi, dan seeder tidak mengisi tabel `favorites` | Sebel menjelaskan atau membangun fitur lintas akun, cek dulu filter `user_id` di `FavoriteController` dan tabel matriks peran di `docs/SECURITY.md`; bila admin memang butuh laporan, buat ADR baru (ADR-011) alih-alih mengubah perilaku F6 | `docs/SECURITY.md` matriks peran, `docs/DECISIONS.md` ADR-011 |
| 2026-11-22 | Saat deploy, form ubah loker admin masih mengembalikan 500 `Unknown column 'is_premium' in 'field list'` padahal pembacaan dasbor sudah aman | Seeder dan query statistik sudah dibuat toleran terhadap kolom yang belum ada, tetapi `Admin\JobController::store/update` tetap menulis `is_premium`; migrasi T-024 ternyata masih `Pending` di DB lokal | Sebelum menyatakan aplikasi siap, jalankan `php artisan migrate:status` dan pastikan tidak ada migrasi `Pending`; toleransi kode saat membaca tidak menggantikan migrasi saat menulis | `docs/EVALS.md` bagian Deploy Gate |
| 2026-11-22 | Berisiko menjalankan `php artisan test` setelah `config:cache` aktif: tes memakai `RefreshDatabase` dan bisa kena DB nyata | `config:cache` membekukan nilai konfigurasi sehingga `<env>` di `phpunit.xml` tidak dipakai saat bootstrap; tes akan memakai koneksi MySQL produksi-lokal | Jalankan `php artisan optimize:clear` sebelum tes, lalu bangun ulang cache produksi setelah seluruh gerbang lulus | `docs/EVALS.md` bagian Deploy Gate |
| 2026-11-22 | `composer install --no-dev --optimize-autoloader` terpotong batas waktu 5 menit dan meninggalkan autoload yang merujuk ke paket yang sudah dihapus | Proses uninstall + dump autoload belum selesai ketika perintah dihentikan timeout, sehingga `vendor/composer/autoload_real.php` memuat berkas paket yang tidak ada | Ulangi perintah yang sama dengan batas waktu lebih lama sampai selesai; jangan menghapus `vendor` atau mengubah `composer.lock` hanya karena timeout | `docs/EVALS.md` bagian Quality Gates |

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
