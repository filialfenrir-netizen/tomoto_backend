<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteCarImageRequest;
use App\Http\Requests\ReorderCarImagesRequest;
use App\Http\Requests\SetMainCarImageRequest;
use App\Http\Requests\UploadCarImagesRequest;
use App\Models\Car;
use App\Models\GrCar;
use App\Services\CarImageService;
use App\Traits\ApiResponse;

/**
 * Admin only (dijaga middleware 'admin' di route) - CRUD gambar untuk Car
 * maupun GrCar. Route berbeda untuk masing-masing tipe mobil ('/admin/cars/..'
 * vs '/admin/gr-cars/..'), tapi seluruh logic (upload/hapus/reorder/set-utama)
 * dipusatkan di CarImageService supaya tidak ada duplikasi.
 */
class CarImageController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CarImageService $images)
    {
    }

    // ---------------------------------------------------------------
    // Car
    // ---------------------------------------------------------------

    public function uploadForCar(UploadCarImagesRequest $request, Car $car)
    {
        $result = $this->images->upload($car, $request->file('images'));

        return $this->success($result, 'Gambar berhasil diunggah.', 201);
    }

    public function deleteForCar(DeleteCarImageRequest $request, Car $car)
    {
        $result = $this->images->delete($car, $request->validated('path'));

        return $this->success($result, 'Gambar berhasil dihapus.');
    }

    public function reorderForCar(ReorderCarImagesRequest $request, Car $car)
    {
        $galeri = $this->images->reorder($car, $request->validated('galeri'));

        return $this->success(['galeri' => $galeri], 'Urutan gambar berhasil diperbarui.');
    }

    public function setMainForCar(SetMainCarImageRequest $request, Car $car)
    {
        $gambarUtama = $this->images->setMain($car, $request->validated('path'));

        return $this->success(['gambar_utama' => $gambarUtama], 'Gambar utama berhasil diperbarui.');
    }

    // ---------------------------------------------------------------
    // GrCar
    // ---------------------------------------------------------------

    public function uploadForGrCar(UploadCarImagesRequest $request, GrCar $grCar)
    {
        $result = $this->images->upload($grCar, $request->file('images'));

        return $this->success($result, 'Gambar berhasil diunggah.', 201);
    }

    public function deleteForGrCar(DeleteCarImageRequest $request, GrCar $grCar)
    {
        $result = $this->images->delete($grCar, $request->validated('path'));

        return $this->success($result, 'Gambar berhasil dihapus.');
    }

    public function reorderForGrCar(ReorderCarImagesRequest $request, GrCar $grCar)
    {
        $galeri = $this->images->reorder($grCar, $request->validated('galeri'));

        return $this->success(['galeri' => $galeri], 'Urutan gambar berhasil diperbarui.');
    }

    public function setMainForGrCar(SetMainCarImageRequest $request, GrCar $grCar)
    {
        $gambarUtama = $this->images->setMain($grCar, $request->validated('path'));

        return $this->success(['gambar_utama' => $gambarUtama], 'Gambar utama berhasil diperbarui.');
    }
}
