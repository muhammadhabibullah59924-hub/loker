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

    /**
     * Pengunjung tamu tidak diminta masuk lebih dulu untuk melihat halaman depan.
     */
    public function test_halaman_depan_bisa_dibuka_tanpa_masuk(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $this->assertGuest();
    }

    /**
     * T-029: hero halaman depan menampilkan eyebrow sebelum judul.
     */
    public function test_halaman_depan_menampilkan_hero_dengan_eyebrow(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Rekomendasi terdekat');
    }

    /**
     * T-036: hero halaman depan menampilkan contoh kartu loker.
     */
    public function test_halaman_depan_menampilkan_contoh_kartu_loker(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Contoh kartu loker');
        $response->assertSee('Batas lamaran: 30 November 2026');
        $response->assertSee('0,8 km');
    }

    /**
     * T-040: halaman depan menampilkan bagian manfaat dan pemisah antar bagian.
     */
    public function test_halaman_depan_menampilkan_bagian_kenapa_lokerdekat(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Kenapa LokerDekat');
        $response->assertSee('Cari kerja tanpa muter-muter');
        $response->assertSee('Diurutkan dari terdekat');
        $response->assertSee('bagian-pemisah', false);
    }

    /**
     * T-045: halaman depan menampilkan FAQ statis dalam accordion.
     */
    public function test_halaman_depan_menampilkan_faq(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang sering ditanyakan');
        $response->assertSee('Apakah LokerDekat berbayar?');
        $response->assertSee('Apakah lokasi saya disimpan?');
        $response->assertSee('Bisa lamar langsung di sini?');
        $response->assertSee('data-bs-toggle="collapse"', false);
    }

    /**
     * T-048: footer memiliki kolom jelajahi, tentang, dan baris bawah.
     */
    public function test_footer_menampilkan_jelajahi_dan_tentang(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertStatus(200);
        $response->assertSee('Jelajahi');
        $response->assertSee('Tentang');
        $response->assertSee('site-footer__bawah', false);
        $response->assertSee('&copy; 2026 LokerDekat', false);
    }
}
