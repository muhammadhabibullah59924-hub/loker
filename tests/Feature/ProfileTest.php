<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $pengguna;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pengguna = User::factory()->create([
            'name' => 'Budi Pencari Kerja',
            'email' => 'budi@uji.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
        ]);
    }

    /**
     * T-043: tamu tidak bisa membuka halaman profil.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $response = $this->get('/profil');

        $response->assertRedirect('/login');
    }

    /**
     * T-043: halaman profil menampilkan data akun pengguna.
     */
    public function test_halaman_profil_menampilkan_data_akun(): void
    {
        $response = $this->actingAs($this->pengguna)->get('/profil');

        $response->assertStatus(200);
        $response->assertSee('Profil');
        $response->assertSee('Budi Pencari Kerja');
        $response->assertSee('budi@uji.test');
        $response->assertSee('Koordinat lokasimu tidak disimpan');
    }

    /**
     * T-043: nama dan email pengguna bisa diperbarui.
     */
    public function test_nama_dan_email_bisa_diperbarui(): void
    {
        $response = $this->actingAs($this->pengguna)->put('/profil', [
            'name' => 'Budi Sudah Diubah',
            'email' => 'budi.baru@uji.test',
        ]);

        $response->assertRedirect(route('profil.index'));
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $this->pengguna->id,
            'name' => 'Budi Sudah Diubah',
            'email' => 'budi.baru@uji.test',
        ]);
    }

    /**
     * T-043: kata sandi lama yang salah menolak perubahan.
     */
    public function test_kata_sandi_lama_salah_ditolak(): void
    {
        $response = $this->actingAs($this->pengguna)->put('/profil', [
            'name' => $this->pengguna->name,
            'email' => $this->pengguna->email,
            'kata_sandi_lama' => 'salah123',
            'kata_sandi_baru' => 'rahasia123',
            'kata_sandi_baru_confirmation' => 'rahasia123',
        ]);

        $response->assertSessionHasErrors('kata_sandi_lama');
        $this->assertTrue(Hash::check('password123', $this->pengguna->fresh()->password));
    }

    /**
     * T-043: kata sandi berhasil diganti bila kata sandi lama benar.
     */
    public function test_kata_sandi_bisa_diganti(): void
    {
        $response = $this->actingAs($this->pengguna)->put('/profil', [
            'name' => $this->pengguna->name,
            'email' => $this->pengguna->email,
            'kata_sandi_lama' => 'password123',
            'kata_sandi_baru' => 'rahasia123',
            'kata_sandi_baru_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('profil.index'));
        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('rahasia123', $this->pengguna->fresh()->password));
    }
}
