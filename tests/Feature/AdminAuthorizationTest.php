<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Tamu yang membuka /admin dialihkan ke halaman Masuk.
     */
    public function test_tamu_dialihkan_ke_halaman_masuk(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
        $response->assertDontSee('Panel admin');
    }

    /**
     * Pengguna biasa ditolak dan dialihkan keluar dari /admin (F4).
     */
    public function test_pengguna_biasa_ditolak_dan_dialihkan(): void
    {
        $pengguna = User::factory()->create([
            'name' => 'Budi Pencari',
            'email' => 'budi@uji.test',
            'role' => 'user',
        ]);

        $response = $this->actingAs($pengguna)->get('/admin');

        $response->assertRedirect('/dasbor');
        $response->assertSessionHas('peringatan');
        $response->assertDontSee('Panel admin');
        $this->assertStringContainsString(
            'pengelola data loker',
            session('peringatan'),
        );
    }

    /**
     * Admin dapat membuka panel dan melihat ringkasan data.
     */
    public function test_admin_dapat_membuka_panel(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin LokerDekat',
            'email' => 'admin@uji.test',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Panel admin');
        $response->assertSee('Loker tayang');
    }

    /**
     * Tautan Panel Admin tidak ditampilkan kepada pengguna biasa.
     */
    public function test_tautan_panel_admin_hanya_untuk_admin(): void
    {
        $pengguna = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($pengguna)
            ->get('/dasbor')
            ->assertDontSee(route('admin.panel'), false);

        $this->actingAs($admin)
            ->get('/dasbor')
            ->assertSee(route('admin.panel'), false);
    }
}
