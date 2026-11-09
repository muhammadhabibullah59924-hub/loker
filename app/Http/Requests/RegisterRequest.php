<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang mengirim permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi pendaftaran akun baru.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Pesan galat berbahasa Indonesia yang selalu menyebut langkah berikutnya.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi supaya bisa tampil di navbar setelah kamu masuk.',
            'name.max' => 'Nama terlalu panjang, gunakan maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi supaya akunmu bisa dibuat.',
            'email.email' => 'Format email belum benar. Contoh: namamu@email.com.',
            'email.max' => 'Email terlalu panjang, periksa lagi isinya.',
            'email.unique' => 'Email ini sudah terdaftar. Coba tombol Masuk atau gunakan email lain.',
            'password.required' => 'Kata sandi wajib diisi supaya akunmu terlindungi.',
            'password.min' => 'Kata sandi minimal 8 karakter. Tambahkan beberapa huruf lagi.',
            'password.confirmed' => 'Konfirmasi kata sandi belum sama. Ketik ulang di kedua kolom.',
        ];
    }

    /**
     * Nama kolom untuk pesan galat.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'password' => 'kata sandi',
        ];
    }
}
