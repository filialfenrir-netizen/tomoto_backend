<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // carable bisa berupa instance Car atau GrCar (polymorphic).
        // Kita tampilkan ringkas saja - cukup untuk kartu ringkasan order,
        // bukan spesifikasi teknis lengkap.
        $carable = $this->whenLoaded('carable');

        return [
            'id' => $this->id,
            'kode_pesanan' => $this->kode_pesanan,

            'kendaraan' => $this->when($carable, function () use ($carable) {
                return [
                    'type' => $this->carable_type === \App\Models\GrCar::class ? 'gr_car' : 'car',
                    'id' => $carable->id,
                    'nama_model' => $carable->nama_model,
                    'gambar_utama' => $carable->gambar_utama,
                ];
            }),

            'warna' => $this->warna,
            'transmisi_dipilih' => $this->transmisi_dipilih,

            'rincian_biaya' => [
                'harga_kendaraan' => $this->harga_kendaraan,
                'biaya_admin' => $this->biaya_admin,
                'pajak' => $this->pajak,
                'booking_fee' => $this->booking_fee,
                'total_tagihan' => $this->total_tagihan,
            ],

            'metode_pembayaran' => $this->metode_pembayaran,
            'status' => $this->status,

            // Ditampilkan hanya saat admin melihat semua order (butuh tahu siapa pemesan)
            'pemesan' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'username' => $this->user->username,
                    'nama_lengkap' => $this->user->buyerProfile?->nama_lengkap,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
