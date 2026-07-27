<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\BuyerProfile;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Registrasi buyer baru. Membuat baris di `users` dan `buyer_profiles`
     * sekaligus dalam satu transaksi DB supaya tidak ada user tanpa profil.
     */
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'role' => 'buyer',
            ]);

            // Hanya email yang diisi saat registrasi. Field profil lain
            // (nama_lengkap, no_hp, alamat, tanggal_lahir) dilengkapi
            // belakangan lewat endpoint update-profile setelah login.
            BuyerProfile::create([
                'user_id' => $user->id,
                'email' => $validated['email'],
            ]);

            return $user;
        });

        $token = $user->createToken('tomoto-frontend')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user->load('buyerProfile')),
            'token' => $token,
        ], 'Registrasi berhasil.', 201);
    }

    /**
     * Login pakai username (bukan email) + password.
     * Satu form untuk admin & buyer - role dikembalikan di response,
     * frontend yang menentukan redirect tujuan.
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('username', $validated['username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return $this->error('Username atau password salah.', 401);
        }

        if (! $user->is_active) {
            return $this->error('Akun Anda telah dinonaktifkan. Silakan hubungi admin.', 403);
        }

        $token = $user->createToken('tomoto-frontend')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user->load('buyerProfile')),
            'token' => $token,
        ], 'Login berhasil.');
    }

    /**
     * Logout - mencabut token yang sedang dipakai request ini saja
     * (bukan semua token milik user, supaya sesi di device lain tidak ikut logout).
     */
    public function logout()
    {
        $request = request();
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil.');
    }

    /**
     * Ambil data user yang sedang login (dipakai frontend untuk validasi token
     * saat pertama kali halaman dimuat, atau untuk refresh data profil di navbar).
     */
    public function me()
    {
        $user = Auth::user()->load('buyerProfile');

        return $this->success(new UserResource($user));
    }
}
