<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponse;

class AdminOrderController extends Controller
{
    use ApiResponse;

    /**
     * Semua pesanan dari semua buyer. Admin only.
     * Query params: status (pending|diproses|selesai|dibatalkan), per_page
     */
    public function index()
    {
        $query = Order::query()->with(['carable', 'user.buyerProfile'])->latest();

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        $perPage = (int) request('per_page', 15);
        $orders = $query->paginate($perPage);

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
        return $this->success(new OrderResource($order->load(['carable', 'user.buyerProfile'])));
    }

    /**
     * Update status pesanan: pending -> diproses -> selesai / dibatalkan.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update(['status' => $request->validated()['status']]);

        return $this->success(new OrderResource($order->load('carable')), 'Status pesanan berhasil diperbarui.');
    }
}
