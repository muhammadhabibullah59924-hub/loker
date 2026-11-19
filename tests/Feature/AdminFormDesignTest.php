<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFormDesignTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /**
     * T-041: form tambah loker dikelompokkan menjadi tiga bagian dan panduan.
     */
    public function test_form_tambah_loker_mengelompokkan_isian(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/loker/create');

        $response->assertStatus(200);
        $response->assertSee('Formulir loker');
        $response->assertSee('Informasi loker');
        $response->assertSee('Jadwal loker');
        $response->assertSee('Lokasi & peta', false);
        $response->assertSee('Sebelum menyimpan');
        $response->assertSee('id="peta-pilih"', false);
    }

    /**
     * T-041: form ubah loker tetap menampilkan nilai yang sudah tersimpan.
     */
    public function test_form_ubah_loker_menampilkan_nilai_lama(): void
    {
        $loker = Job::create([
            'company_name' => 'PT Form Uji Lama',
            'address' => 'Jl. Form Uji No. 3',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi untuk uji form.',
            'requirements' => 'Syarat untuk uji form.',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/loker/'.$loker->id.'/edit');

        $response->assertStatus(200);
        $response->assertSee('Ubah loker');
        $response->assertSee('PT Form Uji Lama');
        $response->assertSee('Jl. Form Uji No. 3');
    }
}
