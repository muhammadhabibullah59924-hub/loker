@extends('layouts.app')

@section('title', 'Panel Admin — LokerDekat')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <h1 class="mb-1">Panel admin</h1>
                <p class="text-secondary mb-0">
                    Kelola data loker LokerDekat. Data yang disimpan langsung tampil di dasbor sesuai urutan jaraknya.
                </p>
            </div>
            <a class="btn btn-primary flex-shrink-0" href="{{ route('admin.loker.create') }}">Tambah loker</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6">
                <div class="card panduan-card">
                    <div class="card-body">
                        <p class="text-secondary mb-1">Loker tayang</p>
                        <p class="mb-0">{{ $jumlahAktif }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="card panduan-card">
                    <div class="card-body">
                        <p class="text-secondary mb-1">Loker nonaktif</p>
                        <p class="mb-0">{{ $jumlahNonaktif }}</p>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="h4 mb-3">Daftar loker</h2>

        @if ($lokers->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    <p class="mb-2">Belum ada loker yang tersimpan.</p>
                    <p class="text-secondary mb-3">Tambahkan loker pertama agar pencari kerja bisa menemukannya di dasbor.</p>
                    <a class="btn btn-primary" href="{{ route('admin.loker.create') }}">Tambah loker</a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <caption class="text-secondary">Seluruh loker beserta status tayang dan tindakan yang tersedia.</caption>
                    <thead>
                        <tr>
                            <th scope="col">Nama PT</th>
                            <th scope="col">Alamat</th>
                            <th scope="col">Divisi</th>
                            <th scope="col">Kota</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lokers as $loker)
                            <tr>
                                <td>{{ $loker->company_name }}</td>
                                <td>{{ $loker->address }}</td>
                                <td>{{ $loker->division }}</td>
                                <td>{{ $loker->city }}</td>
                                <td>
                                    <span class="badge {{ $loker->is_active ? 'job-status-tayang' : 'job-status-nonaktif' }}">
                                        {{ $loker->is_active ? 'Tayang' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="admin-aksi">
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
        @endif
    </div>
@endsection
