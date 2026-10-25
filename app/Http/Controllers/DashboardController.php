<?php

namespace App\Http\Controllers;

use App\Http\Requests\DashboardRequest;
use App\Models\Job;
use App\Services\DistanceService;
use Illuminate\View\View;

/**
 * Dasbor loker terurut dari yang terdekat (F2, lihat docs/PRD.md).
 */
class DashboardController extends Controller
{
    /**
     * Tampilkan loker aktif terurut menaik sesuai jarak dari lokasi pengguna.
     *
     * Seluruh logika jarak berada di DistanceService, bukan di sini
     * (lihat AGENTS.md dan docs/CODE_STYLE.md).
     */
    public function index(DashboardRequest $request, DistanceService $jarak): View
    {
        $pengguna = $request->lokasiPengguna();

        $titik = $pengguna ?? [
            'lat' => (float) config('loker.lokasi_default.lat'),
            'lng' => (float) config('loker.lokasi_default.lng'),
        ];

        // Loker tanpa koordinat tidak pernah ditayangkan (kendali R2).
        $lokers = Job::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(fn (Job $loker): array => [
                'loker' => $loker,
                'jarak' => $jarak->distanceInKm(
                    $titik['lat'],
                    $titik['lng'],
                    (float) $loker->latitude,
                    (float) $loker->longitude,
                ),
            ])
            ->sortBy('jarak')
            ->values();

        return view('dashboard', [
            'lokers' => $lokers,
            'dari_lokasi_pengguna' => $pengguna !== null,
            'lokasi_default' => config('loker.lokasi_default.nama'),
        ]);
    }
}
