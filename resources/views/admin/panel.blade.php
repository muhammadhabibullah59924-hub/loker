@extends('layouts.app')

@section('title', 'Panel Admin — LokerDekat')

@section('content')
    <div class="container py-4">
        <h1 class="mb-2">Panel admin</h1>
        <p class="text-secondary mb-4">
            Kelola data loker LokerDekat dari sini. Data yang disimpan langsung tampil di dasbor sesuai urutan jaraknya.
        </p>

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

        <div class="card panduan-card">
            <div class="card-body">
                <h2 class="h5 mb-2">Pengelolaan loker</h2>
                <p class="text-secondary mb-0">
                    Form tambah, ubah, nonaktifkan, dan hapus loker sedang disiapkan pada task berikutnya
                    dan akan muncul di halaman ini.
                </p>
            </div>
        </div>
    </div>
@endsection
