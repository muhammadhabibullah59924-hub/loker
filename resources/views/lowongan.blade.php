@extends('layouts.app')

@section('title', $loker->company_name . ' — LokerDekat')

@section('styles')
    {{-- Pustaka peta disimpan lokal agar tidak bergantung pada CDN saat dibuka dari HP (ADR-013). --}}
    <link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}?v=1.9.4">
@endsection

@section('content')
    <div class="container py-4">
        <nav aria-label="Navigasi kembali" class="mb-3">
            <a class="d-inline-block" href="{{ route('dasbor') }}">&larr; Kembali ke dasbor</a>
        </nav>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <p class="hero-dasbor__eyebrow mb-0">Detail loker</p>
            {{-- T-056: di HP peta ada di bawah, jadi disediakan tautan lompat. --}}
            <a class="btn btn-sm btn-outline-secondary" href="#peta-loker">Lihat peta lokasi</a>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                {{-- Identitas loker: urutan isi nama PT, alamat, divisi, jarak (AGENTS.md). --}}
                <div class="card panduan-card mb-4">
                    <div class="card-body p-4">
                        <h1 class="mb-2">{{ $loker->company_name }}</h1>
                        <p class="mb-3">
                            <span class="badge border job-card__divisi">{{ $loker->division }}</span>
                            <span class="text-secondary ms-1">{{ $loker->city }}</span>
                        </p>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @if ($loker->is_premium)
                                <span class="badge bg-primary">Premium Employer</span>
                            @endif

                            @if ($loker->job_type)
                                <span class="badge job-tipe job-tipe--{{ strtolower($loker->job_type) }}">{{ $loker->job_type }}</span>
                                <span class="text-secondary small">Lowongan butuh {{ $loker->positions_needed }} orang</span>
                            @endif
                        </div>

                        {{-- Alamat ditulis sebagai teks biasa supaya tetap terbaca bila peta gagal dimuat. --}}
                        <p class="job-detail__alamat mb-4">{{ $loker->address }}</p>

                        @if ($loker->deadline)
                            <p class="text-secondary mb-0">
                                Batas kirim lamaran: {{ $loker->deadline->locale('id')->translatedFormat('j F Y') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="card panduan-card mb-4">
                    <div class="card-body p-4">
                        <h2 class="mb-2">Deskripsi pekerjaan</h2>
                        <p class="mb-0">{{ $loker->description }}</p>
                    </div>
                </div>

                <div class="card panduan-card">
                    <div class="card-body p-4">
                        <h2 class="mb-2">Syarat</h2>
                        <p class="mb-0">{{ $loker->requirements }}</p>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="detail-sidebar">
                    <div class="card panduan-card mb-3">
                        <div class="card-body p-4">
                            <h2 class="mb-3">Ringkasan loker</h2>

                            <dl class="ringkasan-loker mb-0">
                                <div class="ringkasan-loker__baris">
                                    <dt>Divisi</dt>
                                    <dd>{{ $loker->division }}</dd>
                                </div>
                                <div class="ringkasan-loker__baris">
                                    <dt>Tipe</dt>
                                    <dd>
                                        @if ($loker->job_type)
                                            <span class="badge job-tipe job-tipe--{{ strtolower($loker->job_type) }}">{{ $loker->job_type }}</span>
                                        @else
                                            Belum diisi
                                        @endif
                                    </dd>
                                </div>
                                <div class="ringkasan-loker__baris">
                                    <dt>Kebutuhan</dt>
                                    <dd>{{ $loker->positions_needed }} orang</dd>
                                </div>
                                <div class="ringkasan-loker__baris">
                                    <dt>Batas lamaran</dt>
                                    <dd>
                                        @if ($loker->deadline)
                                            {{ $loker->deadline->locale('id')->translatedFormat('j F Y') }}
                                        @else
                                            Tanpa batas waktu
                                        @endif
                                    </dd>
                                </div>
                                <div class="ringkasan-loker__baris">
                                    <dt>Kota</dt>
                                    <dd>{{ $loker->city }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="card panduan-card mb-3">
                        <div class="card-body p-4">
                            <h2 class="mb-2">Lokasi PT</h2>

                            <div id="peta-loker" class="job-map"
                                 data-lat="{{ $loker->latitude }}"
                                 data-lng="{{ $loker->longitude }}"
                                 data-nama="{{ $loker->company_name }}"
                                 aria-label="Peta lokasi {{ $loker->company_name }} di {{ $loker->address }}">
                            </div>

                            {{-- T-056: galat tidak ditelan diam-diam; pengguna diberi langkah berikutnya. --}}
                            <p id="peta-gagal" class="alert alert-warning small mt-2 mb-0 d-none" role="alert">
                                Peta tidak dapat ditampilkan di perangkat ini. Buka lokasi melalui tautan OpenStreetMap di bawah, atau gunakan alamat PT yang tertulis di halaman ini.
                            </p>

                            {{-- T-056: tetap bisa dipakai walau JavaScript, CDN, atau ubin peta gagal. --}}
                            <a class="small d-inline-block mt-2"
                               href="https://www.openstreetmap.org/?mlat={{ $loker->latitude }}&amp;mlng={{ $loker->longitude }}#map=16/{{ $loker->latitude }}/{{ $loker->longitude }}"
                               target="_blank" rel="noopener noreferrer">Buka lokasi di OpenStreetMap</a>

                            <noscript>
                                <p class="text-secondary mt-2 mb-0">
                                    Peta membutuhkan JavaScript. Alamat lokasi PT: {{ $loker->address }}
                                </p>
                            </noscript>
                        </div>
                    </div>

                    {{-- T-038: aksi simpan favorit langsung dari halaman detail. --}}
                    <div class="card panduan-card">
                        <div class="card-body p-4">
                            @if ($sudahFavorit)
                                <a class="btn btn-primary w-100" href="{{ route('favorit.index') }}">Lihat di favorit</a>
                                <p class="text-secondary small text-center mb-0 mt-2">
                                    Loker ini sudah tersimpan di favoritmu.
                                </p>
                            @else
                                <form method="POST" action="{{ route('favorit.store', $loker->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100">Simpan favorit</button>
                                </form>
                                <p class="text-secondary small text-center mb-0 mt-2">
                                    Simpan loker ini agar mudah kamu buka lagi nanti.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- T-044: rekomendasi loker lain dengan divisi atau kota yang sama. --}}
        @if ($serupa->isNotEmpty())
            <section class="mt-5 pt-4 border-top" aria-labelledby="judul-serupa">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-end gap-2 mb-3">
                    <div>
                        <h2 id="judul-serupa" class="mb-1">Loker serupa</h2>
                        <p class="text-secondary mb-0">
                            Loker lain di divisi {{ $loker->division }} atau kota {{ $loker->city }} yang masih tayang.
                        </p>
                    </div>
                </div>

                <div class="row g-3">
                    @foreach ($serupa as $item)
                        <div class="col-12 col-md-6 col-lg-4">
                            <article class="card panduan-card job-card h-100 {{ $item['loker']->job_type ? 'job-card--'.strtolower($item['loker']->job_type) : '' }}">
                                <div class="card-body">
                                    <span class="job-card__jarak">{{ number_format($item['jarak'], 1, ',', '.') }} km</span>
                                    <h3 class="mb-2">
                                        <a class="job-card__judul" href="{{ route('lowongan.show', $item['loker']->id) }}">{{ $item['loker']->company_name }}</a>
                                    </h3>
                                    <p class="text-secondary mb-2">{{ $item['loker']->address }}</p>
                                    <p class="mb-0">
                                        <span class="badge border job-card__divisi">{{ $item['loker']->division }}</span>
                                        <span class="text-secondary small ms-1">{{ $item['loker']->city }}</span>
                                    </p>

                                    @if ($item['loker']->job_type)
                                        <div class="d-flex flex-wrap gap-2 mt-2 mb-2">
                                            <span class="badge job-tipe job-tipe--{{ strtolower($item['loker']->job_type) }}">{{ $item['loker']->job_type }}</span>
                                        </div>
                                    @endif

                                    <p class="text-secondary small mb-0">
                                        @if ($item['loker']->deadline)
                                            Batas lamaran: {{ $item['loker']->deadline->locale('id')->translatedFormat('j F Y') }}
                                        @else
                                            Tanpa batas waktu
                                        @endif
                                    </p>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('vendor/leaflet/leaflet.js') }}?v=1.9.4"></script>
    <script>
        (function () {
            const peta = document.getElementById('peta-loker');
            const pesanGagal = document.getElementById('peta-gagal');

            const tampilkanPesanGagal = function () {
                if (pesanGagal) {
                    pesanGagal.classList.remove('d-none');
                }
            };

            const hentikanPetaKosong = function () {
                if (peta) {
                    peta.classList.add('job-map--gagal');
                }

                tampilkanPesanGagal();
            };

            // Pustaka tidak termuat: jangan biarkan kotak abu-abu tanpa penjelasan.
            if (!peta || typeof L === 'undefined') {
                hentikanPetaKosong();
                return;
            }

            const lat = parseFloat(peta.dataset.lat);
            const lng = parseFloat(peta.dataset.lng);

            if (Number.isNaN(lat) || Number.isNaN(lng)) {
                hentikanPetaKosong();
                return;
            }

            const tampilan = L.map(peta).setView([lat, lng], 16);
            let jumlahGalatUbin = 0;

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" rel="noopener noreferrer" target="_blank">OpenStreetMap</a>'
            }).on('tileerror', function () {
                jumlahGalatUbin += 1;

                // Ubin gagal berulang: peta tetap ada, tapi pengguna diberi tautan OSM.
                if (jumlahGalatUbin >= 3) {
                    tampilkanPesanGagal();
                }
            }).addTo(tampilan);

            // Teks biasa, bukan HTML, supaya nama PT tidak bisa menyuntikkan skrip.
            L.marker([lat, lng]).addTo(tampilan).bindPopup(document.createTextNode(peta.dataset.nama));

            // T-056: layout HP selesai setelah rendering, ukuran peta dihitung ulang.
            const perbaruiUkuran = function () {
                tampilan.invalidateSize();
            };

            window.addEventListener('load', perbaruiUkuran);
            window.addEventListener('resize', perbaruiUkuran);
            window.addEventListener('orientationchange', perbaruiUkuran);
        })();
    </script>
@endsection
