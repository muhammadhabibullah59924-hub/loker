<?php

namespace Tests\Feature;

use App\Models\Favorite;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    private User $pengguna;

    private User $penggunaLain;

    private Job $loker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pengguna = User::factory()->create([
            'name' => 'Budi Pencari Kerja',
            'email' => 'budi@uji.test',
            'role' => 'user',
        ]);

        $this->penggunaLain = User::factory()->create([
            'name' => 'Citra Pencari Kerja',
            'email' => 'citra@uji.test',
            'role' => 'user',
        ]);

        $this->loker = Job::create([
            'company_name' => 'PT Uji Favorit Sejahtera',
            'address' => 'Jl. Uji No. 7, Mayangan',
            'division' => 'Produksi',
            'city' => 'Kota Probolinggo',
            'description' => 'Deskripsi loker untuk keperluan uji.',
            'requirements' => 'Syarat loker untuk keperluan uji.',
            'deadline' => '2026-11-30',
            'latitude' => -7.7145,
            'longitude' => 113.2146,
            'is_active' => true,
            'posted_by' => User::factory()->create(['role' => 'admin'])->id,
        ]);
    }

    /**
     * Menyimpan dan melepas favorit bekerja (F6).
     */
    public function test_menyimpan_dan_melepas_favorit_berhasil(): void
    {
        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $this->pengguna->id,
            'job_id' => $this->loker->id,
        ]);

        $this->actingAs($this->pengguna)->delete('/favorit/'.$this->loker->id);
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $this->pengguna->id,
            'job_id' => $this->loker->id,
        ]);
    }

    /**
     * Menyimpan loker yang sudah menjadi favorit tidak membuat duplikat.
     */
    public function test_favorit_bertindak_idempotent(): void
    {
        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);
        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);

        $this->assertSame(1, Favorite::where('user_id', $this->pengguna->id)->count());
    }

    /**
     * Daftar favorit bertahan setelah keluar lalu masuk kembali.
     */
    public function test_daftar_favorit_bertahan_setelah_keluar_lalu_masuk_kembali(): void
    {
        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);

        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', [
            'email' => 'budi@uji.test',
            'password' => 'password',
        ]);

        $this->get('/favorit')
            ->assertStatus(200)
            ->assertSee('PT Uji Favorit Sejahtera');
    }

    /**
     * Hanya favorit milik pengguna sendiri yang ditampilkan.
     */
    public function test_hanya_milik_pengguna_sendiri_yang_tampil(): void
    {
        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);

        $this->actingAs($this->penggunaLain)->get('/favorit')->assertStatus(200);

        $this->actingAs($this->pengguna)->get('/favorit')->assertStatus(200)->assertSee('PT Uji Favorit Sejahtera');
    }

    /**
     * Loker yang sudah tidak aktif tidak bisa disimpan sebagai favorit.
     */
    public function test_favorit_loker_tidak_aktif_ditolak(): void
    {
        $this->loker->forceFill(['is_active' => false])->save();

        $this->actingAs($this->pengguna)->post('/favorit/'.$this->loker->id);

        $this->assertSame(0, Favorite::count());
    }

    /**
     * Kartu dasbor menyediakan aksi menyimpan favorit.
     */
    public function test_kartu_dasbor_menyediakan_aksi_favorit(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/dasbor?lat=-7.7145&lng=113.2146');

        $response->assertStatus(200);
        $this->assertStringContainsString(
            'http://localhost/favorit/'.$this->loker->id,
            $response->getContent(),
        );
    }

    /**
     * Tamu dialihkan ke halaman Masuk.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $response = $this->get('/favorit');

        $response->assertRedirect('/login');
    }
}
