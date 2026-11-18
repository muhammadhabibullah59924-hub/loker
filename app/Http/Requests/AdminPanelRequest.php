<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi filter daftar loker pada panel admin (T-035).
 *
 * Nilai filter yang tidak dikenal dianggap tidak ada sehingga panel tetap
 * menampilkan seluruh loker alih-alih halaman galat, mengikuti perilaku
 * filter dasbor pada DashboardRequest.
 */
class AdminPanelRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berwenang mengirim permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi kata kunci, status tayang, dan tipe loker.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'q' => ['sometimes', 'nullable', 'string', 'max:100'],
            'status' => ['sometimes', 'nullable', 'string', 'in:tayang,nonaktif'],
            'tipe' => ['sometimes', 'nullable', 'string', 'in:Full-Time,Part-Time,Contract,Internship'],
        ];
    }

    /**
     * Buang nilai filter yang kosong atau tidak dikenal sebelum validasi.
     */
    protected function prepareForValidation(): void
    {
        $kataKunci = $this->input('q');

        if (! is_string($kataKunci)) {
            $kataKunci = '';
        }

        $kataKunci = trim($kataKunci);

        $this->merge(['q' => $kataKunci === '' ? null : $kataKunci]);

        $status = $this->input('status');

        if (! is_string($status) || ! in_array($status, ['tayang', 'nonaktif'], true)) {
            $this->merge(['status' => null]);
        }

        $tipe = $this->input('tipe');

        if (! is_string($tipe) || ! in_array($tipe, ['Full-Time', 'Part-Time', 'Contract', 'Internship'], true)) {
            $this->merge(['tipe' => null]);
        }
    }
}
