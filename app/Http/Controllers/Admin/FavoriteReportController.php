<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Laporan favorit untuk pengelola (T-049).
 *
 * Dua sudut pandang data yang sudah ada di tabel `favorites`:
 * peringkat loker yang paling sering disimpan dan rincian favorit tiap
 * pengguna. Keduanya dibaca saja, tanpa menulis atau mengubah data favorit.
 */
class FavoriteReportController extends Controller
{
    /**
     * Tampilkan peringkat loker favorit dan rincian per pengguna.
     */
    public function index(): View
    {
        $peringkat = Favorite::query()
            ->select('job_id')
            ->selectRaw('COUNT(*) as jumlah')
            ->groupBy('job_id')
            ->orderByDesc('jumlah')
            ->orderBy('job_id')
            ->with('job')
            ->get()
            ->filter(fn (Favorite $favorit): bool => $favorit->job instanceof Job)
            ->map(fn (Favorite $favorit): array => [
                'loker' => $favorit->job,
                'jumlah' => (int) $favorit->jumlah,
            ])
            ->values()
            ->all();

        $perPengguna = Favorite::query()
            ->with(['user', 'job'])
            ->latest('created_at')
            ->get()
            ->filter(fn (Favorite $favorit): bool => $favorit->user instanceof User
                && $favorit->job instanceof Job)
            ->groupBy('user_id')
            ->map(function ($favoritSatuUser): array {
                /** @var Collection<int, Favorite> $favoritSatuUser */
                $favoritPertama = $favoritSatuUser->first();

                return [
                    'user' => $favoritPertama->user,
                    'jumlah' => $favoritSatuUser->count(),
                    'lokers' => $favoritSatuUser
                        ->map(fn (Favorite $favorit): Job => $favorit->job)
                        ->all(),
                ];
            })
            ->values()
            ->all();

        return view('admin.favorit', [
            'peringkat' => $peringkat,
            'perPengguna' => $perPengguna,
            'jumlahFavorit' => array_sum(array_column($peringkat, 'jumlah')),
            'jumlahLokerDifavoritkan' => count($peringkat),
            'jumlahPengguna' => count($perPengguna),
        ]);
    }
}
