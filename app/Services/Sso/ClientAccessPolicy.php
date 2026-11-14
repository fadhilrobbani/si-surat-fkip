<?php

namespace App\Services\Sso;

use App\Models\OAuthClient;
use App\Models\User;

/**
 * Gerbang izin SSO: menentukan apakah seorang user e-Surat boleh login ke
 * sebuah aplikasi klien.
 *
 * Aturan:
 *  - Deny by default. Allowlist kosong = tidak ada yang boleh masuk.
 *  - Query langsung ke tabel pivot SETIAP request (tanpa cache) supaya
 *    pencabutan role langsung berlaku tanpa perlu restart/deploy.
 *  - role_id HANYA dipakai sebagai gerbang, bukan untuk menentukan role di
 *    aplikasi klien. Aplikasi klien tetap pemilik otorisasinya sendiri.
 */
class ClientAccessPolicy
{
    public function isAllowed(User $user, OAuthClient $client): bool
    {
        if (! $client->is_active || $client->revoked) {
            return false;
        }

        if ($client->require_email_verified && is_null($user->email_verified_at)) {
            return false;
        }

        return $client->roles()
            ->where('roles.id', $user->role_id)
            ->exists();
    }

    /**
     * Alasan penolakan untuk ditampilkan pada halaman denied.
     *
     * @return string 'inactive'|'email_unverified'|'role_not_allowed'|''
     */
    public function denialReason(User $user, OAuthClient $client): string
    {
        if (! $client->is_active || $client->revoked) {
            return 'inactive';
        }

        if ($client->require_email_verified && is_null($user->email_verified_at)) {
            return 'email_unverified';
        }

        if (! $client->roles()->where('roles.id', $user->role_id)->exists()) {
            return 'role_not_allowed';
        }

        return '';
    }
}
