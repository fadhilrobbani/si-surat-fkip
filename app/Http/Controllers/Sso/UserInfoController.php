<?php

namespace App\Http\Controllers\Sso;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Kontrak data identitas untuk aplikasi klien SSO.
 *
 * Prinsip:
 *  - `sub` adalah users.id (UUID) di e-Surat dan TIDAK PERNAH berubah.
 *    Kolom `username` (NPM) tidak unik sehingga tidak boleh dipakai sebagai sub.
 *  - Program studi dikirim sebagai `kode` (A1L0, dst), bukan id, karena id
 *    tersebut milik database e-Surat dan tidak bermakna di database klien.
 *  - `role_id` dan `nip` sengaja tidak dikirim: populasi SSO hanya mahasiswa,
 *    dan role tidak boleh diwarisi oleh aplikasi klien.
 */
class UserInfoController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->loadMissing(['programStudi.jurusan']);

        $payload = [
            'sub' => $user->getKey(),
            'name' => $user->name,
            'username' => $user->username,
            'program_studi' => $user->programStudi ? [
                'kode' => $user->programStudi->kode,
                'name' => $user->programStudi->name,
            ] : null,
            'jurusan' => $user->programStudi?->jurusan ? [
                'name' => $user->programStudi->jurusan->name,
            ] : null,
        ];

        if ($user->tokenCan('email')) {
            $payload['email'] = $user->email;
            $payload['email_verified'] = ! is_null($user->email_verified_at);
        }

        return response()->json($payload);
    }
}
