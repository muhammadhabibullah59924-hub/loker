@extends('layouts.app')

@section('title', 'Masuk — LokerDekat')

@section('content')
    <div class="container py-5">
        <div class="row g-4 justify-content-center align-items-stretch">
            <div class="col-12 col-lg-8">
                <div class="row g-0 auth-card">
                    <div class="col-12 col-md-7">
                        <div class="card panduan-card h-100 auth-card__form">
                            <div class="card-body p-4 p-lg-5">
                                <p class="hero-dasbor__eyebrow mb-2">Akun LokerDekat</p>
                                <h1 class="mb-2">Masuk ke LokerDekat</h1>
                                <p class="text-secondary mb-4">Gunakan email dan kata sandi yang terdaftar.</p>

                                @if ($errors->any())
                                    <div class="alert alert-danger" role="alert">
                                        Masuk belum berhasil. Periksa isian di bawah, lalu coba lagi.
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login.store') }}">
                                    @csrf

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
                                               autocomplete="current-password" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4 form-check">
                                        <input type="checkbox" name="remember" id="remember" value="1" class="form-check-input">
                                        <label for="remember" class="form-check-label">Biarkan saya tetap masuk di perangkat ini</label>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                                </form>

                                <p class="text-center mt-4 mb-0">
                                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <aside class="auth-samping h-100" aria-label="Keunggulan LokerDekat">
                            <img src="{{ asset('uploads/logo.svg') }}" alt="" width="40" height="40" class="mb-4">
                            <p class="auth-samping__judul mb-2">Loker terdekat, langsung kelihatan.</p>
                            <p class="mb-4">Masuk untuk melihat loker yang diurutkan dari lokasimu.</p>
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
