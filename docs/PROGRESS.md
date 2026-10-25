# Progress: LokerDekat

Dokumen hidup. Diperbarui di **akhir setiap sesi** supaya sesi berikutnya (oleh agen atau manusia) dapat langsung melanjutkan.

## Current State

Tanggal: 2026-10-25

Paket dokumen proyek LokerDekat baru saja dihasilkan dari `SPEC.json` yang telah dikonfirmasi mahasiswa. Belum ada kode aplikasi sama sekali: folder proyek hanya berisi dokumen, `SPEC.json`, dan folder `kit/` pembangkit dokumen. Belum ada repositori Git, belum ada basis data `db_loker`, dan belum ada satu pun tes yang berjalan. Langkah pertama pengerjaan adalah `T-001` (setup repositori, tooling, dan kerangka Laravel minimal) yang diakhiri dengan seluruh perintah `stack.commands` lulus dan CI hijau. Setelah `T-001`, database dibuat dan dimigrasi sesuai `T-002`.

## Completed

| Tanggal | Task | PR/Commit | Catatan |
|---|---|---|---|
| 2026-10-25 | T-001 Setup repositori, tooling, kerangka Laravel | commit awal `main` | Laravel 13.38.0 + PHP 8.3.35; pint, phpunit (2 tes), dan build lulus |

## In Progress

Tidak ada

## Next Up

1. `T-002` — Migrasi dan seeder tiga tabel beserta model Eloquent.
2. `T-003` — Halaman depan dengan panduan tiga langkah.
3. `T-004` — Registrasi, masuk, dan keluar.

## Blockers

Tidak ada. Format entri nanti: `tanggal | task | hambatan | yang sudah dicoba | siapa yang bisa membantu`.

## Session Log

Format entri: `tanggal | siapa (manusia atau nama alat) | task | hasil | catatan untuk sesi berikutnya`.

`2026-10-25 | asisten penyusun dokumen | - | 16 dokumen proyek dan pendukung git selesai dihasilkan dari SPEC.json | mulai dari T-001, jangan lupa buat repo Git dan database db_loker sebelum menjalankan migrasi`

`2026-10-25 | agen coding (T-001) | T-001 | Laravel 13.38.0 terpasang, ci_setup dijalankan, pint lulus, 2 tes lulus, build lulus; repositori Git diinisialisasi | status T-001 = review: CI hijau belum diverifikasi karena belum ada remote GitHub; langkah berikutnya T-002 (migrasi + seeder) dan pembuatan database db_loker`
