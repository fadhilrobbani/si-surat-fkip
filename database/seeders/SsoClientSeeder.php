<?php

namespace Database\Seeders;

use App\Models\OAuthClient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeder idempoten untuk klien SSO Sistem Bimbingan Skripsi.
 *
 * PENTING — satu klien OAuth2 hanya boleh memiliki SATU redirect URI.
 * Karena itu tiap environment (local / staging / production) WAJIB memakai
 * klien terpisah, bukan satu klien dengan banyak URI:
 *
 *   SSO_CLIENT_NAME="Sistem Bimbingan Skripsi (Staging)"
 *   SSO_SKRIPSI_REDIRECT_URI="http://localhost:8001/auth/esurat/callback"
 *
 * Secret HANYA dicetak ke konsol saat klien pertama kali dibuat. Setelah itu
 * tidak dapat dibaca lagi (Passport::hashClientSecrets aktif), jadi simpan
 * segera ke .env aplikasi klien. Bila secret hilang, pakai aksi
 * "Regenerate Secret" di Filament.
 */
class SsoClientSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('SSO_CLIENT_NAME') ?: 'Sistem Bimbingan Skripsi ('.ucfirst(app()->environment()).')';
        $redirect = env('SSO_SKRIPSI_REDIRECT_URI') ?: 'http://localhost:8001/auth/esurat/callback';

        $client = OAuthClient::where('name', $name)->first();

        if ($client) {
            $this->command->info("Klien SSO \"{$name}\" sudah ada — dilewati.");
            $this->command->line('Client ID    : '.$client->getKey());
            $this->command->line('Redirect URI : '.$client->redirect);
            $this->command->line('Allowed roles: '.($client->roles()->pluck('name')->implode(', ') ?: '(kosong — deny by default)'));

            if (! $client->is_active || $client->revoked) {
                $this->command->warn('PERHATIAN: klien ini tidak aktif / revoked.');
            }

            return;
        }

        $plainSecret = Str::random(40);

        $client = OAuthClient::create([
            'name' => $name,
            'description' => 'Aplikasi bimbingan tugas akhir mahasiswa FKIP.',
            'redirect' => $this->normalizeRedirect($redirect),
            'secret' => $plainSecret,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'is_active' => true,
            'require_email_verified' => false,
            'scopes' => ['profile', 'email'],
            'grant_types' => ['authorization_code', 'refresh_token'],
            'user_id' => null,
        ]);

        // HANYA mahasiswa. Staff/dosen/admin memakai akun lokal di aplikasi klien.
        $client->roles()->sync([User::ROLE_MAHASISWA]);

        $this->command->warn('=== SIMPAN SEKARANG, TIDAK AKAN DITAMPILKAN LAGI ===');
        $this->command->line('Environment   : '.app()->environment());
        $this->command->line('Client Name   : '.$client->name);
        $this->command->line('Client ID     : '.$client->getKey());
        $this->command->line('Client Secret : '.$plainSecret);
        $this->command->line('Redirect URI  : '.$client->redirect);
        $this->command->line('Allowed roles : mahasiswa');
        $this->command->warn('===================================================');
        $this->command->line('Simpan ke .env Sistem Skripsi:');
        $this->command->line('  ESURAT_CLIENT_ID='.$client->getKey());
        $this->command->line('  ESURAT_CLIENT_SECRET='.$plainSecret);
        $this->command->line('  ESURAT_REDIRECT_URI='.$client->redirect);
    }

    /**
     * Redirect URI harus cocok PERSIS dengan yang dikirim klien. Trailing slash
     * sering menjadi penyebab invalid_grant, jadi dinormalkan sejak awal.
     */
    protected function normalizeRedirect(string $redirect): string
    {
        return rtrim(trim($redirect), '/');
    }
}
