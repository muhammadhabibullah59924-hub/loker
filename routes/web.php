<?php

use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\PanelController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobDetailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Dasbor F2 hanya untuk pengguna yang sudah masuk.
Route::get('/dasbor', [DashboardController::class, 'index'])->middleware('auth')->name('dasbor');

// Detail satu loker beserta peta (F3): hanya loker yang tayang, id berupa angka.
Route::get('/lowongan/{id}', [JobDetailController::class, 'show'])
    ->middleware('auth')
    ->whereNumber('id')
    ->name('lowongan.show');

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

/*
 * Daftar loker favorit pengguna (F6).
 */
Route::get('/favorit', [FavoriteController::class, 'index'])->middleware('auth')->name('favorit.index');
Route::post('/favorit/{loker}', [FavoriteController::class, 'store'])
    ->middleware('auth')
    ->whereNumber('loker')
    ->name('favorit.store');
Route::delete('/favorit/{loker}', [FavoriteController::class, 'destroy'])
    ->middleware('auth')
    ->whereNumber('loker')
    ->name('favorit.destroy');

/*
 * Panel admin (F4): seluruh rute diawali `auth` lalu `admin` sehingga tamu
 * dialihkan ke halaman Masuk dan pengguna biasa dialihkan keluar dari /admin.
 * Rute CRUD loker ditambahkan pada T-009 di dalam grup ini.
 */
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('panel');

    // CRUD loker (F4). Rute /admin/loker/create harus dideklarasikan sebelum
    // pemetaan {loker} supaya kata "create" tidak dianggap sebuah id.
    Route::get('loker/create', [JobController::class, 'create'])->name('loker.create');
    Route::post('loker', [JobController::class, 'store'])->name('loker.store');
    Route::get('loker/{loker}/edit', [JobController::class, 'edit'])->whereNumber('loker')->name('loker.edit');
    Route::put('loker/{loker}', [JobController::class, 'update'])->whereNumber('loker')->name('loker.update');
    Route::patch('loker/{loker}/nonaktif', [JobController::class, 'toggle'])->whereNumber('loker')->name('loker.toggle');
    Route::delete('loker/{loker}', [JobController::class, 'destroy'])->whereNumber('loker')->name('loker.destroy');
});
