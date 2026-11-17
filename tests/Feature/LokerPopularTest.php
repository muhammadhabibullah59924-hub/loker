<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LokerPopularTest extends TestCase
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
     * Loker populer tampil di dasbor dan diurut dari yang butuh paling banyak.
     */
    public function test_loker_populer_tampil_dengan_loker_yang_tapi_paling_butuh(): void
    {
        $this->buatLoker('PT Butuh Lima', -7.7145, 113.2146, 5, 'Full-Time');
        $this->buatLoker('PT Butuh Dua', -7.7145, 113.2146, 2, 'Part-Time');

        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('Loker Populer');
        $response->assertSeeInOrder(['PT Butuh Lima', 'PT Butuh Dua']);
        $response->assertSee('Full-Time');
        $response->assertSee('Part-Time');
        $response->assertSee('Lowongan butuh 5 orang');
    }

    /**
     * Loker yang tidak aktif tidak masuk daftar loker populer.
     */
    public function test_loker_nonaktif_tidak_tampil_di_loker_populer(): void
    {
        $this->buatLoker('PT Butuh Lima', -7.7145, 113.2146, 5, 'Full-Time');
        $this->buatLoker('PT Butuh Duduk', -7.7145, 113.2146, 10, 'Full-Time', false);

        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('PT Butuh Lima');
        $response->assertDontSee('PT Butuh Duduk');
    }

    /**
     * Detail loker menampilkan tipe dan jumlah lowongan yang dibutuhkan.
     */
    public function test_detail_menampilkan_tipe_dan_jumlah_lowongan(): void
    {
        $loker = $this->buatLoker('PT Butuh Lima', -7.7145, 113.2146, 3, 'Full-Time');

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Full-Time');
        $response->assertSee('Lowongan butuh 3 orang');
    }

    /**
     * Buat satu loker tayang milik admin untuk keperluan uji.
     *
     * @param  array<string, mixed>  $ubah
     */
    private function buatLoker(
        string $nama,
        float $lat,
        float $lng,
        int $positionsNeeded = 1,
        ?string $jobType = null,
        bool $aktif = true,
    ): Job {
        return Job::create([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'job_type' => $jobType,
            'positions_needed' => $positionsNeeded,
            'deadline' => '2026-11-30',
            'latitude' => $lat,
            'longitude' => $lng,
            'is_active' => $aktif,
            'posted_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);
    }
}
