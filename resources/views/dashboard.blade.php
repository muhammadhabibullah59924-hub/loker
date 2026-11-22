@extends('layouts.app')

@section('title', 'Dasbor — LokerDekat')

@section('content')
    <div class="container py-4">
        <section class="hero-dasbor mb-4" aria-labelledby="judulHero">
            <p class="hero-dasbor__eyebrow mb-2">Rekomendasi terdekat</p>
            <h1 id="judulHero" class="mb-3">Loker terdekat dari lokasimu</h1>
            <p class="hero-dasbor__sub mb-0">
                @if ($dari_lokasi_pengguna)
                    Diurutkan menurut lokasimu saat ini, dari yang paling dekat.
                @else
                    Diurutkan dari {{ $lokasi_default }} sampai kamu mengizinkan lokasi.
                @endif
            </p>
        </section>

        @unless ($dari_lokasi_pengguna)
            <div class="alert alert-warning d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3" role="alert">
                <span id="pesan-lokasi">Izinkan lokasi agar loker diurutkan dari yang terdekat.</span>
                <button type="button" class="btn btn-warning btn-sm flex-shrink-0" id="tombol-izin">Izinkan lokasi</button>
            </div>
        @endunless

        {{-- T-042: pengingat akses dari HP tanpa tautan toko aplikasi palsu. --}}
        <div class="card panduan-card banner-hp mb-4" id="banner-hp" role="note">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
                <span class="banner-hp__telepon" aria-hidden="true"><span></span></span>
                <div class="flex-grow-1">
                    <p class="banner-hp__judul mb-1">Buka LokerDekat di HP</p>
                    <p class="text-secondary small mb-0">
                        Izinkan lokasi agar loker terdekat muncul otomatis, lalu tambahkan halaman ini ke layar utama supaya bisa dibuka dengan sekali ketuk.
                    </p>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary banner-hp__tutup flex-shrink-0" aria-label="Tutup pengingat">
                    Tutup
                </button>
            </div>
        </div>

        @if ($jumlahLokerAktif > 0)
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="card panduan-card stat-card h-100">
                        <div class="card-body">
                            <p class="stat-card__label">Loker aktif tersedia</p>
                            <p class="stat-card__nilai">{{ $jumlahLokerAktif }}</p>
                            <p class="text-secondary small mb-0">Siap dilihat pencari kerja</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card panduan-card stat-card stat-card--secondary h-100">
                        <div class="card-body">
                            <p class="stat-card__label">Loker populer</p>
                            <p class="stat-card__nilai">{{ $jumlahLokerPopuler }}</p>
                            <p class="text-secondary small mb-0">Butuh lebih dari 1 orang</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card panduan-card stat-card stat-card--premium h-100">
                        <div class="card-body">
                            <p class="stat-card__label">Loker premium</p>
                            <p class="stat-card__nilai">{{ $jumlahLokerPremium }}</p>
                            <p class="text-secondary small mb-0">Berlabel Premium Employer</p>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="card panduan-card stat-card stat-card--butuh h-100">
                        <div class="card-body">
                            <p class="stat-card__label">Divisi tersedia</p>
                            <p class="stat-card__nilai">{{ $jumlahDivisi }}</p>
                            <p class="text-secondary small mb-0">Bisa dipilih lewat chip divisi</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <form method="GET" action="{{ route('dasbor') }}" class="card panduan-card mb-4" id="form-filter-loker" data-filter-otomatis>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label" for="cari-loker">Cari loker</label>
                        <input class="form-control" id="cari-loker" name="q" type="search" maxlength="100" value="{{ $q ?? '' }}" placeholder="Nama PT atau divisi">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="tipe-loker">Tipe loker</label>
                        <select class="form-select" id="tipe-loker" name="tipe">
                            <option value="" @selected(($tipe ?? null) === null)>Semua tipe</option>
                            @foreach (['Full-Time', 'Part-Time', 'Contract', 'Internship'] as $tipeOpsi)
                                <option value="{{ $tipeOpsi }}" @selected(($tipe ?? null) === $tipeOpsi)>{{ $tipeOpsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="radius-loker">Radius jarak</label>
                        <select class="form-select" id="radius-loker" name="radius">
                            <option value="" @selected(($radius ?? null) === null)>Semua radius</option>
                            @foreach ([1, 5, 10, 25, 50] as $radiusOpsi)
                                <option value="{{ $radiusOpsi }}" @selected((int) ($radius ?? 0) === $radiusOpsi)>{{ $radiusOpsi }} km</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label" for="urut-loker">Urutkan</label>
                        <select class="form-select" id="urut-loker" name="urut">
                            <option value="terdekat" @selected(($urut ?? null) === null || ($urut ?? null) === 'terdekat')>Terdekat</option>
                            <option value="terbaru" @selected(($urut ?? null) === 'terbaru')>Terbaru</option>
                            <option value="butuh" @selected(($urut ?? null) === 'butuh')>Butuh terbanyak</option>
                        </select>
                    </div>
                    <div class="col-12 d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                        <button type="submit" class="btn btn-primary">Cari loker</button>
                        <span class="text-secondary small" id="status-filter" role="status" aria-live="polite"></span>
                    </div>
                </div>

                @if ($dari_lokasi_pengguna)
                    <input type="hidden" name="lat" value="{{ $titik['lat'] }}">
                    <input type="hidden" name="lng" value="{{ $titik['lng'] }}">
                @endif
            </div>
        </form>

        @if ($divisiTersedia->isNotEmpty())
            {{-- T-050: chip divisi jadi satu pita yang bisa digeser, bukan daftar yang turun semua. --}}
            <div class="chip-scroll d-flex flex-nowrap gap-2 overflow-x-auto pb-2 mb-1" aria-label="Filter divisi" aria-describedby="petunjuk-geser-divisi" tabindex="0">
                <a class="chip-divisi {{ ($divisi ?? null) === null ? 'chip-divisi--aktif' : '' }}" href="{{ request()->fullUrlWithQuery(['divisi' => null]) }}">Semua divisi</a>
                @foreach ($divisiTersedia as $opsi)
                    <a class="chip-divisi {{ ($divisi ?? null) === $opsi ? 'chip-divisi--aktif' : '' }}" href="{{ request()->fullUrlWithQuery(['divisi' => $opsi]) }}">{{ $opsi }}</a>
                @endforeach
            </div>
            <p class="text-secondary small mb-4" id="petunjuk-geser-divisi">
                <span aria-hidden="true">↔</span> Geser ke samping untuk melihat divisi lain.
            </p>
        @endif

        <div class="d-flex flex-wrap gap-2 mb-4" aria-label="Filter tipe loker cepat">
            <a class="chip-divisi {{ ($tipe ?? null) === 'Full-Time' ? 'chip-divisi--aktif' : '' }}" href="{{ request()->fullUrlWithQuery(['tipe' => 'Full-Time']) }}">Full-Time</a>
            <a class="chip-divisi {{ ($tipe ?? null) === 'Part-Time' ? 'chip-divisi--aktif' : '' }}" href="{{ request()->fullUrlWithQuery(['tipe' => 'Part-Time']) }}">Part-Time</a>
            @if ($tipe)
                <a class="chip-divisi" href="{{ request()->fullUrlWithQuery(['tipe' => null]) }}">Semua tipe</a>
            @endif
        </div>

        @if ($lokers->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    @if ($q)
                        <p class="mb-2">Belum ada loker yang cocok. Coba ubah kata kunci pencarianmu.</p>
                        <p class="text-secondary mb-0">Coba kata kunci lain, atau lihat loker terdekat dari lokasimu.</p>
                    @elseif ($radius)
                        <p class="mb-2">Belum ada loker dalam radius {{ $radius }} km. Coba pilih radius yang lebih luas.</p>
                        <p class="text-secondary mb-0">Coba pilih radius yang lebih besar, atau ajak admin memasukkan loker.</p>
                    @elseif ($divisi)
                        <p class="mb-2">Belum ada loker di divisi {{ $divisi }}. Coba pilih divisi lain.</p>
                        <p class="text-secondary mb-0">Pilih chip "Semua divisi" atau ubah kata kunci pencarianmu.</p>
                    @else
                        <p class="mb-2">Belum ada loker yang tayang saat ini.</p>
                        <p class="text-secondary mb-0">Coba buka lagi beberapa saat lagi, atau daftar untuk menyimpan loker favorit.</p>
                    @endif
                </div>
            </div>
        @else
            @if ($divisi)
                <p class="text-secondary mb-3">Menampilkan loker divisi {{ $divisi }}.</p>
            @endif

            @if ($tipe)
                <p class="text-secondary mb-3">Menampilkan loker bertipe {{ $tipe }}.</p>
            @endif

            @if ($radius)
                <p class="text-secondary mb-3">Menampilkan loker terdekat dalam radius {{ $radius }} km dari lokasimu.</p>
            @endif

            @if ($loker_populer->isNotEmpty())
                <h2 class="mb-3" id="loker-popular">Loker Populer</h2>
                <div class="card panduan-card mb-4">
                    <div class="card-body d-flex flex-column flex-md-row align-items-md-center gap-3">
                        <div>
                            <p class="text-uppercase text-secondary small mb-1">Rekomendasi</p>
                            <p class="mb-2 text-secondary">Loker yang sedang butuh banyak orang. Jangan lewatkan kesempatan ini!</p>
                        </div>
                        <a href="#loker-popular" class="btn btn-primary ms-md-auto">Lihat loker populer</a>
                    </div>
                </div>
                <div class="row g-3 mb-4">
                    @foreach ($loker_populer as $item)
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
                                    @if ($item['loker']->is_premium || $item['loker']->job_type)
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            @if ($item['loker']->is_premium)
                                                <span class="badge bg-primary">Premium Employer</span>
                                            @endif

                                            @if ($item['loker']->job_type)
                                                <span class="badge job-tipe job-tipe--{{ strtolower($item['loker']->job_type) }}">{{ $item['loker']->job_type }}</span>
                                                <span class="text-secondary small">Lowongan butuh {{ $item['loker']->positions_needed }} orang</span>
                                            @endif
                                        </div>
                                    @endif
                                    <p class="text-secondary small mb-2">
                                        @if ($item['loker']->deadline)
                                            Batas lamaran: {{ $item['loker']->deadline->locale('id')->translatedFormat('j F Y') }}
                                        @else
                                            Tanpa batas waktu
                                        @endif
                                    </p>
                                    <form method="POST" action="{{ route('favorit.store', $item['loker']->id) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Simpan favorit</button>
                                    </form>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif

            <h2 class="mb-3">Daftar loker</h2>

            <div class="row g-3">
                @foreach ($lokers as $item)
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="card panduan-card job-card h-100 {{ $item['loker']->job_type ? 'job-card--'.strtolower($item['loker']->job_type) : '' }}">
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
                                @if ($item['loker']->is_premium || $item['loker']->job_type)
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @if ($item['loker']->is_premium)
                                            <span class="badge bg-primary">Premium Employer</span>
                                        @endif

                                        @if ($item['loker']->job_type)
                                            <span class="badge job-tipe job-tipe--{{ strtolower($item['loker']->job_type) }}">{{ $item['loker']->job_type }}</span>
                                            <span class="text-secondary small">Lowongan butuh {{ $item['loker']->positions_needed }} orang</span>
                                        @endif
                                    </div>
                                @endif
                                <p class="text-secondary small mb-2">
                                    @if ($item['loker']->deadline)
                                        Batas lamaran: {{ $item['loker']->deadline->locale('id')->translatedFormat('j F Y') }}
                                    @else
                                        Tanpa batas waktu
                                    @endif
                                </p>
                                <form method="POST" action="{{ route('favorit.store', $item['loker']->id) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Simpan favorit</button>
                                </form>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // T-042: pengingat HP bisa ditutup dan pilihannya disimpan di browser.
        (function () {
            const banner = document.getElementById('banner-hp');

            if (!banner) {
                return;
            }

            try {
                if (window.localStorage.getItem('banner-hp-ditutup') === '1') {
                    banner.classList.add('d-none');
                }
            } catch (kesalahan) {
                // Penyimpanan browser tidak tersedia: banner tetap tampil seperti biasa.
            }

            const tombol = banner.querySelector('.banner-hp__tutup');

            if (tombol) {
                tombol.addEventListener('click', function () {
                    banner.classList.add('d-none');

                    try {
                        window.localStorage.setItem('banner-hp-ditutup', '1');
                    } catch (kesalahan) {
                        // Abaikan bila penyimpanan browser diblokir.
                    }
                });
            }
        })();
    </script>

    <script>
        // T-037: filter otomatis. Mengubah pilihan langsung menerapkan filter,
        // dan kata kunci dikirim setelah pengguna berhenti mengetik.
        (function () {
            const form = document.getElementById('form-filter-loker');
            const status = document.getElementById('status-filter');

            if (!form || !status) {
                return;
            }

            const kataKunci = document.getElementById('cari-loker');
            const pilihan = form.querySelectorAll('select[name="tipe"], select[name="radius"], select[name="urut"]');
            let jedaKetik = null;

            function terapkanFilter() {
                status.textContent = 'Menerapkan filter…';
                form.submit();
            }

            pilihan.forEach(function (elemen) {
                elemen.addEventListener('change', terapkanFilter);
            });

            if (kataKunci) {
                kataKunci.addEventListener('input', function () {
                    window.clearTimeout(jedaKetik);
                    jedaKetik = window.setTimeout(terapkanFilter, 600);
                });

                // Enter langsung mengirim form tanpa menunggu jeda ketik.
                kataKunci.addEventListener('keydown', function (peristiwa) {
                    if (peristiwa.key === 'Enter') {
                        window.clearTimeout(jedaKetik);
                    }
                });
            }

            // Penanda kesiapan filter otomatis (dipakai uji E2E dan pengembangan).
            document.documentElement.setAttribute('data-filter-siap', '1');
        })();

        // T-050: bawa chip divisi yang aktif ke dalam pita yang bisa digeser.
        (function () {
            const pita = document.querySelector('.chip-scroll');
            const aktif = pita && pita.querySelector('.chip-divisi--aktif');

            if (aktif && typeof aktif.scrollIntoView === 'function') {
                aktif.scrollIntoView({ inline: 'center', block: 'nearest' });
            }
        })();
    </script>

    @unless ($dari_lokasi_pengguna)
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
    @endunless
@endsection
