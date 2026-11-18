<?php

namespace App\Http\Controllers;

use App\Models\Job;
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
    public function show(Request $request, int $id): View
    {
        $loker = Job::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        // Status favorit dipakai untuk memilih tombol simpan atau tautan favorit (T-038).
        $sudahFavorit = $request->user() !== null
            && $request->user()->favoritedJobs()->where('job_id', $loker->id)->exists();

        return view('lowongan', [
            'loker' => $loker,
            'sudahFavorit' => $sudahFavorit,
        ]);
    }
}
