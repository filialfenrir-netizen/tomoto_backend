<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use App\Traits\ApiResponse;

class CarController extends Controller
{
    use ApiResponse;

    /**
     * Daftar mobil normal. Publik (tanpa auth) - dipakai halaman katalog buyer
     * maupun tabel manajemen inventaris admin.
     * Query params: kategori (hatchback|sedan|suv|mpv), search, per_page
     */
    public function index()
    {
        $query = Car::query()
            ->kategori(request('kategori'))
            ->cariNama(request('search'))
            ->latest();

        $perPage = (int) request('per_page', 12);
        $cars = $query->paginate($perPage);

        return $this->success([
            'items' => CarResource::collection($cars->items()),
            'pagination' => [
                'current_page' => $cars->currentPage(),
                'last_page' => $cars->lastPage(),
                'per_page' => $cars->perPage(),
                'total' => $cars->total(),
            ],
        ]);
    }

    /**
     * Detail satu mobil + daftar mobil serupa (kategori sama, exclude diri sendiri)
     * untuk mengisi bagian "Model Serupa" di halaman detail.
     */
    public function show(Car $car)
    {
        $serupa = Car::where('kategori', $car->kategori)
            ->where('id', '!=', $car->id)
            ->limit(3)
            ->get();

        return $this->success([
            'car' => new CarResource($car),
            'model_serupa' => CarResource::collection($serupa),
        ]);
    }

    /**
     * Admin only - dijaga oleh middleware 'admin' di route.
     */
    public function store(StoreCarRequest $request)
    {
        $car = Car::create($request->validated());

        return $this->success(new CarResource($car), 'Mobil berhasil ditambahkan.', 201);
    }

    public function update(UpdateCarRequest $request, Car $car)
    {
        $car->update($request->validated());

        return $this->success(new CarResource($car), 'Mobil berhasil diperbarui.');
    }

    public function destroy(Car $car)
    {
        $car->delete();

        return $this->success(null, 'Mobil berhasil dihapus.');
    }
}
