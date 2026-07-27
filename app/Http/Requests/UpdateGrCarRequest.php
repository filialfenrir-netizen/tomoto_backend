<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGrCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_model' => ['sometimes', 'required', 'string', 'max:150'],
            'tag' => ['nullable', 'string', 'max:50'],
            'deskripsi_singkat' => ['nullable', 'string'],

            'harga' => ['sometimes', 'required', 'integer', 'min:0'],
            'stok' => ['sometimes', 'required', 'integer', 'min:0'],

            'horsepower' => ['nullable', 'integer', 'min:0'],
            'drivetrain' => ['nullable', 'string', 'max:50'],
            'spec_chip_1' => ['nullable', 'string', 'max:50'],
            'spec_chip_2' => ['nullable', 'string', 'max:50'],

            'tipe_mesin' => ['nullable', 'string', 'max:150'],
            'kapasitas_mesin' => ['nullable', 'string', 'max:50'],
            'tenaga_maksimum' => ['nullable', 'string', 'max:100'],
            'torsi_maksimum' => ['nullable', 'string', 'max:100'],
            'transmisi' => ['nullable', 'string', 'max:50'],
            'suspensi' => ['nullable', 'string', 'max:150'],
            'akselerasi' => ['nullable', 'string', 'max:100'],

            'gambar_utama' => ['nullable', 'string'],
            'galeri' => ['nullable', 'array'],
            'galeri.*' => ['string'],
        ];
    }
}
