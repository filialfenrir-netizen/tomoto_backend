<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pesanan',
        'user_id',
        'carable_type',
        'carable_id',
        'warna',
        'transmisi_dipilih',
        'varian_mesin',
        'harga_kendaraan',
        'biaya_admin',
        'pajak',
        'booking_fee',
        'total_tagihan',
        'metode_pembayaran',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harga_kendaraan' => 'integer',
            'biaya_admin' => 'integer',
            'pajak' => 'integer',
            'booking_fee' => 'integer',
            'total_tagihan' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi polymorphic ke Car atau GrCar.
     * Laravel akan resolve otomatis lewat kolom carable_type (fully qualified class name).
     */
    public function carable(): MorphTo
    {
        return $this->morphTo();
    }
}
