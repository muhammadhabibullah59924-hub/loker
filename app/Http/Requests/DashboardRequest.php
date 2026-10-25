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
     * Aturan validasi parameter lat dan lng.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'lat' => ['sometimes', 'nullable', 'numeric', 'between:-90,90'],
            'lng' => ['sometimes', 'nullable', 'numeric', 'between:-180,180'],
        ];
    }

    /**
     * Buang koordinat yang tidak lengkap atau tidak masuk akal sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $lat = $this->input('lat');
        $lng = $this->input('lng');

        $sah = is_numeric($lat) && is_numeric($lng)
            && $lat >= -90 && $lat <= 90
            && $lng >= -180 && $lng <= 180;

        if (! $sah) {
            $this->merge(['lat' => null, 'lng' => null]);
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
