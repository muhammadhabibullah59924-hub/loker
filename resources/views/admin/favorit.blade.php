@extends('layouts.app')

@section('title', 'Laporan Favorit — LokerDekat')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
            <div>
                <p class="hero-dasbor__eyebrow mb-2">Ruang pengelola</p>
                <h1 class="mb-1">Laporan favorit</h1>
                <p class="text-secondary mb-0">
                    Lihat loker mana yang paling diminati dan siapa yang menyimpannya. Data ini hanya bisa dibuka pengelola.
                </p>
            </div>
            <a class="btn btn-outline-secondary flex-shrink-0" href="{{ route('admin.panel') }}">Kembali ke panel</a>
        </div>

        {{-- T-049: ringkasan agregat dari tabel favorites. --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="card panduan-card stat-card h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Total favorit</p>
                        <p class="stat-card__nilai">{{ $jumlahFavorit }}</p>
                        <p class="text-secondary small mb-0">Seluruh loker yang disimpan</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card panduan-card stat-card stat-card--secondary h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Loker difavoritkan</p>
                        <p class="stat-card__nilai">{{ $jumlahLokerDifavoritkan }}</p>
                        <p class="text-secondary small mb-0">Loker yang punya minimal satu favorit</p>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card panduan-card stat-card stat-card--premium h-100">
                    <div class="card-body">
                        <p class="stat-card__label">Pengguna menyimpan</p>
                        <p class="stat-card__nilai">{{ $jumlahPengguna }}</p>
                        <p class="text-secondary small mb-0">Akun yang punya favorit tersimpan</p>
                    </div>
                </div>
            </div>
        </div>

        <section class="mb-5" aria-labelledby="peringkat-favorit">
            <h2 class="h4 mb-1" id="peringkat-favorit">Loker paling difavoritkan</h2>
            <p class="text-secondary small mb-3">Diurutkan dari jumlah pengguna yang menyimpan loker tersebut.</p>

            @if ($peringkat === [])
                <div class="card panduan-card">
                    <div class="card-body text-center py-5">
                        <p class="mb-2">Belum ada loker yang disimpan sebagai favorit.</p>
                        <p class="text-secondary mb-3">
                            Buka dasbor pencari kerja, tekan tombol Simpan favorit pada sebuah loker, lalu muat ulang halaman ini.
                        </p>
                        <a class="btn btn-outline-primary" href="{{ route('dasbor') }}">Buka dasbor</a>
                    </div>
                </div>
            @else
                <div class="card panduan-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <caption class="px-3 pt-3 text-secondary">Peringkat loker berdasarkan jumlah favorit yang diterima.</caption>
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Nama PT</th>
                                    <th scope="col">Divisi</th>
                                    <th scope="col">Status</th>
                                    <th scope="col" class="text-end">Favorit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($peringkat as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            @if ($item['loker']->is_active)
                                                <a href="{{ route('lowongan.show', $item['loker']->id) }}">{{ $item['loker']->company_name }}</a>
                                            @else
                                                {{ $item['loker']->company_name }}
                                            @endif
                                        </td>
                                        <td>{{ $item['loker']->division }}</td>
                                        <td>
                                            <span class="badge {{ $item['loker']->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                {{ $item['loker']->is_active ? 'Tayang' : 'Nonaktif' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-semibold">{{ $item['jumlah'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

        <section aria-labelledby="favorit-pengguna">
            <h2 class="h4 mb-1" id="favorit-pengguna">Favorit per pengguna</h2>
            <p class="text-secondary small mb-3">
                Rincian ini dipakai untuk memahami minat pencari kerja. Nama dan email hanya terlihat oleh pengelola.
            </p>

            @if ($perPengguna === [])
                <div class="card panduan-card">
                    <div class="card-body text-center py-5">
                        <p class="mb-2">Belum ada pengguna yang menyimpan favorit.</p>
                        <p class="text-secondary mb-3">
                            Setelah pencari kerja menyimpan loker, daftarnya akan muncul di sini secara otomatis.
                        </p>
                        <a class="btn btn-outline-primary" href="{{ route('admin.panel') }}">Kembali ke panel</a>
                    </div>
                </div>
            @else
                <div class="row g-3">
                    @foreach ($perPengguna as $data)
                        <div class="col-12 col-lg-6">
                            <div class="card panduan-card h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <h3 class="h6 mb-1">{{ $data['user']->name }}</h3>
                                            <p class="text-secondary small mb-0">{{ $data['user']->email }}</p>
                                        </div>
                                        <span class="badge text-bg-primary">{{ $data['jumlah'] }} favorit</span>
                                    </div>
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($data['lokers'] as $loker)
                                            <li class="border-top py-2">
                                                <div class="d-flex flex-column">
                                                    @if ($loker->is_active)
                                                        <a href="{{ route('lowongan.show', $loker->id) }}">{{ $loker->company_name }}</a>
                                                    @else
                                                        <span class="text-secondary">{{ $loker->company_name }}</span>
                                                    @endif
                                                    <span class="text-secondary small">{{ $loker->division }} · {{ $loker->address }}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
