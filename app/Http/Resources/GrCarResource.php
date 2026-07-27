<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GrCarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'gr_car',
            'nama_model' => $this->nama_model,
            'tag' => $this->tag,
            'deskripsi_singkat' => $this->deskripsi_singkat,

            'harga' => $this->harga,
            'stok' => $this->stok,

            'horsepower' => $this->horsepower,
            'drivetrain' => $this->drivetrain,
            'spec_chip_1' => $this->spec_chip_1,
            'spec_chip_2' => $this->spec_chip_2,

            'spesifikasi_teknis' => [
                'tipe_mesin' => $this->tipe_mesin,
                'kapasitas_mesin' => $this->kapasitas_mesin,
                'tenaga_maksimum' => $this->tenaga_maksimum,
                'torsi_maksimum' => $this->torsi_maksimum,
                'transmisi' => $this->transmisi,
                'suspensi' => $this->suspensi,
                'akselerasi' => $this->akselerasi,
            ],

            'gambar_utama' => $this->gambar_utama,
            'galeri' => $this->galeri ?? [],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
