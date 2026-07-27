<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom varian_mesin ke orders. Dibuat bebas (nullable string) dulu
     * karena daftar varian resminya belum ditentukan - begitu daftarnya ada,
     * validasi di StoreOrderRequest bisa diganti dari 'string' jadi 'in:...'
     * tanpa perlu migrasi ulang kolom ini.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('varian_mesin')->nullable()->after('transmisi_dipilih');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('varian_mesin');
        });
    }
};
