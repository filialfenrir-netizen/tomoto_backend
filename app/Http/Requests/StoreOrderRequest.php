<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // dicek lewat middleware auth:sanctum di route
    }

    public function rules(): array
    {
        return [
            // 'type' pakai alias aman ('car'/'gr_car'), BUKAN nama class PHP langsung.
            // Controller yang akan memetakan alias ini ke class Model sebenarnya,
            // supaya klien tidak bisa menyuntikkan class name sembarangan.
            'type' => ['required', 'in:car,gr_car'],
            'car_id' => ['required', 'integer'],

            // Dulu isian bebas, sekarang dropdown pilihan tetap -> wajib dipilih.
            'warna' => ['required', 'in:merah,hitam,silver,biru'],
            'transmisi_dipilih' => ['nullable', 'string', 'max:50'],

            // Varian mesin: daftar resminya belum ditentukan, jadi validasi
            // masih bebas (string). Begitu daftarnya ada, ganti jadi 'in:...'
            // seperti warna di atas.
            'varian_mesin' => ['nullable', 'string', 'max:100'],

            'metode_pembayaran' => ['required', 'in:transfer,credit_card,ewallet'],
        ];
    }
}
