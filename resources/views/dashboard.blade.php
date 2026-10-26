@extends('layouts.app')

@section('title', 'Dasbor — LokerDekat')

@section('content')
    <div class="container py-4">
        <h1 class="mb-1">Loker terdekat dari lokasimu</h1>
        <p class="text-secondary mb-4">
            @if ($dari_lokasi_pengguna)
                Diurutkan menurut lokasimu saat ini, dari yang paling dekat.
            @else
                Diurutkan dari {{ $lokasi_default }} sampai kamu mengizinkan lokasi.
            @endif
        </p>

        @unless ($dari_lokasi_pengguna)
            <div class="alert alert-warning d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" role="alert">
                <span id="pesan-lokasi">Izinkan lokasi agar loker diurutkan dari yang terdekat.</span>
                <button type="button" class="btn btn-warning btn-sm flex-shrink-0" id="tombol-izin">Izinkan lokasi</button>
            </div>
        @endunless

        @if ($lokers->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    <p class="mb-2">Belum ada loker yang tayang saat ini.</p>
                    <p class="text-secondary mb-0">Coba buka lagi beberapa saat lagi, atau daftar untuk menyimpan loker favorit.</p>
                </div>
            </div>
        @else
            <h2 class="h4 mb-3">Daftar loker</h2>

            <div class="row g-3">
                @foreach ($lokers as $item)
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="card panduan-card job-card h-100">
                            <div class="card-body">
                                {{-- Urutan isi wajib: nama PT, alamat, divisi, jarak (AGENTS.md) --}}
                                <span class="job-card__jarak">{{ number_format($item['jarak'], 1, ',', '.') }} km</span>
                                <h3 class="mb-2">
                                    <a class="job-card__judul" href="{{ route('lowongan.show', $item['loker']->id) }}">{{ $item['loker']->company_name }}</a>
                                </h3>
                                <p class="text-secondary mb-2">{{ $item['loker']->address }}</p>
                                <p class="mb-0">
                                    <span class="badge border job-card__divisi">{{ $item['loker']->division }}</span>
                                    <span class="text-secondary small ms-1">{{ $item['loker']->city }}</span>
                                </p>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@unless ($dari_lokasi_pengguna)
    @section('scripts')
        <script>
            (function () {
                const pesan = document.getElementById('pesan-lokasi');
                const tombol = document.getElementById('tombol-izin');

                if (!pesan || !tombol) {
                    return;
                }

                function mintaLokasi() {
                    if (!navigator.geolocation) {
                        pesan.textContent = 'Browser kamu belum mendukung izin lokasi. Dasbor tetap menampilkan loker terurut dari pusat Kota Probolinggo.';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function (posisi) {
                            const lat = posisi.coords.latitude.toFixed(7);
                            const lng = posisi.coords.longitude.toFixed(7);
                            window.location.assign('{{ route('dasbor') }}?lat=' + lat + '&lng=' + lng);
                        },
                        function () {
                            pesan.textContent = 'Browser menolak akses lokasi. Izinkan lokasi lewat ikon gembok di address bar agar loker diurutkan dari yang terdekat.';
                        },
                        { enableHighAccuracy: false, timeout: 8000, maximumAge: 60000 }
                    );
                }

                tombol.addEventListener('click', mintaLokasi);

                // Dasbor meminta izin lokasi begitu dibuka (alur F2 di docs/PRD.md).
                mintaLokasi();
            })();
        </script>
    @endsection
@endunless
