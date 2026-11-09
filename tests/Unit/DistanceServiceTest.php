<?php

namespace Tests\Unit;

use App\Services\DistanceService;
use PHPUnit\Framework\TestCase;

class DistanceServiceTest extends TestCase
{
    /**
     * Titik yang berimpit menghasilkan nol kilometer.
     */
    public function test_jarak_antara_titik_yang_sama_adalah_nol(): void
    {
        $service = new DistanceService;

        $hasil = $service->distanceInKm(-7.7145, 113.2146, -7.7145, 113.2146);

        $this->assertSame(0.0, $hasil);
    }

    /**
     * Uji titik 1: London ke Paris (sekitar 343,5 km menurut data geografis).
     */
    public function test_jarak_london_ke_paris(): void
    {
        $service = new DistanceService;

        $hasil = $service->distanceInKm(51.5074, -0.1278, 48.8566, 2.3522);

        $this->assertEqualsWithDelta(343.5, $hasil, 1.0);
    }

    /**
     * Uji titik 2: pusat Probolinggo ke Surabaya (sekitar 72 km).
     */
    public function test_jarak_probolinggo_ke_surabaya(): void
    {
        $service = new DistanceService;

        $hasil = $service->distanceInKm(-7.7145, 113.2146, -7.2575, 112.7521);

        $this->assertEqualsWithDelta(72.0, $hasil, 1.0);
    }

    /**
     * Jarak bersifat simetris: dari A ke B sama dengan dari B ke A.
     */
    public function test_jarak_simetris(): void
    {
        $service = new DistanceService;

        $keB = $service->distanceInKm(-7.7145, 113.2146, -7.9666, 112.6326);
        $keA = $service->distanceInKm(-7.9666, 112.6326, -7.7145, 113.2146);

        $this->assertSame($keB, $keA);
        $this->assertEqualsWithDelta(70.0, $keB, 1.0);
    }

    /**
     * Hasil selalu dibulatkan satu desimal sesuai format jarak di dasbor.
     */
    public function test_hasil_dibulatkan_satu_desimal(): void
    {
        $service = new DistanceService;

        $hasil = $service->distanceInKm(-7.7145, 113.2146, -7.7200, 113.2200);

        $this->assertSame(round($hasil, 1), $hasil);
        $this->assertGreaterThan(0.0, $hasil);
    }
}
