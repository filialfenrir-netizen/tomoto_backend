<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGrCarRequest;
use App\Http\Requests\UpdateGrCarRequest;
use App\Http\Resources\GrCarResource;
use App\Models\GrCar;
use App\Traits\ApiResponse;

class GrCarController extends Controller
{
    use ApiResponse;

    /**
     * Daftar mobil GR. Publik - dipakai halaman GR Performance buyer
     * maupun tabel manajemen GR admin.
     */
    public function index()
    {
        $query = GrCar::query()
            ->cariNama(request('search'))
            ->latest();

        $perPage = (int) request('per_page', 12);
        $grCars = $query->paginate($perPage);

        return $this->success([
            'items' => GrCarResource::collection($grCars->items()),
            'pagination' => [
                'current_page' => $grCars->currentPage(),
                'last_page' => $grCars->lastPage(),
                'per_page' => $grCars->perPage(),
                'total' => $grCars->total(),
            ],
        ]);
    }

    public function show(GrCar $grCar)
    {
        $serupa = GrCar::where('id', '!=', $grCar->id)
            ->limit(3)
            ->get();

        return $this->success([
            'car' => new GrCarResource($grCar),
            'model_serupa' => GrCarResource::collection($serupa),
        ]);
    }

    /**
     * Admin only - dijaga oleh middleware 'admin' di route.
     */
    public function store(StoreGrCarRequest $request)
    {
        $grCar = GrCar::create($request->validated());

        return $this->success(new GrCarResource($grCar), 'Mobil GR berhasil ditambahkan.', 201);
    }

    public function update(UpdateGrCarRequest $request, GrCar $grCar)
    {
        $grCar->update($request->validated());

        return $this->success(new GrCarResource($grCar), 'Mobil GR berhasil diperbarui.');
    }

    public function destroy(GrCar $grCar)
    {
        $grCar->delete();

        return $this->success(null, 'Mobil GR berhasil dihapus.');
    }
}
