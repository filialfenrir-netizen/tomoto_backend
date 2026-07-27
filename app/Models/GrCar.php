<?php

namespace App\Models;

use App\Contracts\HasCarImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GrCar extends Model implements HasCarImages
{
    use HasFactory;

    protected $table = 'gr_cars';

    protected $fillable = [
        'nama_model',
        'tag',
        'deskripsi_singkat',
        'harga',
        'stok',
        'horsepower',
        'drivetrain',
        'spec_chip_1',
        'spec_chip_2',
        'tipe_mesin',
        'kapasitas_mesin',
        'tenaga_maksimum',
        'torsi_maksimum',
        'transmisi',
        'suspensi',
        'akselerasi',
        'gambar_utama',
        'galeri',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'stok' => 'integer',
            'horsepower' => 'integer',
            'galeri' => 'array',
        ];
    }

    public function orders(): MorphMany
    {
        return $this->morphMany(Order::class, 'carable');
    }

    public function imageStorageFolder(): string
    {
        return 'gr-cars';
    }

    public function scopeCariNama($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

         return $query->whereRaw('LOWER(nama_model) LIKE ?', ['%' . mb_strtolower($keyword) . '%']);
    }
}
