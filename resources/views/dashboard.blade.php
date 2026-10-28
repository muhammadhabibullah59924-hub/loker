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

        <form method="GET" action="{{ route('dasbor') }}" class="card panduan-card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-8">
                        <label class="form-label" for="cari-loker">Cari loker</label>
                        <input class="form-control" id="cari-loker" name="q" type="search" maxlength="100" value="{{ $q ?? '' }}" placeholder="Nama PT atau divisi">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="radius-loker">Radius jarak</label>
                        <select class="form-select" id="radius-loker" name="radius">
                            <option value="" @selected(($radius ?? null) === null)>Semua radius</option>
                            @foreach ([1, 5, 10, 25, 50] as $radiusOpsi)
                                <option value="{{ $radiusOpsi }}" @selected((int) ($radius ?? 0) === $radiusOpsi)>{{ $radiusOpsi }} km</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Cari loker</button>
                    </div>
                </div>

                @if ($dari_lokasi_pengguna)
                    <input type="hidden" name="lat" value="{{ $titik['lat'] }}">
                    <input type="hidden" name="lng" value="{{ $titik['lng'] }}">
                @endif
            </div>
        </form>

        @if ($lokers->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    @if ($q)
                        <p class="mb-2">Belum ada loker yang cocok. Coba ubah kata kunci pencarianmu.</p>
                        <p class="text-secondary mb-0">Coba kata kunci lain, atau lihat loker terdekat dari lokasimu.</p>
                    @elseif ($radius)
                        <p class="mb-2">Belum ada loker dalam radius {{ $radius }} km. Coba pilih radius yang lebih luas.</p>
                        <p class="text-secondary mb-0">Coba pilih radius yang lebih besar, atau ajak admin memasukkan loker.</p>
                    @else
                        <p class="mb-2">Belum ada loker yang tayang saat ini.</p>
                        <p class="text-secondary mb-0">Coba buka lagi beberapa saat lagi, atau daftar untuk menyimpan loker favorit.</p>
                    @endif
                </div>
            </div>
        @else
            @if ($radius)
                <p class="text-secondary mb-3">Menampilkan loker terdekat dalam radius {{ $radius }} km dari lokasimu.</p>
            @endif
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
