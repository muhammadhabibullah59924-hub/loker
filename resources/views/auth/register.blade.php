@extends('layouts.app')

@section('title', 'Daftar — LokerDekat')

@section('content')
    <div class="container py-5">
        <div class="row g-4 justify-content-center align-items-stretch">
            <div class="col-12 col-lg-8">
                <div class="row g-0 auth-card">
                    <div class="col-12 col-md-7">
                        <div class="card panduan-card h-100 auth-card__form">
                            <div class="card-body p-4 p-lg-5">
                                <p class="hero-dasbor__eyebrow mb-2">Akun LokerDekat</p>
                                <h1 class="mb-2">Buat akun LokerDekat</h1>
                                <p class="text-secondary mb-4">
                                    Daftar gratis untuk menyimpan loker favorit. Akun langsung aktif, tanpa verifikasi email.
                                </p>

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        Pendaftaran belum berhasil. Periksa isian di bawah, lalu coba lagi.
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register.store') }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama lengkap</label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                                               class="form-control @error('name') is-invalid @enderror"
                                               autocomplete="name" maxlength="100" required
                                               aria-describedby="name-help">
                                        <div id="name-help" class="form-text">Nama ini yang tampil di navbar setelah kamu masuk.</div>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                                               class="form-control @error('email') is-invalid @enderror"
                                               autocomplete="email" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">Kata sandi</label>
                                        <input type="password" name="password" id="password"
                                               class="form-control @error('password') is-invalid @enderror"
                                               autocomplete="new-password" minlength="8" required
                                               aria-describedby="password-help">
                                        <div id="password-help" class="form-text">Minimal 8 karakter.</div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label">Ulangi kata sandi</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                               class="form-control" autocomplete="new-password" minlength="8" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Buat akun</button>
                                </form>

                                <p class="text-center mt-4 mb-0">
                                    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <aside class="auth-samping h-100" aria-label="Keunggulan LokerDekat">
                            <img src="{{ asset('uploads/logo.svg') }}" alt="" width="40" height="40" class="mb-4">
                            <p class="auth-samping__judul mb-2">Cari kerja mulai dari yang terdekat.</p>
                            <p class="mb-4">Satu akun untuk menemukan loker, melihat peta, dan menyimpan favorit.</p>
                            <ul class="auth-samping__daftar">
                                <li>Diurutkan dari yang terdekat</li>
                                <li>Peta lokasi di setiap loker</li>
                                <li>Simpan loker favorit</li>
                            </ul>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
