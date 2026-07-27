<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;

class AdminUserController extends Controller
{
    use ApiResponse;

    /**
     * Daftar buyer (role=buyer saja, admin tidak perlu kelola sesama admin di sini).
     * Query params: search (cari nama/username), per_page
     */
    public function index()
    {
        $query = User::query()
            ->where('role', 'buyer')
            ->with('buyerProfile')
            ->latest();

        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhereHas('buyerProfile', function ($q2) use ($search) {
                        $q2->where('nama_lengkap', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) request('per_page', 15);
        $users = $query->paginate($perPage);

        return $this->success([
            'items' => UserResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Toggle status aktif/nonaktif akun buyer.
     * Nonaktif -> user tidak bisa login (dicek di AuthController@login).
     */
    public function toggleActive(User $user)
    {
        if ($user->role !== 'buyer') {
            return $this->error('Hanya akun buyer yang dapat diaktifkan/dinonaktifkan dari sini.', 422);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return $this->success(
            new UserResource($user->load('buyerProfile')),
            $user->is_active ? 'Akun berhasil diaktifkan.' : 'Akun berhasil dinonaktifkan.'
        );
    }
}
