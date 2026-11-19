<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPanelRequest;
use App\Models\Job;
use Illuminate\View\View;

/**
 * Kerangka panel admin (F4). Form tambah, ubah, nonaktif, dan hapus
 * ditambahkan pada T-009. Filter daftar loker ditambahkan pada T-035.
 */
class PanelController extends Controller
{
    /**
     * Tampilkan ringkasan dan daftar loker untuk pengelola.
     */
    public function index(AdminPanelRequest $request): View
    {
        $kataKunci = $request->input('q');
        $status = $request->input('status');
        $tipe = $request->input('tipe');

        $query = Job::query();

        // Pencarian pada nama PT, alamat, atau divisi (T-035).
        if ($kataKunci !== null && $kataKunci !== '') {
            $query->where(function ($queryPencarian) use ($kataKunci) {
                $queryPencarian->where('company_name', 'like', '%'.$kataKunci.'%')
                    ->orWhere('address', 'like', '%'.$kataKunci.'%')
                    ->orWhere('division', 'like', '%'.$kataKunci.'%');
            });
        }

        if ($status === 'tayang') {
            $query->where('is_active', true);
        } elseif ($status === 'nonaktif') {
            $query->where('is_active', false);
        }

        if ($tipe !== null && $tipe !== '') {
            $query->where('job_type', $tipe);
        }

        return view('admin.panel', [
            'jumlahAktif' => Job::where('is_active', true)->count(),
            'jumlahNonaktif' => Job::where('is_active', false)->count(),
            'jumlahPremium' => Job::where('is_active', true)->get()
                ->filter(fn (Job $loker): bool => (bool) $loker->is_premium)
                ->count(),
            'jumlahButuh' => Job::where('positions_needed', '>', 1)->count(),
            'lokers' => $query->orderByDesc('id')->get(),
            'q' => $kataKunci,
            'status' => $status,
            'tipe' => $tipe,
            'adaFilter' => $kataKunci !== null || $status !== null || $tipe !== null,
        ]);
    }
}
