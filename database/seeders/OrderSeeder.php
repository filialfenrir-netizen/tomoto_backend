<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\GrCar;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Generate 30 transaksi (order) dummy, campuran ke Car (mayoritas)
     * dan GrCar (sebagian), untuk keperluan testing admin/orders.html
     * dan dashboard admin (§8.1, §8.4-8.6 API contract).
     *
     * PENTING: seeder ini HARUS dijalankan setelah UserSeeder (butuh
     * buyer) dan CarSeeder/GrCarSeeder/ToyotaDummySeeder (butuh mobil).
     * Kalau salah satu kosong, seeder ini akan skip dengan pesan info
     * di console, bukan error - supaya `db:seed` tidak gagal total
     * kalau urutan pemanggilan di DatabaseSeeder belum tepat.
     *
     * Rincian biaya (meniru logic asli StoreOrderController §7.7):
     *   pajak         = 11% dari harga_kendaraan (PPN)
     *   biaya_admin   = tetap Rp2.500.000 (sesuai contoh response §7.7)
     *   booking_fee   = tanda jadi 10% dari harga_kendaraan, dibulatkan
     *                   ke ribuan terdekat, dengan batas bawah
     *                   Rp5.000.000 supaya mobil murah tidak dapat
     *                   booking fee tidak masuk akal kecil
     *   total_tagihan = booking_fee (buyer HANYA bayar booking fee di
     *                   awal, pelunasan manual via dealer - komentar
     *                   migration create_orders_table)
     *
     * CATATAN: Ini REKONSTRUKSI logic dari contoh angka di §7.7 kontrak
     * (harga 237.500.000 -> biaya_admin 2.500.000, pajak 26.125.000,
     * booking_fee & total_tagihan 25.000.000 - itu artinya di contoh
     * kontrak booking_fee dibulatkan ke atas dari ~10.4%). Kalau
     * implementasi asli StoreOrderController beda rumus, SESUAIKAN
     * konstanta di bawah supaya angka dummy tidak menyesatkan saat
     * dipakai demo ke stakeholder.
     */
    private const TARIF_PAJAK = 0.11;
    private const BIAYA_ADMIN = 2_500_000;
    private const TARIF_BOOKING_FEE = 0.10;
    private const BOOKING_FEE_MINIMUM = 5_000_000;

    public function run(): void
    {
        $buyerIds = User::where('role', 'buyer')->pluck('id')->all();
        $cars = Car::all();
        $grCars = GrCar::all();

        if (empty($buyerIds)) {
            $this->command?->warn('OrderSeeder: tidak ada user buyer - jalankan UserSeeder/AdminUserSeeder dulu. Dilewati.');
            return;
        }
        if ($cars->isEmpty() && $grCars->isEmpty()) {
            $this->command?->warn('OrderSeeder: tidak ada data Car maupun GrCar - jalankan CarSeeder/GrCarSeeder/ToyotaDummySeeder dulu. Dilewati.');
            return;
        }

        $faker = fake('id_ID');
        $totalOrder = 30;
        // ~70% ke Car biasa, ~30% ke GrCar - sesuai keputusan "campuran
        // keduanya, mayoritas car, sebagian gr_car".
        $jumlahCarOrder = $grCars->isEmpty() ? $totalOrder : (int) round($totalOrder * 0.7);
        $jumlahGrCarOrder = $totalOrder - $jumlahCarOrder;
        if ($cars->isEmpty()) {
            // Fallback: kalau ternyata cuma GrCar yang ada datanya.
            $jumlahGrCarOrder = $totalOrder;
            $jumlahCarOrder = 0;
        }

        $rencana = array_merge(
            array_fill(0, $jumlahCarOrder, 'car'),
            array_fill(0, $jumlahGrCarOrder, 'gr_car')
        );
        shuffle($rencana);

        $statusPool = $this->buatPoolStatus($totalOrder);

        foreach ($rencana as $i => $tipe) {
            $buyerId = $faker->randomElement($buyerIds);
            $kendaraan = $tipe === 'gr_car' ? $faker->randomElement($grCars->all()) : $faker->randomElement($cars->all());
            $carableType = $tipe === 'gr_car' ? GrCar::class : Car::class;

            $hargaKendaraan = (int) $kendaraan->harga;
            $biayaAdmin = self::BIAYA_ADMIN;
            $pajak = (int) round($hargaKendaraan * self::TARIF_PAJAK);
            $bookingFee = max(
                self::BOOKING_FEE_MINIMUM,
                (int) (round($hargaKendaraan * self::TARIF_BOOKING_FEE / 1000) * 1000)
            );
            $totalTagihan = $bookingFee;

            $status = $statusPool[$i];
            $createdAt = $faker->dateTimeBetween('-90 days', 'now');
            $kunciBulan = $createdAt->format('Y-m');

            $kodePesanan = sprintf('ORD-%s-%04d', $kunciBulan, $this->nomorUrutBerikutnya($kunciBulan));

            Order::updateOrCreate(
                ['kode_pesanan' => $kodePesanan],
                [
                    'user_id' => $buyerId,
                    'carable_type' => $carableType,
                    'carable_id' => $kendaraan->id,
                    'warna' => $faker->randomElement(['merah', 'hitam', 'silver', 'biru']),
                    'transmisi_dipilih' => $kendaraan->transmisi ?: $faker->randomElement(['Manual', 'Otomatis', 'CVT']),
                    'varian_mesin' => $kendaraan->tipe_mesin,
                    'harga_kendaraan' => $hargaKendaraan,
                    'biaya_admin' => $biayaAdmin,
                    'pajak' => $pajak,
                    'booking_fee' => $bookingFee,
                    'total_tagihan' => $totalTagihan,
                    'metode_pembayaran' => $faker->randomElement(['transfer', 'credit_card', 'ewallet']),
                    'status' => $status,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]
            );
        }
    }

    /**
     * Distribusi status supaya admin/orders.html punya variasi nyata di
     * tiap tab filter (§8.4), bukan semua kebetulan 'pending'.
     * Kira-kira: 30% pending, 30% diproses, 30% selesai, 10% dibatalkan.
     */
    private function buatPoolStatus(int $total): array
    {
        $pending = (int) round($total * 0.3);
        $diproses = (int) round($total * 0.3);
        $selesai = (int) round($total * 0.3);
        $dibatalkan = $total - $pending - $diproses - $selesai;

        $pool = array_merge(
            array_fill(0, $pending, 'pending'),
            array_fill(0, $diproses, 'diproses'),
            array_fill(0, $selesai, 'selesai'),
            array_fill(0, max(0, $dibatalkan), 'dibatalkan')
        );
        shuffle($pool);

        return $pool;
    }

    /**
     * kode_pesanan unik PER BULAN (format ORD-YYYY-MM-NNNN sesuai contoh
     * komentar migration create_orders_table, mis. "ORD-2026-07-0001").
     * Karena order dummy disebar ke tanggal acak dalam 90 hari terakhir
     * (bisa lintas beberapa bulan berbeda), nomor urut dihitung per
     * kunci bulan (`Y-m`) memakai counter di memori supaya tidak ada
     * dua order di bulan yang sama kebagian nomor urut yang sama.
     */
    private array $nomorUrutPerBulan = [];

    private function nomorUrutBerikutnya(string $kunciBulan): int
    {
        if (!isset($this->nomorUrutPerBulan[$kunciBulan])) {
            // Cek juga order yang SUDAH ada di database untuk bulan ini
            // (dari run seeder sebelumnya) supaya tetap idempotent dan
            // tidak collision dengan kode_pesanan yang sudah dibuat.
            $existingMax = Order::where('kode_pesanan', 'like', "ORD-{$kunciBulan}-%")->count();
            $this->nomorUrutPerBulan[$kunciBulan] = $existingMax;
        }

        $this->nomorUrutPerBulan[$kunciBulan]++;
        return $this->nomorUrutPerBulan[$kunciBulan];
    }
}
