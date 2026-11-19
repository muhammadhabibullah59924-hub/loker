<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthDesignTest extends TestCase
{
    /**
     * T-047: halaman masuk memakai panel samping berisi keunggulan.
     */
    public function test_halaman_masuk_menampilkan_panel_keunggulan(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('auth-samping', false);
        $response->assertSee('Loker terdekat, langsung kelihatan.');
        $response->assertSee('Peta lokasi di setiap loker');
    }

    /**
     * T-047: halaman daftar memakai panel samping berisi keunggulan.
     */
    public function test_halaman_daftar_menampilkan_panel_keunggulan(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('auth-samping', false);
        $response->assertSee('Cari kerja mulai dari yang terdekat.');
        $response->assertSee('Simpan loker favorit');
    }
}
