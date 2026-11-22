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
     * T-031: urutan terbaru menampilkan loker yang paling baru lebih dulu.
     */
    public function test_urutkan_loker_terbaru(): void
    {
        $lama = $this->buatLokerTipe('PT Lama Dekat', 'Operator', 'Full-Time');
        $lama->forceFill(['created_at' => now()->subDays(3)])->save();

        $baru = $this->buatLokerTipe('PT Baru Jauh', 'Operator', 'Full-Time');
        $baru->forceFill(['created_at' => now()->subDay()])->save();

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&urut=terbaru');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Baru Jauh', 'PT Lama Dekat']);
    }

    /**
     * T-031: urutan butuh terbanyak menampilkan loker dengan kebutuhan terbanyak lebih dulu.
     */
    public function test_urutkan_loker_butuh_terbanyak(): void
    {
        $this->buatLokerButuh('PT Butuh Dua', 2);
        $this->buatLokerButuh('PT Butuh Tiga', 3);

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&urut=butuh');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['PT Butuh Tiga', 'PT Butuh Dua']);
    }

    /**
     * T-032: chip divisi menyaring loker sesuai divisi yang dipilih.
     */
    public function test_chip_divisi_menyaring_loker_sesuai_divisi(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&divisi=Operator');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertDontSee('PT Beta Tekstil');
        $response->assertSee('Menampilkan loker divisi Operator.');
    }

    /**
     * T-032: dasbor menampilkan chip divisi beserta opsi semua divisi.
     */
    public function test_dasbor_menampilkan_chip_divisi(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('Semua divisi');
        $response->assertSee('Operator');
        $response->assertSee('Gudang');
    }

    /**
     * T-050: chip divisi tampil sebagai pita horizontal yang bisa digeser.
     */
    public function test_chip_divisi_menjadi_pita_yang_bisa_digeser(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('class="chip-scroll d-flex flex-nowrap gap-2 overflow-x-auto pb-2 mb-1"', false);
        $response->assertSee('Geser ke samping untuk melihat divisi lain.');
        $response->assertSee('divisi=Operator', false);
        $response->assertSee('divisi=Gudang', false);
    }

    /**
     * T-050: chip divisi yang aktif otomatis dibawa ke dalam pita.
     */
    public function test_chip_divisi_aktif_dibawa_ke_tampilan(): void
    {
        $this->buatLoker('PT Alpha Energi', 'Operator');
        $this->buatLoker('PT Beta Tekstil', 'Gudang');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&divisi=Gudang');

        $response->assertStatus(200);
        $response->assertSee('scrollIntoView', false);
        $response->assertSee('chip-divisi--aktif', false);
    }

    /**
     * T-030: filter tipe loker hanya menampilkan loker dengan tipe yang dipilih.
     */
    public function test_filter_tipe_loker_hanya_menampilkan_tipe_yang_dipilih(): void
    {
        $this->buatLokerTipe('PT Alpha Energi', 'Operator', 'Full-Time');
        $this->buatLokerTipe('PT Beta Tekstil', 'Operator', 'Part-Time');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&tipe=Full-Time');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertDontSee('PT Beta Tekstil');
        $response->assertSee('Menampilkan loker bertipe Full-Time.');
    }

    /**
     * T-030: tipe loker yang tidak dikenal diabaikan tanpa menampilkan galat.
     */
    public function test_tipe_loker_tidak_dikenal_diabaikan(): void
    {
        $this->buatLokerTipe('PT Alpha Energi', 'Operator', 'Full-Time');
        $this->buatLokerTipe('PT Beta Tekstil', 'Operator', 'Part-Time');

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&tipe=Kontrak');

        $response->assertStatus(200);
        $response->assertSee('PT Alpha Energi');
        $response->assertSee('PT Beta Tekstil');
    }

    /**
     * T-052: bagian loker populer ikut mematuhi filter tipe yang aktif.
     */
    public function test_loker_populer_juga_mematuhi_filter_tipe(): void
    {
        $this->buatLokerButuh('PT Populer Full', 5);

        $partTime = $this->buatLokerButuh('PT Populer Part', 4);
        $partTime->forceFill(['job_type' => 'Part-Time'])->save();

        $response = $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146&tipe=Full-Time');

        $response->assertStatus(200);
        $response->assertSee('PT Populer Full');
        $response->assertDontSee('PT Populer Part');
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

    /**
     * Buat satu loker dengan tipe tertentu untuk pengujian filter tipe (T-030).
     */
    private function buatLokerTipe(string $nama, string $divisi, string $jobType): Job
    {
        return Job::create([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => $divisi,
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'job_type' => $jobType,
            'positions_needed' => 1,
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => 113.2250,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);
    }

    /**
     * Buat satu loker dengan jumlah kebutuhan tertentu untuk pengujian urutan (T-031).
     */
    private function buatLokerButuh(string $nama, int $positions): Job
    {
        return Job::create([
            'company_name' => $nama,
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'job_type' => 'Full-Time',
            'positions_needed' => $positions,
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => 113.2250,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);
    }
}
