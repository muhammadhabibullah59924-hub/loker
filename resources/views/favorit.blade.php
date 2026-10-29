@extends('layouts.app')

@section('title', 'Favorit — LokerDekat')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <h1 class="mb-0">Daftar favorit</h1>
            <a class="btn btn-outline-secondary flex-shrink-0" href="{{ route('dasbor') }}">&larr; Kembali ke dasbor</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pesan"></button>
            </div>
        @endif

        @if ($favorit->isEmpty())
            <div class="card panduan-card">
                <div class="card-body text-center py-5">
                    <p class="mb-2">Belum ada loker favorit.</p>
                    <p class="text-secondary mb-0">Simpan loker dari dasbor dengan tombol favorit agar cepat ditemukan lagi.</p>
                    <a class="btn btn-primary mt-3" href="{{ route('dasbor') }}">Lihat dasbor</a>
                </div>
            </div>
        @else
            <h2 class="mb-3">Loker favorit kamu</h2>

            <div class="row g-3">
                @foreach ($favorit as $item)
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
                                <form method="POST" action="{{ route('favorit.destroy', $item['loker']->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger mt-2">Batal favorit</button>
                                </form>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
