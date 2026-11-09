<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardNearestVisibleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin LokerDekat',
            'email' => 'admin@uji.test',
            'role' => 'admin',
        ]);
    }

    /**
     * R1: tanpa pencarian atau filter, minimal 3 loker terdekat tampil lengkap
     * dengan nama PT, alamat jalan, divisi, dan angka jarak.
     */
    public function test_dasbor_menampilkan_minimal_tiga_terdekat_dengan_keempat_informasi(): void
    {
        $this->buatLoker('PT Terdekat', -7.7145, 113.2160, 'Jl. Terdekat No. 2', 'Kasir');
        $this->buatLoker('PT Tengah', -7.7145, 113.2190, 'Jl. Tengah No. 3', 'Desain');
        $this->buatLoker('PT Jauh', -7.7145, 113.2240, 'Jl. Jauh No. 4', 'Produksi');
        $this->buatLoker('PT Terjauh', -7.7145, 113.2300, 'Jl. Terjauh No. 9', 'Gudang');

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Terdekat', 'PT Tengah', 'PT Jauh', 'PT Terjauh']);

        foreach (['Jl. Terdekat No. 2', 'Jl. Tengah No. 3', 'Jl. Jauh No. 4', 'Jl. Terjauh No. 9', 'Kasir', 'Desain', 'Produksi', 'Gudang'] as $isi) {
            $response->assertSee($isi);
        }

        $response->assertSee('km');
    }

    /**
     * R1: urutan default saat pengguna belum mengizinkan lokasi tetap memakai
     * pusat Probolinggo dan menampilkan minimal 3 loker terdekat.
     */
    public function test_dasbor_tanpa_izin_memakai_urutan_default_dan_menampilkan_tiga_terdekat(): void
    {
        $this->buatLoker('PT Dekat Pusat', -7.7145, 113.2200, 'Jl. Dekat Pusat No. 1', 'Produksi');
        $this->buatLoker('PT Jauh Pusat', -7.7145, 113.2500, 'Jl. Jauh Pusat No. 2', 'Gudang');

        $response = $this->actingAs($this->admin)->get('/dasbor');

        $response->assertStatus(200);
        $response->assertSee('Izinkan lokasi agar loker diurutkan dari yang terdekat.');
        $response->assertSee('pusat Kota Probolinggo');
        $response->assertSeeInOrder(['PT Dekat Pusat', 'PT Jauh Pusat']);
    }

    /**
     * Buat satu loker dengan admin sebagai pemilik untuk keperluan uji.
     */
    private function buatLoker(
        string $nama,
        float $lat,
        float $lng,
        string $alamat,
        string $divisi,
    ): Job {
        return Job::create([
            'company_name' => $nama,
            'address' => $alamat,
            'division' => $divisi,
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'deadline' => '2026-11-30',
            'latitude' => $lat,
            'longitude' => $lng,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);
    }
}
