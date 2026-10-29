<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Job;
use App\Services\DistanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Favorit pengguna terhadap loker (F6).
 */
class FavoriteController extends Controller
{
    /**
     * Daftar loker favorit milik pengguna yang sedang masuk.
     */
    public function index(Request $request, DistanceService $jarak): View
    {
        $favorit = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with('job')
            ->latest('created_at')
            ->get();

        return view('favorit', [
            'favorit' => $favorit->map(function (Favorite $item) use ($jarak): array {
                return [
                    'loker' => $item->job,
                    'jarak' => $jarak->distanceInKm(
                        (float) config('loker.lokasi_default.lat'),
                        (float) config('loker.lokasi_default.lng'),
                        (float) $item->job->latitude,
                        (float) $item->job->longitude,
                    ),
                ];
            })->values(),
        ]);
    }

    /**
     * Simpan loker sebagai favorit milik pengguna yang sedang masuk.
     */
    public function store(Request $request, Job $loker): RedirectResponse
    {
        if (! $loker->is_active) {
            return redirect()
                ->route('favorit.index')
                ->with('status', 'Loker '.$loker->company_name.' tidak aktif, jadi tidak bisa ditambahkan sebagai favorit.');
        }

        if ($request->user()->favoritedJobs()->where('job_id', $loker->id)->exists()) {
            return redirect()
                ->route('favorit.index')
                ->with('status', 'Loker '.$loker->company_name.' sudah kamu favoritkan.');
        }

        $request->user()->favoritedJobs()->attach($loker->id);

        return redirect()
            ->route('favorit.index')
            ->with('status', 'Loker '.$loker->company_name.' berhasil ditambahkan ke favorit.');
    }

    /**
     * Lepas loker dari favorit milik pengguna yang sedang masuk.
     */
    public function destroy(Request $request, Job $loker): RedirectResponse
    {
        $request->user()->favoritedJobs()->detach($loker->id);

        return redirect()
            ->route('favorit.index')
            ->with('status', 'Loker '.$loker->company_name.' dihapus dari favorit kamu.');
    }
}
