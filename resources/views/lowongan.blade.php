@extends('layouts.app')

@section('title', $loker->company_name . ' — LokerDekat')

@section('styles')
    {{-- Pustaka peta dari CDN tanpa kunci API (docs/ARCHITECTURE.md). --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('content')
    <div class="container py-4">
        <a class="d-inline-block mb-3" href="{{ route('dasbor') }}">&larr; Kembali ke dasbor</a>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <h1 class="mb-2">{{ $loker->company_name }}</h1>
                <p class="mb-3">
                    <span class="badge border job-card__divisi">{{ $loker->division }}</span>
                    <span class="text-secondary ms-1">{{ $loker->city }}</span>
                </p>

                {{-- Alamat ditulis sebagai teks biasa supaya tetap terbaca bila peta gagal dimuat. --}}
                <p class="job-detail__alamat mb-4">{{ $loker->address }}</p>

                @if ($loker->deadline)
                    <p class="text-secondary mb-4">Batas kirim lamaran: {{ $loker->deadline->format('d-m-Y') }}</p>
                @endif

                <h2 class="mb-2">Deskripsi pekerjaan</h2>
                <p>{{ $loker->description }}</p>

                <h2 class="mb-2">Syarat</h2>
                <p class="mb-0">{{ $loker->requirements }}</p>
            </div>

            <div class="col-12 col-lg-5">
                <div class="card panduan-card">
                    <div class="card-body">
                        <h2 class="mb-2">Lokasi PT</h2>

                        <div id="peta-loker" class="job-map"
                             data-lat="{{ $loker->latitude }}"
                             data-lng="{{ $loker->longitude }}"
                             data-nama="{{ $loker->company_name }}"
                             aria-label="Peta lokasi {{ $loker->company_name }} di {{ $loker->address }}">
                        </div>

                        <noscript>
                            <p class="text-secondary mt-2 mb-0">
                                Peta membutuhkan JavaScript. Alamat lokasi PT: {{ $loker->address }}
                            </p>
                        </noscript>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const peta = document.getElementById('peta-loker');

            // Pustaka tidak termuat: alamat sudah tampil sebagai teks di sebelah kiri.
            if (!peta || typeof L === 'undefined') {
                return;
            }

            const lat = parseFloat(peta.dataset.lat);
            const lng = parseFloat(peta.dataset.lng);

            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                return;
            }

            const tampilan = L.map(peta).setView([lat, lng], 16);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" rel="noopener noreferrer" target="_blank">OpenStreetMap</a>'
            }).addTo(tampilan);

            // Teks biasa, bukan HTML, supaya nama PT tidak bisa menyuntikkan skrip.
            L.marker([lat, lng]).addTo(tampilan).bindPopup(document.createTextNode(peta.dataset.nama));
        })();
    </script>
@endsection
