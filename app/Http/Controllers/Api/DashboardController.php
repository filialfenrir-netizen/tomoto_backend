<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\GrCar;
use App\Models\Order;
use App\Models\User;
use App\Traits\ApiResponse;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * Ringkasan untuk KONSOL MANAJEMEN (beranda admin).
     * Sesuai UI: Kelola Mobil (total unit), Kelola Pesanan (tertunda),
     * Kelola Pengguna (aktif), + aktivitas terbaru.
     */
    public function index()
    {
        $totalMobil = Car::sum('stok') + GrCar::sum('stok');
        $pesananTertunda = Order::where('status', 'pending')->count();
        $penggunaAktif = User::where('role', 'buyer')->where('is_active', true)->count();

        $aktivitasTerbaru = Order::with('carable')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (Order $order) {
                return [
                    'deskripsi' => "Pesanan Baru: {$order->kode_pesanan}",
                    'keterangan' => $order->carable?->nama_model ?? '-',
                    'waktu' => $order->created_at->diffForHumans(),
                ];
            });

        return $this->success([
            'total_mobil' => $totalMobil,
            'pesanan_tertunda' => $pesananTertunda,
            'pengguna_aktif' => $penggunaAktif,
            'aktivitas_terbaru' => $aktivitasTerbaru,
        ]);
    }
}
