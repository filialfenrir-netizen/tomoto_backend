<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan buyer. Merujuk ke Car ATAU GrCar lewat polymorphic relation
     * (carable_type + carable_id), supaya satu tabel order bisa menunjuk ke
     * kedua sumber mobil tanpa duplikasi struktur.
     *
     * Mengikuti alur di UI payment_flow: buyer hanya membayar booking fee
     * (tanda jadi) di awal, pelunasan dilakukan langsung via dealer.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pesanan')->unique(); // contoh: ORD-2026-07-0001

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Polymorphic ke Car atau GrCar
            $table->string('carable_type');
            $table->unsignedBigInteger('carable_id');

            // Pilihan bebas dari buyer saat checkout (bukan data master terstruktur)
            $table->string('warna')->nullable();
            $table->string('transmisi_dipilih')->nullable();

            // Rincian biaya (dihitung & dikunci di server saat order dibuat)
            $table->unsignedBigInteger('harga_kendaraan');
            $table->unsignedBigInteger('biaya_admin')->default(0);
            $table->unsignedBigInteger('pajak')->default(0); // PPN 11% dari harga_kendaraan
            $table->unsignedBigInteger('booking_fee'); // tanda jadi yang harus dibayar sekarang
            $table->unsignedBigInteger('total_tagihan'); // = booking_fee (pelunasan via dealer)

            $table->enum('metode_pembayaran', ['transfer', 'credit_card', 'ewallet'])->nullable();
            $table->enum('status', ['pending', 'diproses', 'selesai', 'dibatalkan'])->default('pending');

            $table->timestamps();

            $table->index(['carable_type', 'carable_id']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
