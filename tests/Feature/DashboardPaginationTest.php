<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $pengguna;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pengguna = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * T-053: halaman pertama menampilkan 20 loker pertama.
     */
    public function test_halaman_pertama_menampilkan_dua_puluh_loker(): void
    {
        $this->buatLokerBanyak(45);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?page=1');

        $response->assertStatus(200);
        $response->assertSee('PT Loker 001');
        $response->assertSee('PT Loker 020');
        $response->assertDontSee('PT Loker 021');
        $response->assertSee('Menampilkan loker 1–20 dari 45');
    }

    /**
     * T-053: halaman kedua menampilkan 20 loker berikutnya.
     */
    public function test_halaman_kedua_menampilkan_loker_berikutnya(): void
    {
        $this->buatLokerBanyak(45);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?page=2');

        $response->assertStatus(200);
        $response->assertSee('PT Loker 021');
        $response->assertSee('PT Loker 040');
        $response->assertDontSee('PT Loker 001');
        $response->assertSee('Menampilkan loker 21–40 dari 45');
    }

    /**
     * T-053: halaman terakhir hanya berisi sisa loker.
     */
    public function test_halaman_terakhir_menampilkan_sisa_loker(): void
    {
        $this->buatLokerBanyak(45);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?page=3');

        $response->assertStatus(200);
        $response->assertSee('PT Loker 041');
        $response->assertSee('PT Loker 045');
        $response->assertDontSee('PT Loker 040');
        $response->assertSee('Menampilkan loker 41–45 dari 45');
    }

    /**
     * T-053: halaman di luar rentang jatuh ke halaman terakhir, bukan galat.
     */
    public function test_halaman_di_luar_rentang_jatuh_ke_halaman_terakhir(): void
    {
        $this->buatLokerBanyak(45);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?page=999');

        $response->assertStatus(200);
        $response->assertSee('PT Loker 045');
        $response->assertDontSee('PT Loker 001');
    }

    /**
     * T-053: filter tipe tetap berlaku setelah berpindah halaman.
     */
    public function test_filter_tipe_tetap_berlaku_di_halaman_berikutnya(): void
    {
        $this->buatLokerBanyak(25);

        $partTime = $this->buatLoker('PT Part Time A');
        $partTime->forceFill(['job_type' => 'Part-Time'])->save();

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?page=2&tipe=Full-Time');

        $response->assertStatus(200);
        $response->assertSee('PT Loker 021');
        $response->assertDontSee('PT Part Time A');
        $response->assertSee('page=2', false);
    }

    /**
     * Buat sejumlah loker Full-Time dengan nama yang mudah diuji.
     */
    private function buatLokerBanyak(int $jumlah): void
    {
        for ($i = 1; $i <= $jumlah; $i++) {
            $this->buatLoker('PT Loker '.str_pad((string) $i, 3, '0', STR_PAD_LEFT));
        }
    }

    private function buatLoker(string $nama): Job
    {
        return Job::create([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'job_type' => 'Full-Time',
            'positions_needed' => 1,
            'deadline' => '2026-12-31',
            'latitude' => -7.7145,
            'longitude' => 113.2250,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);
    }
}
