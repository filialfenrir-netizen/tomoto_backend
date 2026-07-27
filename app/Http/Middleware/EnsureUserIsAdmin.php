<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Menolak akses jika user belum login atau bukan admin.
     * Dipasang SETELAH middleware 'auth:sanctum' di route, jadi $request->user()
     * dijamin sudah terisi saat middleware ini dijalankan (kecuali guest, yang
     * juga akan ditolak di sini sebagai lapisan pengaman kedua).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses resource ini.',
            ], 403);
        }

        return $next($request);
    }
}
