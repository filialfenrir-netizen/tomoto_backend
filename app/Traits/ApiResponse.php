<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    /**
     * Respons sukses dengan data.
     * Format: { success: true, data: ... }
     */
    protected function success(mixed $data = null, ?string $message = null, int $code = 200): JsonResponse
    {
        $payload = ['success' => true];

        if ($message !== null) {
            $payload['message'] = $message;
        }

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $code);
    }

    /**
     * Respons gagal dengan pesan (dan opsional detail error validasi per-field).
     * Format: { success: false, message: ..., errors?: {...} }
     */
    protected function error(string $message, int $code = 400, ?array $errors = null): JsonResponse
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $code);
    }
}
