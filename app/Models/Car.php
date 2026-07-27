<?php

namespace App\Models;

use App\Contracts\HasCarImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Car extends Model implements HasCarImages
{
    use HasFactory;

    protected $fillable = [
        'nama_model',
        'kategori',
        'tag',
        'deskripsi_singkat',
        'harga',
        'stok',
        'horsepower',
        'estimasi_konsumsi',
        'drivetrain',
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
        return 'cars';
    }

    public function scopeKategori($query, ?string $kategori)
    {
        if (! $kategori) {
            return $query;
        }

        return $query->where('kategori', $kategori);
    }

    public function scopeCariNama($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }
        return $query->whereRaw('LOWER(nama_model) LIKE ?', ['%' . mb_strtolower($keyword) . '%']);
    }
}
