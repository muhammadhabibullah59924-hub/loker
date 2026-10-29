<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi parameter lokasi pengguna pada dasbor (F2).
 *
 * Koordinat hanya boleh datang dari browser pengguna; nilai yang tidak
 * berupa angka, di luar rentang, atau tidak lengkap dianggap tidak ada
 * sehingga dasbor jatuh ke urutan default, bukan menampilkan halaman galat.
 */
class DashboardRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang mengirim permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi parameter lat, lng, kata kunci, dan radius pada dasbor.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'lat' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
            'q' => ['sometimes', 'nullable', 'string', 'max:100'],
            'radius' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:1000'],
        ];
    }

    /**
     * Buang parameter yang tidak lengkap atau tidak masuk akal sebelum validasi,
     * supaya dasbor tetap memakai urutan default alih-alih menampilkan halaman galat.
     */
    protected function prepareForValidation(): void
    {
        $lat = $this->input('lat');
        $lng = $this->input('lng');

        $sah = is_numeric($lat) && is_numeric($lng)
            && (float) $lat >= -90 && (float) $lat <= 90
            && (float) $lng >= -180 && (float) $lng <= 180;

        if (! $sah) {
            $this->merge(['lat' => null, 'lng' => null]);
        }

        // Kata kunci: dianggap tidak ada bila bukan teks atau hanya spasi.
        $kataKunci = $this->input('q');

        if (! is_string($kataKunci)) {
            $kataKunci = '';
        }

        $kataKunci = trim($kataKunci);

        if ($kataKunci === '') {
            $this->merge(['q' => null]);
        } else {
            $this->merge(['q' => mb_strtolower($kataKunci)]);
        }

        // Radius: bila bukan angka atau di luar rentang, dianggap tanpa filter.
        $radius = $this->input('radius');

        if ($radius === null || ! is_numeric($radius) || (int) $radius < 1 || (int) $radius > 1000) {
            $this->merge(['radius' => null]);
        }
    }

    /**
     * Koordinat pengguna bila valid, atau null bila memakai lokasi default.
     *
     * @return array{lat: float, lng: float}|null
     */
    public function lokasiPengguna(): ?array
    {
        if ($this->input('lat') === null || $this->input('lng') === null) {
            return null;
        }

        return [
            'lat' => (float) $this->input('lat'),
            'lng' => (float) $this->input('lng'),
        ];
    }
}
