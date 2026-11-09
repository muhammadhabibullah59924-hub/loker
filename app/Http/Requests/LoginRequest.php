<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang mengirim permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi form Masuk.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan galat berbahasa Indonesia yang menyebut langkah berikutnya.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi supaya kamu bisa masuk.',
            'email.email' => 'Format email belum benar. Contoh: namamu@email.com.',
            'password.required' => 'Kata sandi wajib diisi supaya kamu bisa masuk.',
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
            'email' => 'email',
            'password' => 'kata sandi',
        ];
    }
}
