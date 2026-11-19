@extends('layouts.app')

@section('title', 'Profil — LokerDekat')

@section('content')
    <div class="container py-4">
        <a class="d-inline-block mb-3" href="{{ route('dasbor') }}">&larr; Kembali ke dasbor</a>

        <p class="hero-dasbor__eyebrow mb-2">Akun saya</p>
        <h1 class="mb-1">Profil</h1>
        <p class="text-secondary mb-4">Perbarui nama dan email, atau ganti kata sandi akunmu di sini.</p>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                Perubahan belum bisa disimpan. Periksa pesan di bawah, lalu coba lagi.
            </div>
        @endif

        <form method="POST" action="{{ route('profil.update') }}" class="row g-4 align-items-start">
            @csrf
            @method('PUT')

            <div class="col-12 col-lg-8">
                <section class="card panduan-card form-group-card mb-4" aria-labelledby="bagian-akun">
                    <div class="card-body p-4">
                        <div class="form-group-card__kepala">
                            <span class="form-group-card__nomor" aria-hidden="true">1</span>
                            <div>
                                <h2 id="bagian-akun" class="mb-0">Data akun</h2>
                                <p class="text-secondary small mb-0">Nama dan email yang dipakai untuk masuk.</p>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="name">Nama lengkap</label>
                                <input class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" type="text" maxlength="255" required
                                       value="{{ old('name', $pengguna->name) }}">
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" type="email" maxlength="255" required
                                       value="{{ old('email', $pengguna->email) }}">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="card panduan-card form-group-card mb-4" aria-labelledby="bagian-katasandi">
                    <div class="card-body p-4">
                        <div class="form-group-card__kepala">
                            <span class="form-group-card__nomor" aria-hidden="true">2</span>
                            <div>
                                <h2 id="bagian-katasandi" class="mb-0">Ganti kata sandi</h2>
                                <p class="text-secondary small mb-0">Kosongkan semua kolom bila tidak ingin mengganti.</p>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label class="form-label" for="kata_sandi_lama">Kata sandi lama</label>
                                <input class="form-control @error('kata_sandi_lama') is-invalid @enderror"
                                       id="kata_sandi_lama" name="kata_sandi_lama" type="password" autocomplete="current-password">
                                @error('kata_sandi_lama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="kata_sandi_baru">Kata sandi baru</label>
                                <input class="form-control @error('kata_sandi_baru') is-invalid @enderror"
                                       id="kata_sandi_baru" name="kata_sandi_baru" type="password" autocomplete="new-password">
                                <div class="form-text">Minimal 8 karakter.</div>
                                @error('kata_sandi_baru')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="kata_sandi_baru_confirmation">Ulangi kata sandi baru</label>
                                <input class="form-control" id="kata_sandi_baru_confirmation"
                                       name="kata_sandi_baru_confirmation" type="password" autocomplete="new-password">
                            </div>
                        </div>
                    </div>
                </section>

                <div class="card panduan-card">
                    <div class="card-body d-flex flex-wrap gap-2 p-4">
                        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                        <a class="btn btn-outline-secondary" href="{{ route('dasbor') }}">Batal</a>
                    </div>
                </div>
            </div>

            <aside class="col-12 col-lg-4" aria-labelledby="judul-info-akun">
                <div class="card panduan-card admin-form__ringkasan">
                    <div class="card-body p-4">
                        <h2 id="judul-info-akun" class="mb-3">Info akun</h2>
                        <dl class="ringkasan-loker mb-0">
                            <div class="ringkasan-loker__baris">
                                <dt>Nama</dt>
                                <dd>{{ $pengguna->name }}</dd>
                            </div>
                            <div class="ringkasan-loker__baris">
                                <dt>Email</dt>
                                <dd class="text-break">{{ $pengguna->email }}</dd>
                            </div>
                            <div class="ringkasan-loker__baris">
                                <dt>Peran</dt>
                                <dd>{{ $pengguna->isAdmin() ? 'Admin' : 'Pencari kerja' }}</dd>
                            </div>
                        </dl>
                        <hr class="my-3">
                        <p class="text-secondary small mb-0">
                            Koordinat lokasimu tidak disimpan di akun ini. Lokasi hanya dipakai sekali untuk mengurutkan loker di dasbor.
                        </p>
                    </div>
                </div>
            </aside>
        </form>
    </div>
@endsection
