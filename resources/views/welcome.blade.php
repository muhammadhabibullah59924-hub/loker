@extends('layouts.app')

@section('title', 'LokerDekat — loker terdekat dari lokasimu')

@section('content')
    {{-- Bagian pembuka: logo, tagline, dan tombol Masuk/Daftar --}}
    <section class="hero text-center" aria-labelledby="judul-hero">
        <div class="container">
            <img src="{{ asset('uploads/logo.svg') }}" alt="Logo LokerDekat" width="64" height="64" class="mb-3">

            <h1 id="judul-hero" class="mb-3">Loker terdekat dari lokasimu</h1>

            {{-- Tagline persis dari SPEC.json (meta.one_liner) --}}
            <p class="lead hero-tagline mx-auto mb-4">
                Website info loker yang mengurutkan lowongan dari yang paling dekat ke lokasi pengguna,
                lengkap dengan peta dan divisi yang lowong.
            </p>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                <a href="/register" class="btn btn-primary btn-lg">Daftar dan lihat loker terdekat</a>
                <a href="/login" class="btn btn-outline-secondary btn-lg">Masuk</a>
            </div>
        </div>
    </section>

    {{-- Panduan tiga langkah sesuai kendali R3 di docs/PRD.md --}}
    <section class="panduan py-5" aria-labelledby="judul-panduan">
        <div class="container">
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
@endsection
