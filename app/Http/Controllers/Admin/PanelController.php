<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\View\View;

/**
 * Kerangka panel admin (F4). Form tambah, ubah, nonaktif, dan hapus
 * ditambahkan pada T-009.
 */
class PanelController extends Controller
{
    /**
     * Tampilkan ringkasan data loker untuk pengelola.
     */
    public function index(): View
    {
        return view('admin.panel', [
            'jumlahAktif' => Job::where('is_active', true)->count(),
            'jumlahNonaktif' => Job::where('is_active', false)->count(),
            'lokers' => Job::orderByDesc('id')->get(),
        ]);
    }
}
