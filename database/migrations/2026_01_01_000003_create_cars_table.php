<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mobil seri NORMAL (Hatchback, Sedan, SUV, MPV).
     * Terpisah dari gr_cars karena field spesifikasi cukup berbeda konteksnya
     * (fokus efisiensi/kenyamanan, bukan performa balap).
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_model');
            $table->enum('kategori', ['hatchback', 'sedan', 'suv', 'mpv']);
            $table->string('tag')->nullable(); // contoh: "Hybrid"
            $table->text('deskripsi_singkat')->nullable();

            $table->unsignedBigInteger('harga'); // dalam Rupiah, tanpa desimal
            $table->unsignedInteger('stok')->default(0);

            // Quick specs (ditampilkan di card katalog)
            $table->unsignedInteger('horsepower')->nullable();
            $table->string('estimasi_konsumsi')->nullable(); // contoh: "54 Est."
            $table->string('drivetrain')->nullable(); // contoh: "AWD", "FWD", "RWD"

            // Spesifikasi teknis (ditampilkan di halaman detail, tabel spec)
            $table->string('tipe_mesin')->nullable();
            $table->string('kapasitas_mesin')->nullable(); // contoh: "2998 cc"
            $table->string('tenaga_maksimum')->nullable(); // contoh: "382 hp @ 5800-6500 rpm"
            $table->string('torsi_maksimum')->nullable();
            $table->string('transmisi')->nullable();
            $table->string('suspensi')->nullable();
            $table->string('akselerasi')->nullable(); // contoh: "0-100 km/h dalam 3.9s"

            $table->string('gambar_utama')->nullable();
            $table->json('galeri')->nullable(); // array path/URL gambar tambahan

            $table->timestamps();

            $table->index('kategori');
            $table->index('nama_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
