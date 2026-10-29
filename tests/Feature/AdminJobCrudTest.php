<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminJobCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $pengguna;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin LokerDekat',
            'email' => 'admin@uji.test',
            'role' => 'admin',
        ]);

        $this->pengguna = User::factory()->create([
            'name' => 'Budi Pencari Kerja',
            'email' => 'budi@uji.test',
            'role' => 'user',
        ]);
    }

    /**
     * R2: form menolak nama PT, alamat, dan divisi yang kosong.
     */
    public function test_form_menolak_nama_pt_alamat_dan_divisi_kosong(): void
    {
        $data = $this->dataValid();
        unset($data['company_name'], $data['address'], $data['division']);

        $response = $this->actingAs($this->admin)->post('/admin/loker', $data);

        $response->assertSessionHasErrors(['company_name', 'address', 'division']);
        $this->assertSame(0, Job::count());
    }

    /**
     * R2: loker tanpa titik koordinat tidak boleh tersimpan.
     */
    public function test_form_menolak_koordinat_kosong(): void
    {
        $data = $this->dataValid();
        unset($data['latitude'], $data['longitude']);

        $response = $this->actingAs($this->admin)->post('/admin/loker', $data);

        $response->assertSessionHasErrors(['latitude', 'longitude']);
        $this->assertSame(0, Job::count());
    }

    /**
     * Pesan galat memakai bahasa Indonesia dan menyebut langkah berikutnya.
     */
    public function test_pesan_galat_validasi_berbahasa_indonesia(): void
    {
        $data = $this->dataValid();
        unset($data['latitude']);

        $this->actingAs($this->admin)
            ->post('/admin/loker', $data)
            ->assertSessionHasErrors('latitude');

        $this->assertSame(
            'Titik lintang wajib diisi agar loker tampil di dasbor.',
            session('errors')->first('latitude'),
        );
    }

    /**
     * F4: loker baru yang disimpan admin langsung muncul di dasbor.
     */
    public function test_admin_menambah_loker_baru(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/loker', $this->dataValid());

        $response->assertRedirect(route('admin.panel'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('jobs', [
            'company_name' => 'PT Uji Tambah Baru',
            'is_active' => true,
            'posted_by' => $this->admin->id,
        ]);

        $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146')
            ->assertSee('href="'.route('lowongan.show', Job::firstOrFail()->id).'"', false);
    }

    /**
     * F4: perubahan data tersimpan dan tercermin di halaman detail.
     */
    public function test_admin_mengubah_loker(): void
    {
        $loker = $this->simpanLoker();

        $response = $this->actingAs($this->admin)->put('/admin/loker/'.$loker->id, array_merge($this->dataValid(), [
            'company_name' => 'PT Nama Sudah Diubah',
            'division' => 'Pengadaan',
        ]));

        $response->assertRedirect(route('admin.panel'));

        $this->assertDatabaseHas('jobs', [
            'id' => $loker->id,
            'company_name' => 'PT Nama Sudah Diubah',
            'division' => 'Pengadaan',
        ]);

        $this->actingAs($this->pengguna)
            ->get('/lowongan/'.$loker->id)
            ->assertSee('<h1 class="mb-2">PT Nama Sudah Diubah</h1>', false);
    }

    /**
     * F4/R2: menonaktifkan menyembunyikan loker dari dasbor tanpa menghapus data.
     */
    public function test_menonaktifkan_menyembunyikan_tanpa_menghapus(): void
    {
        $loker = $this->simpanLoker();

        $response = $this->actingAs($this->admin)->patch('/admin/loker/'.$loker->id.'/nonaktif');

        $response->assertRedirect(route('admin.panel'));

        $this->assertDatabaseHas('jobs', ['id' => $loker->id, 'is_active' => false]);
        $this->assertSame(1, Job::count());

        $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146')
            ->assertSee('Belum ada loker yang tayang saat ini.')
            ->assertDontSee('href="'.route('lowongan.show', $loker->id).'"', false);

        $this->actingAs($this->pengguna)
            ->get('/lowongan/'.$loker->id)
            ->assertStatus(404);
    }

    /**
     * F4: loker bisa ditayangkan kembali setelah dinonaktifkan.
     */
    public function test_mengaktifkan_kembali_loker(): void
    {
        $loker = $this->simpanLoker();

        $this->actingAs($this->admin)->patch('/admin/loker/'.$loker->id.'/nonaktif');
        $this->actingAs($this->admin)->patch('/admin/loker/'.$loker->id.'/nonaktif');

        $this->assertDatabaseHas('jobs', ['id' => $loker->id, 'is_active' => true]);

        $this->actingAs($this->pengguna)
            ->get('/dasbor?lat=-7.7145&lng=113.2146')
            ->assertSee('href="'.route('lowongan.show', $loker->id).'"', false);
    }

    /**
     * F4: menghapus loker benar-benar menghapus baris datanya.
     */
    public function test_admin_menghapus_loker(): void
    {
        $loker = $this->simpanLoker();

        $response = $this->actingAs($this->admin)->delete('/admin/loker/'.$loker->id);

        $response->assertRedirect(route('admin.panel'));
        $this->assertDatabaseMissing('jobs', ['id' => $loker->id]);
    }

    /**
     * Pengguna biasa tidak bisa membuka form maupun menyimpan data admin (F4).
     */
    public function test_pengguna_biasa_ditolak_dari_rute_admin(): void
    {
        $responseBuka = $this->actingAs($this->pengguna)->get('/admin/loker/create');
        $responseSimpan = $this->actingAs($this->pengguna)->post('/admin/loker', $this->dataValid());

        $responseBuka->assertRedirect('/dasbor');
        $responseSimpan->assertRedirect('/dasbor');
        $this->assertSame(0, Job::count());
    }

    /**
     * Tamu dialihkan ke halaman Masuk.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $response = $this->get('/admin/loker/create');

        $response->assertRedirect('/login');
    }

    /**
     * Id bukan angka ditolak rute, bukan diambil modelnya.
     */
    public function test_id_bukan_angka_menghasilkan_404(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/loker/bukan-angka/edit');

        $response->assertStatus(404);
    }

    /**
     * Isian form yang lengkap dan sah.
     *
     * @return array<string, mixed>
     */
    private function dataValid(): array
    {
        return [
            'company_name' => 'PT Uji Tambah Baru',
            'address' => 'Jl. Uji No. 1, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi singkat untuk keperluan uji.',
            'requirements' => 'Syarat singkat untuk keperluan uji.',
            'deadline' => '2026-12-31',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
        ];
    }

    /**
     * Simpan satu loker lewat rute admin agar kondisinya sama dengan produksi.
     */
    private function simpanLoker(): Job
    {
        $this->actingAs($this->admin)->post('/admin/loker', $this->dataValid());

        return Job::firstOrFail();
    }
}
