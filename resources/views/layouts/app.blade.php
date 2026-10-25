<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LokerDekat')</title>

    {{-- Bootstrap 5 via CDN (lihat ADR-002): tanpa langkah build aset --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="d-flex flex-column min-vh-100">
    {{-- Navbar menampilkan tombol Masuk/Daftar untuk tamu, dan nama + Keluar setelah F1 (T-004). --}}
    <nav class="navbar navbar-expand-lg site-navbar border-bottom" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none" href="/">
                <img src="{{ asset('uploads/logo.svg') }}" alt="" width="28" height="28">
                <span class="fw-semibold">LokerDekat</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuUtama"
                    aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu navigasi">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuUtama">
                <div class="navbar-nav ms-auto align-items-lg-center gap-2 flex-row">
                    @guest
                        <a class="btn btn-outline-secondary btn-sm" href="{{ route('login') }}">Masuk</a>
                        <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Daftar</a>
                    @else
                        <a class="nav-link" href="{{ route('dasbor') }}">Dasbor</a>
                        <span class="navbar-text me-2">Halo, {{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary btn-sm">Keluar</button>
                        </form>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow-1">
        @if (session('status'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup pesan"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="container d-flex flex-column flex-sm-row justify-content-between gap-2">
            <span>&copy; 2026 LokerDekat</span>
            <span>Loker terdekat dari lokasimu, lengkap dengan peta dan divisi yang lowong.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
