<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mobil seri GR (Gazoo Racing) - dipisah dari cars karena atribut performa
     * jauh berbeda konteksnya dari mobil standar.
     */
    public function up(): void
    {
        Schema::create('gr_cars', function (Blueprint $table) {
            $table->id();
            $table->string('nama_model');
            $table->string('tag')->nullable(); // contoh: "GR Sport", "New"
            $table->text('deskripsi_singkat')->nullable();

            $table->unsignedBigInteger('harga'); // dalam Rupiah
            $table->unsignedInteger('stok')->default(0);

            // Quick specs (chip di card & tabel admin)
            $table->unsignedInteger('horsepower')->nullable();
            $table->string('drivetrain')->nullable(); // contoh: "GR-FOUR AWD", "RWD"
            $table->string('spec_chip_1')->nullable(); // contoh: "1.6L 3-Cyl Turbo"
            $table->string('spec_chip_2')->nullable(); // contoh: "GR-FOUR AWD"

            // Spesifikasi teknis (halaman detail)
            $table->string('tipe_mesin')->nullable();
            $table->string('kapasitas_mesin')->nullable();
            $table->string('tenaga_maksimum')->nullable();
            $table->string('torsi_maksimum')->nullable();
            $table->string('transmisi')->nullable();
            $table->string('suspensi')->nullable();
            $table->string('akselerasi')->nullable(); // contoh: "0-100 km/h dalam 3.9s"

            $table->string('gambar_utama')->nullable();
            $table->json('galeri')->nullable();

            $table->timestamps();

            $table->index('nama_model');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gr_cars');
    }
};
