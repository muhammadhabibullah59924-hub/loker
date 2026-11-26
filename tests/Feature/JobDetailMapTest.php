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
     * T-056: pustaka peta disimpan lokal dengan versi yang dipin (ADR-013).
     */
    public function test_pustaka_peta_dimuat_dari_aset_lokal_versi_1_9_4(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertSee('vendor/leaflet/leaflet.js', false);
        $response->assertSee('vendor/leaflet/leaflet.css', false);
        $response->assertDontSee('cdn.jsdelivr.net/npm/leaflet', false);
    }

    /**
     * T-056: tautan lompat tersedia karena di HP peta berada di bawah konten.
     */
    public function test_detail_menyediakan_tautan_lompat_ke_peta(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Lihat peta lokasi');
        $response->assertSee('href="#peta-loker"', false);
    }

    /**
     * T-056: kegagalan peta memberi pesan bahasa Indonesia dan tautan OSM.
     */
    public function test_detail_menampilkan_pesan_gagal_dan_tautan_openstreetmap(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('id="peta-gagal"', false);
        $response->assertSee('Peta tidak dapat ditampilkan di perangkat ini', false);
        $response->assertSee('Buka lokasi di OpenStreetMap');
        $response->assertSee('mlat='.$loker->latitude, false);
        $response->assertSee('mlng='.$loker->longitude, false);
    }

    /**
     * T-056: skrip menangani pustaka gagal, ubin gagal, dan perubahan ukuran layar.
     */
    public function test_skrip_peta_memiliki_penanganan_gagal_dan_penyegaran_ukuran(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee("typeof L === 'undefined'", false);
        $response->assertSee("classList.add('job-map--gagal')", false);
        $response->assertSee("on('tileerror'", false);
        $response->assertSee('invalidateSize()', false);
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
     * T-038: detail menampilkan ringkasan loker di sidebar.
     */
    public function test_detail_menampilkan_ringkasan_loker(): void
    {
        $loker = $this->buatLoker(['job_type' => 'Full-Time', 'positions_needed' => 2]);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Ringkasan loker');
        $response->assertSee('Kebutuhan');
        $response->assertSee('2 orang');
        $response->assertSee('30 November 2026');
    }

    /**
     * T-038: pengguna masuk bisa menyimpan favorit langsung dari detail.
     */
    public function test_detail_menampilkan_tombol_simpan_favorit(): void
    {
        $loker = $this->buatLoker();

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Simpan favorit');
        $response->assertSee('action="'.route('favorit.store', $loker->id).'"', false);
    }

    /**
     * T-038: loker yang sudah difavoritkan menampilkan tautan ke halaman favorit.
     */
    public function test_detail_menandai_loker_yang_sudah_favorit(): void
    {
        $loker = $this->buatLoker();

        $this->pengguna->favoritedJobs()->attach($loker->id);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Lihat di favorit');
        $response->assertSee('sudah tersimpan di favoritmu');
    }

    /**
     * T-044: detail menampilkan loker lain dengan divisi atau kota yang sama.
     */
    public function test_detail_menampilkan_loker_serupa(): void
    {
        $loker = $this->buatLoker(['division' => 'Produksi', 'city' => 'Kota Probolinggo']);
        $this->buatLoker(['company_name' => 'PT Serupa Divisi', 'division' => 'Produksi', 'city' => 'Kota Pasuruan']);
        $this->buatLoker(['company_name' => 'PT Serupa Kota', 'division' => 'IT', 'city' => 'Kota Probolinggo']);
        $this->buatLoker(['company_name' => 'PT Tidak Serupa', 'division' => 'Keuangan', 'city' => 'Kota Malang']);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertSee('Loker serupa');
        $response->assertSee('PT Serupa Divisi');
        $response->assertSee('PT Serupa Kota');
        $response->assertDontSee('PT Tidak Serupa');
    }

    /**
     * T-044: loker nonaktif tidak boleh muncul di daftar loker serupa.
     */
    public function test_loker_nonaktif_tidak_muncul_di_loker_serupa(): void
    {
        $loker = $this->buatLoker();
        $this->buatLoker(['company_name' => 'PT Nonaktif Serupa', 'is_active' => false]);

        $response = $this->actingAs($this->pengguna)->get('/lowongan/'.$loker->id);

        $response->assertStatus(200);
        $response->assertDontSee('PT Nonaktif Serupa');
        $response->assertDontSee('Loker serupa');
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
