@extends('layouts.app')

@section('title', 'Panel Admin — LokerDekat')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <p class="hero-dasbor__eyebrow mb-2">Ruang pengelola</p>
                <h1 class="mb-1">Panel admin</h1>
                <p class="text-secondary mb-0">
                    Kelola data loker LokerDekat. Data yang disimpan langsung tampil di dasbor sesuai urutan jaraknya.
                </p>
            </div>
            <a class="btn btn-primary flex-shrink-0" href="{{ route('admin.loker.create') }}">Tambah loker</a>
        </div>

        {{-- T-035: ringkasan data loker dalam kartu statistik. --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="card panduan-card stat-card h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Loker tayang</p>
                        <p class="stat-card__nilai">{{ $jumlahAktif }}</p>
                        <p class="text-secondary small mb-0">Tampil di dasbor pencari kerja</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card panduan-card stat-card stat-card--secondary h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Loker nonaktif</p>
                        <p class="stat-card__nilai">{{ $jumlahNonaktif }}</p>
                        <p class="text-secondary small mb-0">Disembunyikan dari dasbor</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card panduan-card stat-card stat-card--premium h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Loker premium</p>
                        <p class="stat-card__nilai">{{ $jumlahPremium }}</p>
                        <p class="text-secondary small mb-0">Berlabel Premium Employer</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card panduan-card stat-card stat-card--butuh h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Butuh banyak orang</p>
                        <p class="stat-card__nilai">{{ $jumlahButuh }}</p>
                        <p class="text-secondary small mb-0">Lowongan butuh lebih dari 1 orang</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- T-035: filter daftar loker berdasarkan kata kunci, status, dan tipe. --}}
        <form method="GET" action="{{ route('admin.panel') }}" class="card panduan-card mb-4" aria-label="Filter daftar loker">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label" for="admin-q">Cari loker</label>
                        <input type="search" class="form-control" id="admin-q" name="q" value="{{ $q ?? '' }}"
                               placeholder="Nama PT, alamat, atau divisi">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label" for="admin-status">Status</label>
                        <select class="form-select" id="admin-status" name="status">
                            <option value="" @selected(($status ?? null) === null)>Semua status</option>
                            <option value="tayang" @selected(($status ?? null) === 'tayang')>Tayang</option>
                            <option value="nonaktif" @selected(($status ?? null) === 'nonaktif')>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label" for="admin-tipe">Tipe loker</label>
                        <select class="form-select" id="admin-tipe" name="tipe">
                            <option value="" @selected(($tipe ?? null) === null)>Semua tipe</option>
                            @foreach (['Full-Time', 'Part-Time', 'Contract', 'Internship'] as $tipeOpsi)
                                <option value="{{ $tipeOpsi }}" @selected(($tipe ?? null) === $tipeOpsi)>{{ $tipeOpsi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                    </div>
                </div>

                @if ($adaFilter)
                    <a class="btn btn-sm btn-outline-secondary mt-3" href="{{ route('admin.panel') }}">Reset filter</a>
                @endif
            </div>
        </form>

        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
            <h2 class="mb-0">Daftar loker</h2>
            <p class="text-secondary mb-0">{{ $lokers->count() }} loker ditampilkan</p>
        </div>

        @if ($lokers->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    @if ($adaFilter)
                        <p class="mb-2">Belum ada loker yang cocok dengan filter.</p>
                        <p class="text-secondary mb-3">Coba ubah kata kunci, atau pilih "Semua status" dan "Semua tipe".</p>
                        <a class="btn btn-outline-primary" href="{{ route('admin.panel') }}">Reset filter</a>
                    @else
                        <p class="mb-2">Belum ada loker yang tersimpan.</p>
                        <p class="text-secondary mb-3">Tambahkan loker pertama agar pencari kerja bisa menemukannya di dasbor.</p>
                        <a class="btn btn-primary" href="{{ route('admin.loker.create') }}">Tambah loker</a>
                    @endif
                </div>
            </div>
        @else
            <div class="card panduan-card">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <caption class="px-3 pt-3 text-secondary">Seluruh loker beserta tipe, batas lamaran, status tayang, dan tindakan yang tersedia.</caption>
                        <thead>
                            <tr>
                                <th scope="col">Loker</th>
                                <th scope="col">Tipe</th>
                                <th scope="col">Divisi</th>
                                <th scope="col">Batas lamaran</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($lokers as $loker)
                                <tr>
                                    <td>
                                        <span class="fw-semibold">{{ $loker->company_name }}</span>
                                        @if ($loker->is_premium)
                                            <span class="badge bg-primary ms-1">Premium</span>
                                        @endif
                                        <span class="d-block text-secondary small">{{ $loker->address }}, {{ $loker->city }}</span>
                                    </td>
                                    <td>
                                        @if ($loker->job_type)
                                            <span class="badge job-tipe job-tipe--{{ strtolower($loker->job_type) }}">{{ $loker->job_type }}</span>
                                        @else
                                            <span class="text-secondary small">Belum diisi</span>
                                        @endif
                                    </td>
                                    <td>{{ $loker->division }}</td>
                                    <td>
                                        @if ($loker->deadline)
                                            {{ $loker->deadline->locale('id')->translatedFormat('j M Y') }}
                                        @else
                                            <span class="text-secondary small">Tanpa batas</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $loker->is_active ? 'job-status-tayang' : 'job-status-nonaktif' }}">
                                            {{ $loker->is_active ? 'Tayang' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="admin-aksi">
                                        <a class="btn btn-sm btn-outline-secondary" href="{{ route('lowongan.show', $loker->id) }}">Lihat</a>

                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.loker.edit', $loker->id) }}">Ubah</a>

                                        <form method="POST" action="{{ route('admin.loker.toggle', $loker->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $loker->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                {{ $loker->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.loker.destroy', $loker->id) }}"
                                              data-konfirmasi="Hapus loker {{ $loker->company_name }}? Data yang dihapus tidak bisa dikembalikan.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
