<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel users HANYA untuk autentikasi & otorisasi.
     * Data pribadi (nama, email, hp, alamat, dsb) ada di tabel buyer_profiles.
     * Admin tidak punya baris di buyer_profiles - cukup username & password di sini.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'buyer'])->default('buyer');
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable(); // disiapkan untuk Sanctum/Laravel default, tidak wajib dipakai
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
