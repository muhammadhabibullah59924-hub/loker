@extends('layouts.app')

@section('title', ($loker ? 'Ubah Loker — ' : 'Tambah Loker — ') . 'LokerDekat')

@section('styles')
    {{-- Peta pemilih titik dari CDN yang sama dengan halaman detail (F3). --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('content')
    <div class="container py-4">
        <a class="d-inline-block mb-3" href="{{ route('admin.panel') }}">&larr; Kembali ke panel admin</a>

        <p class="hero-dasbor__eyebrow mb-2">Formulir loker</p>
        <h1 class="mb-1">{{ $loker ? 'Ubah loker' : 'Tambah loker' }}</h1>
        <p class="text-secondary mb-4">
            Nama PT, alamat, divisi, kota, dan titik peta wajib diisi. Loker yang disimpan langsung tampil di dasbor.
        </p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                Isian belum bisa disimpan. Periksa pesan di bawah, lalu coba lagi.
            </div>
        @endif

        <form method="POST"
              action="{{ $loker ? route('admin.loker.update', $loker->id) : route('admin.loker.store') }}"
              class="row g-4 align-items-start">
            @csrf
            @if ($loker)
                @method('PUT')
            @endif

            <div class="col-12 col-lg-8">
                {{-- T-041: isian dikelompokkan supaya mudah dipindai. --}}
                <section class="card panduan-card form-group-card mb-4" aria-labelledby="bagian-informasi">
                    <div class="card-body p-4">
                        <div class="form-group-card__kepala">
                            <span class="form-group-card__nomor" aria-hidden="true">1</span>
                            <div>
                                <h2 id="bagian-informasi" class="mb-0">Informasi loker</h2>
                                <p class="text-secondary small mb-0">Apa yang ditawarkan dan di mana lokasinya.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="mb-3">
                                <label class="form-label" for="company_name">Nama PT</label>
                                <input class="form-control @error('company_name') is-invalid @enderror"
                                       id="company_name" name="company_name" type="text" maxlength="255" required
                                       value="{{ old('company_name', $loker?->company_name) }}">
                                <div class="form-text">Nama perusahaan yang akan tampil di kartu loker.</div>
                                @error('company_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="address">Alamat</label>
                                <input class="form-control @error('address') is-invalid @enderror"
                                       id="address" name="address" type="text" maxlength="255" required
                                       value="{{ old('address', $loker?->address) }}">
                                <div class="form-text">Alamat jalan supaya pencari kerja mudah menemukan lokasinya.</div>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="division">Divisi</label>
                                    <input class="form-control @error('division') is-invalid @enderror"
                                           id="division" name="division" type="text" maxlength="255" required
                                           value="{{ old('division', $loker?->division) }}">
                                    @error('division')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="city">Kota</label>
                                    <input class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" type="text" maxlength="255" required
                                           value="{{ old('city', $loker?->city) }}">
                                    @error('city')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Deskripsi pekerjaan</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                          id="description" name="description" rows="4" required>{{ old('description', $loker?->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label" for="requirements">Syarat</label>
                                <textarea class="form-control @error('requirements') is-invalid @enderror"
                                          id="requirements" name="requirements" rows="4" required>{{ old('requirements', $loker?->requirements) }}</textarea>
                                @error('requirements')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card panduan-card form-group-card mb-4" aria-labelledby="bagian-jadwal">
                    <div class="card-body p-4">
                        <div class="form-group-card__kepala">
                            <span class="form-group-card__nomor" aria-hidden="true">2</span>
                            <div>
                                <h2 id="bagian-jadwal" class="mb-0">Jadwal loker</h2>
                                <p class="text-secondary small mb-0">Semua tanggal bersifat opsional.</p>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="deadline">Batas kirim lamaran</label>
                                <input class="form-control @error('deadline') is-invalid @enderror"
                                       id="deadline" name="deadline" type="date"
                                       value="{{ old('deadline', $loker?->deadline?->format('Y-m-d')) }}">
                                @error('deadline')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="opens_at">Tanggal buka</label>
                                <input class="form-control @error('opens_at') is-invalid @enderror"
                                       id="opens_at" name="opens_at" type="date"
                                       value="{{ old('opens_at', $loker?->opens_at?->format('Y-m-d')) }}">
                                @error('opens_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label" for="closes_at">Tanggal tutup</label>
                                <input class="form-control @error('closes_at') is-invalid @enderror"
                                       id="closes_at" name="closes_at" type="date"
                                       value="{{ old('closes_at', $loker?->closes_at?->format('Y-m-d')) }}">
                                @error('closes_at')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card panduan-card form-group-card mb-4" aria-labelledby="bagian-lokasi">
                    <div class="card-body p-4">
                        <div class="form-group-card__kepala">
                            <span class="form-group-card__nomor" aria-hidden="true">3</span>
                            <div>
                                <h2 id="bagian-lokasi" class="mb-0">Lokasi & peta</h2>
                                <p class="text-secondary small mb-0">Titik ini menentukan urutan jarak di dasbor.</p>
                            </div>
                        </div>

                        <fieldset class="mt-4 mb-0">
                            <legend class="visually-hidden">Titik peta lokasi PT</legend>

                            <div class="row g-3 mb-3">
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="latitude">Titik lintang</label>
                                    <input class="form-control @error('latitude') is-invalid @enderror"
                                           id="latitude" name="latitude" type="number" step="any" min="-90" max="90" required
                                           value="{{ old('latitude', $loker?->latitude) }}">
                                    @error('latitude')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-sm-6">
                                    <label class="form-label" for="longitude">Titik bujur</label>
                                    <input class="form-control @error('longitude') is-invalid @enderror"
                                           id="longitude" name="longitude" type="number" step="any" min="-180" max="180" required
                                           value="{{ old('longitude', $loker?->longitude) }}">
                                    @error('longitude')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <p class="text-secondary mb-2">
                                Klik peta untuk mengisi titik koordinat, atau ketik angkanya langsung. Titik peta wajib ada agar loker bisa diurutkan menurut jarak.
                            </p>

                            <div id="peta-pilih" class="job-map"
                                 aria-label="Peta untuk memilih titik lokasi PT"></div>
                        </fieldset>
                    </div>
                </section>

                <div class="card panduan-card">
                    <div class="card-body d-flex flex-wrap gap-2 p-4">
                        <button type="submit" class="btn btn-primary">{{ $loker ? 'Simpan perubahan' : 'Simpan loker' }}</button>
                        <a class="btn btn-outline-secondary" href="{{ route('admin.panel') }}">Batal</a>
                    </div>
                </div>
            </div>

            <aside class="col-12 col-lg-4" aria-labelledby="judul-panduan-form">
                <div class="card panduan-card admin-form__ringkasan">
                    <div class="card-body p-4">
                        <h2 id="judul-panduan-form" class="mb-3">Sebelum menyimpan</h2>
                        <ul class="admin-form__checklist">
                            <li>Nama PT sesuai identitas perusahaan.</li>
                            <li>Alamat jalan dan kota sudah lengkap.</li>
                            <li>Divisi dipilih yang paling mewakili loker.</li>
                            <li>Titik peta ditandai di lokasi sebenarnya.</li>
                        </ul>
                        <hr class="my-3">
                        <p class="text-secondary small mb-0">
                            Loker yang tersimpan langsung tampil di dasbor dan diurutkan dari yang terdekat ke lokasi pencari kerja.
                        </p>
                    </div>
                </div>
            </aside>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            const peta = document.getElementById('peta-pilih');
            const lintang = document.getElementById('latitude');
            const bujur = document.getElementById('longitude');

            // Pustaka tidak termuat: kedua kolom angka tetap bisa diisi manual.
            if (!peta || !lintang || !bujur || typeof L === 'undefined') {
                return;
            }

            const nilaiAwalLat = parseFloat(lintang.value);
            const nilaiAwalLng = parseFloat(bujur.value);
            const adaIsian = !Number.isNaN(nilaiAwalLat) && !Number.isNaN(nilaiAwalLng);

            // Pusat Kota Probolinggo bila form masih kosong (lihat ADR-009).
            const titik = adaIsian ? [nilaiAwalLat, nilaiAwalLng] : [-7.7145, 113.2146];
            const tampilan = L.map(peta).setView(titik, adaIsian ? 16 : 13);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" rel="noopener noreferrer" target="_blank">OpenStreetMap</a>'
            }).addTo(tampilan);

            let penanda = adaIsian ? L.marker(titik).addTo(tampilan) : null;

            tampilan.on('click', function (peristiwa) {
                lintang.value = peristiwa.latlng.lat.toFixed(7);
                bujur.value = peristiwa.latlng.lng.toFixed(7);

                if (penanda) {
                    penanda.setLatLng(peristiwa.latlng);
                } else {
                    penanda = L.marker(peristiwa.latlng).addTo(tampilan);
                }
            });
        })();
    </script>
@endsection
