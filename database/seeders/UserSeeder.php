<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Generate 50 buyer + beberapa admin tambahan untuk keperluan testing
     * (pagination, search, dashboard admin, dsb - lihat Rencana Kerja §4
     * Hari 4 & TOMOTO_API_CONTRACT.md §8.2/§8.3).
     *
     * Dipisah dari AdminUserSeeder (bukan menggantikannya) karena
     * AdminUserSeeder sudah menjamin akun testing baku §14 kontrak
     * (admin/admin12345, budisantoso/password123) selalu ada dengan
     * username tetap. Seeder ini HANYA menambah data massal di atasnya
     * lewat updateOrCreate + username unik acak, jadi aman dijalankan
     * berkali-kali (idempotent, tidak bikin duplikat / tidak menimpa
     * akun baku itu).
     *
     * Password semua akun dummy di sini: "password123" (sama seperti
     * akun budisantoso baku, supaya gampang diingat saat login manual).
     */
    public function run(): void
    {
        $this->seedBuyers(50);
        $this->seedExtraAdmins(3);
    }

    private function seedBuyers(int $jumlah): void
    {
        $faker = fake('id_ID');

        for ($i = 1; $i <= $jumlah; $i++) {
            $namaLengkap = $faker->name();
            $username = $this->buatUsernameUnik($faker, $namaLengkap);
            $emailUnik = $this->buatEmailUnik($faker, $username);

            $user = User::updateOrCreate(
                ['username' => $username],
                [
                    'password' => Hash::make('password123'),
                    'role' => 'buyer',
                    // ~10% buyer dibuat nonaktif dari awal supaya admin
                    // punya data nyata untuk uji filter status di
                    // admin/users.html (bukan cuma toggle manual satu-satu).
                    'is_active' => $faker->boolean(90),
                ]
            );

            // Sengaja sisakan ~15% buyer TANPA nama_lengkap terisi (null),
            // meniru kondisi nyata di migration nullable poin
            // 2026_07_27_000002: buyer baru register tapi belum
            // melengkapi profil. Field lain (no_hp, alamat,
            // tanggal_lahir, foto_profil) juga dibiarkan kosong untuk
            // sebagian supaya konsisten dengan alur itu.
            $profilLengkap = $faker->boolean(85);

            $user->buyerProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nama_lengkap' => $profilLengkap ? $namaLengkap : null,
                    'email' => $emailUnik,
                    'no_hp' => $profilLengkap ? $this->buatNoHpIndonesia($faker) : null,
                    'alamat' => $profilLengkap ? $faker->address() : null,
                    'tanggal_lahir' => $profilLengkap
                        ? $faker->dateTimeBetween('-55 years', '-18 years')->format('Y-m-d')
                        : null,
                    'foto_profil' => null,
                ]
            );
        }
    }

    private function seedExtraAdmins(int $jumlah): void
    {
        $faker = fake('id_ID');

        for ($i = 1; $i <= $jumlah; $i++) {
            $username = 'admin' . str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            User::updateOrCreate(
                ['username' => $username],
                [
                    'password' => Hash::make('password123'),
                    'role' => 'admin',
                    'is_active' => true,
                ]
            );
            // Admin TIDAK punya baris di buyer_profiles (§0.2 kontrak &
            // komentar migration create_buyer_profiles_table) - sengaja
            // tidak dibuatkan profil sama sekali.
        }
    }

    private function buatUsernameUnik($faker, string $namaLengkap): string
    {
        $base = Str::slug(Str::lower($namaLengkap), '');
        $base = substr($base ?: 'buyer', 0, 40);

        do {
            $kandidat = $base . $faker->numberBetween(100, 9999);
        } while (User::where('username', $kandidat)->exists());

        return $kandidat;
    }

    private function buatEmailUnik($faker, string $username): string
    {
        // Email diturunkan dari username yang SUDAH dijamin unik
        // (lihat buatUsernameUnik), jadi otomatis unik juga - tidak
        // perlu loop cek ulang ke database di sini.
        $domain = $faker->randomElement(['example.com', 'mail.test', 'contoh.co.id']);
        return "{$username}@{$domain}";
    }

    private function buatNoHpIndonesia($faker): string
    {
        // Format lokal 08xx sesuai contoh di API contract §7.3
        // ("081234567890"), no_hp kontraknya "hanya angka".
        $prefix = $faker->randomElement(['0812', '0813', '0821', '0822', '0852', '0853', '0895', '0896']);
        return $prefix . $faker->numerify('########');
    }
}
