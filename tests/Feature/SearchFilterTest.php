<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    private User $pengguna;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pengguna = User::factory()->create([
            'name' => 'Budi Pencari Kerja',
            'email' => 'budi@uji.test',
            'role' => 'user',
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin LokerDekat',
            'email' => 'admin@uji.test',
            'role' => 'admin',
        ]);
    }

    /**
     * Pencarian nama PT hanya menampilkan loker yang cocok (F5).
     */
    public function test_pencarian_nama_pt_hanya_menampilkan_yang_cocok(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Operator');
        $this->buatLoker('PT Gamma Makanan', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=alpha');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertDontSee('PT Beta Tekstil');
        $response->assertDontSee('PT Gamma Makanan');
    }

    /**
     * Pencarian divisi juga hanya menampilkan loker yang cocok (F5).
     */
    public function test_pencarian_divisi_hanya_menampilkan_yang_cocok(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Operator');
        $this->buatLoker('PT Gamma Makanan', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=operator');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertSee('PT Beta Tekstil');
        $response->assertDontSee('PT Gamma Makanan');
    }

    /**
     * Kata kunci yang tidak cocok menampilkan pesan kosong (F5).
     */
    public function test_kata_kunci_tanpa_hasil_menampilkan_pesan_kosong(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=zzzx');

        $response->assertStatus(200);
        $response->assertSee('Belum ada loker yang cocok. Coba ubah kata kunci pencarianmu.');
    }

    /**
     * Hasil pencarian tetap menjamin urutan jarak menaik (F5).
     */
    public function test_pencarian_mempertahankan_urutan_jarak(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Operator');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=operator');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Alpha Energi', 'PT Beta Tekstil']);
    }

    /**
     * Filter radius hanya menampilkan loker yang berada dalam radius itu (F5).
     */
    public function test_filter_radius_hanya_menampilkan_loker_dalam_radius(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator', 113.2250);
        $this->buatLoker('PT Beta Tekstil', 'Operator', 113.2427);
        $this->buatLoker('PT Gamma Makanan', 'Gudang', 113.2778);

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&radius=5');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertSee('PT Beta Tekstil');
        $response->assertDontSee('PT Gamma Makanan');
        $response->assertSee('Menampilkan loker terdekat dalam radius 5 km dari lokasimu.');
    }

    /**
     * Tanpa radius menampilkan seluruh loker aktif yang ada.
     */
    public function test_tanpa_radius_seluruh_loker_aktif_tampil(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator', 113.2250);
        $this->buatLoker('PT Beta Tekstil', 'Operator', 113.2427);
        $this->buatLoker('PT Gamma Makanan', 'Gudang', 113.2778);

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertSee('PT Beta Tekstil');
        $response->assertSee('PT Gamma Makanan');
    }

    /**
     * Kata kunci tersimpan di dalam kolom pencarian (alur GET tanpa redirect galat).
     */
    public function test_kata_kunci_tersimpan_ke_form_pencarian(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator', 113.2250);

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=alpha');

        $response->assertStatus(200);
        $response->assertSee('id="cari-loker"', false);
        $response->assertSee('value="alpha"', false);
    }

    /**
     * Koordinat pengguna tetap dipakai sebagai urutan ketika ada pencarian/filter.
     */
    public function test_pencarian_mempertahankan_koordinat_lokasi(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator', 113.2250);

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&q=alpha&radius=5');

        $response->assertStatus(200);
        $response->assertSee('name="lat"', false);
        $response->assertSee('name="lng"', false);
        $response->assertSee('Menampilkan loker terdekat dalam radius 5 km dari lokasimu.');
    }

    /**
     * Buat satu loker berbobot dari titik pusat Probolinggo (lihat ADR-009).
     */
    private function buatLoker(string $nama, string $divisi, float $lng = 113.2250): Job
    {
        $loker = Job::create([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => $divisi,
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => $lng,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);

        return $loker;
    }
}
