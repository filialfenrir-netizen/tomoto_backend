<?php

namespace Database\Seeders;

use App\Models\GrCar;
use Illuminate\Database\Seeder;

class GrCarSeeder extends Seeder
{
    public function run(): void
    {
        $grCars = [
            [
                'nama_model' => 'GR Yaris',
                'tag' => 'New',
                'deskripsi_singkat' => 'Born from the World Rally Championship, GR Yaris delivers pure, unadulterated performance.',
                'harga' => 799_000_000,
                'stok' => 14,
                'horsepower' => 261,
                'drivetrain' => 'GR-FOUR AWD',
                'spec_chip_1' => '1.6L 3-Cyl Turbo',
                'spec_chip_2' => 'GR-FOUR AWD',
                'tipe_mesin' => '1.6L 3-Silinder Turbo G16E-GTS',
                'kapasitas_mesin' => '1618 cc',
                'tenaga_maksimum' => '261 hp @ 6500 rpm',
                'torsi_maksimum' => '360 Nm @ 3000-4600 rpm',
                'transmisi' => 'Manual 6-Percepatan',
                'suspensi' => 'MacPherson Strut / Double Wishbone',
                'akselerasi' => '0-100 km/h dalam 5.2s',
                'gambar_utama' => null,
                'galeri' => [],
            ],
            [
                'nama_model' => 'GR Supra',
                'tag' => 'Performance',
                'deskripsi_singkat' => 'A pure sports car experience with a front-engine, rear-wheel-drive layout and a turbocharged inline-six.',
                'harga' => 1_150_000_000,
                'stok' => 3,
                'horsepower' => 382,
                'drivetrain' => 'RWD',
                'spec_chip_1' => '3.0L Inline-6 Turbo',
                'spec_chip_2' => 'RWD MT',
                'tipe_mesin' => '3.0L Twin-Scroll Single Turbo Inline 6-Cylinder',
                'kapasitas_mesin' => '2998 cc',
                'tenaga_maksimum' => '382 hp @ 5800-6500 rpm',
                'torsi_maksimum' => '368 lb-ft @ 1800-5000 rpm',
                'transmisi' => 'Otomatis 8-Percepatan',
                'suspensi' => 'Adaptive Variable Sport (AVS) System',
                'akselerasi' => '0-100 km/h dalam 3.9s',
                'gambar_utama' => null,
                'galeri' => [],
            ],
            [
                'nama_model' => 'GR 86',
                'tag' => null,
                'deskripsi_singkat' => 'Lightweight, agile, and driver-focused. The ultimate everyday track car engineered for pure enjoyment.',
                'harga' => 620_000_000,
                'stok' => 28,
                'horsepower' => 228,
                'drivetrain' => 'RWD',
                'spec_chip_1' => '2.4L Flat-4 NA',
                'spec_chip_2' => 'Lightweight RWD',
                'tipe_mesin' => '2.4L Boxer 4-Silinder NA',
                'kapasitas_mesin' => '2387 cc',
                'tenaga_maksimum' => '228 hp @ 7000 rpm',
                'torsi_maksimum' => '250 Nm @ 3700 rpm',
                'transmisi' => 'Manual 6-Percepatan',
                'suspensi' => 'MacPherson Strut / Double Wishbone',
                'akselerasi' => '0-100 km/h dalam 6.3s',
                'gambar_utama' => null,
                'galeri' => [],
            ],
            [
                'nama_model' => 'GR Corolla',
                'tag' => null,
                'deskripsi_singkat' => 'Rally-bred hot hatch featuring the revolutionary GR-FOUR AWD system for uncompromised grip and speed.',
                'harga' => 850_000_000,
                'stok' => 10,
                'horsepower' => 300,
                'drivetrain' => 'AWD',
                'spec_chip_1' => '1.6L 3-Cyl Turbo',
                'spec_chip_2' => 'GR-FOUR AWD',
                'tipe_mesin' => '1.6L 3-Silinder Turbo G16E-GTS',
                'kapasitas_mesin' => '1618 cc',
                'tenaga_maksimum' => '300 hp @ 6500 rpm',
                'torsi_maksimum' => '400 Nm @ 3000 rpm',
                'transmisi' => 'Manual 6-Percepatan',
                'suspensi' => 'MacPherson Strut / Multi-link',
                'akselerasi' => '0-100 km/h dalam 4.9s',
                'gambar_utama' => null,
                'galeri' => [],
            ],
        ];

        foreach ($grCars as $grCar) {
            GrCar::updateOrCreate(['nama_model' => $grCar['nama_model']], $grCar);
        }
    }
}
