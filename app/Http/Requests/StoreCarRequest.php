<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware admin di route
    }

    public function rules(): array
    {
        return [
            'nama_model' => ['required', 'string', 'max:150'],
            'kategori' => ['required', 'in:hatchback,sedan,suv,mpv'],
            'tag' => ['nullable', 'string', 'max:50'],
            'deskripsi_singkat' => ['nullable', 'string'],

            'harga' => ['required', 'integer', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],

            'horsepower' => ['nullable', 'integer', 'min:0'],
            'estimasi_konsumsi' => ['nullable', 'string', 'max:50'],
            'drivetrain' => ['nullable', 'string', 'max:20'],

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
