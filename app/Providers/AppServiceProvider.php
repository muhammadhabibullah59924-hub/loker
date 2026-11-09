<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /*
         * Pembatas percobaan masuk (OWASP A07, lihat docs/SECURITY.md):
         * maksimal 5 percobaan per menit untuk kombinasi email dan alamat IP.
         * Pesan galat tetap sama sehingga pelaku tidak bisa menebak
         * apakah emailnya terdaftar atau tidak.
         */
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });
    }
}
