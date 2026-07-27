<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use ApiResponse;

    /**
     * Lihat profil sendiri. Menggabungkan data users + buyer_profiles
     * lewat UserResource, jadi frontend cukup satu kali fetch.
     */
    public function show()
    {
        $user = request()->user()->load('buyerProfile');

        return $this->success(new UserResource($user));
    }

    /**
     * Update profil sendiri. Email SENGAJA tidak termasuk field yang bisa
     * diubah - sesuai UI "Email tidak dapat diubah".
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = request()->user();

        if (! $user->buyerProfile) {
            return $this->error('Profil tidak ditemukan untuk akun ini.', 404);
        }

        $user->buyerProfile->update($request->validated());

        return $this->success(new UserResource($user->load('buyerProfile')), 'Profil berhasil diperbarui.');
    }

    /**
     * Ubah password. Wajib verifikasi password lama terlebih dahulu.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = request()->user();
        $validated = $request->validated();

        if (! Hash::check($validated['password_lama'], $user->password)) {
            return $this->error('Password saat ini salah.', 422, [
                'password_lama' => ['Password saat ini tidak cocok.'],
            ]);
        }

        $user->update(['password' => Hash::make($validated['password_baru'])]);

        return $this->success(null, 'Password berhasil diperbarui.');
    }
}
