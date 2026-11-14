<?php

namespace App\Models;

use Laravel\Passport\Client;

/**
 * Klien OAuth2 (aplikasi pihak pertama yang memakai SSO e-Surat).
 *
 * Model ini meng-extend model Client bawaan Passport agar dapat menyimpan
 * konfigurasi tambahan (deskripsi, status aktif, allowlist role) tanpa
 * mengubah package.
 *
 * PENTING: relasi roles() di sini adalah GERBANG IZIN MASUK, bukan sumber
 * otorisasi di aplikasi klien. Role lokal aplikasi klien tetap milik aplikasi
 * tersebut.
 */
class OAuthClient extends Client
{
    protected $casts = [
        'grant_types' => 'array',
        'scopes' => 'array',
        'personal_access_client' => 'bool',
        'password_client' => 'bool',
        'revoked' => 'bool',
        'is_active' => 'bool',
        'require_email_verified' => 'bool',
    ];

    /**
     * Role e-Surat yang diizinkan login ke aplikasi ini.
     *
     * Kosong = tidak ada yang boleh masuk (deny by default).
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'oauth_client_roles', 'client_id', 'role_id')
            ->withTimestamps();
    }
}
