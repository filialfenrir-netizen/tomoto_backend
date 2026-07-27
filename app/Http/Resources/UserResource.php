<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'role' => $this->role,
            'is_active' => $this->is_active,

            // Hanya terisi untuk buyer. Untuk admin, profile null - frontend
            // tidak perlu tahu bahwa data ini sebenarnya berasal dari tabel terpisah.
            'profile' => $this->whenLoaded('buyerProfile', function () {
                return [
                    'nama_lengkap' => $this->buyerProfile->nama_lengkap,
                    'email' => $this->buyerProfile->email,
                    'no_hp' => $this->buyerProfile->no_hp,
                    'alamat' => $this->buyerProfile->alamat,
                    'tanggal_lahir' => $this->buyerProfile->tanggal_lahir?->format('Y-m-d'),
                    'foto_profil' => $this->buyerProfile->foto_profil,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
