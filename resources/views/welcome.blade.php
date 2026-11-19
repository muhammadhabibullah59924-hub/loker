@extends('layouts.app')

@section('title', 'LokerDekat — loker terdekat dari lokasimu')

@section('content')
    {{-- Bagian pembuka dua kolom: ajakan di kiri, contoh kartu loker di kanan (T-036). --}}
    <section class="hero hero-landing" aria-labelledby="judul-hero">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-center">
                <div class="col-12 col-lg-6">
                    <img src="{{ asset('uploads/logo.svg') }}" alt="Logo LokerDekat" width="56" height="56" class="mb-3">

                    <p class="hero-eyebrow mb-3">Rekomendasi terdekat</p>

                    <h1 id="judul-hero" class="mb-3">Loker terdekat dari lokasimu</h1>

                    {{-- Tagline persis dari SPEC.json (meta.one_liner) --}}
                    <p class="lead hero-tagline mb-4">
                        Website info loker yang mengurutkan lowongan dari yang paling dekat ke lokasi pengguna,
                        lengkap dengan peta dan divisi yang lowong.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mb-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Daftar dan lihat loker terdekat</a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg">Masuk</a>
                    </div>

                    <ul class="list-unstyled d-flex flex-wrap gap-2 mb-0 hero-fitur" aria-label="Keunggulan LokerDekat">
                        <li>Loker terdekat</li>
                        <li>Peta lokasi</li>
                        <li>Simpan favorit</li>
                    </ul>
                </div>

                <div class="col-12 col-lg-6 hero-visual">
                    <span class="hero-radar" aria-hidden="true"></span>

                    <div class="hero-contoh">
                        <p class="hero-contoh__label">Contoh kartu loker</p>
                        <article class="card panduan-card job-card job-card--full-time">
                            <div class="card-body">
                                <span class="job-card__jarak">0,8 km</span>
                                <h3 class="mb-2">PT Sumber Makmur</h3>
                                <p class="text-secondary mb-2">Jl. Mastrip No. 12, Kedopok</p>
                                <p class="mb-0">
                                    <span class="badge border job-card__divisi">Produksi</span>
                                    <span class="text-secondary small ms-1">Kota Probolinggo</span>
                                </p>
                                <div class="d-flex flex-wrap gap-2 mt-2 mb-2">
                                    <span class="badge job-tipe job-tipe--full-time">Full-Time</span>
                                    <span class="text-secondary small">Lowongan butuh 2 orang</span>
                                </div>
                                <p class="text-secondary small mb-0">Batas lamaran: 30 November 2026</p>
                            </div>
                        </article>
                        <p class="hero-contoh__catatan">Beginilah kartu loker yang akan kamu lihat setelah masuk.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bagian-pemisah" aria-hidden="true"><span></span></div>

    {{-- T-040: nilai aplikasi disampaikan lewat tiga kartu manfaat. --}}
    <section class="fitur py-5" aria-labelledby="judul-fitur">
        <div class="container">
            <p class="hero-dasbor__eyebrow mb-2 text-center">Kenapa LokerDekat</p>
            <h2 id="judul-fitur" class="text-center mb-2">Cari kerja tanpa muter-muter</h2>
            <p class="text-center text-secondary mb-4">Kami menampilkan yang paling relevan, bukan sekadar daftar panjang.</p>

            <div class="row g-4">
                <div class="col-12 col-md-4">
                    <div class="card panduan-card fitur-card h-100">
                        <div class="card-body p-4">
                            <span class="fitur-card__ikon fitur-card__ikon--jarak" aria-hidden="true"></span>
                            <h3 class="mt-3">Diurutkan dari terdekat</h3>
                            <p class="text-secondary mb-0">
                                Lihat lebih dulu loker yang paling dekat dengan lokasimu, lengkap dengan jaraknya.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card panduan-card fitur-card h-100">
                        <div class="card-body p-4">
                            <span class="fitur-card__ikon fitur-card__ikon--peta" aria-hidden="true"><span></span></span>
                            <h3 class="mt-3">Lokasi mudah ditemukan</h3>
                            <p class="text-secondary mb-0">
                                Peta titik lokasi dan alamat jalan tampil jelas di setiap halaman detail loker.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card panduan-card fitur-card h-100">
                        <div class="card-body p-4">
                            <span class="fitur-card__ikon fitur-card__ikon--favorit" aria-hidden="true">&#9829;</span>
                            <h3 class="mt-3">Simpan yang kamu minati</h3>
                            <p class="text-secondary mb-0">
                                Tandai loker favorit supaya bisa kamu buka lagi kapan saja dari satu halaman.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Panduan tiga langkah sesuai kendali R3 di docs/PRD.md --}}
    <section class="panduan py-5" aria-labelledby="judul-panduan">
        <div class="container">
            <p class="hero-dasbor__eyebrow mb-2 text-center">Cara pakai</p>
            <h2 id="judul-panduan" class="text-center mb-2">Tiga langkah, langsung jalan</h2>
            <p class="text-center text-secondary mb-4">Panduan singkat untuk kamu yang baru pertama kali memakai LokerDekat.</p>

            <ol class="row list-unstyled g-4 justify-content-center mb-0">
                <li class="col-12 col-md-4">
                    <div class="card panduan-card h-100">
                        <div class="card-body">
                            <span class="panduan-nomor" aria-hidden="true">1</span>
                            <h3 class="mt-3">Izinkan lokasi</h3>
                            <p class="text-secondary mb-0">
                                Izinkan browser mengakses lokasimu agar kami tahu loker mana yang paling dekat denganmu.
                            </p>
                        </div>
                    </div>
                </li>

                <li class="col-12 col-md-4">
                    <div class="card panduan-card h-100">
                        <div class="card-body">
                            <span class="panduan-nomor" aria-hidden="true">2</span>
                            <h3 class="mt-3">Lihat urut terdekat</h3>
                            <p class="text-secondary mb-0">
                                Daftar loker tersusun dari yang paling dekat ke lokasimu, lengkap dengan jarak dan divisi yang lowong.
                            </p>
                        </div>
                    </div>
                </li>

                <li class="col-12 col-md-4">
                    <div class="card panduan-card h-100">
                        <div class="card-body">
                            <span class="panduan-nomor" aria-hidden="true">3</span>
                            <h3 class="mt-3">Buka detail dan peta</h3>
                            <p class="text-secondary mb-0">
                                Ketuk sebuah loker untuk membaca syarat lengkapnya dan melihat titik lokasi di peta.
                            </p>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </section>

    <div class="bagian-pemisah" aria-hidden="true"><span></span></div>

    {{-- T-045: pertanyaan umum dijawab singkat tanpa memakai data database. --}}
    <section class="faq py-5" aria-labelledby="judul-faq">
        <div class="container">
            <p class="hero-dasbor__eyebrow mb-2 text-center">Pertanyaan umum</p>
            <h2 id="judul-faq" class="text-center mb-2">Pertanyaan yang sering ditanyakan</h2>
            <p class="text-center text-secondary mb-4">Singkat dan to-the-point, supaya kamu langsung tahu apa yang bisa dilakukan di sini.</p>

            <div class="row justify-content-center">
                <div class="col-12 col-lg-9">
                    <div class="accordion" id="faqLokerDekat">
                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-satu" aria-expanded="false" aria-controls="faq-satu">
                                    Apakah LokerDekat berbayar?
                                </button>
                            </h3>
                            <div id="faq-satu" class="accordion-collapse collapse" data-bs-parent="#faqLokerDekat">
                                <div class="accordion-body">
                                    Tidak. Kamu bisa mencari dan melihat loker secara gratis. LokerDekat tidak menyediakan layanan berbayar.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-dua" aria-expanded="false" aria-controls="faq-dua">
                                    Apakah lokasi saya disimpan?
                                </button>
                            </h3>
                            <div id="faq-dua" class="accordion-collapse collapse" data-bs-parent="#faqLokerDekat">
                                <div class="accordion-body">
                                    Tidak. Koordinat lokasi hanya dipakai di browser untuk mengurutkan loker dari yang terdekat dan tidak disimpan ke basis data.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h3 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-tiga" aria-expanded="false" aria-controls="faq-tiga">
                                    Bisa lamar langsung di sini?
                                </button>
                            </h3>
                            <div id="faq-tiga" class="accordion-collapse collapse" data-bs-parent="#faqLokerDekat">
                                <div class="accordion-body">
                                    Belum. LokerDekat saat ini hanya menyediakan informasi loker, peta lokasi, dan daftar favorit. Untuk melamar, hubungi pihak perusahaan langsung.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
