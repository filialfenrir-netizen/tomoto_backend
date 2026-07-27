<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Http\Resources\GrCarResource;
use App\Models\Car;
use App\Models\GrCar;
use App\Traits\ApiResponse;

class SearchController extends Controller
{
    use ApiResponse;

    /**
     * Search gabungan nama/model mobil di kedua tabel (cars & gr_cars).
     * User tidak perlu tahu itu mobil GR atau normal - hasil digabung jadi satu list.
     * GET /api/search?q=yaris
     */
    public function index()
    {
        $keyword = request('q', '');

        if (trim($keyword) === '') {
            return $this->success(['items' => []]);
        }

        $cars = Car::cariNama($keyword)->limit(10)->get();
        $grCars = GrCar::cariNama($keyword)->limit(10)->get();

        $hasil = collect()
            ->concat(CarResource::collection($cars)->resolve())
            ->concat(GrCarResource::collection($grCars)->resolve())
            ->values();

        return $this->success(['items' => $hasil]);
    }
}
