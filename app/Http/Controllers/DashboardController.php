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

        $query = Job::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        // Pencarian nama PT atau divisi hanya menampilkan yang cocok (F5).
        $kataKunci = $request->input('q');

        if ($kataKunci !== null && $kataKunci !== '') {
            $query->where(function ($queryPencarian) use ($kataKunci) {
                $queryPencarian->where('company_name', 'like', '%'.$kataKunci.'%')
                    ->orWhere('division', 'like', '%'.$kataKunci.'%');
            });
        }

        $lokers = $query
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

        // Filter radius memakai hasil perhitungan jarak, supaya urutan tetap menaik.
        $radius = $request->input('radius');

        if ($radius !== null) {
            $lokers = $lokers->filter(
                fn (array $item) => $item['jarak'] <= (float) $radius
            )->values();
        }

        return view('dashboard', [
            'lokers' => $lokers,
            'dari_lokasi_pengguna' => $pengguna !== null,
            'lokasi_default' => config('loker.lokasi_default.nama'),
            'q' => $request->input('q'),
            'radius' => $request->input('radius'),
            'titik' => $titik,
        ]);
    }
}
