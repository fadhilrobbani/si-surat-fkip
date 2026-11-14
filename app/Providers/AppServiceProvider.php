<?php

namespace App\Providers;

use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Matikan route bawaan Passport SEBELUM package provider mem-boot.
        // Route bawaan menyertakan manajemen klien (/oauth/clients) untuk semua
        // user login — mahasiswa bisa membuat klien OAuth sendiri. Hanya route
        // SSO yang diperlukan yang didaftarkan ulang (RouteServiceProvider).
        // Harus di register(): seluruh register() berjalan sebelum boot().
        \Laravel\Passport\Passport::ignoreRoutes();

        // Ganti authorization endpoint Passport dengan versi yang memiliki
        // gerbang izin SSO. Binding harus di sini (register), karena route
        // Passport didaftarkan package provider yang boot lebih awal.
        $this->app->bind(
            \Laravel\Passport\Http\Controllers\AuthorizationController::class,
            \App\Http\Controllers\Sso\AuthorizationController::class
        );

        // Passport mengikat StatefulGuard secara kontekstual HANYA untuk kelas
        // aslinya. Subclass kita butuh binding yang sama, kalau tidak container
        // gagal me-resolve "StatefulGuard is not instantiable".
        $this->app->when(\App\Http\Controllers\Sso\AuthorizationController::class)
            ->needs(\Illuminate\Contracts\Auth\StatefulGuard::class)
            ->give(fn () => \Illuminate\Support\Facades\Auth::guard(config('passport.guard', null)));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        config(['app.locale' => 'id']);
        Carbon::setLocale('id');
        Paginator::useTailwind();
        Livewire::setScriptRoute(function ($handle) {
            return Route::get('/vendor/livewire/livewire.js', $handle);
        });
        view()->share('dateService', app(DateService::class));
    }
}
