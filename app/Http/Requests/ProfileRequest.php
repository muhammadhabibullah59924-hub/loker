<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi pembaruan profil dan kata sandi pengguna (T-043).
 *
 * Kata sandi baru hanya diwajibkan bila pengguna memang mengisinya, dan
 * kata sandi lama harus cocok supaya perubahan tidak bisa dilakukan orang
 * yang kebetulan memakai perangkat yang sudah masuk.
 */
class ProfileRequest extends FormRequest
{
    /**
     * Hanya pengguna yang sudah masuk yang boleh memperbarui profilnya.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi nama, email, dan kata sandi.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'kata_sandi_lama' => ['required_with:kata_sandi_baru', 'current_password'],
            'kata_sandi_baru' => ['nullable', 'string', 'min:8', 'confirmed'],
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
            'name.required' => 'Nama wajib diisi supaya profilmu jelas.',
            'name.max' => 'Nama maksimal 255 karakter. Coba singkatkan.',
            'email.required' => 'Email wajib diisi supaya kamu bisa masuk lagi.',
            'email.email' => 'Format email belum benar. Contoh: namamu@email.com.',
            'email.unique' => 'Email ini sudah dipakai akun lain. Coba alamat email lain.',
            'kata_sandi_lama.required_with' => 'Isi kata sandi lama dulu sebelum menggantinya.',
            'kata_sandi_lama.current_password' => 'Kata sandi lama belum benar. Coba lagi atau biarkan kolom kata sandi kosong.',
            'kata_sandi_baru.min' => 'Kata sandi baru minimal 8 karakter. Coba tambahkan angka atau huruf.',
            'kata_sandi_baru.confirmed' => 'Ulangi kata sandi baru belum sama. Coba ketik ulang.',
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
            'kata_sandi_lama' => 'kata sandi lama',
            'kata_sandi_baru' => 'kata sandi baru',
        ];
    }
}
