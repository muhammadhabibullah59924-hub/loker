<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobDetailMapTest extends TestCase
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
     * Halaman detail memuat peta dan menaruh marker di koordinat loker (F3).
     */
    public function test_detail_menampilkan_peta_dan_marker(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('id="peta-loker"', false);
        $response->assertSee('data-lat="'.$loker->latitude.'"', false);
        $response->assertSee('data-lng="'.$loker->longitude.'"', false);
        $response->assertSee('L.marker(', false);
        $response->assertSee('tile.openstreetmap.org', false);
    }

    /**
     * Pustaka peta diambil dari CDN dengan versi yang dipin, bukan versi karangan.
     */
    public function test_pustaka_peta_dimuat_dari_cdn_dengan_versi_persis(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertSee('cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js', false);
        $response->assertSee('cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css', false);
    }

    /**
     * Alamat yang tampil persis sama dengan nilai field alamat di basis data
     * dan ditulis sebagai teks biasa agar tetap terbaca bila peta gagal.
     */
    public function test_alamat_sesuai_nilai_di_basis_data(): void
    {
        $loker = $this->buatLoker([
            'address' => 'Jl. Raya Krucil No. 45, Klandasan',
        ]);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('<p class="job-detail__alamat mb-4">Jl. Raya Krucil No. 45, Klandasan</p>', false);
        $this->assertSame(
            'Jl. Raya Krucil No. 45, Klandasan',
            Job::findOrFail($loker->id)->address,
        );
    }

    /**
     * Loker yang sudah dinonaktifkan tidak bisa dibuka lewat URL (R2).
     */
    public function test_loker_nonaktif_menghasilkan_404(): void
    {
        $loker = $this->buatLoker(['is_active' => false]);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(404);
    }

    /**
     * Id yang tidak dikenal juga dijawab 404, bukan halaman kosong.
     */
    public function test_id_tidak_dikenal_menghasilkan_404(): void
    {
        $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/99999');

        $response->assertStatus(404);
    }

    /**
     * Bukan angka ditolak oleh rute, bukan masuk ke controller.
     */
    public function test_id_bukan_angka_menghasilkan_404(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/lowongan/bukan-angka');

        $response->assertStatus(404);
    }

    /**
     * Tamu yang membuka detail dialihkan ke halaman Masuk.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $loker = $this->buatLoker();

        $response = $this->get('/lowongan/'.$loker->id);

        $response->assertRedirect('/login');
    }

    /**
     * Kartu di dasbor menautkan pengguna ke halaman detail (alur F2 → F3).
     */
    public function test_kartu_dasbor_menghubungkan_ke_detail(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $response->assertSee('href="'.route('lowongan.show', $loker->id).'"', false);
    }

    /**
     * Buat satu loker tayang milik admin untuk keperluan uji.
     *
     * @param  array<string, mixed>  $ubah
     */
    private function buatLoker(array $ubah = []): Job
    {
        $admin = User::factory()->create([
            'name' => 'Admin LokerDekat',
            'email' => 'admin@uji.test',
            'role' => 'admin',
        ]);

        return Job::create(array_merge([
            'company_name' => 'PT Uji Peta Sejahtera',
            'address' => 'Jl. Contoh No. 7, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => $admin->id,
        ], $ubah));
    }
}
