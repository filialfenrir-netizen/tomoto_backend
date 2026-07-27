<?php

namespace App\Services;

use App\Contracts\HasCarImages;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Satu service dipakai untuk Car maupun GrCar (struktur gambarnya identik:
 * gambar_utama + galeri), supaya logic upload/hapus/reorder/set-utama tidak
 * ditulis dua kali. Model yang dipakai harus implement HasCarImages.
 *
 * Semua gambar disimpan di disk 'public' (storage/app/public/{folder}) dan
 * URL publik penuh (bukan path relatif) yang disimpan ke kolom gambar_utama/
 * galeri - konsisten dengan cara data lama diisi manual (string URL).
 */
class CarImageService
{
    /** @var string[] */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const MAX_FILE_KB = 5120; // 5MB

    /**
     * Upload satu atau lebih file gambar baru, ditambahkan ke galeri.
     * Kalau model belum punya gambar_utama sama sekali, gambar pertama yang
     * diupload otomatis dijadikan gambar_utama.
     *
     * @param  UploadedFile[]  $files
     * @return array{gambar_utama: ?string, galeri: array<int, string>}
     */
    public function upload(Model&HasCarImages $mobil, array $files): array
    {
        $galeri = $mobil->galeri ?? [];

        foreach ($files as $file) {
            $path = $file->store($mobil->imageStorageFolder(), 'public');
            $galeri[] = Storage::disk('public')->url($path);
        }

        $galeri = array_values($galeri);
        $mobil->galeri = $galeri;

        if (! $mobil->gambar_utama && count($galeri) > 0) {
            $mobil->gambar_utama = $galeri[0];
        }

        $mobil->save();

        return [
            'gambar_utama' => $mobil->gambar_utama,
            'galeri' => $galeri,
        ];
    }

    /**
     * Hapus satu gambar dari galeri (by URL/path yang tersimpan di DB), dan
     * hapus file fisiknya dari disk. Kalau gambar yang dihapus adalah
     * gambar_utama, gambar_utama otomatis diganti ke item pertama galeri
     * yang tersisa (atau null kalau galeri jadi kosong).
     *
     * @return array{gambar_utama: ?string, galeri: array<int, string>}
     */
    public function delete(Model&HasCarImages $mobil, string $target): array
    {
        $galeri = $mobil->galeri ?? [];

        if (! in_array($target, $galeri, true)) {
            throw ValidationException::withMessages([
                'path' => 'Gambar tersebut tidak ditemukan di galeri mobil ini.',
            ]);
        }

        $galeri = array_values(array_filter($galeri, fn ($item) => $item !== $target));

        Storage::disk('public')->delete($this->urlToDiskPath($target));

        $mobil->galeri = $galeri;

        if ($mobil->gambar_utama === $target) {
            $mobil->gambar_utama = $galeri[0] ?? null;
        }

        $mobil->save();

        return [
            'gambar_utama' => $mobil->gambar_utama,
            'galeri' => $galeri,
        ];
    }

    /**
     * Ganti urutan galeri sesuai hasil drag & drop di admin. Menerima array
     * URL/path gambar dalam urutan baru - harus persis berisi set gambar
     * yang sama dengan galeri saat ini (tidak menambah/menghapus gambar).
     *
     * @param  array<int, string>  $orderedTargets
     * @return array<int, string>
     */
    public function reorder(Model&HasCarImages $mobil, array $orderedTargets): array
    {
        $galeriSaatIni = $mobil->galeri ?? [];

        $samaIsinya = count($orderedTargets) === count($galeriSaatIni)
            && empty(array_diff($orderedTargets, $galeriSaatIni));

        if (! $samaIsinya) {
            throw ValidationException::withMessages([
                'galeri' => 'Urutan baru harus berisi persis gambar yang sama dengan galeri saat ini.',
            ]);
        }

        $orderedTargets = array_values($orderedTargets);
        $mobil->galeri = $orderedTargets;
        $mobil->save();

        return $orderedTargets;
    }

    /**
     * Jadikan salah satu gambar existing (di galeri) sebagai gambar_utama.
     */
    public function setMain(Model&HasCarImages $mobil, string $target): string
    {
        $galeri = $mobil->galeri ?? [];

        if (! in_array($target, $galeri, true)) {
            throw ValidationException::withMessages([
                'path' => 'Gambar tersebut tidak ditemukan di galeri mobil ini.',
            ]);
        }

        $mobil->gambar_utama = $target;
        $mobil->save();

        return $target;
    }

    /**
     * Konversi URL publik yang tersimpan di DB kembali ke path relatif di
     * disk 'public', supaya bisa dihapus lewat Storage::delete(). Kalau URL
     * tidak berasal dari disk ini (misal link eksternal yang diisi manual),
     * hasilnya tidak akan match file apapun di disk - Storage::delete cukup
     * no-op untuk kasus itu (disk 'public' diset throw=>false).
     */
    private function urlToDiskPath(string $url): string
    {
        $base = rtrim(Storage::disk('public')->url(''), '/');

        return ltrim(str_replace($base, '', $url), '/');
    }

    public static function allowedExtensionsRule(): string
    {
        return 'mimes:'.implode(',', self::ALLOWED_EXTENSIONS);
    }

    public static function maxFileKbRule(): string
    {
        return 'max:'.self::MAX_FILE_KB;
    }
}
