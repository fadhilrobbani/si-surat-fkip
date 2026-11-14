<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mencabut access token yang sedang dipakai (logout dari sisi aplikasi klien).
 *
 * Catatan: ini TIDAK menghapus session web e-Surat. Logout e-Surat tetap
 * ditangani AuthController::logout seperti biasa.
 */
class LogoutController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $token = $request->user()->token();

        if ($token) {
            $token->revoke();
            $token->refreshToken?->revoke();
        }

        return response()->json(['message' => 'Token dicabut.']);
    }
}
