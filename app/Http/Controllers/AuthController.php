<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Alur F1: daftar, masuk, dan keluar (lihat docs/PRD.md).
 */
class AuthController extends Controller
{
    /**
     * Tampilkan form Daftar.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Simpan akun baru lalu masukkan pengguna ke sesi.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->lower()->toString(),
            'password' => $request->string('password')->toString(),
            'role' => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')->with('status', 'Akun berhasil dibuat. Selamat datang di LokerDekat!');
    }

    /**
     * Tampilkan form Masuk.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Periksa kredensial lalu buka sesi pengguna.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = [
            'email' => $request->string('email')->lower()->toString(),
            'password' => $request->string('password')->toString(),
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang kamu masukkan belum cocok. Coba periksa lagi.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    /**
     * Akhiri sesi pengguna yang sedang masuk.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
