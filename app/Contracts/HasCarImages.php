<?php

namespace App\Contracts;

/**
 * Kontrak untuk model yang punya struktur gambar "gambar_utama" (string, nullable)
 * + "galeri" (array/json, nullable). Diimplementasikan oleh Car & GrCar supaya
 * CarImageService bisa dipakai untuk keduanya tanpa duplikasi logic upload/hapus/
 * reorder/set-utama.
 */
interface HasCarImages
{
    /**
     * Nama sub-folder di disk 'public' tempat gambar model ini disimpan.
     * Contoh: 'cars' -> storage/app/public/cars, 'gr-cars' -> storage/app/public/gr-cars.
     */
    public function imageStorageFolder(): string;
}
