<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Halaman profil pengguna yang sudah masuk (T-043).
 */
class ProfileController extends Controller
{
    /**
     * Tampilkan data akun pengguna saat ini.
     */
    public function index(Request $request): View
    {
        return view('profil', [
            'pengguna' => $request->user(),
        ]);
    }

    /**
     * Simpan perubahan nama, email, dan kata sandi pengguna.
     */
    public function update(ProfileRequest $request): RedirectResponse
    {
        $pengguna = $request->user();

        $pengguna->name = $request->input('name');
        $pengguna->email = $request->input('email');

        if ($request->filled('kata_sandi_baru')) {
            $pengguna->password = Hash::make($request->input('kata_sandi_baru'));
        }

        $pengguna->save();

        return redirect()
            ->route('profil.index')
            ->with('status', 'Profil berhasil disimpan.');
    }
}
