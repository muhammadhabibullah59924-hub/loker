<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardOrderTest extends TestCase
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
     * Pengguna mengizinkan lokasi: kartu urut menaik sesuai jarak dan ada angka km.
     */
    public function test_kartu_terurut_menaik_sesuai_jarak(): void
    {
        $this->buatLoker('PT Zzz Jauh', 0.0, 0.10);
        $this->buatLoker('PT Aaa Dekat', 0.0, 0.01);
        $this->buatLoker('PT Mmm Tengah', 0.0, 0.05);

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=0&lng=0');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Aaa Dekat', 'PT Mmm Tengah', 'PT Zzz Jauh']);
        $response->assertSee('km');
        $response->assertSee('Diurutkan menurut lokasimu saat ini');
    }

    /**
     * Tanpa lokasi pengguna, dasbor memakai urutan default pusat Probolinggo
     * dan menampilkan peringatan untuk mengizinkan lokasi.
     */
    public function test_tanpa_lokasi_memakai_urutan_default_dan_peringatan(): void
    {
        $this->buatLoker('PT Dekat Pusat', -7.7145, 113.2200);
        $this->buatLoker('PT Jauh Pusat', -7.7500, 113.2500);

        $response = $this->actingAs($this->admin)->get('/dasbor');

        $response->assertStatus(200);
        $response->assertSee('Izinkan lokasi agar loker diurutkan dari yang terdekat.');
        $response->assertSee('pusat Kota Probolinggo');
        $response->assertSee('id="tombol-izin"', false);
        $response->assertSeeInOrder(['PT Dekat Pusat', 'PT Jauh Pusat']);
    }

    /**
     * R1: tanpa pencarian atau filter, minimal 3 loker tampil lengkap dengan
     * nama PT, alamat jalan, divisi, dan jarak.
     */
    public function test_minimal_tiga_loker_tampil_lengkap(): void
    {
        $this->buatLoker('PT Pertama', -7.7145, 113.2150, true, 'Jl. Pertama No. 1', 'Produksi');
        $this->buatLoker('PT Kedua', -7.7150, 113.2160, true, 'Jl. Kedua No. 2', 'Gudang');
        $this->buatLoker('PT Ketiga', -7.7155, 113.2170, true, 'Jl. Ketiga No. 3', 'Kasir');

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Pertama', 'PT Kedua', 'PT Ketiga']);

        foreach (['Jl. Pertama No. 1', 'Jl. Kedua No. 2', 'Jl. Ketiga No. 3', 'Produksi', 'Gudang', 'Kasir'] as $isi) {
            $response->assertSee($isi);
        }

        $response->assertSee('km');
    }

    /**
     * R2: loker nonaktif tidak pernah tampil di dasbor meski datanya masih ada.
     */
    public function test_loker_nonaktif_tidak_tampil(): void
    {
        $aktif = $this->buatLoker('PT Masih Tayang', -7.7145, 113.2150, true);
        $this->buatLoker('PT Sudah Nonaktif', -7.7145, 113.2150, false);

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('PT Masih Tayang');
        $response->assertDontSee('PT Sudah Nonaktif');
        $this->assertDatabaseHas('jobs', ['id' => $aktif->id]);
        $this->assertSame(2, Job::count());
    }

    /**
     * Pengguna yang belum masuk tidak bisa membuka dasbor.
     */
    public function test_pengguna_tanpa_sesi_dialihkan_ke_halaman_masuk(): void
    {
        $response = $this->get('/dasbor');

        $response->assertRedirect('/login');
    }

    /**
     * Koordinat yang tidak berupa angka diabaikan, bukan menampilkan halaman galat.
     */
    public function test_koordinat_tidak_valid_jatuh_ke_urutan_default(): void
    {
        $this->buatLoker('PT Satu', -7.7145, 113.2150);

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=bukan-angka&lng=999');

        $response->assertStatus(200);
        $response->assertSessionHasNoErrors();
        $response->assertSee('Izinkan lokasi agar loker diurutkan dari yang terdekat.');
    }

    /**
     * Buat satu loker dengan admin sebagai pemilik untuk keperluan uji.
     */
    private function buatLoker(
        string $nama,
        float $lat,
        float $lng,
        bool $aktif = true,
        string $alamat = 'Jl. Contoh No. 1, Mayangan',
        string $divisi = 'Produksi',
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
            'is_active' => $aktif,
            'posted_by' => $this->admin->id,
        ]);
    }
}
