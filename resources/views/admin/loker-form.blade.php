@extends('layouts.app')

@section('title', ($loker ? 'Ubah Loker — ' : 'Tambah Loker — ') . 'LokerDekat')

@section('styles')
    {{-- Peta pemilih titik dari CDN yang sama dengan halaman detail (F3). --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
@endsection

@section('content')
    <div class="container py-4">
        <a class="d-inline-block mb-3" href="{{ route('admin.panel') }}">&larr; Kembali ke panel admin</a>

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
              class="card panduan-card">
            @csrf
            @if ($loker)
                @method('PUT')
            @endif

            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="company_name">Nama PT</label>
                    <input class="form-control @error('company_name') is-invalid @enderror"
                           id="company_name" name="company_name" type="text" maxlength="255" required
                           value="{{ old('company_name', $loker?->company_name) }}">
                    @error('company_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="address">Alamat</label>
                    <input class="form-control @error('address') is-invalid @enderror"
                           id="address" name="address" type="text" maxlength="255" required
                           value="{{ old('address', $loker?->address) }}">
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

                <div class="mb-3">
                    <label class="form-label" for="requirements">Syarat</label>
                    <textarea class="form-control @error('requirements') is-invalid @enderror"
                              id="requirements" name="requirements" rows="4" required>{{ old('requirements', $loker?->requirements) }}</textarea>
                    @error('requirements')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="deadline">Batas kirim lamaran <span class="text-secondary">(opsional)</span></label>
                    <input class="form-control @error('deadline') is-invalid @enderror"
                           id="deadline" name="deadline" type="date"
                           value="{{ old('deadline', $loker?->deadline?->format('Y-m-d')) }}">
                    @error('deadline')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <fieldset class="mb-0">
                    <legend class="form-label">Titik peta</legend>
                    <p class="text-secondary mb-2">
                        Klik peta untuk mengisi titik koordinat, atau ketik angkanya langsung. Titik peta wajib ada agar loker bisa diurutkan menurut jarak.
                    </p>

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

                    <div id="peta-pilih" class="job-map"
                         aria-label="Peta untuk memilih titik lokasi PT"></div>
                </fieldset>
            </div>

            <div class="card-footer d-flex flex-wrap gap-2">
                <button type="submit" class="btn btn-primary">{{ $loker ? 'Simpan perubahan' : 'Simpan loker' }}</button>
                <a class="btn btn-outline-secondary" href="{{ route('admin.panel') }}">Batal</a>
            </div>
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
