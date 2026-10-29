<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Form Daftar menyimpan akun baru dengan peran user dan langsung masuk sesi.
     */
    public function test_form_daftar_menyimpan_akun(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@contoh.test',
            'password' => 'katasandi123',
            'password_confirmation' => 'katasandi123',
        ]);

        $response->assertRedirect(route('dasbor'));
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'siti@contoh.test',
            'name' => 'Siti Aminah',
            'role' => 'user',
        ]);

        // Kata sandi tersimpan sebagai hash, bukan teks polos.
        $user = User::where('email', 'siti@contoh.test')->firstOrFail();
        $this->assertTrue(Hash::check('katasandi123', $user->password));
        $this->assertNotSame('katasandi123', $user->password);
    }

    /**
     * Form Daftar menolak email yang sudah terpakai.
     */
    public function test_form_daftar_menolak_email_sudah_terdaftar(): void
    {
        User::factory()->create(['email' => 'ada@contoh.test']);

        $response = $this->post('/register', [
            'name' => 'Pengguna Baru',
            'email' => 'ada@contoh.test',
            'password' => 'katasandi123',
            'password_confirmation' => 'katasandi123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame(1, User::count());
    }

    /**
     * Password salah menampilkan pesan galat umum dan tidak membuka sesi.
     */
    public function test_password_salah_menampilkan_pesan_galat(): void
    {
        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'password' => 'katasandi123',
        ]);

        $response = $this->post('/login', [
            'email' => 'budi@contoh.test',
            'password' => 'salah-banget',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString(
            'belum cocok',
            session('errors')->first('email'),
        );
        $this->assertGuest();
    }

    /**
     * Masuk dengan data benar membuka sesi dan mengarahkan ke halaman depan.
     */
    public function test_masuk_dengan_data_benar_membuka_sesi(): void
    {
        User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi@contoh.test',
            'password' => 'katasandi123',
        ]);

        $response = $this->post('/login', [
            'email' => 'budi@contoh.test',
            'password' => 'katasandi123',
        ]);

        $response->assertRedirect(route('dasbor'));
        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    /**
     * Setelah masuk, nama pengguna tampil di navbar.
     */
    public function test_nama_tampil_di_navbar_setelah_masuk(): void
    {
        $user = User::factory()->create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@contoh.test',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dewi Lestari');
        $response->assertSee('Keluar');
    }

    /**
     * Menekan Keluar mengakhiri sesi dan kembali ke keadaan tamu.
     */
    public function test_keluar_mengakhiri_sesi(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest();
    }

    /**
     * Pengguna yang sudah masuk tidak melihat form Masuk lagi, melainkan dasbor.
     */
    public function test_pengguna_masuk_dialihkan_dari_form_masuk(): void
    {
        $this->actingAs(User::factory()->create());

        $response = $this->get('/login');

        $response->assertRedirect('/dasbor');
    }
}
