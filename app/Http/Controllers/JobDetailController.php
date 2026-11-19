<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\DistanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman detail satu loker beserta peta titik lokasinya (F3).
 */
class JobDetailController extends Controller
{
    /**
     * Tampilkan satu loker aktif beserta koordinat untuk dipetakan.
     *
     * Loker nonaktif dan id yang tidak dikenal dijawab 404 sehingga data
     * yang tidak tayang tidak pernah bisa dibuka lewat URL (kendali R2).
     */
    public function show(Request $request, DistanceService $jarak, int $id): View
    {
        $loker = Job::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        // Status favorit dipakai untuk memilih tombol simpan atau tautan favorit (T-038).
        $sudahFavorit = $request->user() !== null
            && $request->user()->favoritedJobs()->where('job_id', $loker->id)->exists();

        // T-044: loker lain yang masih tayang, divisinya atau kotanya sama.
        $titik = [
            'lat' => (float) config('loker.lokasi_default.lat'),
            'lng' => (float) config('loker.lokasi_default.lng'),
        ];

        $serupa = Job::query()
            ->where('id', '!=', $loker->id)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where(function ($querySerupa) use ($loker) {
                $querySerupa->where('division', $loker->division)
                    ->orWhere('city', $loker->city);
            })
            ->where(function ($queryKadaluarsa) {
                $queryKadaluarsa->whereNull('deadline')
                    ->orWhereDate('deadline', '>=', today());
            })
            ->orderByDesc('id')
            ->limit(3)
            ->get()
            ->map(fn (Job $item): array => [
                'loker' => $item,
                'jarak' => $jarak->distanceInKm(
                    $titik['lat'],
                    $titik['lng'],
                    (float) $item->latitude,
                    (float) $item->longitude,
                ),
            ]);

        return view('lowongan', [
            'loker' => $loker,
            'sudahFavorit' => $sudahFavorit,
            'serupa' => $serupa,
        ]);
    }
}
