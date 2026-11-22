<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LokerDekat')</title>

    {{-- Bootstrap 5 via CDN (lihat ADR-002): tanpa langkah build aset --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    @yield('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    {{-- Navbar menampilkan tombol Masuk/Daftar untuk tamu, dan nama + Keluar setelah F1 (T-004). --}}
    <nav class="navbar navbar-expand-lg site-navbar border-bottom sticky-top" aria-label="Navigasi utama">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2 text-dark text-decoration-none" href="/">
                <span class="navbar-brand__logo">
                    <img src="{{ asset('uploads/logo.svg') }}" alt="" width="24" height="24">
                </span>
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
                        <a class="nav-link {{ request()->routeIs('dasbor') ? 'nav-link--aktif' : '' }}" href="{{ route('dasbor') }}">Dasbor</a>
                        @if (auth()->user()->isAdmin())
                            <a class="nav-link {{ request()->routeIs('admin.panel', 'admin.loker.*') ? 'nav-link--aktif' : '' }}" href="{{ route('admin.panel') }}">Panel Admin</a>
                            <a class="nav-link {{ request()->routeIs('admin.favorit') ? 'nav-link--aktif' : '' }}" href="{{ route('admin.favorit') }}">Laporan Favorit</a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('favorit.*') ? 'nav-link--aktif' : '' }}" href="{{ route('favorit.index') }}">Favorit</a>
                        <a class="nav-link {{ request()->routeIs('profil.*') ? 'nav-link--aktif' : '' }}" href="{{ route('profil.index') }}">Profil</a>
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
        @if (session('peringatan'))
            <div class="container mt-3">
                <div class="alert alert-warning" role="alert">{{ session('peringatan') }}</div>
            </div>
        @endif

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
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-12 col-md-5">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ asset('uploads/logo.svg') }}" alt="" width="24" height="24">
                        <span class="fw-semibold text-dark">LokerDekat</span>
                    </div>
                    <p class="text-secondary small mb-0">Loker terdekat dari lokasimu, lengkap dengan peta dan divisi yang lowong.</p>
                </div>
                <div class="col-6 col-md-3">
                    <p class="site-footer__judul">Jelajahi</p>
                    <ul class="list-unstyled mb-0 site-footer__tautan">
                        <li><a href="{{ route('home') }}">Beranda</a></li>
                        @auth
                            <li><a href="{{ route('dasbor') }}">Dasbor</a></li>
                            <li><a href="{{ route('favorit.index') }}">Favorit</a></li>
                        @endauth
                        @guest
                            <li><a href="{{ route('login') }}">Masuk</a></li>
                            <li><a href="{{ route('register') }}">Daftar</a></li>
                        @endguest
                    </ul>
                </div>
                <div class="col-6 col-md-4">
                    <p class="site-footer__judul">Tentang</p>
                    <p class="text-secondary small mb-0">
                        Informasi lowongan kerja di sekitarmu, tanpa biaya dan tanpa data lokasi yang disimpan.
                    </p>
                </div>
            </div>
            <div class="site-footer__bawah mt-4 pt-3">
                <span>&copy; 2026 LokerDekat</span>
                <span>Dibuat untuk memudahkan pencari kerja menemukan loker terdekat.</span>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Konfirmasi penghapusan memakai atribut data supaya tanda kutip
        // di nama PT tidak merusak markup (docs/DESIGN_SYSTEM.md).
        document.querySelectorAll('form[data-konfirmasi]').forEach(function (form) {
            form.addEventListener('submit', function (peristiwa) {
                if (!window.confirm(form.dataset.konfirmasi)) {
                    peristiwa.preventDefault();
                }
            });
        });

        // Notifikasi alert hilang otomatis setelah beberapa saat (docs/DESIGN_SYSTEM.md).
        document.querySelectorAll('.alert').forEach(function (elemen) {
            window.setTimeout(function () {
                const tombolTutup = elemen.querySelector('.btn-close');

                if (tombolTutup) {
                    bootstrap.Alert.getOrCreateInstance(tombolTutup).close();
                }
            }, 5000);
        });
    </script>
    @yield('scripts')
</body>
</html>
