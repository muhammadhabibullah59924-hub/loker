<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menjaga seluruh rute pada prefix /admin agar hanya peran admin (F4).
 *
 * Pemeriksaan dilakukan di sisi server pada setiap permintaan, bukan lewat
 * penyembunyian tautan (lihat docs/SECURITY.md). Rute admin juga selalu
 * dibungkus middleware `auth`, sehingga permintaan tanpa sesi sudah dialihkan
 * ke halaman Masuk sebelum middleware ini berjalan.
 */
class EnsureUserIsAdmin
{
    /**
     * Teruskan permintaan bila pengguna berperan admin, selain itu alihkan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pengguna = $request->user();

        if (! $pengguna instanceof User || ! $pengguna->isAdmin()) {
            return redirect()->route('dasbor')->with(
                'peringatan',
                'Halaman admin hanya untuk pengelola data loker. Kamu dialihkan kembali ke dasbor.',
            );
        }

        return $next($request);
    }
}
