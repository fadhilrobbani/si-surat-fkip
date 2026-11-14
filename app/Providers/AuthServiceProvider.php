<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Approval;
use App\Models\Surat;
use App\Models\User;
use App\Policies\ApprovalPolicy;
use App\Policies\FilePolicy;
use App\Policies\SuratPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Surat::class => SuratPolicy::class,
        Approval::class => ApprovalPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate::define('show-file-mahasiswa', [FilePolicy::class, 'showFileMahasiswa']);

        $this->registerPassport();
    }

    /**
     * Konfigurasi OAuth2 server (SSO mahasiswa).
     *
     * e-Surat berperan sebagai Identity Provider: aplikasi klien (mis. Sistem
     * Bimbingan Skripsi) memakai Authorization Code Grant + PKCE untuk
     * membuktikan identitas mahasiswa tanpa pernah menerima password.
     *
     * Catatan: scopes TIDAK memuat role/jabatan. Role hanya dipakai sebagai
     * gerbang izin masuk (lihat tabel oauth_client_roles), bukan isi token.
     */
    protected function registerPassport(): void
    {
        Passport::useClientModel(\App\Models\OAuthClient::class);

        // Route bawaan Passport dimatikan di AppServiceProvider::register()
        // (harus sebelum package boot). Hanya endpoint SSO yang didaftarkan
        // ulang, lihat RouteServiceProvider::registerOAuthRoutes().

        Passport::tokensCan([
            'profile' => 'Melihat identitas dasar (nama, NPM, program studi, jurusan)',
            'email' => 'Melihat alamat email',
        ]);

        Passport::tokensExpireIn(now()->addHour());
        Passport::refreshTokensExpireIn(now()->addDays(30));

        Passport::hashClientSecrets();

        Passport::authorizationView('oauth.authorize');
    }
}
