<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware auth:sanctum di route
    }

    public function rules(): array
    {
        return [
            // Email TIDAK termasuk di sini secara sengaja - sesuai UI "Email tidak dapat diubah"
            // Semua field di sini nullable supaya profil bisa disimpan bertahap
            // (isi nama dulu, alamat menyusul, dst). Kewajiban "alamat harus
            // ada" dicek di titik checkout (OrderController::store), bukan di sini.
            'nama_lengkap' => ['nullable', 'string', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'alamat' => ['nullable', 'string'],
            'tanggal_lahir' => ['nullable', 'date', 'before:today'],
            'foto_profil' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak boleh di masa depan.',
        ];
    }
}
