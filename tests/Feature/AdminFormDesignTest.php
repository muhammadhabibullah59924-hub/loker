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

    /**
     * T-046: form loker menyediakan isian tipe, kebutuhan, dan premium.
     */
    public function test_form_loker_menampilkan_isian_tipe_dan_premium(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/loker/create');

        $response->assertStatus(200);
        $response->assertSee('Tipe & status', false);
        $response->assertSee('Tipe loker');
        $response->assertSee('Jumlah orang dibutuhkan');
        $response->assertSee('Tandai sebagai Premium Employer');
        $response->assertSee('id="job_type"', false);
        $response->assertSee('id="is_premium"', false);
    }

    /**
     * T-046: admin bisa menyimpan tipe, jumlah kebutuhan, dan status premium.
     */
    public function test_admin_bisa_menyimpan_tipe_dan_status_premium(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/loker', array_merge($this->dataLoker(), [
            'company_name' => 'PT Tipe Premium',
            'job_type' => 'Part-Time',
            'positions_needed' => 4,
            'is_premium' => '1',
        ]));

        $response->assertRedirect(route('admin.panel'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jobs', [
            'company_name' => 'PT Tipe Premium',
            'job_type' => 'Part-Time',
            'positions_needed' => 4,
            'is_premium' => true,
        ]);
    }

    /**
     * T-046: checkbox premium yang tidak dicentang mematikan status premium saat ubah.
     */
    public function test_status_premium_bisa_dimatikan_saat_ubah(): void
    {
        $loker = Job::create(array_merge($this->dataLoker(), [
            'company_name' => 'PT Premium Lama',
            'is_premium' => true,
        ]));

        $response = $this->actingAs($this->admin)->put('/admin/loker/'.$loker->id, $this->dataLoker());

        $response->assertRedirect(route('admin.panel'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jobs', [
            'id' => $loker->id,
            'is_premium' => false,
        ]);
    }

    /**
     * Isian valid untuk form loker pada keperluan uji.
     *
     * @return array<string, mixed>
     */
    private function dataLoker(): array
    {
        return [
            'company_name' => 'PT Form Uji',
            'address' => 'Jl. Form Uji No. 1',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi untuk uji form.',
            'requirements' => 'Syarat untuk uji form.',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ];
    }
}
