<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardHeroTest extends TestCase
{
    use RefreshDatabase;

    private User $pengguna;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pengguna = User::factory()->create([
            'name' => 'Budi Pencari Kerja',
            'email' => 'budi@uji.test',
            'role' => 'user',
        ]);
    }

    /**
     * T-026: hero dasbor menampilkan eyebrow, judul, dan subjudul.
     */
    public function test_dasbor_menampilkan_hero_dengan_eyebrow_dan_judul(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('hero-dasbor');
        $response->assertSee('Rekomendasi terdekat');
        $response->assertSee('Loker terdekat dari lokasimu');
    }

    /**
     * T-036: navbar menandai menu aktif dan memakai aksen teal.
     */
    public function test_navbar_menandai_menu_aktif(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('nav-link--aktif', false);
        $response->assertSee('site-navbar', false);
    }

    /**
     * T-037: dasbor menyiapkan filter otomatis dan tetap punya tombol "Cari loker".
     */
    public function test_dasbor_menyiapkan_filter_otomatis(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('data-filter-otomatis', false);
        $response->assertSee('id="status-filter"', false);
        $response->assertSee('Cari loker');
    }

    /**
     * T-042: dasbor menampilkan pengingat akses cepat dari HP tanpa tautan toko aplikasi.
     */
    public function test_dasbor_menampilkan_banner_akses_hp(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('id="banner-hp"', false);
        $response->assertSee('Buka LokerDekat di HP');
        $response->assertSee('layar utama');
        $response->assertDontSee('play.google.com');
        $response->assertDontSee('apps.apple.com');
    }

    /**
     * T-048: kartu statistik dasbor menampilkan empat ringkasan.
     */
    public function test_dasbor_menampilkan_empat_kartu_statistik(): void
    {
        Job::create([
            'company_name' => 'PT Statistik Dasbor',
            'address' => 'Jl. Statistik No. 1',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi untuk uji statistik.',
            'requirements' => 'Syarat untuk uji statistik.',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => $this->pengguna->id,
        ]);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('Loker aktif tersedia');
        $response->assertSee('Loker populer');
        $response->assertSee('Loker premium');
        $response->assertSee('Divisi tersedia');
        $response->assertSee('stat-card__nilai', false);
    }
}
