# Progress: LokerDekat

Dokumen hidup. Diperbarui di **akhir setiap sesi** supaya sesi berikutnya (oleh agen atau manusia) dapat langsung melanjutkan.

## Current State

Tanggal: 2026-10-25

Sesudah `T-001`, kerangka Laravel 13.38.0 terpasang di folder proyek, repositori Git sudah ada (commit awal `main`), dan seluruh perintah `stack.commands` lulus. `T-002` juga selesai: basis data `db_loker_v2` (charset `utf8`, ADR-008) berisi tabel `users`, `jobs`, `favorites`, model Eloquent dengan relasi, serta seeder 1 admin, 1 pengguna, dan 24 loker Probolinggo berkoordinat. `QUEUE_CONNECTION=sync` dan penghapusan migrasi antrean bawaan tercatat di ADR-007. Tiga migrasi bawaan + dua migrasi aplikasi sudah dijalankan, `php artisan test` lulus 2/2, dan `vendor/bin/pint --test` bersih. Yang belum: CI hijau menunggu remote GitHub (mahasiswa push sendiri), dan status `T-001`/`T-002` masih `review` menunggu review manusia.

## Completed

| Tanggal | Task | PR/Commit | Catatan |
|---|---|---|---|
| 2026-10-25 | T-001 Setup repositori, tooling, kerangka Laravel | commit awal `main` | Laravel 13.38.0 + PHP 8.3.35; pint, phpunit (2 tes), dan build lulus |
| 2026-10-25 | T-002 Migrasi dan seeder tiga tabel + model Eloquent | branch `feat/T-002-migrasi` | `db_loker_v2` (utf8), 2 user + 24 loker terseeder; relasi teruji; pint + tes lulus |

## In Progress

Tidak ada

## Next Up

1. `T-003` — Halaman depan dengan panduan tiga langkah.
2. `T-004` — Registrasi, masuk, dan keluar.
3. `T-005` — Penjagaan peran dan rute admin.

## Blockers

Tidak ada. Format entri nanti: `tanggal | task | hambatan | yang sudah dicoba | siapa yang bisa membantu`.

## Session Log

Format entri: `tanggal | siapa (manusia atau nama alat) | task | hasil | catatan untuk sesi berikutnya`.

`2026-10-25 | asisten penyusun dokumen | - | 16 dokumen proyek dan pendukung git selesai dihasilkan dari SPEC.json | mulai dari T-001, jangan lupa buat repo Git dan database db_loker sebelum menjalankan migrasi`

`2026-10-25 | agen coding (T-001) | T-001 | Laravel 13.38.0 terpasang, ci_setup dijalankan, pint lulus, 2 tes lulus, build lulus; repositori Git diinisialisasi | status T-001 = review: CI hijau belum diverifikasi karena belum ada remote GitHub`

`2026-10-25 | agen coding (T-002) | T-002 | Migrasi gagal pertama karena batas indeks 767 byte MariaDB 10.1, lalu dibuat db_loker_v2 charset utf8 (ADR-008); antrean bawaan dihapus dan QUEUE_CONNECTION=sync (ADR-007); 5 migrasi jalan, seeder 2 user + 24 loker sukses, relasi teruji, pint dan 2 tes lulus | status T-002 = review; mahasiswa memilih push GitHub sendiri; lanjut T-003 (halaman depan + panduan 3 langkah)`
