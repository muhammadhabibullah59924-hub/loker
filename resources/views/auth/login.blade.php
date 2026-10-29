@extends('layouts.app')

@section('title', 'Masuk — LokerDekat')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                <h1 class="mb-2">Masuk ke LokerDekat</h1>
                <p class="text-secondary mb-4">Gunakan email dan kata sandi yang terdaftar.</p>

                <div class="card panduan-card">
                    <div class="card-body p-4">
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
                    </div>
                </div>

                <p class="text-center mt-3 mb-0">
                    Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
                </p>
            </div>
        </div>
    </div>
@endsection
