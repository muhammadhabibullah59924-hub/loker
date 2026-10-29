<?php

namespace App\Services;

/**
 * Perhitungan jarak antara dua titik koordinat memakai rumus Haversine.
 *
 * Seluruh logika jarak berada di berkas ini — bukan di controller maupun view
 * (lihat AGENTS.md dan docs/CODE_STYLE.md). Hasilnya dipakai dasbor F2 untuk
 * mengurutkan loker dari yang terdekat ke lokasi pengguna.
 */
class DistanceService
{
    /**
     * Jari-jari bumi dalam kilometer.
     */
    private const EARTH_RADIUS_KM = 6371.0;

    /**
     * Hitung jarak lingkaran udara antara dua koordinat, dibulatkan satu desimal.
     *
     * @param  float  $lat1  lintang titik awal
     * @param  float  $lng1  bujur titik awal
     * @param  float  $lat2  lintang titik tujuan
     * @param  float  $lng2  bujur titik tujuan
     * @return float jarak dalam kilometer
     */
    public function distanceInKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        // acos dipakai karena rumus Haversine menghasilkan kosinus sudut pusat
        // bumi; hasil kosinus dibatasi ke rentang [-1, 1] agar pembulatan bilangan
        // titik koma saat kedua titik berimpit tidak memunculkan NAN.
        $cosine = cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * cos(deg2rad($lng2) - deg2rad($lng1))
            + sin(deg2rad($lat1)) * sin(deg2rad($lat2));

        $cosine = max(-1.0, min(1.0, $cosine));

        return round(self::EARTH_RADIUS_KM * acos($cosine), 1);
    }
}
