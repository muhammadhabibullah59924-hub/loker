@extends('layouts.app')

@section('title', 'Daftar — LokerDekat')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <h1 class="mb-2">Buat akun LokerDekat</h1>
                <p class="text-secondary mb-4">
                    Daftar gratis untuk menyimpan loker favorit. Akun langsung aktif, tanpa verifikasi email.
                </p>

                <div class="card panduan-card">
                    <div class="card-body p-4">
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
                    </div>
                </div>

                <p class="text-center mt-3 mb-0">
                    Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
                </p>
            </div>
        </div>
    </div>
@endsection
