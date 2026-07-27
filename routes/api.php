<?php

use App\Http\Controllers\Api\AdminOrderController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarController;
use App\Http\Controllers\Api\CarImageController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\GrCarController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - TOMOTO
|--------------------------------------------------------------------------
| Semua route di sini mengembalikan JSON dengan format konsisten
| { success, data } atau { success, message }.
| Autentikasi pakai Laravel Sanctum (token, bukan session cookie).
*/

// ============================================================
// PUBLIK - tidak butuh login sama sekali
// ============================================================

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Katalog & GR Performance bisa dilihat tanpa login (guest browsing di beranda)
Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{car}', [CarController::class, 'show']);

Route::get('/gr-cars', [GrCarController::class, 'index']);
Route::get('/gr-cars/{grCar}', [GrCarController::class, 'show']);

Route::get('/search', [SearchController::class, 'index']);

// ============================================================
// BUTUH LOGIN (auth:sanctum) - buyer & admin
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Profil buyer (lihat/update profil sendiri)
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Pesanan milik buyer sendiri
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // ============================================================
    // ADMIN ONLY - butuh login DAN role admin
    // ============================================================
    Route::middleware('admin')->prefix('admin')->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index']);

        // Manajemen mobil normal (create/update/delete - index & show sudah publik di atas)
        Route::post('/cars', [CarController::class, 'store']);
        Route::put('/cars/{car}', [CarController::class, 'update']);
        Route::delete('/cars/{car}', [CarController::class, 'destroy']);

        // CRUD gambar mobil normal (drag & drop galeri di admin)
        Route::post('/cars/{car}/images', [CarImageController::class, 'uploadForCar']);
        Route::delete('/cars/{car}/images', [CarImageController::class, 'deleteForCar']);
        Route::patch('/cars/{car}/images/reorder', [CarImageController::class, 'reorderForCar']);
        Route::patch('/cars/{car}/images/main', [CarImageController::class, 'setMainForCar']);

        // Manajemen mobil GR
        Route::post('/gr-cars', [GrCarController::class, 'store']);
        Route::put('/gr-cars/{grCar}', [GrCarController::class, 'update']);
        Route::delete('/gr-cars/{grCar}', [GrCarController::class, 'destroy']);

        // CRUD gambar mobil GR (drag & drop galeri di admin)
        Route::post('/gr-cars/{grCar}/images', [CarImageController::class, 'uploadForGrCar']);
        Route::delete('/gr-cars/{grCar}/images', [CarImageController::class, 'deleteForGrCar']);
        Route::patch('/gr-cars/{grCar}/images/reorder', [CarImageController::class, 'reorderForGrCar']);
        Route::patch('/gr-cars/{grCar}/images/main', [CarImageController::class, 'setMainForGrCar']);

        // Manajemen semua pesanan
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);

        // Manajemen pengguna (buyer)
        Route::get('/users', [AdminUserController::class, 'index']);
        Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']);
    });
});
