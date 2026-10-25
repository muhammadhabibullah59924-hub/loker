<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dasbor F2 hanya untuk pengguna yang sudah masuk.
Route::get('/dasbor', [DashboardController::class, 'index'])->middleware('auth')->name('dasbor');

/*
 * Rute tamu (dijaga middleware `guest`): pengguna yang sudah masuk akan
 * diarahkan ke dasbor (lihat bootstrap/app.php).
 */
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login')->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
