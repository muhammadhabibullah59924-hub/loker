# Workflow: LokerDekat

## Cycle

Setiap task mengikuti siklus yang sama:

`Plan -> Branch -> Implement -> Test -> Review -> Commit -> Pull Request -> Merge -> Update Docs`

1. **Plan** — baca deskripsi task di `docs/TASKS.md` beserta fitur terkait (`F1`…`F6`) di `docs/PRD.md`, lalu tentukan perubahan yang paling kecil untuk memenuhi acceptance criteria.
2. **Branch** — buat cabang bernama sesuai ID task, misalnya `feat/T-006-urut-terdekat` atau `fix/T-009-pesan-login`; satu cabang hanya menampung satu task.
3. **Implement** — tulis kode mengikuti `docs/CODE_STYLE.md` dan keputusan di `docs/DECISIONS.md`.
4. **Test** — jalankan gerbang di `docs/EVALS.md`: `vendor/bin/pint --test`, `php artisan test`, lalu `composer install --no-dev --optimize-autoloader`.
5. **Review** — periksa `git diff` sendiri sebelum commit: tidak ada rahasia, tidak ada perubahan di luar cabang tugas.
6. **Commit** — satu commit satu perubahan logis dengan pesan Conventional Commits dan rujukan task.
7. **Pull Request** — buka PR memakai `.github/pull_request_template.md`, isi ringkasan, ID task, ID fitur, cara menguji, dan hasil gerbang.
8. **Merge** — **manusia wajib memeriksa di titik ini**: PR hanya di-merge bila CI hijau dan review manusia memberi persetujuan.
9. **Update Docs** — perbarui `docs/PROGRESS.md` (status task) dan `docs/DECISIONS.md` bila ada keputusan baru, lalu hapus cabang yang sudah merge.

## Session Start Ritual

1. Baca `AGENTS.md` untuk mengingat batas dan konvensi proyek.
2. Baca `docs/PROGRESS.md` untuk mengetahui keadaan terakhir: task apa yang sedang berjalan dan apa hambatannya.
3. Baca `docs/LESSONS.md` agar kesalahan yang sama tidak terulang.
4. Pilih **tepat satu** task berstatus `todo` dari `docs/TASKS.md`.
5. Ubah status task itu menjadi `doing`.
6. Pastikan berada di cabang kerja, **bukan `main`**, dan working tree bersih (`git status` tanpa perubahan menggantung).

## Session End Ritual

1. Jalankan seluruh gerbang di `docs/EVALS.md` dan pastikan lulus.
2. Commit kecil dengan pesan sesuai `docs/CODE_STYLE.md`.
3. Perbarui status task di `docs/TASKS.md` menjadi `done`, `blocked`, atau `review`.
4. Tulis ringkasan di `docs/PROGRESS.md`: yang selesai, yang dikerjakan berikutnya, dan hambatan.
5. Catat keputusan baru di `docs/DECISIONS.md` (sebagai ADR) dan kesalahan yang dipelajari di `docs/LESSONS.md`.
6. Push cabang dan buka PR.
7. Berhenti dan minta review manusia.

## Branching Strategy

`main` selalu stabil, dilindungi, dan tidak pernah menerima commit langsung. Fitur dikerjakan di `feat/T-nnn-nama`, perbaikan di `fix/T-nnn-nama`, dan perubahan dokumen di `docs/nama`. Satu cabang satu task; cabang dihapus setelah merge. Sebelum membuka PR, cabang diperbarui dengan **merge `main` ke cabang tugas** (bukan rebase) sehingga riwayat tim tetap utuh dan tidak ada force push. Karena tim berjumlah 1 orang (`team_size = 1`), review tetap dilakukan manusia terhadap PR miliknya sendiri sebelum merge — pengganti "reviewer kedua" adalah berhenti dan membaca diff lagi pada sesi berikutnya.

## Commit and Pull Request Rules

Commit mengikuti format di `docs/CODE_STYLE.md`: `tipe(ruang-lingkup): ringkasan imperatif`, maksimal 72 karakter, satu perubahan logis, rujukan task di badan commit. PR wajib memuat: ringkasan perubahan, ID task (mis. `T-006`), ID fitur terdampak (`F2`), cara menguji, hasil gerbang EVALS, dan tangkapan layar bila tampilan berubah. PR dijaga kecil dan berisi satu tujuan; merge hanya terjadi bila CI hijau dan ada satu review manusia. Isi PR mengikuti `.github/pull_request_template.md`.

## Human-in-the-Loop Checkpoints

| Titik kontrol | Siapa | Yang diperiksa |
|---|---|---|
| Sebelum merge PR | Mahasiswa (manusia) | diff, hasil EVALS, kecocokan acceptance di PRD |
| Sebelum menambah dependensi baru | Mahasiswa | kebutuhan nyata, reputasi paket, lisensi |
| Sebelum mengubah skema data/migrasi | Mahasiswa | dampak ke `F2`, `F4`, data seed, dan test |
| Sebelum mengubah arsitektur/ADR | Mahasiswa | alasan, alternatif, konsekuensi ke TASKS |
| Sebelum perintah destruktif | Mahasiswa | perintah lengkap, dampak, ketersediaan backup |
| Sebelum memakai layanan berbayar | Mahasiswa | batas kuota gratis, biaya, alternatif gratis |
| Sebelum memakai data nyata pengguna | Mahasiswa + dosen | privasi, UU PDP, persetujuan |

Level **dasar** menetapkan pemeriksaan manusia **setiap task**, bukan hanya saat merge besar.

## Handling Blockers

Ketika macet: (1) coba maksimal **tiga pendekatan berbeda** untuk masalah yang sama; (2) catat apa yang sudah dicoba ke `docs/PROGRESS.md`; (3) tandai task sebagai `blocked` di `docs/TASKS.md`; (4) minta bantuan ke dosen pembimbing, teman satu tim, atau dokumentasi resmi Laravel. Agen **dilarang** melewati blocker dengan mematikan tes, menurunkan standar keamanan, atau mengarang API dan hasil tes.

## Level Notes

- **Dasar — AKTIF.** Satu pengembang, alur sederhana, gerbang dijalankan lokal sebelum PR, pengujian minimum namun wajib lulus.
- **Menengah.** Tugas terstruktur, CI wajib hijau, review rutin tiap PR; dipakai bila proyek diperpanjang jadi 3–6 minggu atau tim bertambah.
- **Loop.** Agen berputar rencana → kode → uji → tinjau dengan batas ketat; hanya bila level diubah di `SPEC.json` dan `docs/LOOP.md` ditambahkan.
