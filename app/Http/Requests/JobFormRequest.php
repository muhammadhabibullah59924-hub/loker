<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form tambah dan ubah loker (F4).
 *
 * Kendali R2 di docs/PRD.md mewajibkan nama PT, alamat, divisi, dan titik
 * koordinat; nilai kosong harus ditolak sebelum tersimpan ke basis data.
 */
class JobFormRequest extends FormRequest
{
    /**
     * Hanya peran admin yang boleh mengirim form ini; rute pun sudah
     * dijaga middleware `admin` (pemeriksaan ganda di sisi server).
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Aturan wajib untuk setiap isian form loker.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'division' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'requirements' => ['required', 'string', 'max:2000'],
            'deadline' => ['nullable', 'date'],
            'opens_at' => ['nullable', 'date'],
            'closes_at' => ['nullable', 'date'],
            'job_type' => ['sometimes', 'nullable', 'string', 'in:Full-Time,Part-Time,Contract,Internship'],
            'positions_needed' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:99'],
            'is_premium' => ['sometimes', 'nullable', 'boolean'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Kosongkan pilihan tipe dan jumlah kebutuhan sebelum divalidasi.
     */
    protected function prepareForValidation(): void
    {
        if ($this->input('job_type') === '') {
            $this->merge(['job_type' => null]);
        }

        if ($this->input('positions_needed') === '') {
            $this->merge(['positions_needed' => null]);
        }
    }

    /**
     * Pesan galat berbahasa Indonesia sesuai aturan di atas.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'company_name.required' => 'Nama PT wajib diisi.',
            'company_name.string' => 'Nama PT harus berupa teks.',
            'company_name.max' => 'Nama PT maksimal 255 karakter.',
            'address.required' => 'Alamat wajib diisi.',
            'address.string' => 'Alamat harus berupa teks.',
            'address.max' => 'Alamat maksimal 255 karakter.',
            'division.required' => 'Divisi wajib diisi.',
            'division.string' => 'Divisi harus berupa teks.',
            'division.max' => 'Divisi maksimal 255 karakter.',
            'city.required' => 'Kota wajib diisi.',
            'city.string' => 'Kota harus berupa teks.',
            'city.max' => 'Kota maksimal 255 karakter.',
            'description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'description.string' => 'Deskripsi pekerjaan harus berupa teks.',
            'description.max' => 'Deskripsi pekerjaan maksimal 2000 karakter.',
            'requirements.required' => 'Syarat wajib diisi.',
            'requirements.string' => 'Syarat harus berupa teks.',
            'requirements.max' => 'Syarat maksimal 2000 karakter.',
            'deadline.date' => 'Batas kirim lamaran harus berupa tanggal, misalnya 2026-11-30.',
            'opens_at.date' => 'Tanggal buka harus berupa tanggal yang valid.',
            'closes_at.date' => 'Tanggal tutup harus berupa tanggal yang valid.',
            'job_type.in' => 'Tipe loker harus dipilih dari daftar yang tersedia.',
            'positions_needed.integer' => 'Jumlah kebutuhan harus berupa angka, misalnya 2.',
            'positions_needed.min' => 'Jumlah kebutuhan minimal 1 orang.',
            'positions_needed.max' => 'Jumlah kebutuhan maksimal 99 orang. Coba perkecil.',
            'latitude.required' => 'Titik lintang wajib diisi agar loker tampil di dasbor.',
            'latitude.numeric' => 'Titik lintang harus berupa angka, misalnya -7.7145.',
            'latitude.between' => 'Titik lintang harus antara -90 sampai 90.',
            'longitude.required' => 'Titik bujur wajib diisi agar loker tampil di dasbor.',
            'longitude.numeric' => 'Titik bujur harus berupa angka, misalnya 113.2146.',
            'longitude.between' => 'Titik bujur harus antara -180 sampai 180.',
        ];
    }

    /**
     * Nama isian yang dipakai di dalam pesan galat.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'company_name' => 'nama PT',
            'address' => 'alamat',
            'division' => 'divisi',
            'city' => 'kota',
            'description' => 'deskripsi pekerjaan',
            'requirements' => 'syarat',
            'deadline' => 'batas kirim lamaran',
            'opens_at' => 'tanggal buka',
            'closes_at' => 'tanggal tutup',
            'job_type' => 'tipe loker',
            'positions_needed' => 'jumlah kebutuhan',
            'is_premium' => 'status premium',
            'latitude' => 'titik lintang',
            'longitude' => 'titik bujur',
        ];
    }
}
