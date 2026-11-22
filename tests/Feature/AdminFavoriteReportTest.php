<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFavoriteReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * T-049: admin bisa membuka halaman laporan favorit.
     */
    public function test_admin_bisa_membuka_laporan_favorit(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/favorit')
            ->assertStatus(200)
            ->assertSee('Laporan favorit');
    }

    /**
     * T-049: peringkat loker diurutkan dari jumlah favorit terbanyak.
     */
    public function test_ranking_diurutkan_dari_jumlah_favorit_terbanyak(): void
    {
        $ramai = $this->buatLoker('PT Ramai Sekali');
        $sepi = $this->buatLoker('PT Sepi Sendiri');

        $this->buatPengguna('Satu')->favoritedJobs()->attach($ramai->id);
        $this->buatPengguna('Dua')->favoritedJobs()->attach($ramai->id);
        $this->buatPengguna('Tiga')->favoritedJobs()->attach($sepi->id);

        $response = $this->actingAs($this->admin)->get('/admin/favorit');
        $response->assertStatus(200);

        $bagianPeringkat = $this->bagianAntara(
            (string) $response->getContent(),
            'id="peringkat-favorit"',
            'id="favorit-pengguna"',
        );

        $posisiRamai = strpos($bagianPeringkat, 'PT Ramai Sekali');
        $posisiSepi = strpos($bagianPeringkat, 'PT Sepi Sendiri');

        $this->assertNotFalse($posisiRamai);
        $this->assertNotFalse($posisiSepi);
        $this->assertLessThan($posisiSepi, $posisiRamai, 'Loker dengan favorit terbanyak harus tampil lebih dulu.');
    }

    /**
     * T-049: rincian per pengguna menampilkan nama, email, dan loker yang disimpan.
     */
    public function test_rincian_per_pengguna_menampilkan_nama_email_dan_loker(): void
    {
        $loker = $this->buatLoker('PT Minat Pencari');
        $pengguna = $this->buatPengguna('Sinta');
        $pengguna->favoritedJobs()->attach($loker->id);

        $this->actingAs($this->admin)
            ->get('/admin/favorit')
            ->assertStatus(200)
            ->assertSee('Sinta Pencari')
            ->assertSee('sinta@example.test')
            ->assertSee('PT Minat Pencari');
    }

    /**
     * T-049: loker nonaktif tetap dihitung dalam laporan pengelola.
     */
    public function test_loker_nonaktif_tetap_dihitung(): void
    {
        $loker = $this->buatLoker('PT Sudah Nonaktif', ['is_active' => false]);
        $this->buatPengguna('Satu')->favoritedJobs()->attach($loker->id);

        $this->actingAs($this->admin)
            ->get('/admin/favorit')
            ->assertStatus(200)
            ->assertSee('PT Sudah Nonaktif')
            ->assertSee('Nonaktif');
    }

    /**
     * T-049: laporan kosong memberitahu langkah berikutnya kepada admin.
     */
    public function test_laporan_kosong_menampilkan_pesan(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/favorit')
            ->assertStatus(200)
            ->assertSee('Belum ada loker yang disimpan sebagai favorit.')
            ->assertSee('Belum ada pengguna yang menyimpan favorit.');
    }

    /**
     * T-049: pengguna biasa tidak bisa membuka laporan admin.
     */
    public function test_pengguna_biasa_dialihkan_ke_dasbor(): void
    {
        $this->actingAs($this->buatPengguna('Biasa'))
            ->get('/admin/favorit')
            ->assertRedirect('/dasbor')
            ->assertSessionHas('peringatan');
    }

    /**
     * T-049: tamu dialihkan ke halaman masuk.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $this->get('/admin/favorit')->assertRedirect('/login');
    }

    /**
     * Ambil potongan HTML di antara dua penanda agar urutan bisa diuji.
     */
    private function bagianAntara(string $html, string $awal, string $akhir): string
    {
        $posisiAwal = strpos($html, $awal);
        $posisiAkhir = strpos($html, $akhir);

        $this->assertNotFalse($posisiAwal);
        $this->assertNotFalse($posisiAkhir);

        return substr($html, $posisiAwal, $posisiAkhir - $posisiAwal);
    }

    /**
     * Buat satu loker untuk keperluan uji laporan favorit.
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

    private function buatPengguna(string $nama): User
    {
        return User::factory()->create([
            'name' => $nama.' Pencari',
            'email' => strtolower($nama).'@example.test',
            'role' => 'user',
        ]);
    }
}
