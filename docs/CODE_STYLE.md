# Code Style: LokerDekat

## Language and Tooling

Bahasa utama **PHP** untuk backend dan **Blade** untuk template, dengan versi yang dipakai: *latest stable* dari `laravel/framework` (verifikasi: `composer show -a laravel/framework`). Pemformat dan linter standar ekosistem `php` untuk Laravel adalah **Laravel Pint**, dijalankan memakai perintah lint persis dari `SPEC.json`:

```bash
vendor/bin/pint --test
```

Pemeriksaan tipe statis tidak dipakai pada proyek ini (`stack.commands.typecheck` kosong); konsistensi tipe dijaga lewat validasi Form Request dan docblock ringan pada service. Seluruh berkas PHP harus lulus Pint sebelum di-commit.

## Naming

| Objek | Konvensi | Contoh |
|---|---|---|
| Berkas | StudlyCaps sesuai kelas yang berisi | `Job.php`, `Favorite.php`, `JobController.php` |
| Folder | singular untuk model, kebab-case untuk view | `app/Models/Job`, `resources/views/jobs/detail.blade.php` |
| Variabel & fungsi | camelCase | `$nearestJobs`, `distanceInKm()` |
| Kelas & tipe | StudlyCaps | `DistanceService`, `StoreJobRequest` |
| Konstanta | UPPER_SNAKE_CASE | `DEFAULT_LAT`, `DEFAULT_LNG` |
| Komponen Blade | snake_case per blok | `card-loker.blade.php`, `alert-galat.blade.php` |
| Tabel & kolom basis data | snake_case jamak untuk tabel, singular untuk kolom | tabel `jobs`, kolom `company_name`, `is_active` |
| Relasi Eloquent | camelCase verb | `favorites()`, `postedBy()` |
| Variabel lingkungan | UPPER_SNAKE_CASE | `DB_DATABASE`, `APP_KEY` |
| ID fitur & task | huruf kapital + angka | `F1`, `F4`, `T-001`, `R2` |

## Structure and Patterns

Pemisahan tanggung jawab: **Blade** hanya menampilkan data yang sudah disiapkan, **Controller** mengatur alur permintaan, **Form Request** mengatur validasi, **Model** mengatur akses data, dan **Service** mengatur logika bisnis seperti perhitungan jarak. Batas saran: satu fungsi controller maksimal sekitar 30 baris; bila lebih, pindahkan logikanya ke service.

Pola yang dipakai: Form Request untuk validasi, middleware untuk otorisasi peran, Eloquent untuk seluruh query, seeder untuk data dummy. Pola yang dihindari: query mentah tanpa kebutuhan, logika bisnis di dalam view, dan model gemuk yang mencampur validasi, query, dan format tampilan.

Contoh kecil untuk **F2 (dasbor urut terdekat)** — yang benar:

```php
// app/Services/DistanceService.php
public function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    return round(6371 * acos(
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        cos(deg2rad($lng2) - deg2rad($lng1)) +
        sin(deg2rad($lat1)) * sin(deg2rad($lat2))
    ), 1);
}
```

Yang salah:

```php
// jarak dihitung di dalam Blade dan hasil query disambung string
$hasil = DB::select("SELECT * FROM jobs ORDER BY " . $_GET['sort']);
```

## Error Handling

Galat tidak pernah ditelan diam-diam: galat yang bisa dipulihkan (input tidak valid, izin lokasi ditolak) ditampilkan sebagai pesan ramah di dekat input, sedangkan galat fatal dicatat ke log untuk pengembang. Pesan untuk pengguna menjelaskan langkah berikutnya, bukan detail teknis. Saat jaringan putus atau koneksi lambat, halaman tetap menampilkan daftar loker terakhir yang tersedia dan menyediakan tombol coba lagi; permintaan lokasi yang gagal memakai urutan default pusat Probolinggo sehingga dasbor tidak pernah kosong.

## Comments and Documentation

Komentar menjelaskan **mengapa**, bukan mengulang apa yang tertulis di kode — misalnya alasan pemilihan `acos` pada rumus Haversine. Fungsi publik memakai docblock singkat berisi parameter dan nilai balik. Kode mati tidak dikomentari, melainkan dihapus. Setiap perubahan perilaku disertai pembaruan `docs/PROGRESS.md`; keputusan arsitektur baru dicatat di `docs/DECISIONS.md` sebagai ADR.

## Commit Messages

Format Conventional Commits: `tipe(ruang-lingkup): ringkasan imperatif`, satu commit satu perubahan logis, ringkasan maksimal 72 karakter, dan rujukan task di badan commit atau PR.

| Tipe | Contoh nyata untuk proyek ini |
|---|---|
| `feat` | `feat(dasarbor): urutkan loker berdasarkan jarak (T-006)` |
| `fix` | `fix(auth): tampilkan pesan galat saat password salah (T-004)` |
| `docs` | `docs(prd): lengkapi acceptance criteria F4` |
| `test` | `test(jarak): tambah unit test rumus haversine (T-007)` |
| `refactor` | `refactor(service): pindahkan logika urutan ke DistanceService` |
| `chore` | `chore(deps): tambah leaflet via CDN` |
| `ci` | `ci(github): jalankan pint dan phpunit di CI` |

## Dependencies

Menambah dependensi hanya bila dibutuhkan nyata, dipelihara aktif, dan lisensinya kompatibel. Nama paket wajib diverifikasi keberadaannya dan milik penerbit yang benar sebelum instal (waspadai nama paket karangan atau salah eja), `composer.lock` selalu di-commit, versi dipin untuk produksi, dan dependensi besar dicatat di `DECISIONS.md`. CDN pihak ketiga (Bootstrap 5, Leaflet) dipilih agar tidak menambah dependensi build.

## Forbidden Patterns

1. Rahasia (`APP_KEY`, `DB_PASSWORD`) tertulis di kode, view, atau commit.
2. Query SQL hasil penyambungan string — wajib lewat Eloquent/binding parameter.
3. Menonaktifkan lint atau tes agar lulus.
4. Mengubah tes agar lulus tanpa memperbaiki penyebabnya.
5. Kode tak terpakai atau dibiarkan dalam keadaan dikomentari.
6. Dependensi ganda untuk fungsi yang sama (mis. dua pustaka peta).
7. Pesan galat yang membocorkan detail internal seperti stack trace atau nama tabel.
8. Menyimpan koordinat lokasi pengguna ke basis data di luar keputusan `SPEC.json`.
9. Menampilkan data loker yang `is_active` false di dasbor (F4).
10. Merender HTML dari input pengguna tanpa escape (kecuali lewat `@sanitasi` resmi).
11. Memanggil API tanpa penanganan galat, sehingga halaman blank saat jaringan putus.

## AI-Generated Code Rules

Penulis (mahasiswa) wajib bisa menjelaskan setiap perubahan yang diterima; perubahan diminta kecil dan fokus pada satu task; lint dan tes dijalankan sebelum menerima hasil; keberadaan API, fungsi, atau paket yang disarankan diperiksa dulu di dokumentasi resmi Laravel. Kode yang tidak dipahami tidak diterima, dan kesalahan berulang dari agen dicatat di `docs/LESSONS.md`.
