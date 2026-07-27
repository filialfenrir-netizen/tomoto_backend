<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Car;
use App\Models\GrCar;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ApiResponse;

    private const BIAYA_ADMIN = 2_500_000;
    private const PERSEN_PAJAK = 0.11; // PPN 11%
    private const BOOKING_FEE = 25_000_000; // tanda jadi tetap, sesuai UI payment_flow

    /**
     * Riwayat pesanan milik user yang sedang login.
     */
    public function index()
    {
        $orders = request()->user()
            ->orders()
            ->with('carable')
            ->latest()
            ->paginate(10);

        return $this->success([
            'items' => OrderResource::collection($orders->items()),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        // Buyer hanya boleh lihat order miliknya sendiri
        if ($order->user_id !== request()->user()->id) {
            return $this->error('Anda tidak memiliki akses ke pesanan ini.', 403);
        }

        return $this->success(new OrderResource($order->load('carable')));
    }

    /**
     * Buat pesanan baru. Alur mengikuti UI payment_flow: buyer membayar
     * booking fee (tanda jadi) di awal, harga & pajak dihitung SERVER-SIDE
     * dari harga master mobil - tidak pernah percaya angka dari klien,
     * supaya tidak bisa dimanipulasi lewat request langsung.
     */
    public function store(StoreOrderRequest $request)
    {
        $validated = $request->validated();

        // Alamat wajib ada sebelum checkout, meski nullable saat isi profil
        // biasa (lihat UpdateProfileRequest). Kalau kosong, tolak & beri sinyal
        // ke frontend untuk redirect ke halaman edit profil.
        if (blank($request->user()->buyerProfile?->alamat)) {
            return $this->error(
                'Alamat pengiriman belum diisi. Silakan lengkapi profil terlebih dahulu.',
                422,
                ['redirect' => 'edit-profile']
            );
        }

        // Petakan alias aman ('car'/'gr_car') ke Model class sebenarnya.
        // Klien TIDAK PERNAH mengirim nama class PHP secara langsung.
        $modelClass = $validated['type'] === 'gr_car' ? GrCar::class : Car::class;

        $mobil = $modelClass::find($validated['car_id']);

        if (! $mobil) {
            return $this->error('Mobil tidak ditemukan.', 404);
        }

        if ($mobil->stok < 1) {
            return $this->error('Stok mobil ini sedang habis.', 422);
        }

        $order = DB::transaction(function () use ($validated, $mobil, $modelClass) {
            $hargaKendaraan = $mobil->harga;
            $biayaAdmin = self::BIAYA_ADMIN;
            $pajak = (int) round($hargaKendaraan * self::PERSEN_PAJAK);
            $bookingFee = self::BOOKING_FEE;

            $order = Order::create([
                'kode_pesanan' => $this->generateKodePesanan(),
                'user_id' => request()->user()->id,
                'carable_type' => $modelClass,
                'carable_id' => $mobil->id,
                'warna' => $validated['warna'],
                'transmisi_dipilih' => $validated['transmisi_dipilih'] ?? null,
                'varian_mesin' => $validated['varian_mesin'] ?? null,
                'harga_kendaraan' => $hargaKendaraan,
                'biaya_admin' => $biayaAdmin,
                'pajak' => $pajak,
                'booking_fee' => $bookingFee,
                'total_tagihan' => $bookingFee, // pelunasan sisanya dilakukan via dealer
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'status' => 'pending',
            ]);

            // Kurangi stok begitu order dibuat (reservasi unit)
            $mobil->decrement('stok');

            return $order;
        });

        return $this->success(new OrderResource($order->load('carable')), 'Pesanan berhasil dibuat.', 201);
    }

    private function generateKodePesanan(): string
    {
        $tanggal = now()->format('Ymd');
        $urutan = str_pad((string) (Order::whereDate('created_at', now())->count() + 1), 4, '0', STR_PAD_LEFT);

        return "ORD-{$tanggal}-{$urutan}";
    }
}
