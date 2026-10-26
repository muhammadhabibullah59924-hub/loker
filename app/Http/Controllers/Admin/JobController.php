<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobFormRequest;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * CRUD loker dari panel admin (F4) tanpa menyentuh basis data secara langsung.
 */
class JobController extends Controller
{
    /**
     * Tampilkan form tambah loker baru.
     */
    public function create(): View
    {
        return view('admin.loker-form', [
            'loker' => null,
        ]);
    }

    /**
     * Simpan loker baru milik admin yang sedang masuk.
     */
    public function store(JobFormRequest $request): RedirectResponse
    {
        $loker = Job::create($request->safe()->only([
            'company_name',
            'address',
            'division',
            'city',
            'description',
            'requirements',
            'deadline',
            'latitude',
            'longitude',
        ]) + ['is_active' => true, 'posted_by' => $request->user()->id]);

        return redirect()
            ->route('admin.panel')
            ->with('status', 'Loker '.$loker->company_name.' berhasil ditambahkan.');
    }

    /**
     * Tampilkan form ubah untuk satu loker.
     */
    public function edit(Job $loker): View
    {
        return view('admin.loker-form', [
            'loker' => $loker,
        ]);
    }

    /**
     * Simpan perubahan satu loker.
     */
    public function update(JobFormRequest $request, Job $loker): RedirectResponse
    {
        $loker->update($request->safe()->only([
            'company_name',
            'address',
            'division',
            'city',
            'description',
            'requirements',
            'deadline',
            'latitude',
            'longitude',
        ]));

        return redirect()
            ->route('admin.panel')
            ->with('status', 'Loker '.$loker->company_name.' berhasil diperbarui.');
    }

    /**
     * Sembunyikan atau tayangkan kembali satu loker tanpa menghapus datanya.
     */
    public function toggle(Job $loker): RedirectResponse
    {
        $loker->is_active = ! $loker->is_active;
        $loker->save();

        $pesan = $loker->is_active
            ? 'Loker '.$loker->company_name.' kembali tayang di dasbor.'
            : 'Loker '.$loker->company_name.' dinonaktifkan dan disembunyikan dari dasbor.';

        return redirect()->route('admin.panel')->with('status', $pesan);
    }

    /**
     * Hapus satu loker beserta favorit yang menunjuk kepadanya.
     */
    public function destroy(Job $loker): RedirectResponse
    {
        $nama = $loker->company_name;
        $loker->favoritedBy()->detach();
        $loker->delete();

        return redirect()
            ->route('admin.panel')
            ->with('status', 'Loker '.$nama.' berhasil dihapus.');
    }
}
