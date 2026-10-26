<?php

namespace App\Http\Controllers;

use App\Models\Job;
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
    public function show(int $id): View
    {
        $loker = Job::query()
            ->whereKey($id)
            ->where('is_active', true)
            ->firstOrFail();

        return view('lowongan', [
            'loker' => $loker,
        ]);
    }
}
