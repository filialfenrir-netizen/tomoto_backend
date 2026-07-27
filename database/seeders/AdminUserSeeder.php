<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => Hash::make('admin12345'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // Satu buyer contoh untuk memudahkan testing alur order/profil
        $buyer = User::updateOrCreate(
            ['username' => 'budisantoso'],
            [
                'password' => Hash::make('password123'),
                'role' => 'buyer',
                'is_active' => true,
            ]
        );

        $buyer->buyerProfile()->updateOrCreate(
            ['user_id' => $buyer->id],
            [
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Jend. Sudirman Kav. 1, Jakarta Pusat, DKI Jakarta 10220',
            ]
        );
    }
}
