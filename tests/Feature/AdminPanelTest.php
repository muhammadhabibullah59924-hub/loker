<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * T-035: filter status menyaring daftar loker di panel admin.
     */
    public function test_filter_status_menyaring_daftar(): void
    {
        $this->buatLoker('PT Selalu Tayang');
        $this->buatLoker('PT Sudah Nonaktif', ['is_active' => false]);

        $response = $this->actingAs($this->admin)->get('/admin?status=tayang');

        $response->assertStatus(200);
        $response->assertSee('PT Selalu Tayang');
        $response->assertDontSee('PT Sudah Nonaktif');
    }

    /**
     * T-035: filter tipe dan pencarian kata kunci bekerja di panel admin.
     */
    public function test_filter_tipe_dan_pencarian(): void
    {
        $this->buatLoker('PT Penuh Waktu', ['job_type' => 'Full-Time']);
        $this->buatLoker('PT Paruh Waktu', ['job_type' => 'Part-Time']);

        $this->actingAs($this->admin)
            ->get('/admin?tipe=Part-Time')
            ->assertStatus(200)
            ->assertSee('PT Paruh Waktu')
            ->assertDontSee('PT Penuh Waktu');

        $this->actingAs($this->admin)
            ->get('/admin?q=Penuh')
            ->assertStatus(200)
            ->assertSee('PT Penuh Waktu')
            ->assertDontSee('PT Paruh Waktu');
    }

    /**
     * T-035: tabel admin menampilkan tipe dan batas lamaran loker.
     */
    public function test_tabel_menampilkan_tipe_dan_batas_lamaran(): void
    {
        $this->buatLoker('PT Info Lengkap', ['job_type' => 'Full-Time']);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Batas lamaran');
        $response->assertSee('30 Nov 2026');
        $response->assertSee('job-tipe--full-time', false);
    }

    /**
     * T-035: kartu statistik menampilkan jumlah premium dan kebutuhan orang.
     */
    public function test_statistik_menghitung_loker_premium_dan_butuh_banyak(): void
    {
        $this->buatLoker('PT Premium', ['is_premium' => true]);
        $this->buatLoker('PT Butuh Tiga', ['positions_needed' => 3]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Loker premium');
        $response->assertSee('Butuh banyak orang');
    }

    /**
     * Buat satu loker untuk keperluan uji panel admin.
     *
     * @param  array<string, mixed>  $ubah
     */
    private function buatLoker(string $nama, array $ubah = []): Job
    {
        return Job::create(array_merge([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'job_type' => 'Full-Time',
            'positions_needed' => 1,
            'is_premium' => false,
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ], $ubah));
    }
}
