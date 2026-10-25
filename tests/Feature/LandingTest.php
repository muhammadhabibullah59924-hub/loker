<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingTest extends TestCase
{
    /**
     * Halaman depan menampilkan identitas dan tagline LokerDekat.
     */
    public function test_halaman_depan_menampilkan_tagline(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Loker terdekat dari lokasimu');
        $response->assertSee('Website info loker yang mengurutkan lowongan');
    }

    /**
     * Panduan tiga langkah kendali R3 tampil lengkap di halaman depan.
     */
    public function test_halaman_depan_menampilkan_panduan_tiga_langkah(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Izinkan lokasi');
        $response->assertSee('Lihat urut terdekat');
        $response->assertSee('Buka detail dan peta');
    }

    /**
     * Pengunjung yang belum masuk tetap melihat tombol Masuk dan Daftar.
     */
    public function test_halaman_depan_menampilkan_tombol_masuk_dan_daftar(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Masuk');
        $response->assertSee('Daftar');
        $response->assertSee('logo.svg');
    }
}
