<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobRequiredFieldsTest extends TestCase
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
     * R2: penyimpanan loker gagal bila nama PT, alamat, divisi, atau koordinat kosong.
     */
    public function test_simpan_loker_ditolak_bila_field_wajib_kosong(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/loker', [
            'company_name' => '',
            'address' => '',
            'division' => '',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'deadline' => '',
            'latitude' => '',
            'longitude' => '',
        ]);

        $response->assertSessionHasErrors(['company_name', 'address', 'division', 'latitude', 'longitude']);
        $this->assertSame(0, Job::count());
    }

    /**
     * R2: kartu dasbor menampilkan keempat informasi loker: nama PT, alamat jalan,
     * divisi, dan angka jarak.
     */
    public function test_kartu_dasbor_menampilkan_nama_pt_alamat_dan_divisi_dengan_jarak(): void
    {
        $this->buatLoker('PT Satu', -7.7145, 113.2150, 'Jl. Satu No. 1', 'Produksi');
        $this->buatLoker('PT Dua', -7.7145, 113.2160, 'Jl. Dua No. 2', 'Desain');

        $response = $this->actingAs($this->admin)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);

        foreach (Job::all() as $loker) {
            $response->assertSee($loker->company_name);
            $response->assertSee($loker->address);
            $response->assertSee($loker->division);
        }

        $response->assertSee('km');
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
