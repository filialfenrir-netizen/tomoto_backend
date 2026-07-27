<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Alur registrasi baru hanya mengisi username + email + password.
     * nama_lengkap (dan field profil lain) dilengkapi belakangan setelah
     * login, jadi kolom ini tidak boleh lagi NOT NULL.
     */
    public function up(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->string('nama_lengkap')->nullable(false)->change();
        });
    }
};
