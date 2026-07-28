<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Urutan WAJIB seperti ini:
     * 1. AdminUserSeeder    - akun testing baku §14 kontrak (admin,
     *                         budisantoso) harus ada lebih dulu.
     * 2. ToyotaDummySeeder  - data Car & GrCar (OrderSeeder butuh ini).
     * 3. UserSeeder         - 50 buyer + 3 admin tambahan (baru,
     *                         ditambahkan untuk seed data massal).
     * 4. OrderSeeder        - 30 transaksi dummy (baru, butuh buyer
     *                         dari langkah 1 & 3, dan mobil dari
     *                         langkah 2 - makanya paling akhir).
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            ToyotaDummySeeder::class,
            UserSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
